<?php

// Register custom post type

function register_dvourteam_posttype() {
    $labels = array(
        'name'              => esc_attr__( 'DV Team', 'dvteam' ),
        'singular_name'     => esc_attr__( 'Member', 'dvteam' ),
        'add_new'           => esc_attr__( 'Add new member', 'dvteam' ),
        'add_new_item'      => esc_attr__( 'Add new member', 'dvteam' ),
        'edit_item'         => esc_attr__( 'Edit member', 'dvteam' ),
        'new_item'          => esc_attr__( 'New member', 'dvteam' ),
        'view_item'         => esc_attr__( 'View member', 'dvteam' ),
        'search_items'      => esc_attr__( 'Search members', 'dvteam' ),
        'not_found'         => esc_attr__( 'No member found', 'dvteam' ),
        'not_found_in_trash'=> esc_attr__( 'No member found in trash', 'dvteam' ),
        'parent_item_colon' => esc_attr__( 'Parent members:', 'dvteam' ),
        'menu_name'         => esc_attr__( 'DV Team', 'dvteam' )
    );
 
    $supports = array('title','thumbnail','editor','post-formats');
 
    $post_type_args = array(
        'labels'            => $labels,
        'singular_label'    => esc_attr__('Member', 'dvteam'),
        'public'            => true,
        'exclude_from_search' => true,
        'show_ui'           => true,
        'show_in_nav_menus' => false,
        'publicly_queryable'=> true,
        'query_var'         => true,
        'capability_type'   => 'post',
        'has_archive'       => false,
        'hierarchical'      => false,
        'rewrite'           => array( 'slug' => 'dvteam', 'with_front' => false ),
        'supports'          => $supports,
        'menu_position'     => 99,
        'menu_icon'         => 'dashicons-groups'
    );
    register_post_type('dvourteam',$post_type_args);
}
add_action('init', 'register_dvourteam_posttype');

// Custom post formats

add_action('load-post.php','add_dvteam_postformats');
add_action('load-post-new.php','add_dvteam_postformats');
function add_dvteam_postformats( $post ){

    $screen = get_current_screen();
    $post_type = $screen->post_type;

    if( $post_type == 'dvourteam' ) {
        add_theme_support('post-formats',array( 'image', 'gallery', 'video', 'link', 'quote' ));
        remove_filter('mce_external_plugins', 'dvteam_add_plugin');
        remove_filter('mce_buttons_3', 'dvteam_register_button');
    }

}

// Register taxonomy

