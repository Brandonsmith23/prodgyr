<?php
if(!class_exists('WP_Bullhorn_Settings'))
{
    class WP_Bullhorn_Settings
    {
        const OPTIONS_GROUP = "bullhorn";
        const MENU_PAGE_TITLE = "WP Bullhorn";
        const MENU_TITLE = "Bullhorn";
        const MENU_URI = "";

        /**
         * Construct the plugin object
         */
        public function __construct($plugin)
        {
            // register actions
            add_action('admin_init', array(&$this, 'admin_init'));
            add_action( 'admin_init', array( &$this, 'activate_license') );
            add_action('admin_menu', array(&$this, 'add_menu'));
            add_action('parent_file', array(&$this, 'menu_correction'));
            add_action('admin_notices', array(&$this, 'admin_notice'));
            add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_options_styles' ) );


            add_filter("plugin_action_links_$plugin", array(&$this, 'plugin_settings_link'));
        } // END public function __construct

        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init()
        {
            // register the settings for this plugin
            register_setting(self::OPTIONS_GROUP, 'bh_username');
            register_setting(self::OPTIONS_GROUP, 'bh_password');
            register_setting(self::OPTIONS_GROUP, 'bh_client_id');
            register_setting(self::OPTIONS_GROUP, 'bh_client_secret');
            register_setting(self::OPTIONS_GROUP, 'bh_joborder_sync_where');
            register_setting(self::OPTIONS_GROUP, 'bh_category_sync_where');

            // Register settings for license key activation
            register_setting( self::OPTIONS_GROUP, 'bh_license_key', array( &$this, 'sanitize_license' ) );

            // add settings sections
            add_settings_section(
                'bullhorn-licenses',
                __('Plugin Licenses', WP_Bullhorn::TEXTDOMAIN),
                array(&$this, 'section_licenses'),
                'bullhorn'
            );
            add_settings_section(
                'bullhorn-api',
                __('API Credentials', WP_Bullhorn::TEXTDOMAIN),
                array(&$this, 'section_api'),
                'bullhorn'
            );
            add_settings_section(
                'bullhorn-config',
                __('Plugin Configuration', WP_Bullhorn::TEXTDOMAIN),
                array(&$this, 'section_config'),
                'bullhorn'
            );

            // Settings Fields
            add_settings_field(
                'id_bh_license_key',
                sprintf('<label for="%s">%s</label>', 'id_bh_license_key', __('Base Plugin License Key', WP_Bullhorn::TEXTDOMAIN)),
                array(&$this, 'settings_field_license_input'),
                'bullhorn',
                'bullhorn-licenses',
                array(
                    'field' => 'bh_license_key'
                )
            );
            add_settings_field(
                'id_bh_username',
                sprintf('<label for="%s">%s</label>', 'id_bh_username', __('Username', WP_Bullhorn::TEXTDOMAIN)),
                array(&$this, 'settings_field_input_text'),
                'bullhorn',
                'bullhorn-api',
                array(
                    'field' => 'bh_username'
                )
            );
            add_settings_field(
                'id_bh_password',
                sprintf('<label for="%s">%s</label>', 'id_bh_password', __('Password', WP_Bullhorn::TEXTDOMAIN)),
                array(&$this, 'settings_field_input_text'),
                'bullhorn',
                'bullhorn-api',
                array(
                    'field' => 'bh_password'
                )
            );
            add_settings_field(
                'id_bh_client_id',
                sprintf('<label for="%s">%s</label>', 'id_bh_client_id', __('Client ID', WP_Bullhorn::TEXTDOMAIN)),
                array(&$this, 'settings_field_input_text'),
                'bullhorn',
                'bullhorn-api',
                array(
                    'field' => 'bh_client_id'
                )
            );
            add_settings_field(
                'id_bh_client_secret',
                sprintf('<label for="%s">%s</label>', 'id_bh_client_secret', __('Client Secret', WP_Bullhorn::TEXTDOMAIN)),
                array(&$this, 'settings_field_input_text'),
                'bullhorn',
                'bullhorn-api',
                array(
                    'field' => 'bh_client_secret'
                )
            );
            add_settings_field(
                'id_bh_joborder_sync_where',
                sprintf('<label for="%s">%s</label>', 'id_bh_joborder_sync_where', __('Where Clause for JobOrder Sync', WP_Bullhorn::TEXTDOMAIN)),
                array(&$this, 'settings_field_input_text'),
                'bullhorn',
                'bullhorn-config',
                array(
                    'field' => 'bh_joborder_sync_where'
                )
            );
            add_settings_field(
                'id_bh_category_sync_where',
                sprintf('<label for="%s">%s</label>', 'id_bh_category_sync_where', __('Where Clause for Category Sync', WP_Bullhorn::TEXTDOMAIN)),
                array(&$this, 'settings_field_input_text'),
                'bullhorn',
                'bullhorn-config',
                array(
                    'field' => 'bh_category_sync_where'
                )
            );
        } // END public static function activate

        /**
         * Set up the Licenses section
         */
        public function section_licenses()
        {
            // help text
            echo __('Manage Licenses', WP_Bullhorn::TEXTDOMAIN);
        } // END public function section_licenses()

        /**
         * Set up the Licenses section
         */
        public function section_api()
        {
            // help text
            echo __('Manage Bullhorn API Credentials', WP_Bullhorn::TEXTDOMAIN);
        } // END public function section_api()

        /**
         * Set up the Licenses section
         */
        public function section_config()
        {
            // help text
            echo __('Plugin Options', WP_Bullhorn::TEXTDOMAIN);
        } // END public function section_config()

        /**
         * This function provides text inputs for settings fields
         */
        public function settings_field_input_text($args)
        {
            // Get the field name from the $args array
            $field = $args['field'];
            // Get the value of this setting
            $value = get_option($field);
            // echo a proper input type="text"
            echo sprintf('<input type="text" name="%s" id="id_%s" value="%s" />', $field, $field, $value);
        } // END public function settings_field_input_text($args)

        /*
         * Echos out a text input for license key activations with all of the
         * fancy markup.
         * @see settings_field_input_text()
         * @param {Array} $args Arguments passed to settings_field_input_text()
         */
        public function settings_field_license_input( $args ) {
            // Ensure that the cache is cleared before getting options after the
            // POST request followed by the GET request.
            wp_cache_delete( 'alloptions', 'options' );

            // Call original `settings_field_input_text()` method.
            $this->settings_field_input_text( $args );

            // Get the status of the license
            $license_status = get_option( 'bh_license_status' );

            // Append some additional markup for the license key input
            if ( false !== $license_status && 'valid' === $license_status ) {
                echo sprintf( '<span class="%s-valid-license dashicons dashicons-yes"></span>', WP_Bullhorn::TEXTDOMAIN );
            } else {
                // The license in the database is invalid, so let's do something about it.
                echo sprintf( '<span class="%s-invalid-license dashicons dashicons-no"></span>', WP_Bullhorn::TEXTDOMAIN );
                echo sprintf(
                    "<p class='%s-validation-message'>%s</p>",
                    WP_Bullhorn::TEXTDOMAIN,
                    'Please enter a valid license key.'
                );
            }
            wp_nonce_field( 'bh_license_nonce', 'bh_license_nonce' );
            echo sprintf(
                "<p>
                <input
                    type='submit'
                    class='button button-secondary'
                    name='bh_license_activate'
                    value='%s' />
                </p>",
                __( 'Activate License' )
            );
        }

        /**
         * add a menu
         */
        public function add_menu()
        {
            // Add a page to manage this plugin's settings
            add_menu_page(
                __(self::MENU_PAGE_TITLE, WP_Bullhorn::TEXTDOMAIN),
                __(self::MENU_TITLE, WP_Bullhorn::TEXTDOMAIN),
                "manage_options",
                "bullhorn",
                "",
                "dashicons-universal-access-alt",
                21
            );

            // Add Taxonomy Page to the new menu
            add_submenu_page(
                "bullhorn",
                __("Categories", WP_Bullhorn::TEXTDOMAIN),
                __("Categories", WP_Bullhorn::TEXTDOMAIN),
                "manage_options",
                sprintf("edit-tags.php?taxonomy=%s", BullhornCategory::SLUG)
            );

            // Add Settings Page to the new menu
            add_submenu_page(
                "bullhorn",
                __("Settings", WP_Bullhorn::TEXTDOMAIN),
                __("Settings", WP_Bullhorn::TEXTDOMAIN),
                "manage_options",
                "bullhorn",
                array(&$this, 'settings_page')
            );
        } // END public function add_menu()

        /**
         * highlight the proper top level menu
         */
        public function menu_correction($parent_file)
        {
            global $current_screen;
            global $submenu_file;

            if($current_screen->taxonomy == BullhornCategory::SLUG)
            {
                $parent_file = "bullhorn";
            }

            return $parent_file;
        } // END public function menu_correction($parent_file)

        /**
         * Admin Notice to check API TOS acceptance
         */
        public function admin_notice()
        {
            // Only check the TOS if it hasn't already passed the test
            if(get_option('bh_tos_accepted', FALSE) === FALSE)
            {
                // Fetch the API credentials
                $bh_client_id = get_option('bh_client_id');
                $bh_username = get_option('bh_username');
                $bh_password = get_option('bh_password');
                $bh_client_secret = get_option('bh_client_secret');

                if(!empty($bh_client_id) && !empty($bh_client_secret) && !empty($bh_username) && !empty($bh_password))
                {
                    // Import the required api classes
                    require_once(sprintf("%s/rest/connection.php", dirname(__FILE__)));

                    // The connection object will throw an Exception if the TOS have not been accepted
                    try
                    {
                        // Create a connection to bullhorn
                        $bh_connection = new BullhornRestConnection($bh_client_id, $bh_client_secret, $bh_username, $bh_password);

                        // Mark the TOS as accepted
                        update_option("bh_tos_accepted", TRUE);
                    }
                    catch(Exception $e)
                    {
                        echo sprintf('<div class="updated"><p>%s</p></div>', __($e->getMessage(), WP_Bullhorn::TEXTDOMAIN));
                    }
                }
                else
                {
                    echo sprintf('<div class="updated"><p>%s</p></div>', __("Please enter your Bullhorn API Credentials on the plugin settings page.", WP_Bullhorn::TEXTDOMAIN));
                }
            }
        }

        /**
         * Menu Callback
         */
        public function settings_page()
        {
            if(!current_user_can('manage_options'))
            {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            // Render the settings template
            include(sprintf("%s/../templates/settings.php", dirname(__FILE__)));
        } // END public function settings_page()

        /**
         * Add the settings link to the plugins page
         */
        public function plugin_settings_link($links)
        {
            $settings_link = '<a href="admin.php?page=bullhorn">Settings</a>';
            array_unshift($links, $settings_link);
            return $links;
        } // END public function plugin_settings_link($links)


        /*
         * Sanitize callback for the `bh_license_key` setting. The license does
         * not require any validation. Instead, we're going to clear out the
         * `bh_license_status` if the license's don't match._
         * @param {String} $new_license The new value of `bh_license_key`.
         * @return {String} The new sanitized value.
         */
        public function sanitize_license( $new_license ) {
            $current_license = get_option( 'bh_license_key' );

            $license = wp_filter_nohtml_kses( $new_license );

            if ( $current_license && $license !== $current_license ) {
                delete_option( 'bh_license_status' );
            }

            return $license;
        }

        /*
         * Activate License using the EDD API at REMOTE_UPDATE_URL
         */
        public function activate_license() {

            if ( isset( $_POST[ 'bh_license_activate' ] ) ) {

                if ( ! check_admin_referer( 'bh_license_nonce', 'bh_license_nonce' ) ) {
                    return;
                }

                $license_key = trim( $_POST[ 'bh_license_key' ] );

                if ( empty( $license_key ) ) {
                    return false;
                }

                $composed_request = array(
                    'edd_action' => 'activate_license',
                    'license' => $license_key,
                    'item_name' => urlencode( WP_Bullhorn::REMOTE_PLUGIN_NAME ),
                    'url' => home_url(),
                );

                $response = wp_remote_post( WP_Bullhorn::REMOTE_UPDATE_URL , array(
                    'timeout' => 15,
                    'sslverify' => false,
                    'body' => $composed_request,
                ) );

                if ( is_wp_error( $response ) ) {
                    return false;
                }

                $license_data = json_decode( wp_remote_retrieve_body( $response ) );

                update_option( 'bh_license_status', $license_data->license );

            }

        }

        /*
         * Enqueue the option's page stylesheet if on that WPBullhorn options page.
         * @param {String} $hook The page or 'hook' being viewed on the admin.
         */
        public function enqueue_options_styles( $hook ) {
            if ( 'toplevel_page_bullhorn' !== $hook ) {
                return;
            }
            // Register WP-Bullhorn plugin stylesheet
            wp_register_style(
                WP_Bullhorn::TEXTDOMAIN . '_option_stylesheet',
                plugins_url( '../css/options.css', __FILE__ )
            );
            wp_enqueue_style( WP_Bullhorn::TEXTDOMAIN . '_option_stylesheet' );
        }

    } // END class WP_Bullhorn_Settings
} // END if(!class_exists('WP_Bullhorn_Settings'))
