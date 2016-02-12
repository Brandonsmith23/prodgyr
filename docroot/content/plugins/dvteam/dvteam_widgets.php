<?php
/* ----------------------------------------------------------
Carousel widget
------------------------------------------------------------- */

add_action( 'widgets_init', 'dv_dvteamcarousel_widget' );

function dv_dvteamcarousel_widget() {
	register_widget( 'dv_dvteamcarouselwidget' );
}

class dv_dvteamcarouselwidget extends WP_Widget {

    function __construct() {
		parent::__construct(
			'dv-dvteamcarousel-widget', // Base ID
			esc_attr__('DV Team Carousel', 'dvteam'), // Name
			array( 'description' => esc_attr__('DV Team Carousel Widget.', 'dvteam'), ) // Args
		);
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
        $max = $instance['max'];
        $columns = $instance['columns'];
        $gridstyle = $instance['gridstyle'];
        $categoryid = $instance['categoryid'];
        $autoplay = $instance['autoplay'];
        $duration = $instance['duration'];
        $spacing = $instance['spacing'];
        $side = $instance['side'];

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}  
        echo do_shortcode("[dvteamcarousel max='" . esc_attr($max) . "' columns='" . esc_attr($columns) . "' gridstyle='" . esc_attr($gridstyle) . "' categoryid='" . esc_attr($categoryid) . "' autoplay='" . esc_attr($autoplay) . "' duration='" . esc_attr($duration) . "' spacing='" . esc_attr($spacing) . "' side='" . esc_attr($side) . "']");
		
