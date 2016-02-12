<?php
global $paged;

if ( get_query_var('paged') ) { 
    $paged = get_query_var('paged'); 
}
elseif ( get_query_var('page') ) { 
    $paged = get_query_var('page'); 
}
else { 
    $paged = 1; 
}
if (empty($categoryid)) {
    $dvteamthumbargs = array(
        'post_type' => 'dvourteam',
        'posts_per_page' => $max,
        'paged' => $paged
    );
}
else {
    if ( function_exists('icl_object_id') ) {
        $dvteamtcatid_array = (int)$categoryid;
    }
    else {
        $dvteamtcatid_array = explode(',', $categoryid);
    }
    $dvteamthumbargs = array(
        'post_type' => 'dvourteam',
        'posts_per_page' => $max,
        'tax_query' => array(
            array(
                'taxonomy' => 'dvteamtaxonomy',
                'terms'    => $dvteamtcatid_array,
            ),
        ),
        'paged' => $paged
    );
}
$dvteamthumbs_query = new WP_Query( $dvteamthumbargs );
$random = rand();
$pagination = esc_attr(get_option('dvteam_pagination'));
?>
<div id="dv-overlay"></div>
<ul id="dvteam-thumbnails<?php echo esc_js($random); ?>" class="dvteam-thumbnails">
<?php while($dvteamthumbs_query->have_posts()) : $dvteamthumbs_query->the_post(); ?>
    <?php 
if ( has_post_thumbnail() ) {
$thumb_id = get_post_thumbnail_id();    
$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail', true);
$thumb_url = $thumb_url_array[0];  
    ?>
    <?php $dvptlink = get_post_meta( get_the_id(), 'dvptlink', true ); ?>
    <li>
            <a id="dvgridboxlink<?php echo esc_attr($random); ?><?php the_ID(); ?>"<?php if ( has_post_format( 'link' )) { ?> href="<?php echo esc_url($dvptlink); ?>" target="_blank"<?php } else { ?> href="#dvteambox<?php echo esc_attr($random); ?><?php the_ID(); ?>"<?php } ?> <?php if ($side == 'center') { ?> class="popup-with-zoom-anim" <?php } ?>>
                <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title(); ?>" />
            </a>
    </li>
    <?php } ?>
    <?php endwhile; ?>
</ul>
<div class="dv-clear"></div>

<?php if ($pagination == 'true') { ?>
<div class="dvteam-blogpager thumb-pager">
    <div class="dvteam-previous">
        <?php next_posts_link( esc_attr__( '&#8249; Previous', 'dvteam' ), $dvteamthumbs_query->max_num_pages ); ?>
    </div>
    <div class="dvteam-next">
        <?php previous_posts_link( esc_attr__( 'Next &#8250;', 'dvteam' ), $dvteamthumbs_query->max_num_pages ); ?>
    </div>
</div>
<div class="dv-clear"></div>
<?php } ?>

