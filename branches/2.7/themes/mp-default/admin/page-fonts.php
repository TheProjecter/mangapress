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
?>
<?php
    global $current_screen;
    $screen = new WP_Screen();
    var_dump($current_screen);
    if ($_POST['action'] == 'set_theme_options') {
        $new_values = $this->set_theme_options($_POST);

        // update function should go here.
        $diff = array_diff($new_values, $this->_theme_options);
        if (!empty($diff)) {
            $status = $this->update_css_files();
            $error_msg = '';
            if (is_wp_error($status)) {
                $error_msg = "<p class=\"error\">" . $status->get_error_message() . "</p>";
            }
        }

        $new_values = $this->_theme_options;
    }
?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        
        $('.colorwheel').each(function(){
            var input = $(this).parent('td.color-picker').find('.color-value');
            $(this).farbtastic(input);
        })
        
        $('.colorwheel').hide();
        
        $('.color-button').click(function(e){    
            $(this).parent('td.color-picker').find('div.colorwheel').toggle();            
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
    <div class="icon32" id="icon-options-general"><br /></div>
    <h2>Manga+Press Theme Options</h2>

    <?php if (isset($new_values)) : ?>
    <div class="updated below-h2" id="message">
        <p><?php _e("Theme options have been updated. <a href=\"" . get_bloginfo('url') . "\">Visit your site</a> to see how it looks.", $theme_dir) ?></p>
        <?php echo $error_msg;  ?>
    </div>
    <?php endif; ?>
    
    <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
        <input type="hidden" name="_wp_nonce" value="<?php echo wp_create_nonce('mangapress-theme-options') ?>" />
        <input type="hidden" name="action" value="set_theme_options" />
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php _e("Heading Font:  <p class=\"description\">Sets the font for all headers (defined by H-tags)</p>", $theme_dir) ?></th>
                    <td class="font-picker">
                        <select class="font-dropdown" id="header_font" name="mp_theme_opts[header-font]">

                        <?php foreach($this->fonts as $font_name => $font_family) : ?>
                            <option value="<?php echo $font_name ?>" <?php selected($font_name, $this->_theme_options['header-font'], true) ?>><?php echo $font_family ?></option>
                        <?php endforeach; ?>
                            
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="header_color"><?php _e("Heading Color:", $theme_dir) ?></label> <p class="description"><?php _e("Sets the color for all headers (defined by H-tags)", $theme_dir); ?></p></th>
                    <td class="color-picker">
                        <input id="header_color" class="color-value" name="mp_theme_opts[header-color]" type="text" value="<?php echo $this->_theme_options['header-color']; ?>" /><input id="open_header_color" type="button" value="<?php _e("Select Color", $theme_dir) ?>" class="button-secondary color-button" />
                        <div class="colorwheel dropdown"></div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="body_font"><?php _e("Body Font:", $theme_dir) ?></label><p class="description"><?php _e("Sets the font for the body text.", $theme_dir) ?></p></th>
                    <td class="font-picker">
                        <select class="font-dropdown" id="body_font" name="mp_theme_opts[body-font]">

                        <?php foreach($this->fonts as $font_name => $font_family) : ?>
                            <option value="<?php echo $font_name ?>" <?php selected($font_name, $this->_theme_options['body-font'], true) ?>><?php echo $font_family ?></option>
                        <?php endforeach; ?>

                        </select>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="body_color"><?php _e("Body Color:", $theme_dir) ?></label> <p class="description"><?php _e("Sets the color for the body text.", $theme_dir) ?></p></th>
                    <td class="color-picker">
                        <input id="body_color" class="color-value" name="mp_theme_opts[body-color]" type="text" value="<?php echo $this->_theme_options['body-color'] ?>" /><input type="button" value="<?php _e("Select Color", $theme_dir); ?>" class="button-secondary color-button" />
                        <div class="colorwheel dropdown"></div>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="link_color"><?php _e("Link Color:", $theme_dir) ?></label> <p class="description"<?php _e("Sets the color for the normal link state.", $theme_dir) ?></p></th>
                    <td class="color-picker">
                        <input id="link_color" class="color-value" name="mp_theme_opts[link-color]" type="text" value="<?php echo $this->_theme_options['link-color'] ?>" /><input type="button" value="<?php _e("Select Color", $theme_dir) ?>" class="button-secondary color-button" />
                        <div class="colorwheel dropdown"></div>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="vlink_color"><?php _e("Visited Link Color:", $theme_dir) ?></label> <p class="description"><?php _e("Sets the color for the visited link state.", $theme_dir) ?></p></th>
                    <td class="color-picker">
                        <input id="vlink_color" class="color-value" name="mp_theme_opts[vlink-color]" type="text" value="<?php echo $this->_theme_options['vlink-color'] ?>" /><input type="button" value="<?php _e("Select Color", $theme_dir) ?>" class="button-secondary color-button" />
                        <div class="colorwheel dropdown"></div>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="hlink_color"><?php _e("Hover Link Color:", $theme_dir); ?></label> <p class="description"><?php _e("Sets the color for the hover link state.", $theme_dir) ?></p></th>
                    <td class="color-picker">
                        <input id="hlink_color" class="color-value" name="mp_theme_opts[hlink-color]" type="text" value="<?php echo $this->_theme_options['hlink-color'] ?>" /><input type="button" value="<?php _e("Select Color", $theme_dir) ?>" class="button-secondary color-button" />
                        <div class="colorwheel dropdown"></div>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="alink_color"><?php _e("Active Link Color:", $theme_dir); ?></label> <p class="description"><?php _e("Sets the color for the active link state.", $theme_dir) ?></p></th>
                    <td class="color-picker">
                        <input id="alink_color" class="color-value" name="mp_theme_opts[alink-color]" type="text" value="<?php echo $this->_theme_options['alink-color'] ?>" /><input type="button" value="<?php _e("Select Color", $theme_dir) ?>" class="button-secondary color-button" />
                        <div class="colorwheel dropdown"></div>
                    </td>
                </tr>

            </tbody>
        </table>
        <p class="submit">
            <input type="submit" value="<?php _e("Save Changes", $theme_dir) ?>" class="button-primary" name="submit">
        </p>
    </form>
</div>