		echo $args['after_widget'];
	}
	 
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['max'] = $new_instance['max'];
        $instance['columns'] = $new_instance['columns'];
        $instance['gridstyle'] = $new_instance['gridstyle'];
        $instance['categoryid'] = $new_instance['categoryid'];
        $instance['autoplay'] = $new_instance['autoplay'];
        $instance['duration'] = $new_instance['duration'];
        $instance['spacing'] = $new_instance['spacing'];
        $instance['side'] = $new_instance['side'];

		return $instance;
	}
	
	public function form( $instance ) {
		$defaults = array( 'title' => '', 'max' => '99', 'duration' => '4', 'spacing' => '0');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_attr_e('Title:', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'max' )); ?>"><?php esc_attr_e('Maximum number of members:', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'max' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'max' )); ?>" value="<?php if (isset($instance['max'])) { echo esc_attr($instance['max']); } ?>" onkeypress="return validate(event)" type="number" style="width:100%;" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'columns' )); ?>"><?php esc_attr_e('Columns:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'columns' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'columns' )); ?>">
        <option value="1" <?php if (isset($instance['columns'])) { if ($instance['columns'] == 1) { echo esc_attr('selected="selected"'); }} ?>>1</option>
        <option value="2" <?php if (isset($instance['columns'])) { if ($instance['columns'] == 2) { echo esc_attr('selected="selected"'); }} ?>>2</option>
        <option value="3" <?php if (isset($instance['columns'])) { if ($instance['columns'] == 3) { echo esc_attr('selected="selected"'); }} ?>>3</option>
        <option value="4" <?php if (isset($instance['columns'])) { if ($instance['columns'] == 4) { echo esc_attr('selected="selected"'); }} ?>>4</option>
    </select>
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'gridstyle' )); ?>"><?php esc_attr_e('Grid Style:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'gridstyle' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'gridstyle' )); ?>">
        <option value="square" <?php if (isset($instance['gridstyle'])) { if ($instance['gridstyle'] == 'square') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Square', 'dvteam'); ?></option>
        <option value="rectangle" <?php if (isset($instance['gridstyle'])) { if ($instance['gridstyle'] == 'rectangle') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Rectangle', 'dvteam'); ?></option>
        <option value="full" <?php if (isset($instance['gridstyle'])) { if ($instance['gridstyle'] == 'full') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Full', 'dvteam'); ?></option>
    </select>
</p>
<?php $carouselterms = get_terms( 'dvteamtaxonomy'); ?>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'categoryid' )); ?>"><?php esc_attr_e('Select Category:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'categoryid' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'categoryid' )); ?>">
        <option value=""><?php esc_attr_e('All Categories', 'dvteam'); ?></option>        
        <?php if ($carouselterms && !is_wp_error($carouselterms)) { ?>
        <?php foreach( $carouselterms as $term ) { ?>
        <?php $termname = $term->name; ?>
        <?php $termid = $term->term_id; ?>
        <option value="<?php echo esc_attr($termid) ?>" <?php if (isset($instance['categoryid'])) { if ($instance['categoryid'] == $termid) { echo esc_attr('selected="selected"'); }} ?>><?php echo esc_attr($termname) ?></option>
        <?php } ?>
        <?php } ?>
    </select>
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'autoplay' )); ?>"><?php esc_attr_e('Autoplay:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'autoplay' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'autoplay' )); ?>">
        <option value="false" <?php if (isset($instance['autoplay'])) { if ($instance['autoplay'] == 'false') { echo esc_attr('selected="selected"'); }} ?>>False</option>
        <option value="true" <?php if (isset($instance['autoplay'])) { if ($instance['autoplay'] == 'true') { echo esc_attr('selected="selected"'); }} ?>>True</option>
    </select>
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'duration' )); ?>"><?php esc_attr_e('Autoplay duration (second):', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'duration' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'duration' )); ?>" value="<?php if (isset($instance['duration'])) { echo esc_attr($instance['duration']); } ?>" onkeypress="return validate(event)" type="number" style="width:100%;" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'spacing' )); ?>"><?php esc_attr_e('Spacing (px):', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'spacing' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'spacing' )); ?>" value="<?php if (isset($instance['spacing'])) { echo esc_attr($instance['spacing']); } ?>" onkeypress="return validate(event)" type="number" style="width:100%;" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'side' )); ?>"><?php esc_attr_e('Side:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'side' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'autoplay' )); ?>">
        <option value="right" <?php if (isset($instance['side'])) { if ($instance['side'] == 'right') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Right', 'dvteam'); ?></option>
        <option value="left" <?php if (isset($instance['side'])) { if ($instance['side'] == 'left') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Left', 'dvteam'); ?></option>
        <option value="center" <?php if (isset($instance['side'])) { if ($instance['side'] == 'center') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Center', 'dvteam'); ?></option>
    </select>
</p>
<?php }} ?>
<?php
/* ----------------------------------------------------------
Team widget
------------------------------------------------------------- */

add_action( 'widgets_init', 'dv_dvteam_widget' );

function dv_dvteam_widget() {
	register_widget( 'dv_dvteamwidget' );
}

class dv_dvteamwidget extends WP_Widget {

    function __construct() {
		parent::__construct(
			'dv-dvteam-widget', // Base ID
			esc_attr__('DV Team', 'dvteam'), // Name
			array( 'description' => esc_attr__('DV Team Widget.', 'dvteam'), ) // Args
		);
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
        $max = $instance['max'];
        $categoryid = $instance['categoryid'];
        $gridstyle = $instance['gridstyle'];
        $offset = $instance['offset'];
        $itemwidth = $instance['itemwidth'];
        $side = $instance['side'];

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}  
        echo do_shortcode("[dvteam max='" . esc_attr($max) . "' categoryid='" . esc_attr($categoryid) . "' gridstyle='" . esc_attr($gridstyle) . "' offset='" . esc_attr($offset) . "' itemwidth='" . esc_attr($itemwidth) . "' side='" . esc_attr($side) . "']");
		
		echo $args['after_widget'];
	}
	 
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['max'] = $new_instance['max'];
        $instance['categoryid'] = $new_instance['categoryid'];
        $instance['gridstyle'] = $new_instance['gridstyle'];
        $instance['offset'] = $new_instance['offset'];
        $instance['itemwidth'] = $new_instance['itemwidth'];
        $instance['side'] = $new_instance['side'];

		return $instance;
	}
	
	public function form( $instance ) {
		$defaults = array( 'title' => '', 'max' => '5', 'gridstyle' => 'full', 'offset' => '0', 'itemwidth' => '250', 'side' => 'right');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_attr_e('Title:', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'max' )); ?>"><?php esc_attr_e('Maximum number of team member:', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'max' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'max' )); ?>" value="<?php if (isset($instance['max'])) { echo esc_attr($instance['max']); } ?>" onkeypress="return validate(event)" type="number" style="width:100%;" />