<?php if ($side != 'center') { ?>
<?php while($dvteamthumbs_query->have_posts()) : $dvteamthumbs_query->the_post(); ?>
<?php if ( has_post_thumbnail() ) { ?>
<?php $dvexcerpt = get_post_meta( get_the_id(), 'dvexcerpt', true ); ?>
<?php $dvptfimage = get_post_meta( get_the_id(), 'dvptfimage', true ); ?>
<?php $dvptvideo = get_post_meta( get_the_id(), 'dvptvideo', true ); ?>
<?php $dvptteamimages = get_post_meta( get_the_id(), 'dvptteamimages', true ); ?>
<?php $dvptquote = get_post_meta( get_the_id(), 'dvptquote', true ); ?>
<?php $dvactivateicons = get_post_meta( get_the_id(), 'dvactivateicons', true ); ?>
<?php $dvrepeatdvicons = get_post_meta( get_the_id(), 'dvrepeatdvicons', true ); ?>
<div id="dvteambox<?php echo esc_attr($random); ?><?php the_ID(); ?>" class="dv-panel <?php if($dvactivateicons == "on") { echo esc_attr('dv-with-socialbar'); }?>">
    <?php if($dvactivateicons == "on") { ?>
    <div class="dv-panel-left">
        <?php $iconurl = plugin_dir_url( __FILE__ ) . 'social-icons/'; ?>
        <ul class="dvteam-icons">
            <?php
foreach ( (array) $dvrepeatdvicons as $key => $entry ) {

    $dviconimg = $dvcustomimg = $dvcustomimgcolor = $dviconurl = '';

    if ( isset( $entry['dviconimg'] ) ) {
        $dviconimg = $entry['dviconimg'];
    }

    if ( isset( $entry['dvcustomimg'] ) ) {            
        $dvcustomimg = $entry['dvcustomimg'];
    }
    
    if ( isset( $entry['dvcustomimgcolor'] ) ) {            
        $dvcustomimgcolor = $entry['dvcustomimgcolor'];
    }
    
    if ( isset( $entry['dviconurl'] ) ) {
        $dviconurl = $entry['dviconurl'];
    } ?>
    
    <li class="<?php if (empty($dvcustomimg)) { echo esc_attr($dviconimg); } else { echo esc_attr('custombg'); echo esc_attr($random); the_ID(); } ?>">
        <?php if (!empty($dvcustomimg)) { ?>
        <style type="text/css">.custombg<?php echo esc_attr($random); ?><?php the_ID(); ?>:hover{ background-color:<?php echo esc_attr($dvcustomimgcolor); ?> }</style>
        <?php } ?>
        <a href="<?php echo esc_attr($dviconurl); ?>" target="_blank">

    <?php if (!empty($dvcustomimg)) { ?>
        <img src="<?php echo esc_url($dvcustomimg); ?>" alt="" /> 
    <?php } else { ?>
        <img src="<?php echo esc_attr($iconurl); ?><?php echo esc_attr($dviconimg); ?>.png" alt="" /> 
    <?php } ?>
            
        </a>
    </li>
            <?php } ?>
        </ul>
    </div>
    <div class="dv-panel-right">
    <?php } ?>    
    <div class="dv-panel-title">
        <?php the_title(); ?>
        <div class="close-dv-panel-bt"></div>
    </div>
    
    <?php if (!empty($dvexcerpt)) { ?>
    <div class="dv-panel-info"><?php echo esc_attr($dvexcerpt); ?></div>
    <?php } ?>
    
    <?php if ( has_post_format( 'image' )) { ?>
    <div class="dv-panel-image"><img src="<?php echo esc_url($dvptfimage); ?>" alt="" /></div>
    <?php } ?>
    
    <?php if ( has_post_format( 'video' )) { ?>
    <div class="dv-panel-video"><?php echo balanceTags($dvptvideo); ?></div>
    <?php } ?>
    
    <?php if ( has_post_format( 'gallery' )) { ?>
    <div id="dvteamslider<?php echo esc_attr($random); ?><?php the_ID(); ?>" class="dvteam-slider" data-slidizle data-slidizle-loop="true">
        <ul class="slider-content" data-slidizle-content>
            <?php foreach ($dvptteamimages as $image => $link) { ?>
            <?php $dvfullimage = wp_get_attachment_image_src( $image, 'full' ); ?>
            <li class="slider-item" style="background-image:url('<?php echo esc_js($dvfullimage['0']); ?>')"></li>
            <?php } ?>
        </ul>
        
        <div class="slider-next" data-slidizle-next></div>
        <div class="slider-previous" data-slidizle-previous></div>
        <ul class="slider-navigation" data-slidizle-navigation></ul>
        
        <script type="text/javascript">jQuery(document).ready(function ($) {$('#dvteamslider<?php echo esc_attr($random); ?><?php the_ID(); ?>').slidizle();});</script>
		</div> 
    <?php } ?>
    
    <div class="dv-panel-inner">
        <?php if ( has_post_format( 'quote' )) { ?>
        <div class="dvteam-blockquote"><p><?php echo esc_attr($dvptquote); ?></p></div>
        <hr/>
        <?php } ?>
        <?php the_content(); ?>
    </div>
        <?php if($dvactivateicons == "on") { ?>
    </div>
    <div class="dv-clear"></div>
    <?php } ?>
</div>    
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#dvgridboxlink<?php echo esc_attr($random); ?><?php the_ID(); ?>').panelslider({
            side: '<?php if (!empty($side)) { echo esc_js($side); } else { echo esc_js('right'); } ?>',
            clickClose: true,
            duration: 200
        });
        jQuery('.close-dv-panel-bt').click(function () {
            jQuery.panelslider.close();
        });
    });
</script>
<?php } ?>
<?php endwhile; ?>

