<?php
/**
 * MangaPress
 *
 * @package MangaPress
 * @subpackage MangaPress_Form_Element_Textarea
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
/**
 * MangaPress_Form_Element_Textarea
 *
 * @package MangaPress_Form_Element_Textarea
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class MangaPress_Form_Element_Textarea extends MangaPress_Form_Element
{
    public function __toString()
    {
        $attr = $this->build_attr_string();

        $htmlArray = array(
            'open'    => '<p>',
            'content' => '',
            'closing' => '<p>',
        );

        $label = '';
        if (!empty($this->_label)) {
            $id = $this->getAttributes('id');
            $class = " class=\"label-$id\"";
            $label = "<label for=\"$id\"$class>$this->_label</label>\r\n";
        }

        $htmlArray['content'] = $label . "<textarea $attr>%$this->_name%</textarea>\r\n";

        $this->_html = implode(' ', $htmlArray);

        return $this->_html;
    }

}
