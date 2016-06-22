<?php

add_shortcode('dvteam', 'dvteam');
add_shortcode('dvteamfilter', 'dvteamfilter');
add_shortcode('dvteamcarousel', 'dvteamcarousel');
add_shortcode('dvmember', 'dvmember');
add_shortcode('dvthumbnails', 'dvthumbnails');
add_shortcode('dvskills', 'dvskills');
add_shortcode('dvskill', 'dvskill');
add_shortcode('dvcv', 'dvcv');

add_filter("the_content", "dvteam_content_filter");
add_filter("widget_text", "dvteam_content_filter", 9);

function dvteam_content_filter($content) {
 
	// array of custom shortcodes requiring the fix 
	$block = join("|",array("dvteam","dvteamfilter","dvteamcarousel","dvmember","dvthumbnails","dvskills","dvskill","dvcv"));
 
	// opening tag
	$rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);
		
	// closing tag
	$rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/","[/$2]",$rep);
 
	return $rep;
 
}

// Filterable Dv Team Shortcode
function dvteamfilter($atts) {
    extract(shortcode_atts(array(
        "max" => 'max',
        "gridstyle" => 'gridstyle',
        "offset" => 'offset',
		"itemwidth" => 'itemwidth',
        "side" => 'side',
        "exclude" => 'exclude'
	), $atts));
    ob_start();
    include('ourteam-filter.php');
    $content = ob_get_clean();
    return $content;
}

// Dv Thumbnails
function dvthumbnails($atts) {
    extract(shortcode_atts(array(
        "max" => 'max',
        "categoryid" => 'categoryid',
        "side" => 'side'
	), $atts));
    ob_start();
    include('dvteam-thumbnails.php');
    $content = ob_get_clean();
    return $content;
}

// DV Team Carousel
function dvteamcarousel($atts) {
    extract(shortcode_atts(array(
		"max" => 'max',
        "categoryid" => 'categoryid',
        "columns" => 'columns',
        "gridstyle" => 'gridstyle',
        "autoplay" => 'autoplay',
        "duration" => 'duration',
        "spacing" => 'spacing',
        "side" => 'side'
	), $atts));
    ob_start();
    include('dvteam-carousel.php');
    $content = ob_get_clean();
    return $content;
}

// Dv CV Shortcode
function dvcv($atts, $content = null) {
    extract(shortcode_atts(array(
        "title" => 'title',
        "subtitle" => 'subtitle'
	), $atts));
    return '<div class="dvcv-title">' . $title . '</div><div class="dvcv-subtitle">' . $subtitle . '</div><div class="dvcv-content">' . do_shortcode( $content ) . '</div>';
}

// Dv Team Shortcode
function dvteam($atts) {
    extract(shortcode_atts(array(
        "max" => 'max',
        "categoryid" => 'categoryid',
        "gridstyle" => 'gridstyle',
        "offset" => 'offset',
		"itemwidth" => 'itemwidth',
        "side" => 'side'
	), $atts));
    ob_start();
    include('ourteam.php');
    $content = ob_get_clean();
    return $content;
}

// Dv Member Shortcode
function dvmember($atts) {
    extract(shortcode_atts(array(
        "id" => 'id',
        "gridstyle" => 'gridstyle',
        "offset" => 'offset',
		"itemwidth" => 'itemwidth',
        "side" => 'side'
	), $atts));
    ob_start();
    include('member.php');
    $content = ob_get_clean();
    return $content;
}

// Skills
function dvskills($atts, $content = null) {
    return '<div class="dvskills">' . do_shortcode(stripslashes($content)) . '</div>';
}

// Skill
function dvskill($atts, $content = null) {
    extract(shortcode_atts(array(
		"title" => 'title',
        "percent" => 'percent'
	), $atts));
    return '<div class="dvskillbar" data-percent="' . $percent . '%"><div class="dvskillbar-title"><span>' . $title . '</span></div><div class="dvskillbar-bar"></div><div class="dvskill-bar-percent">' . $percent . '%</div></div>';
}
?>