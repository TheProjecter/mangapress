<?php
/**
 * @package Manga_Press
 * @subpackage Pages
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

/**
 * @package Pages
 * @subpackage Upgrade
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
    $msg = '';

    if ( ! current_user_can('update_plugins') )
            wp_die(__('You do not have sufficient permissions to remove this plugin!', 'mangapress'));

    if ( count($_POST) > 0 ){
            if ($_POST['action'] == 'upgrade_mangapress') $msg = mangapress_upgrade();
    }
?>
<script type="text/javascript">
    jQuery(function() {
            jQuery('#row_confirm').hide();
    });
</script>
<div class="wrap">
    <div id="mangapress_uninstall">
    	<form action="<?php echo $_SERVER['REQUEST_URI']?>" method="post" id="mangapress_upgrade_form">
        <?php wp_nonce_field('mangapress-upgrade-form'); ?>
        <h3><?php _e('Upgrade Manga+Press', 'mangapress'); ?></h3>
        <p class="description"><?php _e("Use this section to upgrade Manga+Press from a previous version. Make sure to back up your Wordpress database before proceeding!", 'mangapress'); ?></p>
        <fieldset class="options">
        <table class="form-table">
        <input type="hidden" name="action" value="upgrade_mangapress" />
            <tr>
                <td><label><?php _e('Upgrade Manga+Press?', 'mangapress'); ?> <input type="button" name="upgrade" id="upgrade_btn" value="<?php _e('Yes', 'mangapress'); ?>" onclick="jQuery('#row_confirm').show('slow')" class="yes-btn" style="background: #6F9; border: 1px solid green!important; width: 200px;" /></label></td>
            </tr>
            <tr>
            	<td><div id="row_confirm" style="border: 1px solid red; padding: 5px; margin: 5px; width: 50%;"><label><?php _e('Confirm. Upgrade Mangapress?', 'mangapress'); ?> <input type="submit" id="confirm_btn" value="<?php _e('Yes, Upgrade', 'mangapress'); ?>" class="remove-btn" style="background:#FCC; border: 1px solid red!important; width: 200px;" /></label> <input type="button" name="cancel" id="cancel_btn" value="<?php _e('Cancel', 'mangapress'); ?>" onclick="jQuery('#row_confirm').hide('slow')" /></div></td>
            </tr>
            <tr>
                <td><div id="confirm_message"><?php echo $msg; ?></div></td>
            </tr>
        </table>
        </fieldset>
        </form>
    </div>
</div>