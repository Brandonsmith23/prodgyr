<?php
/**
 * Plugin Name: DV Team
 * Plugin URI: http://codecanyon.net/item/dv-team-responsive-team-showcase-wordpress-plugin/9962337?ref=egemenerd
 * Description: Responsive Team Members Plugin for Wordpress
 * Version: 1.4
 * Author: Egemenerd
 * Author URI: http://codecanyon.net/user/egemenerd?ref=egemenerd
 * License: http://codecanyon.net/licenses?ref=egemenerd
 */

/* Language File */

add_action( 'init', 'dvteamdomain' );

function dvteamdomain() {
	load_plugin_textdomain( 'dvteam', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/*---------------------------------------------------
custom image sizes
----------------------------------------------------*/

add_image_size( 'dv-team-rectangle', 600, 400, true);
add_image_size( 'dv-team-square', 600, 600, true);
add_filter('image_size_names_choose', 'dvteam_image_sizes');

function dvteam_image_sizes($gridsizes) {
    $gridaddsizes = array(
        "dv-team-rectangle" => __( 'DV-Team Rectangle', 'dvteam'),
        "dv-team-square" => __( 'DV-Team Square', 'dvteam')
    );
    $gridnewsizes = array_merge($gridsizes, $gridaddsizes);
    return $gridnewsizes;
}

/*---------------------------------------------------
Register Scripts
----------------------------------------------------*/

function dvteam_scripts(){
    wp_enqueue_style('dvteam_styles', plugin_dir_url( __FILE__ ) . 'css/style.css', true, '1.0');
    wp_enqueue_style('dvteam_scrollbar_styles', plugin_dir_url( __FILE__ ) . 'css/scrollbar.css', true, '1.0');
    wp_enqueue_style('dv_owl_style', plugin_dir_url( __FILE__ ) . 'css/owl.css', true, '1.0');
    wp_enqueue_style('dv_popup_style', plugin_dir_url( __FILE__ ) . 'css/popup.css', true, '1.0');
    if (is_rtl()) {
        wp_enqueue_style('dvteam_styles_rtl', plugin_dir_url( __FILE__ ) . 'css/rtl.css', true, '1.0');
    }
    wp_enqueue_script("jquery");
    wp_register_script('dv_wookmark',plugin_dir_url( __FILE__ ).'js/wookmark.js','','',true);
    wp_register_script('dv_owl',plugin_dir_url( __FILE__ ).'js/owl.js','','',true);
    wp_register_script('dv_scrollbar',plugin_dir_url( __FILE__ ).'js/scrollbar.js','','',true);
    wp_register_script('dv_slidepanel',plugin_dir_url( __FILE__ ).'js/panelslider.js','','',true);
    wp_register_script('dv_popup',plugin_dir_url( __FILE__ ).'js/popup.js','','',true);
    wp_register_script('dv_slidizle',plugin_dir_url( __FILE__ ).'js/slidizle.js','','',true);
    wp_register_script('dvteam_custom',plugin_dir_url( __FILE__ ).'js/custom.js','','',true);
    wp_enqueue_script('dv_wookmark');
    wp_enqueue_script('dv_owl');
    wp_enqueue_script('dv_scrollbar');
    wp_enqueue_script('dv_slidepanel');
    wp_enqueue_script('dv_popup');
    wp_enqueue_script('dv_slidizle');
    wp_enqueue_script('dvteam_custom');
}
add_action('wp_enqueue_scripts','dvteam_scripts');

function dvteamadmin() {
	wp_enqueue_style('dvteam-adminstyle', plugins_url('css/admin.css', __FILE__));
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('dvteam_panel_script', plugin_dir_url( __FILE__ ) . 'js/panel.js','','',true);
}

add_action('admin_enqueue_scripts', 'dvteamadmin');

/* ---------------------------------------------------------
Custom Metaboxes - https://github.com/WebDevStudios/CMB2
----------------------------------------------------------- */

// Check for  PHP version and use the correct dir
$dvteamdir = ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) ? __DIR__ : dirname( __FILE__ );

if ( file_exists(  $dvteamdir . '/cmb2/init.php' ) ) {
	require_once  $dvteamdir . '/cmb2/init.php';
} elseif ( file_exists(  $dvteamdir . '/CMB2/init.php' ) ) {
	require_once  $dvteamdir . '/CMB2/init.php';
}

/* ---------------------------------------------------------
Add Featured Image Support for DV Team Custom Post Type
----------------------------------------------------------- */

function DvteamAddFeatured() {

    global $_wp_theme_features;

    if( empty($_wp_theme_features['post-thumbnails']) )
    {
        $_wp_theme_features['post-thumbnails'] = array( array('dvourteam') );
    }

    elseif( true === $_wp_theme_features['post-thumbnails'])
    {
        return;
    }

    elseif( is_array($_wp_theme_features['post-thumbnails'][0]) )
    {
        $_wp_theme_features['post-thumbnails'][0][] = 'dvourteam';
    }
}
add_action( 'after_setup_theme', 'DvteamAddFeatured', 99 );

/* ---------------------------------------------------------
Add Required Featured Image message for DV Team Custom Post Type
----------------------------------------------------------- */

add_action( 'transition_post_status', 'dvteam_rfi_dont_publish_post', 10, 3 );
function dvteam_rfi_dont_publish_post( $new_status, $old_status, $post ) {
    if ( $new_status === 'publish' && $old_status !== 'publish'
        && !dvteam_rfi_should_let_post_publish( $post ) ) {
        wp_die( esc_attr__( 'You cannot publish without a featured image.', 'dvteam' ) );
    }
}

add_action( 'admin_enqueue_scripts', 'dvteam_rfi_enqueue_edit_screen_js' );
function dvteam_rfi_enqueue_edit_screen_js( $hook ) {

    global $post;
    $post_types = array(
        'dvourteam'
    );
    if ( $hook !== 'post.php' && $hook !== 'post-new.php' ) {
        return;
    }

    if ( in_array( $post->post_type, $post_types ) ) {
        wp_register_script( 'dvteam-rfi-admin-js', plugins_url( 'js/require-featured-image-on-edit.js', __FILE__ ), array( 'jquery' ) );
        wp_enqueue_script( 'dvteam-rfi-admin-js' );
        wp_localize_script(
            'dvteam-rfi-admin-js',
            'objectL10n',
            array(
                'jsWarningHtml' => esc_attr__( 'This member has no featured image. Please set one. You need to set a featured image before publishing.', 'dvteam' ),
            )
        );
    }
}

function dvteam_rfi_should_let_post_publish( $post ) {
    global $post;
    $post_types = array(
        'dvourteam'
    );
    return !( in_array( $post->post_type, $post_types ) && !has_post_thumbnail( $post->ID ) );
}

/*---------------------------------------------------
Hide dv-team custom post type post view and post preview links/buttons
----------------------------------------------------*/
function dvteam_posttype_admin_css() {
    global $post_type;
    $post_types = array(
        'dvourteam'
    );
    if(in_array($post_type, $post_types)) { ?>
    <style type="text/css">#post-preview, #view-post-btn, .updated > p > a, #wp-admin-bar-view, #edit-slug-box{display: none !important;}</style>
    <?php }
}
add_action( 'admin_head-post-new.php', 'dvteam_posttype_admin_css' );
add_action( 'admin_head-post.php', 'dvteam_posttype_admin_css' );

function dvteam_row_actions( $actions )
{
    if(get_post_type() != 'dvourteam') {
        return $actions;
    }
    else {
        unset( $actions['view'] );
        return $actions;
    }
}
add_filter( 'post_row_actions', 'dvteam_row_actions', 10, 1 );

/*---------------------------------------------------
Tinymce Our Team Button
----------------------------------------------------*/

if ( ! function_exists( 'dvteamshortcodes_add_button' ) ) {
add_action('init', 'dvteamshortcodes_add_button');  
function dvteamshortcodes_add_button() {  
   if ( current_user_can('edit_posts') && current_user_can('edit_pages'))  
   {  
     add_filter('mce_external_plugins', 'dvteam_add_plugin');  
     add_filter('mce_buttons_3', 'dvteam_register_button');  
   }  
} 
}

if ( ! function_exists( 'dvteam_register_button' ) ) {
function dvteam_register_button($buttons) {
    array_push($buttons, "dvteam");
    array_push($buttons, "dvteamfilter");
    array_push($buttons, "dvteamcarousel");
    array_push($buttons, "dvthumbnails");
    return $buttons;  
}  
}

if ( ! function_exists( 'dvteam_add_plugin' ) ) {
function dvteam_add_plugin($plugin_array) {
    $plugin_array['dvteam'] = plugin_dir_url( __FILE__ ) . 'js/shortcodes/ourteam.js';
    $plugin_array['dvteamfilter'] = plugin_dir_url( __FILE__ ) . 'js/shortcodes/ourteam.js';
    $plugin_array['dvteamcarousel'] = plugin_dir_url( __FILE__ ) . 'js/shortcodes/ourteam.js';
    $plugin_array['dvthumbnails'] = plugin_dir_url( __FILE__ ) . 'js/shortcodes/ourteam.js';
    return $plugin_array;  
}
}

/*---------------------------------------------------
Tinymce Skills and CV Button
----------------------------------------------------*/

if ( ! function_exists( 'dvskillshortcodes_add_button' ) ) {
add_action('init', 'dvskillshortcodes_add_button');  
function dvskillshortcodes_add_button() {  
   if ( current_user_can('edit_posts') && current_user_can('edit_pages'))  
   {  
     add_filter('mce_external_plugins', 'dvskill_add_plugin');  
     add_filter('mce_buttons_3', 'dvskill_register_button');  
   }  
} 
}

if ( ! function_exists( 'dvskill_register_button' ) ) {
function dvskill_register_button($buttons) {
    array_push($buttons, "dvskills");
    array_push($buttons, "dvcv");
    return $buttons;  
}  
}

if ( ! function_exists( 'dvskill_add_plugin' ) ) {
function dvskill_add_plugin($plugin_array) {
    $plugin_array['dvskills'] = plugin_dir_url( __FILE__ ) . 'js/shortcodes/skills.js';
    $plugin_array['dvcv'] = plugin_dir_url( __FILE__ ) . 'js/shortcodes/skills.js';
    return $plugin_array;  
}
}

/* ---------------------------------------------------------
Include required files
----------------------------------------------------------- */

include_once('ourteam_cpt.php');
include_once('dvteam_shortcodes.php');
include_once('dvteam_widgets.php');

/* ----------------------------------------------------------
Register Styles
------------------------------------------------------------- */

add_action('wp_head', 'dvteam_styles');
function dvteam_styles() {
    include('styles.php');
}

/* ----------------------------------------------------------
Declare vars
------------------------------------------------------------- */

$dvteam = "dvteam";

/* ---------------------------------------------------------
Declare plugin options
----------------------------------------------------------- */
 
$dv_plugin_options = array (
 
array( "name" => $dvteam." Options",
"type" => "title"),
    
/* ---------------------------------------------------------
General
----------------------------------------------------------- */
array( "name" => esc_attr__( 'General', 'dvteam'),
"icon" => "dashicons-admin-generic",      
"type" => "section"),
array( "type" => "open"),
    
array( "name" => esc_attr__( 'Custom CSS', 'dvteam'),
"desc" => esc_attr__( 'You can use this field to add your own css codes', 'dvteam'),
"id" => $dvteam . "_customcode",
"type" => "textarea",
"std" => ""),
    
array( "name" => esc_attr__( 'Title Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #ffffff (h1,h2,h3,h4,h5,h6)', 'dvteam'),
"id" => $dvteam . "_phtitlescolor",
"type" => "colorpicker",
"std" => "#ffffff"),
    
array( "name" => esc_attr__( 'Bold Titles', 'dvteam'),
"desc" => esc_attr__( 'To make bold titles, check this box.', 'dvteam'),
"id" => $dvteam . "_boldtitles",
"type" => "checkbox",
"std" => ""),
    
array( "name" => esc_attr__( 'Title bottom margin', 'dvteam'),
"desc" => esc_attr__( 'The distance between title and content (px)', 'dvteam'),
"id" => $dvteam . "_titlemargin",
"type" => "number",
"std" => "20"),    
    
array( "name" => esc_attr__( 'H1 (px)', 'dvteam'),
"desc" => esc_attr__( 'H1 Title Font Size (px)', 'dvteam'),
"id" => $dvteam . "_h1",
"type" => "number",
"std" => "34"),

array( "name" => esc_attr__( 'H2 (px)', 'dvteam'),
"desc" => esc_attr__( 'H2 Title Font Size (px)', 'dvteam'),
"id" => $dvteam . "_h2",
"type" => "number",
"std" => "28"),

array( "name" => esc_attr__( 'H3 (px)', 'dvteam'),
"desc" => esc_attr__( 'H3 Title (and dvcv shortcode title) Font Size (px)', 'dvteam'),
"id" => $dvteam . "_h3",
"type" => "number",
"std" => "24"), 

array( "name" => esc_attr__( 'H4 (px)', 'dvteam'),
"desc" => esc_attr__( 'H4 Title Font Size (px)', 'dvteam'),
"id" => $dvteam . "_h4",
"type" => "number",
"std" => "20"), 

array( "name" => esc_attr__( 'H5 (px)', 'dvteam'),
"desc" => esc_attr__( 'H5 Title Font Size (px)', 'dvteam'),
"id" => $dvteam . "_h5",
"type" => "number",
"std" => "18"), 

array( "name" => esc_attr__( 'H6 (px)', 'dvteam'),
"desc" => esc_attr__( 'H6 Title (and dvcv shortcode subtitle) Font Size (px)', 'dvteam'),
"id" => $dvteam . "_h6",
"type" => "number",
"std" => "16"),

array( "name" => esc_attr__( 'Quote Text (px)', 'dvteam'),
"desc" => esc_attr__( 'Quote text (quote post format) Font Size (px)', 'dvteam'),
"id" => $dvteam . "_quote",
"type" => "number",
"std" => "28"), 
    
array( "name" => esc_attr__( 'Quotes (em)', 'dvteam'),
"desc" => esc_attr__( 'Quotation Marks (quote post format) Size (em)', 'dvteam'),
"id" => $dvteam . "_quotemarks",
"type" => "number",
"std" => "6"), 
    
array( "name" => esc_attr__( 'Quote Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #414141', 'dvteam'),
"id" => $dvteam . "_quotescolor",
"type" => "colorpicker",
"std" => "#414141"),    
    
array( "name" => esc_attr__( 'Text Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #c7c7c7', 'dvteam'),
"id" => $dvteam . "_ptextcolor",
"type" => "colorpicker",
"std" => "#c7c7c7"),
    
array( "name" => esc_attr__( 'Text Font Size', 'dvteam'),
"desc" => esc_attr__( 'Paragraphs and other texts font size (px)', 'dvteam'),
"id" => $dvteam . "_p",
"type" => "number",
"std" => "14"),    
    
array( "name" => esc_attr__( 'Divider Height', 'dvteam'),
"desc" => esc_attr__( 'Divider Height (px)', 'dvteam'),
"id" => $dvteam . "_pdividersize",
"type" => "number",
"std" => "1"),    
    
array( "name" => esc_attr__( 'Divider Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #414141', 'dvteam'),
"id" => $dvteam . "_pdividercolor",
"type" => "colorpicker",
"std" => "#414141"),    
    
array( "type" => "close"),
    
/* ---------------------------------------------------------
Panel
----------------------------------------------------------- */
array( "name" => esc_attr__( 'Panel', 'dvteam'),
"icon" => "dashicons-admin-page",      
"type" => "section"),
array( "type" => "open"),
    
array( "name" => esc_attr__( 'Activate Body Scroll Effect', 'dvteam'),
"desc" => esc_attr__( 'It may not work properly on some themes.', 'dvteam'),
"id" => $dvteam . "_activatescroll",
"type" => "checkbox",
"std" => ""),    
    
array( "name" => esc_attr__( 'Custom Scrollbar Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #ffffff', 'dvteam'),
"id" => $dvteam . "_scrollbarcolor",
"type" => "colorpicker",
"std" => "#ffffff"),    
    
array( "name" => esc_attr__( 'Overlay Opacity', 'dvteam'),
"desc" => esc_attr__( 'Overlay Opacity', 'dvteam'),
"id" => $dvteam . "_overlayopacity",
"type" => "select",
"std" => array('0' => '0','0.1' => '0.1','0.2' => '0.2','0.3' => '0.3','0.4' => '0.4','0.5' => '0.5','0.6' => '0.6','0.7' => '0.7','0.8' => '0.8','0.9' => '0.9','1' => '1')),
    
array( "name" => esc_attr__( 'Overlay Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #212121', 'dvteam'),
"id" => $dvteam . "_overlay_color",
"type" => "colorpicker",
"std" => "#212121"),
    
array( "name" => esc_attr__( 'Panel Width', 'dvteam'),
"desc" => esc_attr__( 'Max. Width of the panel (px)', 'dvteam'),
"id" => $dvteam . "_panelwidth",
"type" => "number",
"std" => "640"),
    
array( "name" => esc_attr__( 'Inner Spacing', 'dvteam'),
"desc" => esc_attr__( 'The distance between content and main container (px)', 'dvteam'),
"id" => $dvteam . "_spaceinner",
"type" => "number",
"std" => "30"),
    
array( "name" => esc_attr__( 'Panel Background Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #313131', 'dvteam'),
"id" => $dvteam . "_panelbgcolor",
"type" => "rgbacolorpicker",
"std" => "#313131"),
    
array( "name" => esc_attr__( 'Social Bar Background Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #212121', 'dvteam'),
"id" => $dvteam . "_socialbgcolor",
"type" => "rgbacolorpicker",
"std" => "#212121"),    
    
array( "name" => esc_attr__( 'Main Title Font Size', 'dvteam'),
"desc" => esc_attr__( 'Main Title Font Size (px)', 'dvteam'),
"id" => $dvteam . "_ptitlesize",
"type" => "number",
"std" => "28"),    
    
array( "name" => esc_attr__( 'Main Title Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #ffffff', 'dvteam'),
"id" => $dvteam . "_ptitlecolor",
"type" => "colorpicker",
"std" => "#ffffff"),
    
array( "name" => esc_attr__( 'Main Title Background Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #eb5656', 'dvteam'),
"id" => $dvteam . "_ptitlebgcolor",
"type" => "rgbacolorpicker",
"std" => "#eb5656"),
    
array( "name" => esc_attr__( 'Subtitle Font Size', 'dvteam'),
"desc" => esc_attr__( 'Subtitle Font Size (px)', 'dvteam'),
"id" => $dvteam . "_psubtitlesize",
"type" => "number",
"std" => "18"),    
    
array( "name" => esc_attr__( 'Subtitle Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #ffffff', 'dvteam'),
"id" => $dvteam . "_psubtitlecolor",
"type" => "colorpicker",
"std" => "#ffffff"),
    
array( "name" => esc_attr__( 'Subtitle Background Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #414141', 'dvteam'),
"id" => $dvteam . "_psubtitlebgcolor",
"type" => "rgbacolorpicker",
"std" => "#414141"),
    
array( "name" => esc_attr__( 'Contact Form 7 Color 1', 'dvteam'),
"desc" => esc_attr__( 'Default color is #414141', 'dvteam'),
"id" => $dvteam . "_cfcolorone",
"type" => "colorpicker",
"std" => "#414141"),
    
array( "name" => esc_attr__( 'Contact Form 7 Color 2', 'dvteam'),
"desc" => esc_attr__( 'Default color is #eb5656', 'dvteam'),
"id" => $dvteam . "_cfcolortwo",
"type" => "colorpicker",
"std" => "#eb5656"), 
    
array( "name" => esc_attr__( 'Contact Form 7 Color 3', 'dvteam'),
"desc" => esc_attr__( 'Default color is #ffffff', 'dvteam'),
"id" => $dvteam . "_cfcolorthree",
"type" => "colorpicker",
"std" => "#ffffff"),       
    
array( "type" => "close"),
    
/* ---------------------------------------------------------
Grid
----------------------------------------------------------- */
array( "name" => esc_attr__( 'Grid', 'dvteam'),
"icon" => "dashicons-tagcloud",      
"type" => "section"),
array( "type" => "open"),
    
array( "name" => esc_attr__( 'Align', 'dvteam'),
"desc" => esc_attr__( 'Thumbnail Alignment', 'dvteam'),
"id" => $dvteam . "_thumbnailalign",
"type" => "select",
"std" => array('left' => 'Left','center' => 'Center','right' => 'Right')),    
    
array( "name" => esc_attr__( 'Thumbnail Opacity', 'dvteam'),
"desc" => esc_attr__( 'Thumbnail Opacity (onMouseOver)', 'dvteam'),
"id" => $dvteam . "_thumbnailopacity",
"type" => "select",
"std" => array('0' => '0','0.1' => '0.1','0.2' => '0.2','0.3' => '0.3','0.4' => '0.4','0.5' => '0.5','0.6' => '0.6','0.7' => '0.7','0.8' => '0.8','0.9' => '0.9','1' => '1')),
    
array( "name" => esc_attr__( 'Thumbnail Overlay Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #212121', 'dvteam'),
"id" => $dvteam . "_thumbnailoverlay",
"type" => "colorpicker",
"std" => "#212121"),
    
array( "name" => esc_attr__( 'Disable Thumbnail Zoom Effect', 'dvteam'),
"desc" => esc_attr__( 'To disable thumbnail zoom effect (onMouseOver), check this box.', 'dvteam'),
"id" => $dvteam . "_removezoom",
"type" => "checkbox",
"std" => ""),    
    
array( "name" => esc_attr__( 'Remove Info Icon', 'dvteam'),
"desc" => esc_attr__( 'To remove info icon, check this box.', 'dvteam'),
"id" => $dvteam . "_removeinfo",
"type" => "checkbox",
"std" => ""),    
    
array( "name" => esc_attr__( 'Info Icon Background Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #eb5656', 'dvteam'),
"id" => $dvteam . "_infobgcolor",
"type" => "colorpicker",
"std" => "#eb5656"),
    
array( "name" => esc_attr__( 'Disable Thumbnail Text Effect', 'dvteam'),
"desc" => esc_attr__( 'To disable thumbnail text effect (onMouseOver), check this box.', 'dvteam'),
"id" => $dvteam . "_removetextanim",
"type" => "checkbox",
"std" => ""),     
    
array( "name" => esc_attr__( 'Thumbnail Title Font Size', 'dvteam'),
"desc" => esc_attr__( 'Panel Title Font Size (px)', 'dvteam'),
"id" => $dvteam . "_imgtitlesize",
"type" => "number",
"std" => "18"),    
    
array( "name" => esc_attr__( 'Thumbnail Title Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #ffffff', 'dvteam'),
"id" => $dvteam . "_imgtitlecolor",
"type" => "colorpicker",
"std" => "#ffffff"),
    
array( "name" => esc_attr__( 'Thumbnail Title Bg Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #eb5656', 'dvteam'),
"id" => $dvteam . "_imgtitlebgcolor",
"type" => "rgbacolorpicker",
"std" => "#eb5656"),
    
array( "name" => esc_attr__( 'Thumbnail Subtitle Font Size', 'dvteam'),
"desc" => esc_attr__( 'Panel Subtitle Font Size (px)', 'dvteam'),
"id" => $dvteam . "_imgsubtitlesize",
"type" => "number",
"std" => "14"),    
    
array( "name" => esc_attr__( 'Thumbnail Subtitle Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #ffffff', 'dvteam'),
"id" => $dvteam . "_imgsubtitlecolor",
"type" => "colorpicker",
"std" => "#ffffff"),
    
array( "name" => esc_attr__( 'Thumbnail Subtitle Bg Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #313131', 'dvteam'),
"id" => $dvteam . "_imgsubtitlebgcolor",
"type" => "rgbacolorpicker",
"std" => "#313131"),
    
array( "name" => esc_attr__( 'Activate Pagination', 'dvteam'),
"desc" => esc_attr__( 'To activate pagination for dvteam and dvthumbnails shortcodes, check this box. If you add multiple shortcode to the same page, pagination may not work properly.', 'dvteam'),
"id" => $dvteam . "_pagination",
"type" => "checkbox",
"std" => ""),
    
array( "name" => esc_attr__( 'Pagination Font Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #ffffff', 'dvteam'),
"id" => $dvteam . "_paginationfont",
"type" => "colorpicker",
"std" => "#ffffff"),    
    
array( "name" => esc_attr__( 'Pagination Bg Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #212121', 'dvteam'),
"id" => $dvteam . "_paginationbg",
"type" => "colorpicker",
"std" => "#212121"),
    
array( "name" => esc_attr__( 'Pagination Mouse Over Bg Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #eb5656', 'dvteam'),
"id" => $dvteam . "_paginationbghover",
"type" => "colorpicker",
"std" => "#eb5656"),    
    
array( "type" => "close"),
    
/* ---------------------------------------------------------
Grid Filter Menu
----------------------------------------------------------- */

array( "name" => esc_attr__( 'Grid Filter Menu', 'dvteam'),
"icon" => "dashicons-archive",      
"type" => "section"),
array( "type" => "open"),   
    
array( "name" => esc_attr__( 'Outer Spacing', 'dvteam'),
"desc" => esc_attr__( 'The distance between filter menu and members (px)', 'dvteam'),
"id" => $dvteam . "_filterbottom",
"type" => "number",
"std" => "20"),
    
array( "name" => esc_attr__( 'Menu Item Horizontal Spacing', 'dvteam'),
"desc" => esc_attr__( 'Menu item right-left spacing (px)', 'dvteam'),
"id" => $dvteam . "_filterhorizontal",
"type" => "number",
"std" => "15"),
    
array( "name" => esc_attr__( 'Menu Item Vertical Spacing', 'dvteam'),
"desc" => esc_attr__( 'Menu item top-bottom spacing (px)', 'dvteam'),
"id" => $dvteam . "_filtervertical",
"type" => "number",
"std" => "5"),
    
array( "name" => esc_attr__( 'Background Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #f5f1f0', 'dvteam'),
"id" => $dvteam . "_filterbgcolor",
"type" => "colorpicker",
"std" => "#f5f1f0"),
    
array( "name" => esc_attr__( 'Mouseover color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #414141', 'dvteam'),
"id" => $dvteam . "_filtermover",
"type" => "colorpicker",
"std" => "#414141"),
    
array( "name" => esc_attr__( 'Active filter color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #eb5656', 'dvteam'),
"id" => $dvteam . "_filteractive",
"type" => "colorpicker",
"std" => "#eb5656"),
    
array( "name" => esc_attr__( 'Filter font size (px)', 'dvteam'),
"desc" => esc_attr__( 'Filter font size (px)', 'dvteam'),
"id" => $dvteam . "_filterfont",
"type" => "number",
"std" => "18"),     
    
array( "name" => esc_attr__( 'Font color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #ffffff', 'dvteam'),
"id" => $dvteam . "_filterfontcolor",
"type" => "colorpicker",
"std" => "#ffffff"),    
    
array( "type" => "close"),        
    
/* ---------------------------------------------------------
Skills
----------------------------------------------------------- */
array( "name" => esc_attr__( 'Skills', 'dvteam'),
"icon" => "dashicons-welcome-learn-more",      
"type" => "section"),
array( "type" => "open"),
    
array( "name" => esc_attr__( 'Skill Font Size', 'dvteam'),
"desc" => esc_attr__( 'Skill Font Size (px)', 'dvteam'),
"id" => $dvteam . "_skillsize",
"type" => "number",
"std" => "14"),
    
array( "name" => esc_attr__( 'Skill Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #ffffff', 'dvteam'),
"id" => $dvteam . "_skillcolor",
"type" => "colorpicker",
"std" => "#ffffff"),
    
array( "name" => esc_attr__( 'Skill Background Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #212121', 'dvteam'),
"id" => $dvteam . "_skillbg",
"type" => "colorpicker",
"std" => "#212121"),    
    
array( "name" => esc_attr__( 'Percent Font Size', 'dvteam'),
"desc" => esc_attr__( 'Percent Font Size (px)', 'dvteam'),
"id" => $dvteam . "_percentsize",
"type" => "number",
"std" => "14"),
    
array( "name" => esc_attr__( 'Percent Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #ffffff', 'dvteam'),
"id" => $dvteam . "_percentcolor",
"type" => "colorpicker",
"std" => "#ffffff"),
    
array( "name" => esc_attr__( 'Skill Bar Background Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #212121', 'dvteam'),
"id" => $dvteam . "_skillbarbg",
"type" => "colorpicker",
"std" => "#212121"),    
    
array( "name" => esc_attr__( 'Skill Bar Border Size', 'dvteam'),
"desc" => esc_attr__( 'Border Size (px)', 'dvteam'),
"id" => $dvteam . "_bordersize",
"type" => "number",
"std" => "1"),
    
array( "name" => esc_attr__( 'Skill Bar Border Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #414141', 'dvteam'),
"id" => $dvteam . "_bordercolor",
"type" => "colorpicker",
"std" => "#414141"),    
    
array( "type" => "close"),    
    
/* ---------------------------------------------------------
Icons
----------------------------------------------------------- */
array( "name" => esc_attr__( 'Icons', 'dvteam'),
"icon" => "dashicons-info",      
"type" => "section"),
array( "type" => "open"),
    
array( "name" => esc_attr__( 'Close Panel Icon (24x24 px)', 'dvteam'),
"desc" => esc_attr__( 'Close Panel Icon (24x24 px)', 'dvteam'),
"id" => $dvteam . "_closeicon",
"type" => "media",
"std" => plugin_dir_url( __FILE__ ) ."css/icons/close.png"),
    
array( "name" => esc_attr__( 'Thumbnail Info Icon (32x32 px)', 'dvteam'),
"desc" => esc_attr__( 'Thumbnail Info Icon (32x32 px)', 'dvteam'),
"id" => $dvteam . "_infoicon",
"type" => "media",
"std" => plugin_dir_url( __FILE__ ) ."css/icons/info.png"),
    
array( "name" => esc_attr__( 'Small Thumbnail Info Icon (24x24 px)', 'dvteam'),
"desc" => esc_attr__( 'Small Thumbnail Info Icon (24x24 px)', 'dvteam'),
"id" => $dvteam . "_smallinfoicon",
"type" => "media",
"std" => plugin_dir_url( __FILE__ ) ."css/icons/s-info.png"),    
    
array( "name" => esc_attr__( 'Slider Left Arrow (16x30 px)', 'dvteam'),
"desc" => esc_attr__( 'Slider Left Arrow (16x30 px)', 'dvteam'),
"id" => $dvteam . "_sliderleft",
"type" => "media",
"std" => plugin_dir_url( __FILE__ ) ."css/icons/left.png"),
    
array( "name" => esc_attr__( 'Slider Right Arrow (16x30 px)', 'dvteam'),
"desc" => esc_attr__( 'Slider Right Arrow (16x30 px)', 'dvteam'),
"id" => $dvteam . "_sliderright",
"type" => "media",
"std" => plugin_dir_url( __FILE__ ) ."css/icons/right.png"),
    
array( "name" => esc_attr__( 'Carousel Left Arrow (24x24 px)', 'dvteam'),
"desc" => esc_attr__( 'Carousel Left Arrow (24x24 px)', 'dvteam'),
"id" => $dvteam . "_carouselleft",
"type" => "media",
"std" => plugin_dir_url( __FILE__ ) ."css/icons/c-left.png"),
    
array( "name" => esc_attr__( 'Carousel Right Arrow (24x24 px)', 'dvteam'),
"desc" => esc_attr__( 'Carousel Right Arrow (24x24 px)', 'dvteam'),
"id" => $dvteam . "_carouselright",
"type" => "media",
"std" => plugin_dir_url( __FILE__ ) ."css/icons/c-right.png"),
    
array( "name" => esc_attr__( 'Carousel Arrow Background Color', 'dvteam'),
"desc" => esc_attr__( 'Default color is #f5f1f0', 'dvteam'),
"id" => $dvteam . "_cararrowbg",
"type" => "colorpicker",
"std" => "#f5f1f0"),     
    
array( "type" => "close")    
);    

/*---------------------------------------------------
Plugin Settings Panel Output
----------------------------------------------------*/

if ( ! function_exists( 'add_dvteam_page' ) ) {
function add_dvteam_page() {
add_plugins_page( esc_attr__( 'DV Team Settings', 'dvteam'), esc_attr__( 'DV Team Settings', 'dvteam'), 'manage_options', 'dvteamsettings', 'dvteam_settings_page');
}
add_action( 'admin_menu', 'add_dvteam_page' );    
}

if ( ! function_exists( 'dvteam_settings_page' ) ) {
function dvteam_settings_page() {
if ( ! did_action( 'wp_enqueue_media' ) ){
    wp_enqueue_media();
}      
global $dvteam,$dv_plugin_options;
$i=0;
$message='';
if ( 'save' == @$_REQUEST['action'] ) {

foreach ($dv_plugin_options as $value) {
update_option( @$value['id'], @$_REQUEST[ $value['id'] ] ); }

 
if ( array_key_exists('id', $value) ) { 
foreach ($dv_plugin_options as $value) {
if( isset( $_REQUEST[ @$value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
}
$message='saved';
}
else if( 'reset' == @$_REQUEST['action'] ) {
 
foreach ($dv_plugin_options as $value) {
delete_option( @$value['id'] ); }
$message='reset';
}
 
if ( $message=='saved' ) {
?>
    <div id="message" class="updated"><p><strong><?php esc_attr_e( 'Settings saved', 'dvteam'); ?></strong></p></div>
<?php
}
if ( $message=='reset' ) {
?>
    <div id="message" class="updated"><p><strong><?php esc_attr_e( 'Settings reset', 'dvteam'); ?></strong></p></div>
<?php    
}
 
?>
<div id="panelwrapper">
<div class="options_wrap">
<div id="icon-options-general"></div>
<h1><?php esc_attr_e( 'DV Team Settings', 'dvteam'); ?></h1>
<ul class="doclinks">
<li><a class="button-primary" href="<?php echo plugin_dir_url( __FILE__ ); ?>documentation/index.html" target="_blank"><?php esc_attr_e( 'Help Documentation', 'dvteam'); ?></a></li>
<li><a class="button-secondary" href="http://help.wp4life.com/category/wp-plugins/dv-team/" target="_blank"><?php esc_attr_e( 'Knowledge Base', 'dvteam'); ?></a></li>
<li><a class="button-secondary" href="http://codecanyon.net/user/egemenerd?ref=egemenerd" target="_blank"><?php esc_attr_e( 'Visit Support', 'dvteam'); ?></a></li>    
</ul>
<div>
<form method="post">
 
<?php foreach ($dv_plugin_options as $value) {
 
switch ( $value['type'] ) {
 
case "open": ?>
<?php break;
 
case "close": ?>
</div>
</div><br />

<?php break;
 
case 'text': ?>
<div class="option_input4">
<label for="<?php esc_attr_e($value['id']); ?>">
<?php esc_attr_e($value['name']); ?></label>
<input id="<?php esc_attr_e($value['id']); ?>" type="<?php esc_attr_e($value['type']); ?>" name="<?php esc_attr_e($value['id']); ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(esc_attr(get_option( $value['id']))); } else { esc_attr_e($value['std']); } ?>" />
<small><?php esc_attr_e($value['desc']); ?></small>
<div class="clearfix"></div>
</div>
<?php break;    
    
case 'select': ?>
<div class="option_input4">
<label for="<?php esc_attr_e($value['id']); ?>">
<?php esc_attr_e($value['name']); ?></label>    
<select name="<?php esc_attr_e($value['id']); ?>" id="<?php esc_attr_e($value['id']); ?>">
<?php foreach ($value['std'] as $key => $optiontext) { ?>
<option value="<?php echo esc_attr($key); ?>" <?php if (get_option( $value['id'] ) == $key) { echo esc_attr('selected="selected"'); } ?>><?php echo esc_attr($optiontext); ?></option>
<?php } ?>
</select>
<small><?php esc_attr_e($value['desc']); ?></small>
<div class="clearfix"></div>
</div>
<?php break;    
    
case 'colorpicker': ?>
<div class="option_input4">
<label for="<?php esc_attr_e($value['id']); ?>">
<?php esc_attr_e($value['name']); ?></label>
<input id="<?php esc_attr_e($value['id']); ?>" class="dvteam-color" type="<?php esc_attr_e($value['type']); ?>" name="<?php esc_attr_e($value['id']); ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(esc_attr(get_option( $value['id']))); } else { esc_attr_e($value['std']); } ?>" />
<small><?php esc_attr_e($value['desc']); ?></small>
<div class="clearfix"></div>
</div>
<?php break;
        
case 'rgbacolorpicker': ?>
<div class="option_input4">
<label for="<?php echo esc_attr($value['id']); ?>">
<?php echo esc_attr($value['name']); ?></label>
<input id="<?php echo esc_attr($value['id']); ?>" class="dvteam-wp-color-picker" type="<?php echo esc_attr($value['type']); ?>" name="<?php echo esc_attr($value['id']); ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(esc_attr(get_option( $value['id']))); } else { echo esc_attr($value['std']); } ?>" />
<small><?php echo esc_attr($value['desc']); ?></small>
<div class="clearfix"></div>
</div>
<?php break;        
    
case 'number': ?>
<div class="option_input4">
<label for="<?php esc_attr_e($value['id']); ?>">
<?php esc_attr_e($value['name']); ?></label>
<input id="<?php esc_attr_e($value['id']); ?>" type="<?php esc_attr_e($value['type']); ?>" onkeypress="return validate(event)" name="<?php esc_attr_e($value['id']); ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(esc_attr(get_option( $value['id']))); } else { esc_attr_e($value['std']); } ?>" />
<small><?php esc_attr_e($value['desc']); ?></small>   
<div class="clearfix"></div>
</div>
<?php break;    
 
case 'textarea': ?>
<div class="option_input4">
<label for="<?php esc_attr_e($value['id']); ?>"><?php esc_attr_e($value['name']); ?></label>
<textarea name="<?php esc_attr_e($value['id']); ?>" rows="" cols=""><?php if ( get_option( $value['id'] ) != "") { echo stripslashes(esc_attr(get_option( $value['id']))); } else { esc_attr_e($value['std']); } ?></textarea>
<small><?php esc_attr_e($value['desc']); ?></small>
<div class="clearfix"></div>
</div>
<?php break;
    
case 'info': ?>
<div class="option_input4">
<div class="option_info_box3"><h4><?php esc_attr_e($value['name']); ?></h4></div>
<div class="clearfix"></div>
</div>
<?php break;    
 
case 'editor': ?>
<div class="option_input4">
<?php wp_editor( stripslashes(get_option( $value['id'])), $value['id']); ?> 
<div class="clearfix"></div>
<div style="font-size:10px; color:#F00;"><?php esc_attr_e($value['desc']); ?></div>
<div class="clearfix"></div>
</div>
<?php break;   
    
case 'media': ?>
<div class="option_input4">
<label for="<?php esc_attr_e($value['id']); ?>"><?php esc_attr_e($value['name']); ?></label>
<input id="<?php esc_attr_e($value['id']); ?>_image" type="text" name="<?php esc_attr_e($value['id']); ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(esc_attr(get_option( $value['id']))); } else { esc_attr_e($value['std']); } ?>" /> 
<input id="<?php esc_attr_e($value['id']); ?>_image_button" class="button uploadbutton" type="button" value="<?php esc_attr_e( 'Upload', 'dvteam') ?>" />
<script type="text/javascript">
jQuery(document).ready(function($){ 
    var custom_uploader; 
    $('#<?php echo esc_js($value['id']); ?>_image_button').click(function(e) { 
        e.preventDefault();
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#<?php echo esc_js($value['id']); ?>_image').val(attachment.url);
        });
        custom_uploader.open(); 
    }); 
});    
</script>    
<div class="clearfix"></div>
</div>
<?php break;    
 
case "checkbox": ?>
<div class="option_input4">
<label for="<?php esc_attr_e($value['id']); ?>"><?php esc_attr_e($value['name']); ?></label>
<?php if(get_option($value['id'])){ $checked = 'checked="checked"'; }else{ $checked = "";} ?>
<input id="<?php esc_attr_e($value['id']); ?>" type="checkbox" name="<?php esc_attr_e($value['id']); ?>" value="true" <?php echo esc_attr($checked); ?> />
<small><?php esc_attr_e($value['desc']); ?></small>
<div class="clearfix"></div>
</div>
<?php break;
 
case "section":
$i++; ?>
<div class="input_section4">
<div class="input_title4">
 
<h3><i class="dvteam-i-icon <?php echo esc_attr($value['icon']); ?>"></i>&nbsp;<?php echo esc_attr($value['name']); ?></h3>
<span class="submit"><input name="save<?php echo esc_attr($i); ?>" type="submit" value="<?php esc_attr_e( 'Save Changes', 'dvteam') ?>" class="button-primary" /></span>
<div class="clearfix"></div>
</div>
<div class="all_options4">
<?php break;
 
}
}?>
<input type="hidden" name="action" value="save" class="button-primary" />
</form>
<form method="post">
<p class="submit">
<input name="reset" type="submit" value="<?php esc_attr_e( 'Reset', 'dvteam') ?>" class="button-secondary" />
<input type="hidden" name="action" value="reset" />
</p>
</form>
</div>
<div>
<p>This plugin was made by <a href="http://codecanyon.net/user/egemenerd?ref=egemenerd" target="_blank" >Egemenerd</a>.</p>
</div>
</div>
</div>
<?php
}
}
?>