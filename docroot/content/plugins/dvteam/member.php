<?php
$removeinfo = esc_attr(get_option('dvteam_removeinfo'));
$memberid_array = explode(',', $id);	
$dvmemberargs = array(
    'post_type' => 'dvourteam',
    'posts_per_page' => 99,
    'post__in' => $memberid_array
);

$dvmember_query = new WP_Query( $dvmemberargs );
$random = rand();
?>
<div id="dv-overlay"></div>
<ul id="dvteamgrid<?php echo esc_attr($random); ?>" class="dvteamgrid">
<?php while($dvmember_query->have_posts()) : $dvmember_query->the_post(); ?>
    <?php if ( has_post_thumbnail() ) { ?>
    <?php 
$thumb_id = get_post_thumbnail_id();
if ($gridstyle == 'rectangle') {                                       
    $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'dv-team-rectangle', true);
}
elseif ($gridstyle == 'square') {                                       
    $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'dv-team-square', true);
}
else {
    $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'large', true);
}
$thumb_url = $thumb_url_array[0];  
    ?>
    <?php $dvexcerpt = get_post_meta( get_the_id(), 'dvexcerpt', true ); ?>
    <?php $dvptlink = get_post_meta( get_the_id(), 'dvptlink', true ); ?>
    <li>
        <figure>
            <a id="dvgridboxlink<?php echo esc_attr($random); ?><?php the_ID(); ?>"<?php if ( has_post_format( 'link' )) { ?> href="<?php echo esc_url($dvptlink); ?>" target="_blank"<?php } else { ?> href="#dvteambox<?php echo esc_attr($random); ?><?php the_ID(); ?>"<?php } ?> <?php if ($side == 'center') { ?> class="popup-with-zoom-anim" <?php } ?>>
                <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title(); ?>" />
                <?php if ($removeinfo != 'true') { ?>
                <div class="dv-member-zoom"></div>
                <?php } ?>
            </a>
                <figcaption>
                    <div class="dv-member-desc">
                        <div><span class="dv-member-name"><?php the_title(); ?></span></div>
                        <?php if (!empty($dvexcerpt)) { ?>
                        <div><span class="dv-member-info"><?php echo esc_attr($dvexcerpt); ?></span></div>
                        <?php } ?>
                    </div>
                </figcaption>
        </figure>
    </li>
    <?php } ?>
    <?php endwhile; ?>
</ul>
<div class="dv-clear"></div>
<?php if ($side != 'center') { ?>
<?php while($dvmember_query->have_posts()) : $dvmember_query->the_post(); ?>
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
<?php while($dvmember_query->have_posts()) : $dvmember_query->the_post(); ?>
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
        imagesLoaded('#dvteamgrid<?php echo esc_attr($random); ?>', function () {
            wookmark = new Wookmark('#dvteamgrid<?php echo esc_attr($random); ?>', {
                itemWidth: <?php if (!empty($itemwidth)) { echo esc_js($itemwidth); } else { echo esc_js('250'); } ?>,
                autoResize: true,
                resizeDelay: 500,
                <?php if (is_rtl()) { echo stripslashes(esc_js("direction: 'right',")); } ?>
                align: '<?php if (!empty($align)) { echo esc_js($align); } else { echo esc_js('left'); } ?>',
                container: jQuery('#dvteamgrid<?php echo esc_attr($random); ?>'),
                offset: <?php if (!empty($offset) || $offset == '0') { echo esc_js($offset); } else { echo esc_js('20'); } ?>,
                outerOffset: 0,
                fillEmptySpace: false,
                flexibleWidth: '100%'
            });
            setTimeout(function(){
                jQuery("#dvteamgrid<?php echo esc_attr($random); ?>").css('visibility','visible');
            }, 500);
        });
    });
</script>
<?php wp_reset_postdata(); ?>