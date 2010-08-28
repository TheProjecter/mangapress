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
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('#colorPickerCopy').farbtastic('#copy_color');
        jQuery('#colorPickerHeader').farbtastic('#header_color');
        //jQuery('#colorPickerFooterBG').farbtastic('#footer_bg_color');
        //jQuery('#colorPickerFooterTxt').farbtastic('#footer_txt_color');
    });
</script>

<div class="wrap">
    <div class="icon32" id="icon-options-general"><br /></div>
    <h2>Manga+Press Theme Options</h2>

    <form action="options.php" method="post">
        <?php settings_fields('portfolio-options'); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">Heading Font: </th>
                    <td>
                        <select id="header_font" name="port_options[header-font]">
                            <?php
                                $font_stack_html = array();
                                foreach($this->fonts as $key=>$value)
                                    array_push($font_stack_html, '<option value="'.$key.'" '.selected( $key, $pfolio_options['header-font']).'>'.$value.'</option>');

                                echo implode($font_stack_html, '\n' )
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Body Font: </th>
                    <td>
                        <select id="body_font" name="port_options[body-font]">
                            <?php
                                $font_stack_html = array();
                                foreach($this->fonts as $key=>$value)
                                    array_push($font_stack_html, '<option value="'.$key.'" '.selected( $key, $pfolio_options['body-font']).'>'.$value.'</option>');

                                echo implode($font_stack_html, '\n' )
                            ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>