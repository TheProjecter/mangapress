<?php

class Select extends Element
{
    public $options = array();
    
    public function __toString()
    {
        return __CLASS__;
    }
    
    public function set_default($values)
    {
        foreach ($values as $key => $value) {
            $this->options[$key] = $value;
        }        
        
        return $this;
    }
}
