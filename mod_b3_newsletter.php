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

$app     = JFactory::getApplication();
$session = JFactory::getSession();
$jinput  = $app->input;

// Form fields
$myNameLabel  = $params->get('name_label', JText::_('MOD_B3_NEWSLETTER_NAME_DEFAULT'));
$myEmailLabel = $params->get('email_label', JText::_('MOD_B3_NEWSLETTER_EMAIL_DEFAULT'));
$buttonText = $params->get('button_text', JText::_('MOD_B3_NEWSLETTER_BUTTON_TEXT_DEFAULT'));
$pre_text = $params->get('pre_text', '');
$unique_id = $params->get('unique_id', '');

// Mail options
$recipient    = modB3NewsletterHelper::getRecipient($params);
$subject             = $params->get('subject', JText::_('MOD_B3_NEWSLETTER_SUBJECT_DEFAULT'));
$fromName            = $params->get('from_name', JText::_('MOD_B3_NEWSLETTER_FROM_NAME_DEFAULT'));
$fromEmail           = $params->get('from_email', JText::_('MOD_B3_NEWSLETTER_FROM_EMAIL_DEFAULT'));
$sendingWithSetEmail = $params->get('sending_from_set', true);

$custom_redirect = $params->get('custom_redirect', null);
if ($custom_redirect !== null)
{
    $url = $custom_redirect;
}
else
{
    $url = JRoute::_('index.php');
}
$url = htmlentities($url, ENT_COMPAT, "UTF-8");

$enable_anti_spam = $params->get('enable_anti_spam', true);

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

// Messages
$pageText     = $params->get('page_text', JText::_('MOD_B3_NEWSLETTER_PAGE_TEXT_DEFAULT'));
$errorText    = $params->get('errot_text', JText::_('MOD_B3_NEWSLETTER_ERROR_TEXT_DEFAULT'));
$noName       = $params->get('no_name', JText::_('MOD_B3_NEWSLETTER_NO_NAME_DEFAULT'));
$invalidEmail = $params->get('invalid_email', JText::_('MOD_B3_NEWSLETTER_INVALID_EMAIL_DEFAULT'));

// List
$saveList = $params->get('save_list', true);

// Retrived data
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
