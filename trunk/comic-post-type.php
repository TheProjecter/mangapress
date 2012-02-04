<?php
/**
 * MangaPress
 * 
 * @package MangaPress
 * @author Jess Green <jgreen@psy-dreamer.com> 
 * @version $Id$
 */
/**
 * ComicPostType
 * 
 * @package ComicPostType
 * @author Jess Green <jgreen@psy-dreamer.com> 
 */
class ComicPostType extends PostType
{
    /**
     * Post-type name.
     * 
     * @var string
     */
    protected $_name = 'mangapress_comic';
    
    /**
     * Human-readable post-type name (singular)
     * 
     * @var string
     */
    protected $_label_single;
    
    /**
     * Human-readable post-type name (plural)
     * 
     * @var string
     */
    protected $_label_plural;


    /**
     * PHP5 constructor function
     * 
     * @return void 
     */
    public function __construct()
    {
        
        /*
         * Assemble our taxonomies first
         */
        $series_tax = new Taxonomy(
            array(
                'name'       => 'mangapress_series',
                'singlename' => 'Series',
                'pluralname' => 'Series',
                'objects'   => $this->_name,
                'arguments'  => array(
                    'hierarchical' => true,
                    'query_var' => 'series',
                    'rewrite' => array('slug' => 'series'),                    
                )
            )
        );

        $issue_tax = new Taxonomy(
            array(
                'name'       => 'mangapress_issue',
                'singlename' => 'Issue',
                'pluralname' => 'Issues',
                'objects'   => $this->_name,
                'arguments'  => array(
                    'hierarchical' => true,
                    'query_var' => 'issue',
                    'rewrite' => array('slug' => 'issue'),                    
                )
            )
        );
        
        /*
         * Now we put together our post-type
         */
        $this
            ->set_singlename(__('Comic', MP_DOMAIN))
            ->set_pluralname(__('Comics', MP_DOMAIN))
            ->set_taxonomies(
                array(
                    $series_tax->name,
                    $issue_tax->name,
                )
            )
            ->set_arguments()
            ->init()
            ;        
    }
    
    public function set_arguments($args = array())
    {
        $args = array(
            'capability_type' => 'post',
            'supports'        => array(
                'thumbnail',
                'author',
                'title',
                'editor',
                'comments',
            ),
            'rewrite'         => array('slug' => 'comic'),            
        );              
        
        return parent::set_arguments($args);
    }
    
    public function get_name()
    {
        return $this->_name;
    }
}
