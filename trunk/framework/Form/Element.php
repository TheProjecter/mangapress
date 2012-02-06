<?php

class Element
{
    protected $_attr;

    protected $_label;

    protected $_name;

    protected $_html;

    protected $_form_ID;

    public function addAttributes(array $attributes = array())
    {
        foreach ($attributes as $attr => $value) {
            $this->setAttributes($attr, $value);
        }

        return $this;
    }

    public function getAttributes($key)
    {
        if (!isset($this->_attr[$key])) {
            return null;
        }

        return $this->_attr[$key];
    }

    public function setAttributes($key, $value)
    {
        $this->_attr[$key] = $value;

        return $this;

    }

    public function setLabel($text = '') {

        $this->_label = $text;

        return $this;
    }

    public function setForm_ID($form_ID)
    {
        $this->_form_ID = $form_ID;

        return $this;
    }

    public function getForm_ID()
    {
        return $this->_form_ID;
    }

    public function getHtml()
    {
        return $this->_html;
    }

    public function getName()
    {
        return $this->getAttributes('name');
    }
    
}