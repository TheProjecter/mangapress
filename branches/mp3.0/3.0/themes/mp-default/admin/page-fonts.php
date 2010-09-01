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

    if ($_POST['action'] == 'set_theme_options')
        $this->_theme_options = $this->set_theme_options($_POST);
?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('#colorPickerBody').farbtastic('#body_color');
        jQuery('#colorPickerHeader').farbtastic('#header_color');

        jQuery('#colorPickerBody').hide();
        jQuery('#colorPickerHeader').hide();

        jQuery('#open_body_color').click(function(e){
            jQuery('#colorPickerBody').toggle()
        });

        jQuery('#open_header_color').click(function(e){
            jQuery('#colorPickerHeader').toggle()
        });

     });
</script>
<?php
/*
 * @todo Add Updated text
 */
?>
<div class="wrap">
    <div class="icon32" id="icon-options-general"><br /></div>
    <h2>Manga+Press Theme Options</h2>

    <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
        <input type="hidden" name="_wp_nonce" value="<?php echo wp_create_nonce('mangapress-theme-options') ?>" />
        <input type="hidden" name="action" value="set_theme_options" />
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">Heading Font:  <p class="description">Sets the font for all headers (defined by H-tags)</p></th>
                    <td>
                        <select id="header_font" name="mp_theme_opts[header-font]">

                        <?php foreach($this->fonts as $font_name => $font_family) : ?>
                            <option value="<?php echo $font_name ?>" <?php selected($font_name, $this->_theme_options['header-font'], true) ?>><?php echo $font_family ?></option>
                        <?php endforeach; ?>
                            
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="header_color">Heading Color:</label> <p class="description">Sets the color for all headers (defined by H-tags)</p></th>
                    <td>
                        <input id="header_color" name="mp_theme_opts[header-color]" type="text" value="<?php echo $this->_theme_options['header-color']; ?>" /><input id="open_header_color" type="button" value="Select Color" class="button-secondary" />
                        <div id="colorPickerHeader" class="dropdown"></div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="body_font">Body Font:</label><p class="description">Sets the font for the body text.</p></th>
                    <td>
                        <select id="body_font" name="mp_theme_opts[body-font]">

                        <?php foreach($this->fonts as $font_name => $font_family) : ?>
                            <option value="<?php echo $font_name ?>" <?php selected($font_name, $this->_theme_options['body-font'], true) ?>><?php echo $font_family ?></option>
                        <?php endforeach; ?>

                        </select>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="body_color">Body Color:</label> <p class="description">Sets the color for the body text.</p></th>
                    <td>
                        <input id="body_color" name="mp_theme_opts[body-color]" type="text" value="<?php echo $this->_theme_options['body-color'] ?>" /><input id="open_body_color" type="button" value="Select Color" class="button-secondary" />
                        <div id="colorPickerBody" class="dropdown"></div>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="link_color">Link Color:</label> <p class="description">Sets the color for the body text.</p></th>
                    <td>
                        <input id="link_color" name="mp_theme_opts[link-color]" type="text" value="<?php echo $this->_theme_options['link-color'] ?>" /><input id="open_link_color" type="button" value="Select Color" class="button-secondary" />
                        <div id="colorPickerLink" class="dropdown"></div>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="vlink_color">Visited Link Color:</label> <p class="description">Sets the color for the body text.</p></th>
                    <td>
                        <input id="vlink_color" name="mp_theme_opts[vlink-color]" type="text" value="<?php echo $this->_theme_options['vlink-color'] ?>" /><input id="open_vlink_color" type="button" value="Select Color" class="button-secondary" />
                        <div id="colorPickerVLink" class="dropdown"></div>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="hlink_color">Hover Link Color:</label> <p class="description">Sets the color for the body text.</p></th>
                    <td>
                        <input id="hlink_color" name="mp_theme_opts[hlink-color]" type="text" value="<?php echo $this->_theme_options['hlink-color'] ?>" /><input id="open_hlink_color" type="button" value="Select Color" class="button-secondary" />
                        <div id="colorPickerHLink" class="dropdown"></div>
                    </td>
                </tr>
               <tr>
                    <th scope="row"><label for="body_color">Hover Link Color:</label> <p class="description">Sets the color for the body text.</p></th>
                    <td>
                        <input id="body_color" name="mp_theme_opts[body-color]" type="text" value="<?php echo $this->_theme_options['body-color'] ?>" /><input id="open_body_color" type="button" value="Select Color" class="button-secondary" />
                        <div id="colorPickerBody" class="dropdown"></div>
                    </td>
                </tr>

            </tbody>
        </table>
        <p class="submit">
            <input type="submit" value="Save Changes" class="button-primary" name="Submit">
        </p>
    </form>
</div>