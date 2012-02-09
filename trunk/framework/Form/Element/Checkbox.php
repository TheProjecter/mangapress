<?php

class Checkbox extends Element
{
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

        //$this->set_attributes(array('value' => $this->get_default()));
        var_dump($this->_attr['value'], $this->get_default());
        $attr = $this->build_attr_string();
        $checked = checked('1', $this->get_value(), false);

        $htmlArray['content'] = "{$label}<input type=\"checkbox\" $attr $checked />\r\n{$description}";

        $this->_html = implode(' ', $htmlArray);

        return $this->_html;

    }
}