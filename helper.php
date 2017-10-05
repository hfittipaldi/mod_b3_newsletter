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

/**
 * Helper for mod_b3_newsletter
 *
 * @package     Joomla.Site
 * @subpackage  mod_b3_newsletter
 * @since       1.0
 */
class ModB3NewsletterHelper
{
    /**
     * Retrieves the destinatary of
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */
    public static function getRecipient($params)
    {
        $recipient = $params->get('email_recipient', 'email@email.com');

        if ($recipient === 'email@email.com' || $recipient === '')
        {
            $recipient = JFactory::getConfig()->get('mailfrom');
        }

        return $recipient;
    }

    /**
     * Validates a given email
     *
     * @since   1.0
     * @see     http://php.net/manual/en/function.filter-var.php
     *
     * @param   string  $value  passed email to be verified
     * @return  bool
     */
    public static function validateEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Load Recaptcha API if not already loaded
     */
    public static function loadRecaptcha()
    {
        $doc = JFactory::getDocument();
        $scripts = array_keys($doc->_scripts);
        $n_scripts = count($scripts);

        $scriptFound = false;
        for ($i = 0; $i < $n_scripts; $i++)
        {
            if (stripos($scripts[$i], 'api.js') !== false)
            {
                $scriptFound = true;
                break;
            }
        }

        if (!$scriptFound)
        {
            $doc->addScript("https://www.google.com/recaptcha/api.js");
        }
    }
}
