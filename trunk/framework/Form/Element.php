<?php

class Element
{
    protected $_attr;

    protected $_label;

    protected $_name;
    
    protected $_default_value;

    protected $_data_type;
    
    protected $_validation;
    
    protected $_html;

    protected $_form_ID;
    
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->set_options($options);
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
    
    public function add_attributes(array $attributes = array())
    {
        foreach ($attributes as $attr => $value) {
            $this->set_attributes($attr, $value);
        }

        return $this;
    }

    public function get_attributes($key)
    {
        if (!isset($this->_attr[$key])) {
            return null;
        }

        return $this->_attr[$key];
    }

    public function set_attributes($attr)
    {
        foreach ($attr as $key => $value)
            $this->_attr[$key] = $value;

        return $this;

    }

    public function set_label($text = '') {

        $this->_label = $text;

        return $this;
    }
    
    public function set_default($default)
    {
        $this->_default_value = $default;
        
        return $this;
    }
    
    public function set_validation($validation)
    {
        $this->_validation = $validation;
        
        return $this;
    }
    
    public function set_data_type($data_type)
    {
        $this->_data_type = $data_type;
        
        return $this;
    }
    
    public function get_name()
    {
        return $this->get_attributes('name');
    }
    
}