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

$errors = $session->get('errors', '');
?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" role="form" novalidate>
    <fieldset><legend class="modnsintro"><?php echo $pre_text; ?></legend>
        <?php if ($enable_anti_spam) : ?>
        <div class="form-group">
            <label for="modns_anti_spam_answer<?php echo $unique_id; ?>"><?php echo $myAntiSpamQuestion; ?></label>
            <input type="text" class="form-control" name="modns_anti_spam_answer<?php echo $unique_id; ?>" />
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="m_name<?php echo $unique_id; ?>" class="sr-only"><?php echo $myNameLabel; ?></label>
            <input type="text" class="form-control" name="m_name<?php echo $unique_id; ?>" size="50"<?php echo (($errors & 1) != 1) ? ' value="' . $session->get('subscriberName'). '"' : ''; ?> placeholder="Nome completo" />
        </div>
        <div class="form-group">
            <label for="m_email<?php echo $unique_id; ?>" class="sr-only"><?php echo $myEmailLabel; ?></label>
            <input type="email" class="form-control" name="m_email<?php echo $unique_id; ?>" size="50"<?php echo (($errors & 2) != 2) ? ' value="' . $session->get('subscriberEmail') . '"' : ''; ?> placeholder="Email" />
        </div>

        <button type="submit" class="btn btn-primary"><?php echo $buttonText; ?></button>
    </fieldset>
</form>