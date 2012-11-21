<?php
/**
 * MangaPress
 *
 * @package MangaPress
 * @subpackage MangaPress_FrameWork_Helper
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
/**
 * MangaPress_FrameWork_Helper
 *
 * @package MangaPress_FrameWork_Helper
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
abstract class MangaPress_FrameWork_Helper
{
    /**
     * Read-only version of self::$_name
     *
     * @var string
     */
    public $name;

    /**
     * Read-only version of self::$_label_single
     *
     * @var string
     */
    public $label_single;

    /**
     * Read-only version of self::$_label_plural
     * @var string
     */
    public $label_plural;

    /**
     * Object name
     *
     * @var string
     */
    protected $_name;

    /**
     * Object singular (human-readable) label
     *
     * @var string
     */
    protected $_label_single;

    /**
     * Object plural (human-readable) label
     *
     * @var string
     */
    protected $_label_plural;

    /**
     * Object arguments
     *
     * @var array
     */
    protected $_args;

    /**
     * Object init
     *
     * @return void
     */
    abstract public function init();

    /**
     * PHP5 Constructor method
     * @param array $options Optional. Pass Object parameters on construct
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->set_options($options)
                 ->init();
        }
    }

    /**
     * Set the object name
     *
     * @param string $object_name
     * @return MangaPress_FrameWork_Helper
     */
    public function set_name($object_name)
    {
        $this->name = $this->_name = $object_name;

        return $this;
    }

    /**
     * Set object options
     *
     * @param array $options
     * @return MangaPress_FrameWork_Helper
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
     * Set the object's singular label
     *
     * @param string $object_single_name
     * @return MangaPress_FrameWork_Helper
     */
    public function set_singlename($object_single_name)
    {
        $this->label_single = $this->_label_single = $object_single_name;

        return $this;
    }

    /**
     * Set the object's plural label
     *
     * @param string $object_pluralname
     * @return MangaPress_FrameWork_Helper
     */
    public function set_pluralname($object_pluralname)
    {

        $this->label_plural = $this->_label_plural = $object_pluralname;

        return $this;
    }

    /**
     * Set object arguments
     *
     * @param array $args
     * @return MangaPress_FrameWork_Helper
     */
    public function set_arguments($args)
    {
        $this->_args = $args;

        return $this;
    }

}
