<?php
/**
 * MangaPress
 *
 * @package MangaPress
 * @subpackage MangaPress_Form_Element_Checkbox
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
/**
 * MangaPress_Form_Element_Checkbox
 * @package MangaPress_Form_Element_Checkbox
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class MangaPress_Form_Element_Checkbox extends MangaPress_Form_Element
{
    /**
     * Return the form element as HTML
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
        if ($desc) {
            $description = "<span class=\"description\">{$desc}</span>";
        }

        $default = $this->get_default();
        $attr_arr = array();
        foreach ($this->_attr as $name => $value) {
            if ($name != 'value')
                $attr_arr[] = "{$name}=\"{$value}\"";
            else
                $attr_arr[] = "{$name}=\"" . $default . "\"";
        }

        $attr = implode(" ", $attr_arr);

        $checked = checked($default, $this->get_value(), false);

        $html_array['content'] = "{$label}<input type=\"checkbox\" $attr $checked />\r\n{$description}";

        $this->_html = implode(' ', $html_array);

        return $this->_html;

    }
}
