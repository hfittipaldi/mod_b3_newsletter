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

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

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
$noEmail      = $params->get('no_email', 'Please write your email');
$invalidEmail = $params->get('invalid_email', 'Please write a valid email');

$saveList = $params->get('save_list', true);
$savePath = $params->get('save_path', 'mailing_list.csv');

$pre_text = $params->get('pre_text', '');

$disable_https = $params->get('disable_https', true);

$exact_url = $params->get('exact_url', true);
if (!$exact_url)
{
    $url = JURI::current();
}
else
{
    if (!$disable_https)
    {
        $url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }
    else
    {
        $url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }
}

$fixed_url = $params->get('fixed_url', true);
if ($fixed_url)
{
    $url = $params->get('fixed_url_address', "");
}

$url = htmlentities($url, ENT_COMPAT, "UTF-8");

$unique_id = $params->get('unique_id', "");

$enable_anti_spam = $params->get('enable_anti_spam', true);
$myAntiSpamQuestion = $params->get('anti_spam_q', 'How many eyes has a typical person? (ex: 1)');
$myAntiSpamAnswer = $params->get('anti_spam_a', '2');

$errors = 3;

$subscriberName  = $jinput->getString('m_name'.$unique_id, null);
$subscriberEmail = strtolower($jinput->getString('m_email'.$unique_id, null));

if ($subscriberName !== null)
{
    $session->set('subscriberName', $subscriberName);
    $session->set('subscriberEmail', $subscriberEmail);

    $errors = 0;
    if ($enable_anti_spam)
    {
        if ($jinput->getString("modns_anti_spam_answer".$unique_id) != $myAntiSpamAnswer)
        {
            $app->enqueueMessage(JText::_('Wrong anti-spam answer'), 'warning');
        }
    }

    if ($subscriberName === "")
    {
        $app->enqueueMessage(JText::_($noName), 'warning');
        $errors = $errors + 1;
    }

    if ($subscriberEmail === "")
    {
        $app->enqueueMessage(JText::_($noEmail), 'warning');
        $errors = $errors + 2;
    }
    elseif (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $subscriberEmail))
    {
        $app->enqueueMessage(JText::_($invalidEmail), 'warning');
        $errors = $errors + 2;
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
                $savePath = JPATH_BASE . '/images/' . $savePath;
                $file = fopen($savePath, "a");
                fwrite($file, "\n" . $subscriberName . ";" . $subscriberEmail);
                fclose($file);
            }

            $app->enqueueMessage(JText::_($pageText), 'success');
        }

        $session->clear('subscriberName');
        $session->clear('subscriberEmail');
        $session->clear('errors');
    }
    else
    {
        $session->set('errors', $errors);
    }

    $app->redirect($url);
}

require JModuleHelper::getLayoutPath('mod_b3_newsletter', $params->get('layout', 'default'));
