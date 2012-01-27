<?php
/**
 * @package Manga_Press
 * @subpackage MangaPress_Bundled_Theme
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
/**
 * Fonts/Text Color Page
 * 
 * @package MangaPress_Bundled_Theme
 * @subpackage Admin_Page_Fonts
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */

global $theme_dir;
?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        
        $('.colorwheel').each(function(){
            var input = $(this).parents('td').find('.color-value');
            $(this).farbtastic(input);
        })
        
        $('.colorwheel').hide();
        
        $('.color-button').click(function(e){    
            $(this).parents('td').find('div.colorwheel').toggle();            
        });

        $('.colorwheel').blur(function(e){
            $(this).hide();
        });

        $(document).mousedown(function(){
        $('.colorwheel').each( function() {
                var display = $(this).css('display');
                if (display == 'block')
                    $(this).fadeOut(2);
            });
        });
    });
</script>

<div class="wrap">
    
    <?php screen_icon(); ?>
    
    <h2>Manga+Press Theme Options</h2>
    
    <form action="options.php" method="post" id="mp-theme-options-form">
        <?php settings_fields('theme_mods_mp-default'); ?>
        
        <?php do_settings_sections("mangapress-theme"); ?>
        
        <p class="submit">
            <?php submit_button(__('Save Settings', 'mangapress'), 'primary', 'submit', false); ?>
            <?php submit_button(__('Reset Defaults', $theme_dir), 'secondary', 'reset', false); ?>
        </p>
        
    </form>
    
</div>