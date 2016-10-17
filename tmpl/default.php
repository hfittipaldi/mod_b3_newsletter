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

// No direct access
defined('_JEXEC') or die;
?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" class="b3Newsletter">
    <fieldset><?php if ($pre_text != '') { ?><legend class="modnsintro"><?php echo $pre_text; ?></legend><?php } ?>
        <div class="form-group">
            <label class="sr-only"><?php echo $myNameLabel; ?></label>
            <input type="text" class="form-control" name="m_name<?php echo $unique_id; ?>" size="50" value="<?php echo $session->get('subscriberName'); ?>" placeholder="Nome completo" required />
        </div>
        <div class="form-group">
            <label class="sr-only"><?php echo $myEmailLabel; ?></label>
            <input type="email" class="form-control" name="m_email<?php echo $unique_id; ?>" value="<?php echo $session->get('subscriberEmail'); ?>" size="50" placeholder="Email" required />
        </div>

        <?php
        if ($captchaEnabled  && $enable_anti_spam)
        {
            echo '<div class="g-recaptcha" data-size="compact" data-sitekey="' . $key . '"></div>';
        }
        ?>

        <button type="submit" class="btn btn-primary"><?php echo $buttonText; ?></button>
    </fieldset>
</form>
