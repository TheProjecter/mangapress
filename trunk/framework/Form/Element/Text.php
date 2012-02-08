<?php
/**
 * @package Framework
 * @subpackage Text
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class Text extends Element
{
    
    public function __toString()
    {
        $label = '';
        if (!empty($this->_label)) {
            $id = $this->get_attributes('id');
            $class = " class=\"label-$id\"";
            $label = "<label for=\"$id\"$class>$this->_label</label>\r\n";
        }

        $htmlArray['content'] = $label . "<input type=\"text\" $attr />\r\n";

        $this->_html = implode(' ', $htmlArray);

        return $this->_html;
    }
}

