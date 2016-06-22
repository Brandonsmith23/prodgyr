<?php
/*
Plugin Name: WP-Bullhorn Base Plugin (REST API)
Plugin URI: http://www.wpbullhorn.com
Description: Bullhorn Integration Plugin for Wordpress
Version: 3.0.2
Author: CO+LAB Multimedia, LLC
Author URI: http://www.teamcolab.com
License: Custom (See Below)
Text Domain: wpbullhorn
*/
/*
Copyright 2013  CO+LAB Multimedia, LLC  (email : support@teamcolab.com)

See LICENSE file for licensinginfo
*/


if(!class_exists('WP_Bullhorn'))
{
    class WP_Bullhorn
    {
        const TEXTDOMAIN = 'wpbullhorn';
        const REMOTE_PLUGIN_NAME = 'WP-Bullhorn Plugin';
        const REMOTE_UPDATE_URL = 'http://www.wpbullhorn.com';
        const PLUGIN_AUTHOR = 'CO+LAB Multimedia, LLC';
        const VERSION = '3.0.2';

        /**
         * Construct the plugin object
         */
        public function __construct()
        {
            // Localize the plugin
            load_plugin_textdomain(self::TEXTDOMAIN);

            // Setup the plugin's requirements
            self::setup();

            // Check for plugin updates
            $this->update_plugin();
        } // END public function __construct

        /**
         * This function sets up the plugin's requirements
         */
        public static function setup()
        {
            // Pull in Advanced Custom Fields
            //define('ACF_LITE' , true); // To hide the ACF plugin admin screens
            require_once('includes/advanced-custom-fields/acf.php');
            require_once('includes/acf-repeater/acf-repeater.php');

            // Settings link on the plugin page
            require_once(sprintf("%s/includes/settings.php", dirname(__FILE__)));
            $settings = new WP_Bullhorn_Settings(plugin_basename(__FILE__));

            // Import JobOrder class and register an instance of it as a globally
            global $bh_job_order;
            require_once(sprintf("%s/includes/joborder.php", dirname(__FILE__)));
            $bh_job_order = new JobOrder();

            // Import BullhornCategory and register an instance of it globally
            global $bh_category;
            require_once(sprintf("%s/includes/category.php", dirname(__FILE__)));
            $bh_category = new BullhornCategory();
        } // END public static function setup()

        /**
         * Goes out to the update server and looks for/fetches updates
         */
        public function update_plugin()
        {
            set_site_transient('update_plugins', null);

            if(!class_exists('EDD_SL_Plugin_Updater'))
            {
                // load our custom updater
                include(dirname(__FILE__) . '/EDD_SL_Plugin_Updater.php');
            }

            // retrieve our license key from the DB
            $license_key = trim(get_option('bh_license_key'));

            // setup the updater
            $edd_updater = new EDD_SL_Plugin_Updater(self::REMOTE_UPDATE_URL, __FILE__, array(
                    'version'   => self::VERSION,
                    'license'   => $license_key,
                    'item_name' => self::REMOTE_PLUGIN_NAME,
                    'author'    => self::PLUGIN_AUTHOR,
                    'url' => home_url()
                )
            );
        } // END public function update_plugin()

        /*
         * Verify the license status in the `wp_options` table. If there is not
         * a valid license, then the script just exits. This method is only called
         * by the `sync` method of the WP_Bullhorn class which in turn is called
         * by the `cron.php` file in the plugin.
         * @see `WP_Bullhorn::sync()`
         */
        public static function verify_license_status() {

            $license_status = get_option( 'bh_license_status' );

            if ( false === $license_status || 'valid' !== $license_status ) {

                exit();

            }

        }

        /**
         * Create a function that syncs all of the API data
         *
         * @uses `verify_license_status()`
         *
         */
        public static function sync()
        {

            self::verify_license_status();

            ob_start();

            self::setup();
            global $bh_category, $bh_job_order;

            $bh_category->sync();
            $bh_job_order->sync();

            ob_end_clean();
        } // END public static function sync()

        /**
         * Activate the plugin
         */
        public static function activate()
        {
            // Setup the plugin's requirements & Flush Rewrite rules for permalinks
            // http://codex.wordpress.org/Function_Reference/register_post_type#Flushing_Rewrite_on_Activation
            self::setup();
            flush_rewrite_rules();

            // Set default where clauses for sync utilities
            $where_clause = get_option("bh_joborder_sync_where");
            if(empty($where_clause))
            {
                update_option("bh_joborder_sync_where", "isDeleted = false AND isOpen = true AND isPublic = 1");
            }
            $where_clause = get_option("bh_category_sync_where");
            if(empty($where_clause))
            {
                update_option("bh_category_sync_where", "enabled = true");
            }
        } // END public static function activate

        /**
         * Deactivate the plugin
         */
        public static function deactivate()
        {
            // N/A
        } // END public static function deactivate
    } // END class WP_Bullhorn
} // END if(!class_exists('WP_Bullhorn'))

if(class_exists('WP_Bullhorn'))
{
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('WP_Bullhorn', 'activate'));
    register_deactivation_hook(__FILE__, array('WP_Bullhorn', 'deactivate'));

    // instantiate the plugin class
    $wp_bullhorn_plugin = new WP_Bullhorn();
} // END if(class_exists('WP_Bullhorn'))
