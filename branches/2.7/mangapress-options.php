<?php
/**
 * @package MangaPress
 * @subpackage MangaPress_Options
 * @author Jess Green <jgreen@psy-dreamer.com> 
 * @version $Id$
 */
class Manga_Press_Options
{
    public function __construct()
    {
        add_action('mangapress_option_fields', array(&$this, 'option_fields'));
        add_action('mangapress_option_sections', array(&$this, 'option_sections'));
    }
   
    public function options_fields($options = array())
    {
        
        $options = array(
            'basic' => array(
                'order_by' => array(
                    'id'    => 'order-by',
                    'title' => __('Order By', MP_DOMAIN),
                    'type'  => 'select',
                    'valid' => array(
                        'post_date' => __('Date', MP_DOMAIN),
                        'post_id'   => __('Post ID', MP_DOMAIN),
                    ),
                    'default' => 'post_date',
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'group_comics'      => array( // New options in 3.0
                    'id'    => 'group-comics',
                    'type'  => 'checkbox',
                    'title' => __('Group Comics', MP_DOMAIN),
                    'valid' => 'boolean',
                    'default' => false,
                    'callback' => array(&$this, 'settings_field_cb'),
                ), 
                'latestcomic_page'  => array(
                    'id'    => 'latest-comic-page',
                    'type'  => 'select',
                    'title' => __('Latest Comic Page', MP_DOMAIN),
                    'valid'    => array(),
                    'default'  => 0,
                    'callback' => array(&$this, 'ft_basic_page_dropdowns_cb'),
                ),
                'comicarchive_page' => array(
                    'id'    => 'archive-page',
                    'type'  => 'select',
                    'title' => __('Comic Archive Page', MP_DOMAIN),
                    'valid' => array(),
                    'default' => 0,      
                    'callback' => array(&$this, 'ft_basic_page_dropdowns_cb'),
                ),
            ),
            'comic_page' => array(                
                'banner_width'        => array(
                    'id'    => 'banner-width',
                    'type'  => 'text',
                    'title' => __('Banner Width', MP_DOMAIN),
                    'valid' => '/[0-9]/',
                    'default' => 450,
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'banner_height'       =>  array(
                    'id'    => 'banner-height',
                    'type'  => 'text',
                    'title'   => __('Banner Height', MP_DOMAIN),
                    'valid'   => '/[0-9]/',
                    'default' => 100,
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'comic_post_count'    =>  array( // New option in 3.0
                    'id'    => 'number-posts',
                    'type'  => 'text',
                    'title' => __('Comic Posts to Display', MP_DOMAIN),
                    'description' => 'Overrides ',
                    'valid' => '/[0-9]/',
                    'default' => 10,
                    'callback' => array(&$this, 'settings_field_cb'),
                ), 
                'generate_comic_page' => array(  // New option in 3.0
                    'id'    => 'generate-page',
                    'type'  => 'checkbox',
                    'title'       => __('Generate Comic Page', MP_DOMAIN),
                    'description' => 'Generate a comic page based on values below.',
                    'valid'       => 'boolean',
                    'default'     => false,
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'comic_page_width'    => array( // New option in 3.0
                    'id'    => 'page-width',
                    'type'  => 'text',
                    'title'   => __('Comic Page Width', MP_DOMAIN),
                    'valid'   => '/[0-9]/',
                    'default' => 600,
                    'callback' => array(&$this, 'settings_field_cb'),
                ), 
                'comic_page_height'   => array( // New option in 3.0
                    'id'    => 'page-height',
                    'type'  => 'text',
                    'title'   => __('Comic Page Height', MP_DOMAIN),
                    'valid'   => '/[0-9]/',
                    'default' => 1000,
                    'callback' => array(&$this, 'settings_field_cb'),
                ), 
            ),
            'nav' => array(
                'insert_nav' => array(
                    'id'      => 'insert',
                    'title'   => __('Insert Navigation', MP_DOMAIN),
                    'description' => __('Automatically insert comic navigation code into comic posts.', MP_DOMAIN),
                    'type'    => 'checkbox',
                    'valid'   => 'boolean',
                    'default' => false,
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'nav_css'    => array(
                    'id'     => 'navigation-css',
                    'title'  => __('Navigation CSS', MP_DOMAIN),
                    'description' => __('Turn this off. You know you want to!', MP_DOMAIN),
                    'type'   => 'select',
                    'valid'  => array(
                        'default_css' => __('Default CSS', MP_DOMAIN),
                        'custom_css' => __('Custom CSS', MP_DOMAIN),
                    ),
                    'default' => 'default_css',
                    'callback' => array(&$this, 'settings_field_cb'),
                ),
                'display_css' => array(
                    'id'       => 'display',
                    'callback' => array(&$this, 'ft_navigation_css_display_cb'),
                )
            ),            
        );
        
        return $options;
        
    }
    
    public function options_sections($sections = array())
    {
        $sections = array(
            'basic'      => array(               
                'title'       => __('Basic Options', MP_DOMAIN),
                'description' => __(
                    'This section sets the &ldquo;Latest-&rdquo; and '
                    . '&ldquo;Comic Archive&rdquo; pages, number of comics ' 
                    . 'per page, and grouping comics together by category.',
                    MP_DOMAIN
                ),
            ),
            
            'comic_page' => array(
                'title'       => __('Comic Page Options', MP_DOMAIN),
                'description' => __(
                    'Handles image sizing options for comic pages. '
                    . 'Thumbnail support may need to be enabled for some '
                    . 'features to work properly.',
                    MP_DOMAIN
                ),
            ),
            
            'nav'        => array(
                'title'       => __('Navigation Options', MP_DOMAIN),
                'description' => __(
                    'Options for comic navigation. Whether to have navigation'
                    . ' automatically inserted on comic pages, or to enable/disable'
                    . ' default comic navigation CSS.',
                    MP_DOMAIN
                ),
            ),
        );
        
        return $sections;
    }
    
    public function options_print_scripts()
    {
        wp_enqueue_script('syntax-highlighter-cssbrush');
    }

    public function options_print_styles()
    {
        wp_enqueue_style('syntax-highlighter-css');
    }

    /**
     * mangapress_options_init()
     *
     * @since 2.6b
     *
     * Registers Manga+Press settings
     *
     * @return void
     */
    public function options_init()
    {       
        
        // Adding new options...
        register_setting(
            'mangapress_options',
            'mangapress_options',
            array(&$this, 'update_options')
        );
        
 	// Add the section to reading settings so we can add our
 	// fields to it
 	add_settings_section(
            'mangapress_options_basic',
            'Manga+Press Options',
            array(&$this, 'settings_section_cb'),
            'mangapress-options-basic'
        );
 	
        add_settings_section(
            'mangapress_options_comic_page',
            'Manga+Press Image Options',
            array(&$this, 'settings_section_cb'),
            'mangapress-options-comic_page'
        );

 	add_settings_section(
            'mangapress_options_nav',
            'Manga+Press Navigation Options',
            array(&$this, 'settings_section_cb'),
            'mangapress-options-nav'
        );
        
        // set navigation fields...
        $this->output_settings_fields();
                        
    }
    
    public function output_settings_fields()
    {
        global $mp_options;
        $field_sections = $this->options_fields();
        $current_tab    = $this->_get_current_tab();
        $fields         = $field_sections[$current_tab];

        foreach ($fields as $field_name => $field) {

            add_settings_field(
                "{$current_tab}-options-{$field['id']}",
                (isset($field['title']) ? $field['title'] : " "),
                $field['callback'],
                "mangapress-options-{$current_tab}",
                "mangapress_options_{$current_tab}",
                array_merge(array('name' => $field_name), $field)
            );

        }
                
    }
    
    public function settings_section_cb()
    {
        $options = $this->options_sections();
        
        if ( isset ( $_GET['tab'] ) ) {
            $current = $_GET['tab'];
        } else {
            $current = 'basic';
        }
   
        echo "<p>{$options[$current]['description']}</p>";
    }
    
    public function settings_field_cb($option)
    {
        global $mp_options;

        $current_tab = $this->_get_current_tab();
        $field_type  = isset($option['type']) ? $option['type'] : '';
        
        if ($field_type == 'text') {
        ?>
        
        <input type="text" 
               name="mangapress_options[<?php echo $current_tab; ?>][<?php echo $option['name']; ?>]" 
               id="<?php echo $option['id'] ?>" 
               value="<?php echo (!isset($mp_options[$current_tab][$option['name']]) ? $option['default'] : $mp_options[$current_tab][$option['name']]); ?>" />
        
        <?php
        } elseif ($field_type == 'checkbox') {                                       
            if (isset($option['description'])) {
                echo "<label for=\"{$option['id']}\">";
            } ?>
                           
        <input type="checkbox" 
               name="mangapress_options[<?php echo $current_tab; ?>][<?php echo $option['name']; ?>]" 
               id="<?php echo $option['id'] ?>" 
               value="1"
               <?php checked('1', $mp_options[$current_tab][$option['name']], true) ?> />
        
        <?php
            if (isset($option['description'])) {
                echo $option['description'] . '</label>';
            }
            
        } elseif ($field_type == 'radio') {
            // should be an array of radio buttons
        } elseif ($field_type == 'select') {
        ?>
            <select name="mangapress_options[<?php echo $current_tab; ?>][<?php echo $option['name']; ?>]" id="<?php echo $option['id'] ?>">
            <?php foreach($option['valid'] as $value => $text) { 
                $selected = selected($value, $mp_options[$current_tab][$option['name']], false);
            ?>
                <option value="<?php echo $value; ?>"<?php echo $selected ?>><?php echo $text; ?></option>
            <?php } ?>
            </select>        

        <?php
            if (isset($option['description'])) {
                echo "<span class=\"description\">{$option['description']}</span>";
            }
        } elseif ($field_type == 'textarea') {
            
        }
    }
    
    public function ft_navigation_css_display_cb()
    {?>

<?php _e('Copy and paste this code into the <code>style.css</code> file of your theme.', MP_DOMAIN); ?>
<code style="display: block; width: 550px;"><pre class="brush: css;">

/* comic navigation */
.comic-navigation {
    text-align: center;
    margin: 5px 0 10px 0;
}

.comic-nav-span {
    padding: 3px 10px;
    text-decoration: none;
}

ul.comic-nav  {
    margin: 0;
    padding: 0;
    white-space: nowrap;
}

ul.comic-nav li {
    display: inline;
    list-style-type: none;
}

ul.comic-nav a {
    text-decoration: none;
    padding: 3px 10px;
}

ul.comic-nav a:link,
ul.comic-nav a:visited {
    color: #ccc;
    text-decoration: none;
}

ul.comic-nav a:hover { text-decoration: none; }
ul.comic-nav li:before{ content: ""; }

</pre></code>
    <?php
    }
    
    public function ft_basic_page_dropdowns_cb($option)
    {               
        global $mp_options;

        // get pages for dropdown...
        $pages = get_pages();
            
        ?>
            <select name="mangapress_options[basic][<?php echo $option['name']; ?>]" id="<?php echo $option['id'] ?>">
            <?php foreach($pages as $page) { 
                $selected = selected($page->ID, $mp_options['basic'][$option['name']], false);
            ?>
                <option value="<?php echo $page->ID; ?>"<?php echo $selected ?>><?php echo $page->post_title; ?></option>
            <?php } ?>
            </select>        

        <?php
    }

    /**
     * Updates multiple options from page-comic-options.php
     * 
     * @since 2.6
     *
     * Originally update_options. Was modified and renamed in Manga+Press 2.6
     *
     * @global array $mp_options
     * @param array $options
     * @return string
     */
    function update_options($options)
    {
                
        global $mp_options;
        
        $section           = key($options);
        $available_options = $this->options_fields();
        $new_optons        = $mp_options;
        
        if ($section == 'nav'){
            
            $new_optons['nav']['insert_nav'] = intval($options['nav']['insert_nav']);

            //
            // if the value of the option doesn't match the correct values in the array, then
            // the value of the option is set to its default.
            $nav_css_values = array_keys($available_options['nav']['nav_css']['valid']);
            
            if (in_array($mp_options['nav']['nav_css'], $nav_css_values)){
                $new_optons['nav']['nav_css'] = strval($options['nav']['nav_css']);
            } else {
                $new_optons['nav']['nav_css'] = 'default_css';
            }
        }
        
        if ($section == 'basic') {
            $order_by_values = array_keys($available_options['nav']['nav_css']['valid']);
            //
            // Converting the values to their correct data-types should be enough for now...        
            $new_optons['basic'] = array(
                'order_by'           => (in_array($mp_options['basic']['order_by'], $order_by_values))
                                            ? strval($options['basic']['order_by']) : 'post_date',
                'group_comics'       => intval($options['basic']['group_comics']),
                'latestcomic_page'   => intval($options['basic']['latestcomic_page']),
                'comic_archive_page' => intval($options['basic']['comic_archive_page']),

            );
        }
        
        if ($section == 'comic_page') {
            $new_optons['comic_page'] = array(
                'make_thumb'          => intval($options['comic_page']['make_thumb']),
                'banner_width'        => intval($options['comic_page']['banner_width']),
                'banner_height'       => intval($options['comic_page']['banner_height']),
                'generate_comic_page' => intval($options['comic_page']['generate_comic_page']),
                'comic_page_width'    => intval($options['comic_page']['comic_page_width']),
                'comic_page_height'   => intval($options['comic_page']['comic_page_height']),            
            );
        }
        
        $options = array_merge($mp_options, $new_optons);
        
        return $options;

    }
    
    /**
     *
     */
    public function page_options()
    {
        global $mp_options;

        if ( ! current_user_can('manage_options') )
            wp_die(
                __('You do not have sufficient permissions to manage options for this blog.',
                MP_DOMAIN)
            );
        
        include_once('pages/options.php');

    }

    private function _get_settings_page_tabs()
    {
         $tabs = array(
            'basic'      => 'Basic Manga+Press Options',
            'comic_page' => 'Comic Page Options',            
            'nav'        => 'Navigation Options',
         );
         
         return $tabs;
    }
    
    private function _get_current_tab()
    {
        $valid_tabs  = array_keys($this->_get_settings_page_tabs());
        $current_tab = $_GET['tab'];
        
        if (in_array($current_tab, $valid_tabs)) {
            return $current_tab;
        } else {
            return 'basic';
        }
        
    }

    public function options_page_tabs($current = 'basic')
    {
        if ( isset ( $_GET['tab'] ) ) {
            $current = $_GET['tab'];
        } else {
            $current = 'basic';
        }
        
        $tabs = $this->_get_settings_page_tabs();
        $links = array();
        foreach( $tabs as $tab => $name ){
            if ( $tab == $current ){
                $links[] = "<a class=\"nav-tab nav-tab-active\" href=\"?page=mangapress-options-page&tab={$tab}\">{$name}</a>";
            } else {
                $links[] = "<a class=\"nav-tab\" href=\"?page=mangapress-options-page&tab={$tab}\">{$name}</a>";
            };
        }
        
        echo '<div id="icon-themes" class="icon32"><br /></div>';
        echo '<h2 class="nav-tab-wrapper">';
        
        foreach ( $links as $link )
            echo $link;
        echo '</h2>';        
    }
    
}