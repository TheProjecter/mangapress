<?php
require_once 'View/ViewHelper.php';
/**
 * Description of View
 *
 * @author Jessica
 */
class View extends ViewHelper
{
    
    /**
     * WordPress Screen name, ex. post.php, edit.php. Can
     * be an array for multiple screens
     * 
     * @var string|array
     */
    protected $_hook = array();

    /**
     * Post-type that is to be used for enqueuing. Can be
     * an array for multiple post-types
     * 
     * @var string|array
     */
    protected $_post_type = array();
    
    /**
     * Array of stylesheet handles registered by wp_register_style()
     * 
     * @var array
     */
    protected $_styles = array();

    /**
     * Array of script handles registered by wp_register_script()
     * 
     * @var array
     */
    protected $_scripts = array();
    
    /**
     * Path to scripts/styles
     * 
     * @var string
     */
    protected $_path;

    public function init()
    {
        $this->enqueue_scripts()
             ->enqueue_styles();
    }

    /**
     * Set the relative path to the scripts/styles
     * 
     * @param string $path Path to scripts/styles
     * @return \View 
     */
    public function set_path($path)
    {
        $this->_path = $path;
        
        return $this;
    }
    
    /**
     * Set the hook that the scripts and styles are to be enqueued.
     * 
     * @param string $hook Screen hook name
     * @return \View
     */
    public function set_hook($hook)
    {
        $this->_hook = $hook;
        
        return $this;
    }
    
    /**
     * Set JS files for enqueuing.
     *
     * @param array $scripts Array of JS script handles to be enqueued.
     * @return PostType_Class
     */
    public function set_js_scripts($scripts = array())
    {
        $this->_scripts = $scripts;

        return $this;
    }

    /**
     * Set CSS files for enqueuing.
     *
     * @param array $styles Array of CSS file handles to be enqueued.
     * @return PostType_Class
     */
    public function set_css_styles($styles = array())
    {
        $this->_styles = $styles;

        return $this;
    }
    
    /**
     * Enqueues all styles and scripts. Runs when admin_enqueue_scripts is
     * called.
     *
     * @global string $post_type
     * @global string $hook_suffix
     * @return void
     */
    public function enqueue_styles()
    {
        global $post_type, $hook_suffix;
        
        $is_post_type = $this->is_post_type($post_type);
        $is_screen;
        
        if ($post_type == $this->_name
                && (($hook_suffix == 'post-new.php')
                || ($hook_suffix == 'post.php'))) {

            $scripts = $this->_styles;

            foreach ($scripts as $script) {
                wp_enqueue_style($script);
            }
        }
        
        return $this;
    }
    
    /**
     * Enqueues all scripts. Runs when admin_enqueue_scripts is
     * called.
     *
     * @global string $post_type
     * @global string $hook_suffix
     * @return void
     */
    public function enqueue_scripts()
    {
        global $post_type, $hook_suffix;        

        if ($post_type == $this->_post_type
                && (($hook_suffix == $this->_hook)
                || ($hook_suffix == 'post.php'))) {

            $scripts = $this->_scripts;

            foreach ($scripts as $script) {
                wp_enqueue_script($script);
            }
        }
        
        return $this;
    }

    
}
