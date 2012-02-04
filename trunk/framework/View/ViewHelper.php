<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ViewHelper
 *
 * @author Jessica
 */
abstract class ViewHelper
{
    protected $_post_type;
    
    abstract public function init();
    
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->set_options($options)
                 ->init();
        }
    }
    
    public function set_options($options)
    {

        foreach ($options as $option_name => $value) {
            $method = 'set_' . $option_name;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        
        return $this;
    }
    
    public function is_post_type($post_type)
    {
        if (is_array($this->_post_type)) {
            return in_array($post_type, $this->_post_type);
        } else if (is_string($this->_post_type)) {
            return ($this->_post_type == $post_type);
        } else {
            return false;
        }
    }
    
}
