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
defined( '_JEXEC' ) or die;

date_default_timezone_set($app->get('offset'));

$myMessage = 'Sent: ' . date("r") . "\n" .
             $myNameLabel . ' ' . $subscriberName . ",\n" .
             $myEmailLabel . ' ' . $subscriberEmail;
