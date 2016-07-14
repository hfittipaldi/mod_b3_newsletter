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
            $config = JFactory::getConfig();
            $recipient = $config->get('mailfrom');
        }

        return $recipient;
    }
}