<?php } else { ?>
<?php while($dvteamthumbs_query->have_posts()) : $dvteamthumbs_query->the_post(); ?>
<?php if ( has_post_thumbnail() ) { ?>
<?php $dvexcerpt = get_post_meta( get_the_id(), 'dvexcerpt', true ); ?>
<?php $dvptfimage = get_post_meta( get_the_id(), 'dvptfimage', true ); ?>
<?php $dvptvideo = get_post_meta( get_the_id(), 'dvptvideo', true ); ?>
<?php $dvptteamimages = get_post_meta( get_the_id(), 'dvptteamimages', true ); ?>
<?php $dvptquote = get_post_meta( get_the_id(), 'dvptquote', true ); ?>
<?php $dvactivateicons = get_post_meta( get_the_id(), 'dvactivateicons', true ); ?>
<?php $dvrepeatdvicons = get_post_meta( get_the_id(), 'dvrepeatdvicons', true ); ?>
<div id="dvteambox<?php echo esc_attr($random); ?><?php the_ID(); ?>" class="teamlist-popup zoom-anim-dialog mfp-hide <?php if($dvactivateicons == "on") { echo esc_attr('dv-with-socialbar'); }?>">
    <?php if($dvactivateicons == "on") { ?>
    <div class="dv-panel-left">
        <?php $iconurl = plugin_dir_url( __FILE__ ) . 'social-icons/'; ?>
        <ul class="dvteam-icons">
            <?php
foreach ( (array) $dvrepeatdvicons as $key => $entry ) {

    $dviconimg = $dvcustomimg = $dvcustomimgcolor = $dviconurl = '';

    if ( isset( $entry['dviconimg'] ) ) {
        $dviconimg = $entry['dviconimg'];
    }

    if ( isset( $entry['dvcustomimg'] ) ) {            
        $dvcustomimg = $entry['dvcustomimg'];
    }
    
    if ( isset( $entry['dvcustomimgcolor'] ) ) {            
        $dvcustomimgcolor = $entry['dvcustomimgcolor'];
    }
    
    if ( isset( $entry['dviconurl'] ) ) {
        $dviconurl = $entry['dviconurl'];
    } ?>
    
    <li class="<?php if (empty($dvcustomimg)) { echo esc_attr($dviconimg); } else { echo esc_attr('custombg'); echo esc_attr($random); the_ID(); } ?>">
        <?php if (!empty($dvcustomimg)) { ?>
        <style type="text/css">.custombg<?php echo esc_attr($random); ?><?php the_ID(); ?>:hover{ background-color:<?php echo esc_attr($dvcustomimgcolor); ?> }</style>
        <?php } ?>
        <a href="<?php echo esc_attr($dviconurl); ?>" target="_blank">

    <?php if (!empty($dvcustomimg)) { ?>
        <img src="<?php echo esc_url($dvcustomimg); ?>" alt="" /> 
    <?php } else { ?>
        <img src="<?php echo esc_attr($iconurl); ?><?php echo esc_attr($dviconimg); ?>.png" alt="" /> 
    <?php } ?>
            
        </a>
    </li>
            <?php } ?>
        </ul>
    </div>
    <div class="dv-panel-right">
    <?php } ?>    
    <div class="dv-panel-title">
        <?php the_title(); ?>
    </div>
    
    <?php if (!empty($dvexcerpt)) { ?>
    <div class="dv-panel-info"><?php echo esc_attr($dvexcerpt); ?></div>
    <?php } ?>
    
    <?php if ( has_post_format( 'image' )) { ?>
    <div class="dv-panel-image"><img src="<?php echo esc_url($dvptfimage); ?>" alt="" /></div>
    <?php } ?>
    
    <?php if ( has_post_format( 'video' )) { ?>
    <div class="dv-panel-video"><?php echo balanceTags($dvptvideo); ?></div>
    <?php } ?>
    
    <?php if ( has_post_format( 'gallery' )) { ?>
    <div id="dvteamslider<?php echo esc_attr($random); ?><?php the_ID(); ?>" class="dvteam-slider" data-slidizle data-slidizle-loop="true">
        <ul class="slider-content" data-slidizle-content>
            <?php foreach ($dvptteamimages as $image => $link) { ?>
            <?php $dvfullimage = wp_get_attachment_image_src( $image, 'full' ); ?>
            <li class="slider-item" style="background-image:url('<?php echo esc_js($dvfullimage['0']); ?>')"></li>
            <?php } ?>
        </ul>
        
        <div class="slider-next" data-slidizle-next></div>
        <div class="slider-previous" data-slidizle-previous></div>
        <ul class="slider-navigation" data-slidizle-navigation></ul>
        
        <script type="text/javascript">jQuery(document).ready(function ($) {$('#dvteamslider<?php echo esc_attr($random); ?><?php the_ID(); ?>').slidizle();});</script>
		</div> 
    <?php } ?>
    
    <div class="dv-panel-inner">
        <?php if ( has_post_format( 'quote' )) { ?>
        <div class="dvteam-blockquote"><p><?php echo esc_attr($dvptquote); ?></p></div>
        <hr/>
        <?php } ?>
        <?php the_content(); ?>
    </div>
        <?php if($dvactivateicons == "on") { ?>
    </div>
    <div class="dv-clear"></div>
    <?php } ?>
</div>
<?php } ?>
<?php endwhile; ?>
<?php } ?>
<?php $align = esc_attr(get_option('dvteam_thumbnailalign')); ?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        "use strict";
        var wookmark;
        imagesLoaded('#dvteam-thumbnails<?php echo esc_attr($random); ?>', function () {
            wookmark = new Wookmark('#dvteam-thumbnails<?php echo esc_attr($random); ?>', {
                itemWidth: 100,
                autoResize: true,
                resizeDelay: 500,
                <?php if (is_rtl()) { echo stripslashes(esc_js("direction: 'right',")); } ?>
                align: '<?php if (!empty($align)) { echo esc_js($align); } else { echo esc_js('left'); } ?>',
                container: jQuery('#dvteam-thumbnails<?php echo esc_attr($random); ?>'),
                offset: 0,
                outerOffset: 0,
                fillEmptySpace: false,
                flexibleWidth: '100%'
            });
            setTimeout(function(){
                jQuery("#dvteam-thumbnails<?php echo esc_attr($random); ?>").css('visibility','visible');
            }, 100);
        });
    });
</script>
<?php wp_reset_postdata(); ?>