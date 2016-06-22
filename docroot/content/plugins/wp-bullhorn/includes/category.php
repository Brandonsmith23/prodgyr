<?php
if(!class_exists('BullhornCategory'))
{
	/**
	 * A BullhornCategory class that creates a custom taxonomy to link categories from the bullhorn API
	 */
	class BullhornCategory
	{
        const SLUG = 'job-category';

        /**
         * Construct the plugin object
         */
        function __construct()
        {
            // register actions
            add_action('init', array(&$this, 'init'));
        } // END function __construct
                    
        /**
         * Initialize the plugin
         */
        function init()
        {
            // Make sure that the JobOrder has been loaded
            if(class_exists('JobOrder'))
            {
                // Taxonomy args
                $args = array(
                    'label' => __('Job Categories', WP_Bullhorn::TEXTDOMAIN),
                    'labels' => array(
                        'name' => __('Bullhorn Job Categories', WP_Bullhorn::TEXTDOMAIN),
                        'singular_name' => __('Bullhorn Job Category', WP_Bullhorn::TEXTDOMAIN),
                        'search_items' => __('Bullhorn Job Categories', WP_Bullhorn::TEXTDOMAIN),
                        'popular_items' => __('Popular Bullhorn Job Categories', WP_Bullhorn::TEXTDOMAIN),
                        'all_items' => __('All Bullhorn Job Categories', WP_Bullhorn::TEXTDOMAIN),
                        'parent_item' => __('Parent Bullhorn Job Category', WP_Bullhorn::TEXTDOMAIN),
                        'edit_item'  => __('Edit Bullhorn Job Category', WP_Bullhorn::TEXTDOMAIN),
                        'update_item' => __('Update Bullhorn Job Category', WP_Bullhorn::TEXTDOMAIN),
                        'add_new_item' => __('Add New Bullhorn Job Category', WP_Bullhorn::TEXTDOMAIN),
                        'new_item_name' => __('New Bullhorn Job Category', WP_Bullhorn::TEXTDOMAIN),
                        'separate_items_with_commas' => __('Separate Bullhorn Job Categories with commas', WP_Bullhorn::TEXTDOMAIN),
                        'add_or_remove_items' => __('Add or remove Bullhorn Job Categories', WP_Bullhorn::TEXTDOMAIN),
                        'choose_from_most_used' => __('Choose from most used Bullhorn Job Categories', WP_Bullhorn::TEXTDOMAIN)
                    ),
                    'args' => array('orderby' => self::SLUG),
                    'rewrite' => array('slug' => self::SLUG),
                    'public' => TRUE,
                    'hierarchical' => FALSE,
                    'show_ui' => TRUE,
                    'show_in_nav_menus' => TRUE,
                    'show_admin_column' => TRUE,
                );
            
                // attach BullhornCategory taxonomy to the JobOrder Post Type
                register_taxonomy(self::SLUG, JobOrder::POST_TYPE, $args);                
            }
            // http://codex.wordpress.org/Function_Reference/register_taxonomy for more options
        } // END function init()
        
        /**
		 * Sync the BullhornCategory from Bullhorn's API
		 */
		public function sync()
		{
            // Import the required api classes
            require_once(sprintf("%s/rest/entity.php", dirname(__FILE__)));
            require_once(sprintf("%s/rest/connection.php", dirname(__FILE__)));

            // Create a connection to bullhorn
            $bh_connection = new BullhornRestConnection(get_option('bh_client_id'), get_option('bh_client_secret'), get_option('bh_username'), get_option('bh_password'));
            $bh_entity = new BullhornEntity($bh_connection, "Category");

            // Query for all job order JobOrderIDs
            $entities = $bh_entity->query("id,name,description", get_option("bh_category_sync_where"));
            foreach($entities as $category)
            {
                if(!get_term_by("name", $category->name, self::SLUG, ARRAY_A))
                {
                    // Create the new term
                    $t = wp_insert_term(
                        $category->name, // the term 
                        self::SLUG, // the taxonomy
                        array(
                            "description"=> $category->description,
                        )
                    );
                } // END if(!get_term_by("name", $category->name, self::SLUG, ARRAY_A))
            } // END foreach($entities as $category)
        } // END public function sync()
    } // END class BullhornCategory
} // END if(!class_exists('BullhornCategory'))
