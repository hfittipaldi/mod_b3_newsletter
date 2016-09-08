<?php
/**
 * B3 Newsletter Module
 *
 * @package     Joomla.Site
 * @subpackage  mod_b3_newsletter
 *
 * @author      Hugo Fittipaldi <hugo.fittipaldi@gmail.com>
 * @copyright   Copyright (C) 2016 Hugo Fittipaldi. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 * @link        https://github.com/hfittipaldi/mod_b3_newsletter
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$captchaEnabled = false;

foreach (JPluginHelper::getPlugin('captcha') as $plugin)
{
    $plg_params = json_decode($plugin->params);
    $key        = $plg_params->public_key;
    $secret     = $plg_params->private_key;

    if ($plugin->name === 'recaptcha')
    {
        $captchaEnabled = true;
        JFactory::getDocument()->addScript("https://www.google.com/recaptcha/api.js");

        break;
    }
}

$app     = JFactory::getApplication();
$session = JFactory::getSession();
$jinput  = $app->input;

$recipient    = modB3NewsletterHelper::getRecipient($params);

$myNameLabel  = $params->get('name_label', 'Name:');
$myEmailLabel = $params->get('email_label', 'Email:');

$buttonText = $params->get('button_text', 'Subscribe to Newsletter');
$pageText   = $params->get('page_text', 'Thank you for subscribing to our site.');
$errorText  = $params->get('errot_text', 'Your subscription could not be submitted. Please try again.');

$subject             = $params->get('subject', 'New subscription to your site!');
$fromName            = $params->get('from_name', 'Newsletter Subscriber');
$fromEmail           = $params->get('from_email', 'newsletter_subscriber@yoursite.com');
$sendingWithSetEmail = $params->get('sending_from_set', true);

$noName       = $params->get('no_name', 'Please write your name');
$invalidEmail = $params->get('invalid_email', 'Please write a valid email');

$saveList = $params->get('save_list', true);

$pre_text = $params->get('pre_text', '');

$fixed_url = $params->get('fixed_url', true);
$fixed_url_address = $params->get('fixed_url_address', null);
if ($fixed_url && $fixed_url_address !== null)
{
    $url = JRoute::_($fixed_url_address);
}
else
{
    $url = JRoute::_('index.php');
}

$url = htmlentities($url, ENT_COMPAT, "UTF-8");

$unique_id = $params->get('unique_id', "");

$enable_anti_spam = $params->get('enable_anti_spam', true);

$subscriberName  = $jinput->getString('m_name'.$unique_id, null);
$subscriberEmail = strtolower($jinput->getString('m_email'.$unique_id, null));

if ($subscriberName !== null)
{
    $session->set('subscriberName', $subscriberName);
    $session->set('subscriberEmail', $subscriberEmail);

    $errors = 0;
    if ($enable_anti_spam)
    {
        $captcha_data = $jinput->getString('g-recaptcha-response', null);
        if ($captcha_data !== null)
        {
            $resposta = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $captcha_data . "&remoteip=" . $_SERVER['REMOTE_ADDR']));

            if ($resposta->success === false)
            {
                $app->enqueueMessage(JText::_('Wrong anti-spam answer'), 'warning');
                $errors++;
            }
        }
    }

    if ($subscriberName === "")
    {
        $app->enqueueMessage(JText::_($noName), 'warning');
        $errors++;
    }

    if (modB3NewsletterHelper::validateEmail($subscriberEmail) === false)
    {
        $app->enqueueMessage(JText::_($invalidEmail), 'warning');
        $errors++;
    }

    if ($errors === 0)
    {
        require JModuleHelper::getLayoutPath('mod_b3_newsletter', $params->get('layout_mail') . '_mail');

        $mailSender = JFactory::getMailer();
        $mailSender->addRecipient($recipient);
        if ($sendingWithSetEmail)
        {
            $mailSender->setSender(array($fromEmail, $fromName));
        }
        else
        {
            $mailSender->setSender(array($subscriberName, $subscriberEmail));
        }

        $mailSender->setSubject($subject);
        $mailSender->setBody($myMessage);

        if (!$mailSender->Send())
        {
            $app->enqueueMessage(JText::_($errorText), 'error');
        }
        else
        {
            if ($saveList)
            {
                $savePath = JPATH_BASE . '/images/mailing_list.csv';
                $file = fopen($savePath, "a");
                fwrite($file, PHP_EOL . $subscriberName . ";" . $subscriberEmail);
                fclose($file);
            }

            $app->enqueueMessage(JText::_($pageText), 'success');
        }

        $session->clear('subscriberName');
        $session->clear('subscriberEmail');
    }

    $app->redirect($url);
}

require JModuleHelper::getLayoutPath('mod_b3_newsletter', $params->get('layout', 'default'));

// Clear existing sessions
$session->clear('subscriberName');
$session->clear('subscriberEmail');
