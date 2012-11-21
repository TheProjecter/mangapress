<?php
/**
 * MangaPress
 *
 * @package MangaPress
 * @subpackage MangaPress_Form_Element_Select
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
/**
 * MangaPress_Form_Element_Select
 *
 * @package MangaPress_Form_Element_Select
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class MangaPress_Form_Element_Select extends MangaPress_Form_Element
{
    /**
     * Options array (<option>)
     * @var array
     */
    public $options = array();

    /**
     * Returns the object as an HTML string
     *
     * @return string
     */
    public function __toString()
    {
        $options = $this->get_default();
        $attr_arr = array();
        foreach ($this->_attr as $name => $value) {
            if ($name != 'value')
                $attr_arr[] = "{$name}=\"{$value}\"";
        }

        $attr = implode(" ", $attr_arr);

        $value = $this->get_value();
        $options_str = "";
        foreach ($options as $option_val => $option_text) {
            $selected = selected($value, $option_val, false);
            $options_str .= "<option value=\"$option_val\" $selected>{$option_text}</option>";
        }

        $this->_html = "<select $attr>\n$options_str</select>";

        return $this->_html;
    }

    /**
     * Set default value
     *
     * @param mixed $values
     * @return MangaPress_Form_Element|MangaPress_Form_Element_Select
     */
    public function set_default($values)
    {
        foreach ($values as $key => $value) {
            $this->options[$key] = $value;
        }

        return $this;
    }

    /**
     * Get default values
     *
     * @return array|mixed
     */
    public function get_default()
    {
        return $this->options;
    }
}