</p>
<?php $gridterms = get_terms( 'dvteamtaxonomy'); ?>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'categoryid' )); ?>"><?php esc_attr_e('Select Category:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'categoryid' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'categoryid' )); ?>">
        <option value=""><?php esc_attr_e('All Categories', 'dvteam'); ?></option>        
        <?php if ($gridterms && !is_wp_error($gridterms)) { ?>
        <?php foreach( $gridterms as $term ) { ?>
        <?php $termname = $term->name; ?>
        <?php $termid = $term->term_id; ?>
        <option value="<?php echo esc_attr($termid) ?>" <?php if (isset($instance['categoryid'])) { if ($instance['categoryid'] == $termid) { echo esc_attr('selected="selected"'); }} ?>><?php echo esc_attr($termname) ?></option>
        <?php } ?>
        <?php } ?>
    </select>
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'gridstyle' )); ?>"><?php esc_attr_e('Grid Style:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'gridstyle' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'gridstyle' )); ?>">
        <option value="full" <?php if (isset($instance['gridstyle'])) { if ($instance['gridstyle'] == 'full') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Full', 'dvteam'); ?></option>
        <option value="square" <?php if (isset($instance['gridstyle'])) { if ($instance['gridstyle'] == 'square') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Square', 'dvteam'); ?></option>
        <option value="rectangle" <?php if (isset($instance['gridstyle'])) { if ($instance['gridstyle'] == 'rectangle') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Rectangle', 'dvteam'); ?></option>
    </select>
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'offset' )); ?>"><?php esc_attr_e('Offset (px):', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'offset' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'offset' )); ?>" value="<?php if (isset($instance['offset'])) { echo esc_attr($instance['offset']); } ?>" onkeypress="return validate(event)" type="number" style="width:100%;" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'itemwidth' )); ?>"><?php esc_attr_e('Min. Item Width (px):', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'itemwidth' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'itemwidth' )); ?>" value="<?php if (isset($instance['itemwidth'])) { echo esc_attr($instance['itemwidth']); } ?>" onkeypress="return validate(event)" type="number" style="width:100%;" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'side' )); ?>"><?php esc_attr_e('Panel Side:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'side' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'side' )); ?>">
        <option value="right" <?php if (isset($instance['side'])) { if ($instance['side'] == 'right') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Right', 'dvteam'); ?></option>
        <option value="left" <?php if (isset($instance['side'])) { if ($instance['side'] == 'left') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Left', 'dvteam'); ?></option>
        <option value="center" <?php if (isset($instance['side'])) { if ($instance['side'] == 'center') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Center', 'dvteam'); ?></option>
    </select>
</p>
<?php }} ?>
<?php
/* ----------------------------------------------------------
Member widget
------------------------------------------------------------- */

add_action( 'widgets_init', 'dv_dvmember_widget' );

function dv_dvmember_widget() {
	register_widget( 'dv_dvmemberwidget' );
}

class dv_dvmemberwidget extends WP_Widget {

    function __construct() {
		parent::__construct(
			'dv-dvmember-widget', // Base ID
			esc_attr__('DV Member', 'dvteam'), // Name
			array( 'description' => esc_attr__('DV Member Widget.', 'dvteam'), ) // Args
		);
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
        $id = $instance['id'];
        $gridstyle = $instance['gridstyle'];
        $offset = $instance['offset'];
        $itemwidth = $instance['itemwidth'];
        $side = $instance['side'];

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}  
        echo do_shortcode("[dvmember id='" . esc_attr($id) . "' gridstyle='" . esc_attr($gridstyle) . "' offset='" . esc_attr($offset) . "' itemwidth='" . esc_attr($itemwidth) . "' side='" . esc_attr($side) . "']");
		
		echo $args['after_widget'];
	}
	 
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['id'] = $new_instance['id'];
        $instance['gridstyle'] = $new_instance['gridstyle'];
        $instance['offset'] = $new_instance['offset'];
        $instance['itemwidth'] = $new_instance['itemwidth'];
        $instance['side'] = $new_instance['side'];

		return $instance;
	}
	
	public function form( $instance ) {
		$defaults = array( 'title' => '', 'offset' => '0', 'itemwidth' => '250', 'side' => 'right');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_attr_e('Title:', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;" />
</p>
<?php $memberargs = array('post_type' => 'dvourteam','posts_per_page' => 999); ?>
<?php $member_query = new WP_Query( $memberargs ); ?>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'id' )); ?>"><?php esc_attr_e('Select Member:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'id' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'id' )); ?>">       
        <?php while($member_query->have_posts()) : $member_query->the_post(); ?>
        <?php $id = get_the_ID(); ?>
        <option value="<?php the_ID(); ?>" <?php if (isset($instance['id'])) { if ($instance['id'] == $id) { echo 'selected="selected"'; }} ?>><?php echo the_title(); ?></option>    
        <?php endwhile; ?>
        <?php wp_reset_query(); ?>        
    </select>
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'gridstyle' )); ?>"><?php esc_attr_e('Grid Style:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'gridstyle' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'gridstyle' )); ?>">
        <option value="full" <?php if (isset($instance['gridstyle'])) { if ($instance['gridstyle'] == 'full') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Full', 'dvteam'); ?></option>
        <option value="square" <?php if (isset($instance['gridstyle'])) { if ($instance['gridstyle'] == 'square') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Square', 'dvteam'); ?></option>
        <option value="rectangle" <?php if (isset($instance['gridstyle'])) { if ($instance['gridstyle'] == 'rectangle') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Rectangle', 'dvteam'); ?></option>
    </select>
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'offset' )); ?>"><?php esc_attr_e('Offset (px):', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'offset' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'offset' )); ?>" value="<?php if (isset($instance['offset'])) { echo esc_attr($instance['offset']); } ?>" onkeypress="return validate(event)" type="number" style="width:100%;" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'itemwidth' )); ?>"><?php esc_attr_e('Min. Item Width (px):', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'itemwidth' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'itemwidth' )); ?>" value="<?php if (isset($instance['itemwidth'])) { echo esc_attr($instance['itemwidth']); } ?>" onkeypress="return validate(event)" type="number" style="width:100%;" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'side' )); ?>"><?php esc_attr_e('Panel Side:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'side' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'side' )); ?>">
        <option value="right" <?php if (isset($instance['side'])) { if ($instance['side'] == 'right') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Right', 'dvteam'); ?></option>
        <option value="left" <?php if (isset($instance['side'])) { if ($instance['side'] == 'left') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Left', 'dvteam'); ?></option>
        <option value="center" <?php if (isset($instance['side'])) { if ($instance['side'] == 'center') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Center', 'dvteam'); ?></option>
    </select>
</p>
<?php }} ?>
<?php
/* ----------------------------------------------------------
Thumbnails widget
------------------------------------------------------------- */

add_action( 'widgets_init', 'dv_dvteamthumb_widget' );

function dv_dvteamthumb_widget() {
	register_widget( 'dv_dvteamthumbwidget' );
}

class dv_dvteamthumbwidget extends WP_Widget {

    function __construct() {
		parent::__construct(
			'dv-dvteamthumb-widget', // Base ID
			esc_attr__('DV Team Thumbnails', 'dvteam'), // Name
			array( 'description' => esc_attr__('DV Team Thumbnails Widget.', 'dvteam'), ) // Args
		);
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
        $max = $instance['max'];
        $categoryid = $instance['categoryid'];
        $side = $instance['side'];

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}  
        echo do_shortcode("[dvthumbnails max='" . esc_attr($max) . "' categoryid='" . esc_attr($categoryid) . "' side='" . esc_attr($side) . "']");
		
		echo $args['after_widget'];
	}
	 
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['max'] = $new_instance['max'];
        $instance['categoryid'] = $new_instance['categoryid'];
        $instance['side'] = $new_instance['side'];

		return $instance;
	}
	
	public function form( $instance ) {
		$defaults = array( 'title' => '', 'max' => '5', 'width' => '120', 'side' => 'right');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_attr_e('Title:', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'max' )); ?>"><?php esc_attr_e('Maximum number of team member:', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'max' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'max' )); ?>" value="<?php if (isset($instance['max'])) { echo esc_attr($instance['max']); } ?>" onkeypress="return validate(event)" type="number" style="width:100%;" />
