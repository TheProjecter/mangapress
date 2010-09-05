<?php
/**
 * @package MangaPress
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
/**
 * Fonts/Text Color Page
 * 
 * @package MangaPress
 * @subpackage MangaPress_Bundled_Theme
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
?>
<?php

    if ($_POST['action'] == 'set_theme_options') {
        $this->_theme_options = $this->set_theme_options($_POST);
        $new_values = $this->_theme_options;
    }
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        
        jQuery('.colorwheel').each(function(){
            var input = jQuery(this).parent('td.color-picker').find('.color-value');
            jQuery(this).farbtastic(input);
        })
        
        jQuery('.colorwheel').hide();
        
        jQuery('.color-button').click(function(e){    
            jQuery(this).parent('td.color-picker').find('div.colorwheel').toggle();            
        });

        jQuery('.colorwheel').blur(function(e){
            jQuery(this).hide();
        });

		jQuery(document).mousedown(function(){
			jQuery('.colorwheel').each( function() {
				var display = jQuery(this).css('display');
				if (display == 'block')
					jQuery(this).fadeOut(2);
			});
		});
    });
</script>
<div class="wrap">
    <div class="icon32" id="icon-options-general"><br /></div>
    <h2>Manga+Press Theme Options</h2>

    <?php if (isset($new_values)) : ?>
    <div class="updated below-h2" id="message">
        <p>Theme options have been updated. <a href="<?php echo get_bloginfo('url') ?>">Visit your site</a> to see how it looks.</p>
    </div>
    <?php endif; ?>
    
    <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
        <input type="hidden" name="_wp_nonce" value="<?php echo wp_create_nonce('mangapress-theme-options') ?>" />
        <input type="hidden" name="action" value="set_theme_options" />
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">Heading Font:  <p class="description">Sets the font for all headers (defined by H-tags)</p></th>
                    <td class="font-picker">
                        <select class="font-dropdown" id="header_font" name="mp_theme_opts[header-font]">

                        <?php foreach($this->fonts as $font_name => $font_family) : ?>
                            <option value="<?php echo $font_name ?>" <?php selected($font_name, $this->_theme_options['header-font'], true) ?>><?php echo $font_family ?></option>
                        <?php endforeach; ?>
                            
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="header_color">Heading Color:</label> <p class="description">Sets the color for all headers (defined by H-tags)</p></th>
                    <td class="color-picker">
                        <input id="header_color" class="color-value" name="mp_theme_opts[header-color]" type="text" value="<?php echo $this->_theme_options['header-color']; ?>" /><input id="open_header_color" type="button" value="Select Color" class="button-secondary color-button" />
                        <div class="colorwheel dropdown"></div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="body_font">Body Font:</label><p class="description">Sets the font for the body text.</p></th>
                    <td class="font-picker">
                        <select class="font-dropdown" id="body_font" name="mp_theme_opts[body-font]">

                        <?php foreach($this->fonts as $font_name => $font_family) : ?>
                            <option value="<?php echo $font_name ?>" <?php selected($font_name, $this->_theme_options['body-font'], true) ?>><?php echo $font_family ?></option>
                        <?php endforeach; ?>

                        </select>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="body_color">Body Color:</label> <p class="description">Sets the color for the body text.</p></th>
                    <td class="color-picker">
                        <input id="body_color" class="color-value" name="mp_theme_opts[body-color]" type="text" value="<?php echo $this->_theme_options['body-color'] ?>" /><input type="button" value="Select Color" class="button-secondary color-button" />
                        <div class="colorwheel dropdown"></div>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="link_color">Link Color:</label> <p class="description">Sets the color for the normal link state.</p></th>
                    <td class="color-picker">
                        <input id="link_color" class="color-value" name="mp_theme_opts[link-color]" type="text" value="<?php echo $this->_theme_options['link-color'] ?>" /><input type="button" value="Select Color" class="button-secondary color-button" />
                        <div class="colorwheel dropdown"></div>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="vlink_color">Visited Link Color:</label> <p class="description">Sets the color for the visited link state.</p></th>
                    <td class="color-picker">
                        <input id="vlink_color" class="color-value" name="mp_theme_opts[vlink-color]" type="text" value="<?php echo $this->_theme_options['vlink-color'] ?>" /><input type="button" value="Select Color" class="button-secondary color-button" />
                        <div class="colorwheel dropdown"></div>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="hlink_color">Hover Link Color:</label> <p class="description">Sets the color for the hover link state.</p></th>
                    <td class="color-picker">
                        <input id="hlink_color" class="color-value" name="mp_theme_opts[hlink-color]" type="text" value="<?php echo $this->_theme_options['hlink-color'] ?>" /><input type="button" value="Select Color" class="button-secondary color-button" />
                        <div class="colorwheel dropdown"></div>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="alink_color">Active Link Color:</label> <p class="description">Sets the color for the active link state.</p></th>
                    <td class="color-picker">
                        <input id="alink_color" class="color-value" name="mp_theme_opts[alink-color]" type="text" value="<?php echo $this->_theme_options['alink-color'] ?>" /><input type="button" value="Select Color" class="button-secondary color-button" />
                        <div class="colorwheel dropdown"></div>
                    </td>
                </tr>

            </tbody>
        </table>
        <p class="submit">
            <input type="submit" value="Save Changes" class="button-primary" name="Submit">
        </p>
    </form>
</div>