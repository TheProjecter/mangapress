<?php
/**
 * MangaPress
 *
 * @package MangaPress
 * @subpackage MangaPress_Form_Element
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
require_once 'Element/Button.php';
require_once 'Element/Checkbox.php';
require_once 'Element/Custom.php';
require_once 'Element/Option.php';
require_once 'Element/Radio.php';
require_once 'Element/Select.php';
require_once 'Element/Text.php';
require_once 'Element/Textarea.php';
/**
 * MangaPress_Form_Element
 *
 * @package MangaPress_Form_Element
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class MangaPress_Form_Element
{
    /**
     * Attributes array
     *
     * @var array
     */
    protected $_attr;

    /**
     * Element Label
     *
     * @var string
     */
    protected $_label;

    /**
     * Element name
     *
     * @var string
     */
    protected $_name;

    /**
     * Default value of element
     *
     * @var mixed
     */
    protected $_default_value;

    /**
     * Assigned value of element
     *
     * @var mixed
     */
    protected $_value;

    /**
     * How to validate element
     *
     * @var mixed
     */
    protected $_validation;

    /**
     * Element markup
     *
     * @var string
     */
    protected $_html;

    /**
     * Element description
     *
     * @var string
     */
    protected $_description;

    /**
     * Form ID.
     *
     * @var string
     */
    protected $_form_ID;

    /**
     * PHP5 Constructor function
     *
     * @param array|null $options Options passed in on object initialization
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->set_options($options);
        }

    }

    /**
     * Set options
     *
     * @param $options
     * @return MangaPress_Form_Element
     */
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

    /**
     * Add form element attributes
     *
     * @param array $attributes Form element attributes
     * @return MangaPress_Form_Element
     */
    public function add_attributes(array $attributes = array())
    {
        foreach ($attributes as $attr => $value) {
            $this->set_attributes($attr, $value);
        }

        return $this;
    }

    /**
     * Retrieve an attribute based on name
     *
     * @param string $key
     * @return mixed|null
     */
    public function get_attributes($key)
    {
        if (!isset($this->_attr[$key])) {
            return null;
        }

        return $this->_attr[$key];
    }

    /**
     * Set element attributes
     *
     * @param array $attr Attribute, defined by key/value pairs
     * @return MangaPress_Form_Element
     */
    public function set_attributes($attr)
    {
        foreach ($attr as $key => $value)
            $this->_attr[$key] = $value;

        return $this;

    }

    /**
     * Set label text
     *
     * @param string $text Label text
     * @return MangaPress_Form_Element
     */
    public function set_label($text = '') {

        $this->_label = $text;

        return $this;
    }

    /**
     * Set default value
     *
     * @param mixed $default Default value
     * @return MangaPress_Form_Element
     */
    public function set_default($default)
    {
        $this->_default_value = $default;

        return $this;
    }

    /**
     * Get the default value
     *
     * @return mixed
     */
    public function get_default()
    {
        return $this->_default_value;
    }

    /**
     * Get the element value (value="")
     *
     * @return mixed
     */
    public function get_value()
    {
        return $this->_attr['value'];
    }

    public function set_validation($validation)
    {
        $this->_validation = $validation;

        return $this;
    }

    /**
     * Return element name
     *
     * @return string
     */
    public function get_name()
    {
        return $this->get_attributes('name');
    }

    /**
     * Set element description
     *
     * @param string $description Element description
     * @return MangaPress_Form_Element
     */
    public function set_description($description)
    {
        $this->_description = $description;

        return $this;
    }

    /**
     * Get element description
     *
     * @return string
     */
    public function get_description()
    {
        return $this->_description;
    }

    /**
     * Build the attribute string
     *
     * @return string
     */
    public function build_attr_string()
    {
        $attr_arr = array();
        foreach ($this->_attr as $name => $value)
            $attr_arr[] = "{$name}=\"{$value}\"";

        $attr = implode(" ", $attr_arr);

        return $attr;
    }

}
?>