</p>
<?php $gridterms = get_terms( 'dvteamtaxonomy'); ?>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'categoryid' )); ?>"><?php esc_attr_e('Select Category:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'categoryid' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'categoryid' )); ?>">
        <option value=""><?php esc_attr_e('All Categories', 'dvteam'); ?></option>        
        <?php if ($gridterms && !is_wp_error($gridterms)) { ?>
        <?php foreach( $gridterms as $term ) { ?>
        <?php $termname = $term->name; ?>
        <?php $termid = $term->term_id; ?>
        <option value="<?php echo esc_attr($termid) ?>" <?php if (isset($instance['categoryid'])) { if ($instance['categoryid'] == $termid) { echo esc_attr('selected="selected"'); }} ?>><?php echo esc_attr($termname) ?></option>
        <?php } ?>
        <?php } ?>
    </select>
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'side' )); ?>"><?php esc_attr_e('Panel Side:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'side' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'side' )); ?>">
        <option value="right" <?php if (isset($instance['side'])) { if ($instance['side'] == 'right') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Right', 'dvteam'); ?></option>
        <option value="left" <?php if (isset($instance['side'])) { if ($instance['side'] == 'left') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Left', 'dvteam'); ?></option>
        <option value="center" <?php if (isset($instance['side'])) { if ($instance['side'] == 'center') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Center', 'dvteam'); ?></option>
    </select>
</p>
<?php }} ?>
<?php
/* ----------------------------------------------------------
Filterable grid widget
------------------------------------------------------------- */

add_action( 'widgets_init', 'dv_dvteamfilter_widget' );

function dv_dvteamfilter_widget() {
	register_widget( 'dv_dvteamfilterwidget' );
}

class dv_dvteamfilterwidget extends WP_Widget {

    function __construct() {
		parent::__construct(
			'dv-dvteamfilter-widget', // Base ID
			esc_attr__('DV Team (Filterable)', 'dvteam'), // Name
			array( 'description' => esc_attr__('Filterable DV Team Widget.', 'dvteam'), ) // Args
		);
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
        $max = $instance['max'];
        $gridstyle = $instance['gridstyle'];
        $offset = $instance['offset'];
        $itemwidth = $instance['itemwidth'];
        $side = $instance['side'];
        $exclude = $instance['exclude'];

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}  
        echo do_shortcode("[dvteamfilter max='" . esc_attr($max) . "' gridstyle='" . esc_attr($gridstyle) . "' offset='" . esc_attr($offset) . "' itemwidth='" . esc_attr($itemwidth) . "' side='" . esc_attr($side) . "' exclude='" . esc_attr($exclude) . "']");
		
		echo $args['after_widget'];
	}
	 
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['max'] = $new_instance['max'];
        $instance['gridstyle'] = $new_instance['gridstyle'];
        $instance['offset'] = $new_instance['offset'];
        $instance['itemwidth'] = $new_instance['itemwidth'];
        $instance['side'] = $new_instance['side'];
        $instance['exclude'] = $new_instance['exclude'];

		return $instance;
	}
	
	public function form( $instance ) {
		$defaults = array( 'title' => '', 'max' => '5', 'gridstyle' => 'full', 'offset' => '0', 'itemwidth' => '250', 'side' => 'right');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_attr_e('Title:', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'max' )); ?>"><?php esc_attr_e('Maximum number of team member:', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'max' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'max' )); ?>" value="<?php if (isset($instance['max'])) { echo esc_attr($instance['max']); } ?>" onkeypress="return validate(event)" type="number" style="width:100%;" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'gridstyle' )); ?>"><?php esc_attr_e('Grid Style:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'gridstyle' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'gridstyle' )); ?>">
        <option value="full" <?php if (isset($instance['gridstyle'])) { if ($instance['gridstyle'] == 'full') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Full', 'dvteam'); ?></option>
        <option value="square" <?php if (isset($instance['gridstyle'])) { if ($instance['gridstyle'] == 'square') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Square', 'dvteam'); ?></option>
        <option value="rectangle" <?php if (isset($instance['gridstyle'])) { if ($instance['gridstyle'] == 'rectangle') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Rectangle', 'dvteam'); ?></option>
    </select>
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'offset' )); ?>"><?php esc_attr_e('Offset (px):', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'offset' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'offset' )); ?>" value="<?php if (isset($instance['offset'])) { echo esc_attr($instance['offset']); } ?>" onkeypress="return validate(event)" type="number" style="width:100%;" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'itemwidth' )); ?>"><?php esc_attr_e('Min. Item Width (px):', 'dvteam'); ?></label>
    <input id="<?php echo esc_attr($this->get_field_id( 'itemwidth' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'itemwidth' )); ?>" value="<?php if (isset($instance['itemwidth'])) { echo esc_attr($instance['itemwidth']); } ?>" onkeypress="return validate(event)" type="number" style="width:100%;" />
