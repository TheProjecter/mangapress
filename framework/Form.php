<?php
include('form/Element.php');
include('form/Element/Text.php');
include('form/Element/Textarea.php');
include('form/Element/Select.php');
include('form/Element/Radio.php');
include('form/Element/Checkbox.php');
include('form/Element/Button.php');

class Form
{
    
    public $_form_elements = array();

    public $_form_properties;

    public $form;

    public function __construct()
    {
    }

    public function setProperties(array $properties = array())
    {
        $this->_form_properties = $properties;

        return $this;
    }

    public function getProperties()
    {
        return $this->_form_properties;
    }

    public function addElement($element, $name = null, $options = array())
    {
        if (is_string($element)) {
            if ($name === null) {
                // Change this to throw custom exception
                return new WP_Error('name_is_null', __('Name cannot be null.'));
            }
            
            $this->_form_elements[$name] = $this->createElement($element, $name, $this->_form_properties);

        } elseif (is_object($element)) {
            
            if ($name === null) {
                $name = $element->getAttributes('name');
            }

            $this->_form_elements[$name] = $this->createElement($element, $name, $this->_form_properties);
        }
        
        return $this->_form_elements[$name];
    }

    public function createElement($type, $name, $options = array())
    {

        if (is_string($type)) {
            $class = 'WP_' . ucfirst($type);
            $element = new $class($name, $options);
            
        } elseif (is_object($type)) {
            $element = $type;
        }

        return $element;
    }
    
    public function __toString()
    {
        $form = "";
        foreach ($this->_form_elements as $element) {
            $form .= $element;
        }
        
        return $form;
    }
}

