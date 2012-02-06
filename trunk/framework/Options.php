<?php
/**
 * @package WordPress
 * @subpackage Options
 * @author Jess Green <jgreen@nerdery.com>
 * @version $Id$
 */

/**
 * options
 * Created Jan 30, 2012 @ 9:23:16 AM
 *
 * @author Jess Green <jgreen@nerdery.com>
 */
abstract class Options extends FrameWork_Helper
{

    /**
     * Option group name. Used as html field name for option output.
     *
     * @var string
     */
    protected $_options_group;

    /**
     * Array of available option fields.
     *
     * @var array
     */
    protected $_option_fields;

    /**
     * Array of option sections.
     *
     * @var array
     */
    protected $_option_sections;

    /**
     * PHP5 constructor function
     *
     * @return void
     */
    public function __construct($args = array())
    {
        $name              = $args['name'];
        
        if (is_array($args)) {
            $this->set_options($args);
        }
                
        add_action("{$name}_option_fields", array(&$this, 'set_options_field'), 10, 1);
        add_action("{$name}_option_section", array(&$this, 'set_section'), 10, 1);
        add_action('admin_init', array(&$this, 'options_init'));

    }

    /**
     * Register settings, sections and fields
     *
     * @return void
     */
    public function options_init()
    {

        /*
         * register_setting()
         * Settings should be stored as an array in the options table to
         * limit the number of queries made to the DB. The option name should
         * be the same as the option group.
         *
         * Using the options group in a page registered with add_options_page():
         * settings_fields($my_options_class->get_optiongroup_name())
         */
        register_setting(
            $this->_options_group,
            $this->_options_group,
            array(&$this, 'sanitize_options')
        );

    }

    /**
     * Sets the options
     *
     * @param type $options
     * @return \Options
     */
    public function set_optiongroup_name($options)
    {
        $this->_options_group = $options;

        return $this;
    }

    /**
     * Returns the options group name
     *
     * @return array|\WP_Error
     */
    public function get_optiongroup_name()
    {
        if ($this->_options_group == "") {
            return new WP_Error("no_option_name", "Option name is empty");
        }

        return $this->_options_group;
    }

    /**
     * Sets the options fields sections.
     *
     * @param type $sections
     * @return \Options
     */
    public function set_sections($sections)
    {
        $this->_option_sections = $sections;

        return $this;
    }

    /**
     * Returns the available options fields sections.
     *
     * @return array|\WP_Error
     */
    public function get_sections()
    {
        if ($this->_option_sections == "") {
            return new WP_Error("no_option_sections", "Sections not set.");
        }

        return $this->_option_sections;

    }

    /**
     * Adds a new section to available sections array. To be used by plugins
     * for modifying available
     *
     * @param array $section
     * @return array
     */
    public function set_section($section)
    {
        if (!is_array($section)) {
            return new WP_Error("section_not_array", "Section should be an array");
        }

        $this->_option_sections = array_merge($this->_option_sections, $section);

        return $this->_option_sections;
    }

    /**
     * Retrieves a section.
     *
     * @param string $section_name Name of section being retrieved.
     * @return array
     */
    public function get_a_section($section_name)
    {
        if (!isset($this->_option_sections[$section_name])) {
            return new WP_Error("no_section_exists", "Section does not exist.");
        }

        return $this->_option_sections[$section_name];
    }

    /**
     * Set option fields
     *
     * @param array $option Option field array
     * @return array|\WP_Error
     */
    public function set_options_field($option)
    {
        if (!is_array($option)) {
            return new WP_Error("option_not_array", "Options must be an array");
        }

        $this->_option_fields = array_merge($this->_option_fields, $option);

        return $this->_option_fields;

    }

    /**
     * Returns an option field
     *
     * @param string $option_name Name of option to be returned
     * @return array|\WP_Error
     */
    public function get_options_field($option_name)
    {
        if (!isset($this->_option_fields[$option_name])) {
            return new WP_Error("no_option_exists", "Option does not exist.");
        }

        return $this->_option_fields[$option_name];

    }
    
    public function init()
    {
        return $this;
    }

    /**
     * Set option fields
     *
     * @param $options Array of available options
     * @return array
     */
    abstract public function options_fields($options);

    /**
     * Output option fields
     *
     * @param mixed $option Current option to output
     * @return string
     */
    abstract public function settings_field_cb($option);

    /**
     *
     * @param array $options Array of options to be sanitized
     * @return array Sanitized options array
     */
    abstract public function sanitize_options($options);

}

