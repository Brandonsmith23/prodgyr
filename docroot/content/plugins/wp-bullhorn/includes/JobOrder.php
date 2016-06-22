<?php
if(!class_exists('JobOrder'))
{
    /**
     * A JobOrder class that provides both Bullhorn and various WP related functionality
     */
    class JobOrder
    {
        const POST_TYPE = "job";
        private $_acf_config = array();
        
        /**
         * The Constructor
         */
        public function __construct()
        {
            // register actions
            add_action('init', array(&$this, 'init'));
            add_action('admin_init', array(&$this, 'admin_init'));
        } // END public function __construct()
        
        /**
         * hook into WP's init action hook
         */
        public function init()
        {
            // Initialize Post Type
            $this->create_post_type();
        } // END public function init()

        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init()
        {           
            // Add custom columns to post type list view
            add_filter(sprintf('manage_edit-%s_columns', self::POST_TYPE), array(&$this, 'custom_column_titles'));
            add_action('manage_posts_custom_column', array(&$this, 'custom_columns'));
            add_filter(sprintf('manage_edit-%s_sortable_columns', self::POST_TYPE), array(&$this, 'custom_column_sort'));
            add_filter('request', array(&$this, 'custom_column_orderby'));
        } // END public function admin_init()

        /**
         * Add custom column title in the proper position
         */
        public function custom_column_titles($tmp_columns)
        {
            $columns = array();
            foreach($tmp_columns as $key => $value)
            {
                $columns[$key] = $value;
                if($key == "title")
                {
                    $columns['employmentType'] = 'Employment Type';
                }
            }

            return $columns;
        }

        /**
         * Add custom column values
         */
        public function custom_columns($column)
        {
            global $post;

            switch($column)
            {
                case 'employmentType':
                    echo get_post_meta($post->ID, 'employmentType', 'true');
                break;
            }
        }
        
        /**
         * Allow Sorting of the custom column
         */
        function custom_column_sort($columns)
        {
            $columns['employmentType'] = 'employmentType';
            
            return $columns;
        }
        
        /**
         * Allow Ordering of the custom column
         */
        public function custom_column_orderby($vars)
        {
            if(isset($vars['orderby']) && 'employmentType' == $vars['orderby'])
            {
                $vars = array_merge(
                    $vars, 
                    array(
                        'meta_key' => 'employmentType',
                        'orderby' => 'meta_value'
                    )
                );
            }
    
            return $vars;
        }
        
        /**
         * Sync the JobOrders from Bullhorn's API
         */
        public function sync()
        {
            $this->init();
            global $wpdb;
            
            // Import the required api classes
            require_once(sprintf("%s/rest/entity.php", dirname(__FILE__)));
            require_once(sprintf("%s/rest/connection.php", dirname(__FILE__)));

            // Create a connection to bullhorn
            $bh_connection = new BullhornRestConnection(get_option('bh_client_id'), get_option('bh_client_secret'), get_option('bh_username'), get_option('bh_password'));
            $bh_entity = new BullhornEntity($bh_connection, "JobOrder");
            $bh_country = new BullhornEntity($bh_connection, "Country");
            
            // Query for all job order JobOrderIDs
            $this->_query_parts = array();
            foreach($this->_acf_config['fields'] as $field)
            {
                if($field['type'] == 'tab')
                { /* Do nothing this isn't an API content part */ }
                elseif($field['type'] == 'repeater')
                {
                    if("address" == $field['name'])
                    {
                        $this->_query_parts[] = $field['name'];
                    }
                    if(in_array($field['name'], array("responseUser", "owner")))
                    {
                        $sub_query_parts = array();
                        foreach($field['sub_fields'] as $sub_field)
                        {
                            $sub_query_parts[] = $sub_field['name'];
                        }
                        $this->_query_parts[] = sprintf("%s(%s)", $field['name'], implode($sub_query_parts, ","));
                    }
                }
                else
                {
                    $this->_query_parts[] = $field['name'];
                }
            }
            $this->_query_parts[] = "categories(name,description)";
            
            $entities = $bh_entity->query(implode($this->_query_parts, ","), get_option("bh_joborder_sync_where"));
            if(is_array($entities))
            {
                // Unpublish all job-order posts
                $wpdb->query(
                    sprintf("
                        UPDATE $wpdb->posts 
                        SET post_status = 'draft' 
                        WHERE post_type = '%s'", 
                        self::POST_TYPE
                    )
                );
                
                // Get all of the job-order that are active currently
                foreach($entities as $job)
                {   
                    // Set up the post
                    $post = array(
                        'post_status' => 'publish',
                        'post_type' => self::POST_TYPE,
                        'post_title' => (string) $job->title,
                        'post_content' => (string) $job->publicDescription,
                        'post_author' => 1,
                        'filter' => true
                    );
                    
                    // Try to get a post with this JobOrderID
                    $post_id = $wpdb->get_var(
                        sprintf("
                            SELECT post_id
                            FROM $wpdb->postmeta
                            WHERE meta_key = 'id'
                            AND meta_value = %s
                            LIMIT 1",
                            $job->id
                        )
                    );
                    
                    // Insert or update a post depending on whther the
                    // JobOrderID exists in the system already
                    if($post_id != 0)
                    {
                        $post['ID'] = $post_id;
                        $post_id = wp_update_post($post);
                    }
                    else
                    {
                        $post_id = wp_insert_post($post);
                    }
                    
                    // If post_id 
                    if(!empty($post_id) && $post_id > 0)
                    {
                        // then update all of the metadata
                        foreach($job as $field_name => $field_value)
                        {
                            if(is_object($field_value))
                            {
                                // Handle the main Job Address
                                if("address" == $field_name)
                                {
                                    $address = array((array)$field_value);
                                    try
                                    {
                                        $country = $bh_country->get($address[0]['countryID'], "name");
                                        $address[0]['country_name'] = $country->data->name;
                                    }
                                    catch(Exception $e)
                                    {
                                        $address[0]['country_name'] = "";
                                    }
                                    update_field("field_53bfeccc29e78", $address, $post_id);
                                } // END if("address" == $field_name)

                                // Handle the ResponseUser
                                if("responseUser" == $field_name)
                                {
                                    // Get the CorporateUser Fields
                                    $u = array((array)$field_value);

                                    // Pull the Address Out
                                    $a = array((array)$u[0]['address']);
                                    
                                    // Pull the CountryName by countryID
                                    try
                                    {
                                        $country = $bh_country->get($a[0]['countryID'], "name");
                                        $a[0]['country_name'] = $country->data->name;
                                    }
                                    catch(Exception $e)
                                    {
                                        $a[0]['country_name'] = "";
                                    }

                                    $u[0]['address'] = $a;

                                    // Save the CorporateUser Fields
                                    update_field("field_53c04224c7cf3", $u, $post_id);
                                } // END if("responseUser" == $field_name)

                                // Handle the owner
                                if("owner" == $field_name)
                                {
                                    // Get the CorporateUser Fields
                                    $u = array((array)$field_value);

                                    // Pull the Address Out
                                    $a = array((array)$u[0]['address']);
                                    
                                    // Pull the CountryName by countryID
                                    try
                                    {
                                        $country = $bh_country->get($a[0]['countryID'], "name");
                                        $a[0]['country_name'] = $country->data->name;
                                    }
                                    catch(Exception $e)
                                    {
                                        $a[0]['country_name'] = "";
                                    }

                                    $u[0]['address'] = $a;

                                    // Save the CorporateUser Fields
                                    update_field("field_53c054d0c6231", $u, $post_id);
                                } // END if("owner" == $field_name)
                            }
                            else
                            {
                                @update_post_meta($post_id, $field_name, (string)$field_value);
                            }
                        } // END foreach($job as $field_name => $field_value)
                        
                        // Then update the attached terms
                        $terms = array();
                        foreach($job->categories->data as $category)
                        {
                            $t = get_term_by("name", $category->name, BullhornCategory::SLUG, ARRAY_A);

                            if(!$t)
                            {
                                // Create the new term
                                $t = wp_insert_term(
                                    $category->name, // the term 
                                    BullhornCategory::SLUG, // the taxonomy
                                    array(
                                        "description"=> $category->description,
                                    )
                                );
                                
                                if(is_wp_error($t))
                                {
                                    $terms[] = $t->error_data["term_exists"];
                                }
                                else
                                {
                                    $terms[] = $t["term_id"];
                                }
                            }
                            else
                            {
                                $terms[] = $t["term_id"];
                            }
                        } // END for($job->categories->data as $category)

                        // Set Object Terms
                        wp_set_post_terms($post_id, $terms, BullhornCategory::SLUG, FALSE);
                    } // END if(!empty($post_id) && $post_id > 0)
                } // END foreach($jobs as $job)
            } // END if(($arr_ids = $bh_job_order->query($bh_connection)) != False)
        } // END public function sync()

        /**
         * Create the post type
         */
        public function create_post_type()
        {
            // Register the Job post type
            register_post_type(self::POST_TYPE,
                array(
                    'labels' => array(
                        'name' => __(sprintf('%ss', ucwords(str_replace("_", " ", self::POST_TYPE))), WP_Bullhorn::TEXTDOMAIN),
                        'singular_name' => __(ucwords(str_replace("_", " ", self::POST_TYPE)), WP_Bullhorn::TEXTDOMAIN)
                    ),
                    'description' => __("JobOrders from the Bullhorn API", WP_Bullhorn::TEXTDOMAIN),
                    'supports' => array(
                        'title',
                    ),
                    'public' => true,
                    'show_ui' => true,
                    'has_archive' => true,
                    'show_in_menu' => 'bullhorn',
                )
            );

            // Generated From ACF
            if(function_exists("register_field_group"))
            {
                $this->_acf_config = array(
                    'id' => 'acf_bullhorn-joborder',
                    'title' => 'Bullhorn JobOrder',
                    'fields' => array(
                        array(
                            'key' => 'field_53c045392a817',
                            'label' => __('Basic', WP_Bullhorn::TEXTDOMAIN),
                            'name' => '',
                            'type' => 'tab',
                        ),
                        array(
                            'key' => 'field_53bfe9c614233',
                            'label' => __('ID', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'id',
                            'type' => 'number',
                            'instructions' => __('Bullhorn Id of this entity.', WP_Bullhorn::TEXTDOMAIN),
                            'required' => 1,
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'min' => '',
                            'max' => '',
                            'step' => '',
                        ),
                        array(
                            'key' => 'field_53c042d8c7cfb',
                            'label' => __('Title', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'title',
                            'type' => 'text',
                            'instructions' => __('Job title.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53bfeccc29e78',
                            'label' => __('Address', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'address',
                            'type' => 'repeater',
                            'instructions' => __('Address of the hiring company; when the record is created in the Bullhorn application, this data is pulled from the client contact record.', WP_Bullhorn::TEXTDOMAIN),
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_53bfed2d29e79',
                                    'label' => __('Address 1', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'address1',
                                    'type' => 'text',
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53bfed4429e7a',
                                    'label' => __('Address 2', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'address2',
                                    'type' => 'text',
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53bfed4e29e7b',
                                    'label' => __('City', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'city',
                                    'type' => 'text',
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53bfed5929e7c',
                                    'label' => __('State', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'state',
                                    'type' => 'text',
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53bfed6329e7d',
                                    'label' => __('Postal Code', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'zip',
                                    'type' => 'text',
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53bfed6f29e7e',
                                    'label' => __('Country ID', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'countryID',
                                    'type' => 'text',
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53bfeda829e7f',
                                    'label' => __('Country Name', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'country_name',
                                    'type' => 'text',
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                            ),
                            'row_min' => 1,
                            'row_limit' => 1,
                            'layout' => 'row',
                            'button_label' => 'Add Row',
                        ),
                        array(
                            'key' => 'field_53c0428ec7cf9',
                            'label' => __('Status', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'status',
                            'type' => 'text',
                            'instructions' => __('Current status of the Job Order. Examples: Accepting Candidates, Currently Interviewing, Covered, Offer Out, Placed', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c0416589bac',
                            'label' => __('Employment Type', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'employmentType',
                            'type' => 'text',
                            'instructions' => __('Type of employment offered: for example, contract, permanent, and so forth. Determines which of the five job types are used.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c03c85ad9cd',
                            'label' => __('Date Added', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'dateAdded',
                            'type' => 'text',
                            'instructions' => __('Date when this record was created in the Bullhorn system.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c03ca3ad9ce',
                            'label' => __('Date Closed', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'dateClosed',
                            'type' => 'text',
                            'instructions' => __('Date when the job was marked as closed.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c045582a818',
                            'label' => __('Description', WP_Bullhorn::TEXTDOMAIN),
                            'name' => '',
                            'type' => 'tab',
                        ),
                        array(
                            'key' => 'field_53c03cd3ad9d1',
                            'label' => __('Description', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'description',
                            'type' => 'wysiwyg',
                            'instructions' => __('Text description of the job.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'toolbar' => 'full',
                            'media_upload' => 'yes',
                        ),
                        array(
                            'key' => 'field_53c04203c7cf1',
                            'label' => __('Public Description', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'publicDescription',
                            'type' => 'wysiwyg',
                            'instructions' => __('Description of this job for use on public job boards.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'toolbar' => 'full',
                            'media_upload' => 'yes',
                        ),
                        array(
                            'key' => 'field_53c045632a819',
                            'label' => __('Pay & Benefits', WP_Bullhorn::TEXTDOMAIN),
                            'name' => '',
                            'type' => 'tab',
                        ),
                        array(
                            'key' => 'field_53c03b7ead9c8',
                            'label' => __('Benefits', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'benefits',
                            'type' => 'text',
                            'instructions' => __('Text description of benefits offered with this job.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c03ba9ad9ca',
                            'label' => __('Bonus Package', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'bonusPackage',
                            'type' => 'text',
                            'instructions' => __('Text description of the bonus package offered with this job.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c042c2c7cfa',
                            'label' => __('Tax Status', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'taxStatus',
                            'type' => 'text',
                            'instructions' => __('Tax Status, for example, 1099, W-2, and so forth.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c03c68ad9cc',
                            'label' => __('Client Bill Rate', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'clientBillRate',
                            'type' => 'text',
                            'instructions' => __('Amount to be billed to the client for this job when it is filled.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c041fac7cf0',
                            'label' => __('Pay Rate', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'payRate',
                            'type' => 'text',
                            'instructions' => __('Pay rate offered with this job.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c0417089bad',
                            'label' => __('Fee Arrangement', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'feeArrangement',
                            'type' => 'text',
                            'instructions' => __('Fee, expressed as a percentage, that will be paid by the ClientCorporation when the job is filled.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c03cbfad9d0',
                            'label' => __('Degree List', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'degreeList',
                            'type' => 'text',
                            'instructions' => __('List of educational degrees required for this job.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c0403389baa',
                            'label' => __('Duration Weeks', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'durationWeeks',
                            'type' => 'text',
                            'instructions' => __('Expected duration the job. For a permanent position, this is null.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c0415789bab',
                            'label' => __('Education Degree', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'educationDegree',
                            'type' => 'text',
                            'instructions' => __('Required degree for the job.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c0424bc7cf4',
                            'label' => __('Salary', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'salary',
                            'type' => 'text',
                            'instructions' => __('Salary offered for this job.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c04255c7cf5',
                            'label' => __('Salary Unit', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'salaryUnit',
                            'type' => 'text',
                            'instructions' => __('Salary unit represented by the range (for example, per hour, yearly).', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c0457a2a81a',
                            'label' => __('Additional Information', WP_Bullhorn::TEXTDOMAIN),
                            'name' => '',
                            'type' => 'tab',
                        ),
                        array(
                            'key' => 'field_53c0418289bae',
                            'label' => __('Hours Per Week', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'hoursPerWeek',
                            'type' => 'text',
                            'instructions' => __('Number of hours per week that the employee will work.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c0419489baf',
                            'label' => __('On Site', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'onSite',
                            'type' => 'text',
                            'instructions' => __('Location requirements; for example, on- site, off-site, no preference.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c04215c7cf2',
                            'label' => __('Published Zip', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'publishedZip',
                            'type' => 'text',
                            'instructions' => __('Published Zip Code of the job location.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c04261c7cf6',
                            'label' => __('Skill List', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'skillList',
                            'type' => 'textarea',
                            'instructions' => __('Comma-separated list of skills the applicants should have.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'maxlength' => '',
                            'rows' => '',
                            'formatting' => 'br',
                        ),
                        array(
                            'key' => 'field_53c0426dc7cf7',
                            'label' => __('Source', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'source',
                            'type' => 'text',
                            'instructions' => __('Source of the job.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c04281c7cf8',
                            'label' => __('Start Date', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'startDate',
                            'type' => 'text',
                            'instructions' => 'Desired start date for the position.
                The default value is 12 AM on day record is added.',
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c042efc7cfc',
                            'label' => __('Travel Requirements', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'travelRequirements',
                            'type' => 'text',
                            'instructions' => __('Text description of the amount of travel required for this job.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c042f8c7cfd',
                            'label' => __('Years Required', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'yearsRequired',
                            'type' => 'text',
                            'instructions' => __('Number of years of experience required for the job.', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c03cafad9cf',
                            'label' => __('Date End', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'dateEnd',
                            'type' => 'text',
                            'instructions' => __('Date when the job will end (if applicable).', WP_Bullhorn::TEXTDOMAIN),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'html',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_53c046af3f2b8',
                            'label' => __('People', WP_Bullhorn::TEXTDOMAIN),
                            'name' => '',
                            'type' => 'tab',
                        ),
                        array(
                            'key' => 'field_53c04224c7cf3',
                            'label' => __('Response User', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'responseUser',
                            'type' => 'repeater',
                            'instructions' => __('CorporateUser to whom submissions should be sent.', WP_Bullhorn::TEXTDOMAIN),
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_53c047197ae5e',
                                    'label' => __('ID', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'id',
                                    'type' => 'text',
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c04a467ae5f',
                                    'label' => __('External Email', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'externalEmail',
                                    'type' => 'text',
                                    'instructions' => __('User\'s external non-Bullhorn email address. Used for forwarding', WP_Bullhorn::TEXTDOMAIN),
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c04a5d7ae60',
                                    'label' => __('Name', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'name',
                                    'type' => 'text',
                                    'instructions' => __('Name of the CorporateUser.', WP_Bullhorn::TEXTDOMAIN),
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c04c9b7ae61',
                                    'label' => __('Address', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'address',
                                    'type' => 'repeater',
                                    'column_width' => '',
                                    'sub_fields' => array(
                                        array(
                                            'key' => 'field_53c04efc7ae62',
                                            'label' => __('Address 1', WP_Bullhorn::TEXTDOMAIN),
                                            'name' => 'address1',
                                            'type' => 'text',
                                            'column_width' => '',
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'formatting' => 'html',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_53c04f197ae63',
                                            'label' => __('Address 2', WP_Bullhorn::TEXTDOMAIN),
                                            'name' => 'address2',
                                            'type' => 'text',
                                            'column_width' => '',
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'formatting' => 'html',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_53c04f257ae64',
                                            'label' => __('City', WP_Bullhorn::TEXTDOMAIN),
                                            'name' => 'city',
                                            'type' => 'text',
                                            'column_width' => '',
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'formatting' => 'html',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_53c04f2b7ae65',
                                            'label' => __('State', WP_Bullhorn::TEXTDOMAIN),
                                            'name' => 'state',
                                            'type' => 'text',
                                            'column_width' => '',
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'formatting' => 'html',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_53c04f347ae66',
                                            'label' => __('Postal Code', WP_Bullhorn::TEXTDOMAIN),
                                            'name' => 'zip',
                                            'type' => 'text',
                                            'column_width' => '',
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'formatting' => 'html',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_53c04f437ae67',
                                            'label' => __('Country ID', WP_Bullhorn::TEXTDOMAIN),
                                            'name' => 'countryID',
                                            'type' => 'text',
                                            'column_width' => '',
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'formatting' => 'html',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_53c04f547ae68',
                                            'label' => __('Country Name', WP_Bullhorn::TEXTDOMAIN),
                                            'name' => 'country_name',
                                            'type' => 'text',
                                            'column_width' => '',
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'formatting' => 'html',
                                            'maxlength' => '',
                                        ),
                                    ),
                                    'row_min' => 1,
                                    'row_limit' => 1,
                                    'layout' => 'row',
                                    'button_label' => 'Add Row',
                                ),
                                array(
                                    'key' => 'field_53c04f6e7ae69',
                                    'label' => __('Occupation', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'occupation',
                                    'type' => 'text',
                                    'instructions' => __('Occupation of the CorporateUser.', WP_Bullhorn::TEXTDOMAIN),
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c04f8b7ae6a',
                                    'label' => __('Phone', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'phone',
                                    'type' => 'text',
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c04fa37ae6b',
                                    'label' => __('Mobile', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'mobile',
                                    'type' => 'text',
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c04fb07ae6c',
                                    'label' => __('Custom Text 1', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'customText1',
                                    'type' => 'text',
                                    'instructions' => __('Configurable text fields that can be used to store custom data depending on the needs of a particular deployment.', WP_Bullhorn::TEXTDOMAIN),
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c04fe57ae6d',
                                    'label' => __('Custom Text 2', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'customText2',
                                    'type' => 'text',
                                    'instructions' => __('Configurable text fields that can be used to store custom data depending on the needs of a particular deployment.', WP_Bullhorn::TEXTDOMAIN),
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c04ffe7ae6e',
                                    'label' => __('Custom Text 3', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'customText3',
                                    'type' => 'text',
                                    'instructions' => __('Configurable text fields that can be used to store custom data depending on the needs of a particular deployment.', WP_Bullhorn::TEXTDOMAIN),
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                            ),
                            'row_min' => 1,
                            'row_limit' => 1,
                            'layout' => 'row',
                            'button_label' => 'Add Row',
                        ),
                        array(
                            'key' => 'field_53c054d0c6231',
                            'label' => __('Owner', WP_Bullhorn::TEXTDOMAIN),
                            'name' => 'owner',
                            'type' => 'repeater',
                            'instructions' => 'CorporateUser who owns this job.
                The default value is user who creates the JobOrder.',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_53c054d0c6232',
                                    'label' => __('ID', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'id',
                                    'type' => 'text',
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c054d0c6233',
                                    'label' => __('External Email', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'externalEmail',
                                    'type' => 'text',
                                    'instructions' => __('User\'s external non-Bullhorn email address. Used for forwarding', WP_Bullhorn::TEXTDOMAIN),
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c054d0c6234',
                                    'label' => __('Name', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'name',
                                    'type' => 'text',
                                    'instructions' => __('Name of the CorporateUser.', WP_Bullhorn::TEXTDOMAIN),
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c054d0c6235',
                                    'label' => __('Address', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'address',
                                    'type' => 'repeater',
                                    'column_width' => '',
                                    'sub_fields' => array(
                                        array(
                                            'key' => 'field_53c054d0c6236',
                                            'label' => __('Address 1', WP_Bullhorn::TEXTDOMAIN),
                                            'name' => 'address1',
                                            'type' => 'text',
                                            'column_width' => '',
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'formatting' => 'html',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_53c054d0c6237',
                                            'label' => __('Address 2', WP_Bullhorn::TEXTDOMAIN),
                                            'name' => 'address2',
                                            'type' => 'text',
                                            'column_width' => '',
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'formatting' => 'html',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_53c054d0c6238',
                                            'label' => __('City', WP_Bullhorn::TEXTDOMAIN),
                                            'name' => 'city',
                                            'type' => 'text',
                                            'column_width' => '',
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'formatting' => 'html',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_53c054d0c6239',
                                            'label' => __('State', WP_Bullhorn::TEXTDOMAIN),
                                            'name' => 'state',
                                            'type' => 'text',
                                            'column_width' => '',
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'formatting' => 'html',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_53c054d0c623a',
                                            'label' => __('Postal Code', WP_Bullhorn::TEXTDOMAIN),
                                            'name' => 'zip',
                                            'type' => 'text',
                                            'column_width' => '',
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'formatting' => 'html',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_53c054d0c623b',
                                            'label' => __('Country ID', WP_Bullhorn::TEXTDOMAIN),
                                            'name' => 'countryID',
                                            'type' => 'text',
                                            'column_width' => '',
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'formatting' => 'html',
                                            'maxlength' => '',
                                        ),
                                        array(
                                            'key' => 'field_53c054d0c623c',
                                            'label' => __('Country Name', WP_Bullhorn::TEXTDOMAIN),
                                            'name' => 'country_name',
                                            'type' => 'text',
                                            'column_width' => '',
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => '',
                                            'append' => '',
                                            'formatting' => 'html',
                                            'maxlength' => '',
                                        ),
                                    ),
                                    'row_min' => 1,
                                    'row_limit' => 1,
                                    'layout' => 'row',
                                    'button_label' => 'Add Row',
                                ),
                                array(
                                    'key' => 'field_53c054d0c623d',
                                    'label' => __('Occupation', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'occupation',
                                    'type' => 'text',
                                    'instructions' => __('Occupation of the CorporateUser.', WP_Bullhorn::TEXTDOMAIN),
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c054d0c623e',
                                    'label' => __('Phone', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'phone',
                                    'type' => 'text',
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c054d0c623f',
                                    'label' => __('Mobile', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'mobile',
                                    'type' => 'text',
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c054d0c6240',
                                    'label' => __('Custom Text 1', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'customText1',
                                    'type' => 'text',
                                    'instructions' => __('Configurable text fields that can be used to store custom data depending on the needs of a particular deployment.', WP_Bullhorn::TEXTDOMAIN),
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c054d0c6241',
                                    'label' => __('Custom Text 2', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'customText2',
                                    'type' => 'text',
                                    'instructions' => __('Configurable text fields that can be used to store custom data depending on the needs of a particular deployment.', WP_Bullhorn::TEXTDOMAIN),
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_53c054d0c6242',
                                    'label' => __('Custom Text 3', WP_Bullhorn::TEXTDOMAIN),
                                    'name' => 'customText3',
                                    'type' => 'text',
                                    'instructions' => __('Configurable text fields that can be used to store custom data depending on the needs of a particular deployment.', WP_Bullhorn::TEXTDOMAIN),
                                    'column_width' => '',
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'formatting' => 'html',
                                    'maxlength' => '',
                                ),
                            ),
                            'row_min' => 1,
                            'row_limit' => 1,
                            'layout' => 'row',
                            'button_label' => 'Add Row',
                        ),
                    ),
                    'location' => array(
                        array(
                            array(
                                'param' => 'post_type',
                                'operator' => '==',
                                'value' => self::POST_TYPE,
                                'order_no' => 0,
                                'group_no' => 0,
                            ),
                        ),
                    ),
                    'options' => array(
                        'position' => 'normal',
                        'layout' => 'no_box',
                        'hide_on_screen' => array(
                        ),
                    ),
                    'menu_order' => 0,
                );
                register_field_group($this->_acf_config);
            } // END if(function_exists("register_field_group"))
        } // END public function create_post_type()
    } // END class JobOrder
} // END if(!class_exists('JobOrder'))