</p>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'side' )); ?>"><?php esc_attr_e('Panel Side:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'side' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'side' )); ?>">
        <option value="right" <?php if (isset($instance['side'])) { if ($instance['side'] == 'right') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Right', 'dvteam'); ?></option>
        <option value="left" <?php if (isset($instance['side'])) { if ($instance['side'] == 'left') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Left', 'dvteam'); ?></option>
        <option value="center" <?php if (isset($instance['side'])) { if ($instance['side'] == 'center') { echo esc_attr('selected="selected"'); }} ?>><?php esc_attr_e('Center', 'dvteam'); ?></option>
    </select>
</p>
<?php $gridterms = get_terms( 'dvteamtaxonomy'); ?>
<p>
    <label for="<?php echo esc_attr($this->get_field_id( 'exclude' )); ?>"><?php esc_attr_e('Exclude Category:', 'dvteam'); ?></label>
    <select name="<?php echo esc_attr($this->get_field_name( 'exclude' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'exclude' )); ?>">
        <option value=""><?php esc_attr_e('Display All', 'dvteam'); ?></option>        
        <?php if ($gridterms && !is_wp_error($gridterms)) { ?>
        <?php foreach( $gridterms as $term ) { ?>
        <?php $termname = $term->name; ?>
        <?php $termid = $term->term_id; ?>
        <option value="<?php echo esc_attr($termid) ?>" <?php if (isset($instance['exclude'])) { if ($instance['exclude'] == $termid) { echo esc_attr('selected="selected"'); }} ?>><?php echo esc_attr($termname) ?></option>
        <?php } ?>
        <?php } ?>
    </select>
</p>
<?php }} ?>