function dvourteam_taxonomy() {
    register_taxonomy(
        'dvteamtaxonomy',
        'dvourteam',
        array(
            'labels' => array(
                'name' => esc_attr__( 'Categories', 'dvteam' ),
                'add_new_item' => esc_attr__( 'Add new category', 'dvteam' ),
                'new_item_name' => esc_attr__( 'New category', 'dvteam' )
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'hierarchical' => true
        )
    );
}
add_action( 'init', 'dvourteam_taxonomy', 0 );

/*---------------------------------------------------
Add shortcode and thumbnail column to the member list
----------------------------------------------------*/
add_filter('manage_edit-dvourteam_columns', 'dvourteam_id', 5);
add_action('manage_posts_custom_column', 'dvourteam_custom_id', 5, 2);

function dvourteam_id($defaults){
    $defaults['dvourteam_id'] = esc_attr__('Member Shortcode', 'dvteam');
    $defaults['dvourteam_thumb'] = '';
    return $defaults;
}
function dvourteam_custom_id($column_name, $post_id){
    global $post;
    if($column_name === 'dvourteam_id'){
        echo '[dvmember id="' . esc_attr($post_id) . '" gridstyle="full" offset="20" itemwidth="250" side="right"]';
    }
    if($column_name === 'dvourteam_thumb'){
        if ( has_post_thumbnail() ) {
            $thumb_id = get_post_thumbnail_id();
            $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail', true);
            $thumb_url = $thumb_url_array[0];
            echo '<img src="' . esc_url($thumb_url) . '" class="dvthumb" alt="" />';
        }
    }
}

/*---------------------------------------------------
Add id column to the taxonomy list
----------------------------------------------------*/
add_action( "manage_edit-dvteamtaxonomy_columns", 'dvteamtaxonomy_add_col' );
add_filter( "manage_dvteamtaxonomy_custom_column", 'dvteamtaxonomy_show_id', 10, 3 );

function dvteamtaxonomy_add_col( $columns )
{
    unset($columns['description']);
    return $columns + array ( 'dvteamtaxonomyid' => __('ID', 'dvteam') );
}
function dvteamtaxonomy_show_id( $ver, $name, $id )
{
    $term = get_term( $id, 'dvteamtaxonomy' );
    $term_id = $term->term_id;
    $taxonomy = $term->name;
    return 'dvteamtaxonomyid' === $name ? $term_id : $ver;
}

/*---------------------------------------------------
Add category filter to the member list
----------------------------------------------------*/

function dvourteam_filter_list() {
    $screen = get_current_screen();
    global $wp_query;
    if ( $screen->post_type == 'dvourteam' ) {
        wp_dropdown_categories( array(
            'show_option_all' => esc_attr__( 'Show all categories', 'dvteam' ),
            'taxonomy' => 'dvteamtaxonomy',
            'name' => 'dvteamtaxonomy',
            'orderby' => 'name',
            'selected' => ( isset( $wp_query->query['dvteamtaxonomy'] ) ? $wp_query->query['dvteamtaxonomy'] : '' ),
            'hierarchical' => false,
            'depth' => 3,
            'show_count' => false,
            'hide_empty' => true,
        ) );
    }
}
add_action( 'restrict_manage_posts', 'dvourteam_filter_list' );

function dvourteam_filtering( $query ) {
    $qv = &$query->query_vars;
    if ( isset( $qv['dvteamtaxonomy'] ) && is_numeric( $qv['dvteamtaxonomy'] ) ) {
        $term = get_term_by( 'id', $qv['dvteamtaxonomy'], 'dvteamtaxonomy' );
        if( !is_object($term) ) { 
        return;
        }
        else {
        $qv['dvteamtaxonomy'] = $term->slug;
        }
    }
}
add_filter( 'parse_query','dvourteam_filtering' );

// Subtitle

function dv_teamexcerpt( $meta_boxes ) {
    $prefix = 'dv'; // Prefix for all fields
    $meta_boxes['dv_teamexc'] = array(
        'id' => 'dv_teamexc',
        'title' => esc_attr__( 'Subtitle', 'dvteam'),
        'object_types' => array('dvourteam'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => false, // Show field names on the left
        'fields' => array(
            array(
                'id' => $prefix . 'excerpt',
                'type' => 'text'
            ),
        ),
    );

    return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', 'dv_teamexcerpt' );

// Post Type: Image

function dv_teamimage( $meta_boxes ) {
    $prefix = 'dv'; // Prefix for all fields
    $meta_boxes['dv_teamfimg'] = array(
        'id' => 'dv_teamfimg',
        'title' => esc_attr__( 'Post Format: Image', 'dvteam'),
        'object_types' => array('dvourteam'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => esc_attr__( 'Image: ', 'dvteam'),
                'desc' => esc_attr__( 'Upload an image or enter an URL', 'dvteam'),
                'id' => $prefix . 'ptfimage',
                'type' => 'file'
            ),
        ),
    );

    return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', 'dv_teamimage' );

// Post Type: Gallery

function dv_teamimages( $meta_boxes ) {
    $prefix = 'dv'; // Prefix for all fields
    $meta_boxes['dv_teamimg'] = array(
        'id' => 'dv_teamimg',
        'title' => esc_attr__( 'Post Format: Gallery', 'dvteam'),
        'object_types' => array('dvourteam'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'id' => $prefix . 'ptteamimages',
                'name' => esc_attr__( 'Select Images:', 'dvteam'),
                'desc' => esc_attr__( 'You can make a multiselection with CTRL + click.', 'dvteam'),
                'type' => 'file_list',
                'preview_size' => array( 100, 100 )
            ),
        ),
    );

    return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', 'dv_teamimages' );

// Post Type: Video

function dv_teamvideo( $meta_boxes ) {
    $prefix = 'dv'; // Prefix for all fields
    $meta_boxes['dv_teamvid'] = array(
        'id' => 'dv_teamvid',
        'title' => esc_attr__( 'Post Format: Video', 'dvteam'),
        'object_types' => array('dvourteam'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => esc_attr__( 'Video code: ', 'dvteam'),
                'id' => $prefix . 'ptvideo',
                'type' => 'textarea_code'
            ),
        ),
    );

    return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', 'dv_teamvideo' );

// Post Type: Link

function dv_teamlink( $meta_boxes ) {
    $prefix = 'dv'; // Prefix for all fields
    $meta_boxes['dv_teamurl'] = array(
        'id' => 'dv_teamurl',
        'title' => esc_attr__( 'Post Format: Link', 'dvteam'),
        'object_types' => array('dvourteam'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => esc_attr__( 'URL: ', 'dvteam'),
                'desc' => esc_attr__( 'Correct link format; http://www.themeforest.net', 'dvteam'),
                'id' => $prefix . 'ptlink',
                'type' => 'text'
            ),
        ),
    );

    return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', 'dv_teamlink' );

// Post Type: Quote

function dv_teamquote( $meta_boxes ) {
    $prefix = 'dv'; // Prefix for all fields
    $meta_boxes['dv_teamqid'] = array(
        'id' => 'dv_teamqid',
        'title' => esc_attr__( 'Post Format: Quote', 'dvteam'),
        'object_types' => array('dvourteam'), // post type
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => esc_attr__( 'Quote: ', 'dvteam'),
                'id' => $prefix . 'ptquote',
                'type' => 'textarea_small'
            ),
        ),
    );

    return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', 'dv_teamquote' );

// Social icons

function dv_teamsocial( $meta_boxes ) {
    $prefix = 'dv'; // Prefix for all fields
    $meta_boxes['dv_teamicons'] = array(
		'id' => 'dv_teamicons',
		'title' => esc_attr__( 'Social Icons (Optional)', 'dvteam'),
		'object_types' => array( 'dvourteam' ),
        'context' => 'normal',
        'priority' => 'default',
		'fields' => array(
            array(
                'name' => esc_attr__( 'Activate Social Icons', 'dvteam'),
                'desc' => esc_attr__( 'To activate social icons, check this box.', 'dvteam'),
                'id' => $prefix . 'activateicons',
                'type' => 'checkbox'
            ),
            array(
                'id' => $prefix . 'repeatdvicons',
                'type' => 'group',
                'options' => array(
                    'group_title'   => esc_attr__( 'Icon {#}', 'dvteam' ),
                    'add_button' => esc_attr__( 'Add Another Icon', 'dvteam' ),
                    'remove_button' => esc_attr__( 'Remove Icon', 'dvteam' ),
                    'sortable' => false, // beta
				),
				// Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
				'fields' => array(
					array(
                    'name' => esc_attr__( 'Select Icon:', 'dvteam'),
                    'id' => $prefix . 'iconimg',
                    'desc' => esc_attr__( 'Select an icon or upload your own icon', 'dvteam'),
                    'type' => 'select',
                    'options' => array(
                        'facebook' => esc_attr__( 'Facebook', 'dvteam' ),
                        'twitter' => esc_attr__( 'Twitter', 'dvteam' ),
                        'google' => esc_attr__( 'Google', 'dvteam' ),
                        'linkedin' => esc_attr__( 'Linkedin', 'dvteam' ),
                        'instagram' => esc_attr__( 'Instagram', 'dvteam' ),
                        'skype' => esc_attr__( 'Skype', 'dvteam' ),
                        'vimeo' => esc_attr__( 'Vimeo', 'dvteam' ),
                        'youtube' => esc_attr__( 'You Tube', 'dvteam' ),
                    ),
                ),
                array(
                    'name' => esc_attr__( 'Custom Icon:', 'dvteam'),
                    'desc' => esc_attr__( 'Upload your own icon (24x24 px)', 'dvteam'),
                    'id' => $prefix . 'customimg',
                    'type' => 'file',
                    'allow' => array( 'attachment' )
                ),
                array(
                    'name' => 'Custom Icon Background color:',
                    'id'   => $prefix . 'customimgcolor',
                    'type' => 'colorpicker',
                    'default'  => '#ffffff',
                ),    
                array(
                    'name' => esc_attr__( 'Link:', 'dvteam'),
                    'desc' => esc_attr__( 'Correct link format; http://www.themeforest.net', 'dvteam'),
                    'id' => $prefix . 'iconurl',
                    'type' => 'text'
                ),
                ),
			),
		),
	);

    return $meta_boxes;
}
add_filter( 'cmb2_meta_boxes', 'dv_teamsocial' );
?>