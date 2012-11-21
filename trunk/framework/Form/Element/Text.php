<?php
/**
 * MangaPress
 *
 * @package MangaPress
 * @subpackage MangaPress_Form_Element_Text
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
/**
 * MangaPress_Form_Element_Text
 *
 * @package MangaPress_Form_Element_Text
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class MangaPress_Form_Element_Text extends MangaPress_Form_Element
{

    /**
     * Returns the object as an HTML string
     *
     * @return string
     */
    public function __toString()
    {
        $label = '';
        if (!empty($this->_label)) {
            $id = $this->get_attributes('id');
            $class = " class=\"label-$id\"";
            $label = "<label for=\"$id\"$class>$this->_label</label>\r\n";
        }

        $desc = $this->get_description();
        $description = "";
        if ($desc) {
            $description = "<span class=\"description\">{$desc}</span>";
        }

        $attr = $this->build_attr_string();

        $htmlArray['content'] = "{$label}<input type=\"text\" $attr />\r\n{$description}";

        $this->_html = implode(' ', $htmlArray);

        return $this->_html;
    }
}
