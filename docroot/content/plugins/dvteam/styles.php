<?php
$customcode = esc_attr(get_option('dvteam_customcode'));
$boldtitles = esc_attr(get_option('dvteam_boldtitles'));
$closeicon = esc_url(get_option('dvteam_closeicon'));
$infoicon = esc_url(get_option('dvteam_infoicon'));
$smallinfoicon = esc_url(get_option('dvteam_smallinfoicon'));
$sliderleft = esc_url(get_option('dvteam_sliderleft'));
$sliderright = esc_url(get_option('dvteam_sliderright'));
$carouselleft = esc_url(get_option('dvteam_carouselleft'));
$carouselright = esc_url(get_option('dvteam_carouselright'));
$cararrowbg = esc_url(get_option('dvteam_cararrowbg'));
$overlayopacity = esc_attr(get_option('dvteam_overlayopacity'));
$overlaycolor = esc_attr(get_option('dvteam_overlay_color'));
$panelwidth = esc_attr(get_option('dvteam_panelwidth'));
$ptitlesize = esc_attr(get_option('dvteam_ptitlesize'));
$ptitlecolor = esc_attr(get_option('dvteam_ptitlecolor'));
$ptitlebgcolor = esc_attr(get_option('dvteam_ptitlebgcolor'));
$psubtitlesize = esc_attr(get_option('dvteam_psubtitlesize'));
$psubtitlecolor = esc_attr(get_option('dvteam_psubtitlecolor'));
$psubtitlebgcolor = esc_attr(get_option('dvteam_psubtitlebgcolor'));
$phtitlescolor = esc_attr(get_option('dvteam_phtitlescolor'));
$ptextcolor = esc_attr(get_option('dvteam_ptextcolor'));
$pdividercolor = esc_attr(get_option('dvteam_pdividercolor'));
$pdividersize = esc_attr(get_option('dvteam_pdividersize'));
$spaceinner = esc_attr(get_option('dvteam_spaceinner'));
$panelbgcolor = esc_attr(get_option('dvteam_panelbgcolor'));
$socialbgcolor = esc_attr(get_option('dvteam_socialbgcolor'));
$thumbnailopacity = esc_attr(get_option('dvteam_thumbnailopacity'));
$thumbnailoverlay = esc_attr(get_option('dvteam_thumbnailoverlay'));
$infobgcolor = esc_attr(get_option('dvteam_infobgcolor'));
$imgtitlesize = esc_attr(get_option('dvteam_imgtitlesize'));
$imgtitlecolor = esc_attr(get_option('dvteam_imgtitlecolor'));
$imgtitlebgcolor = esc_attr(get_option('dvteam_imgtitlebgcolor'));
$imgsubtitlesize = esc_attr(get_option('dvteam_imgsubtitlesize'));
$imgsubtitlecolor = esc_attr(get_option('dvteam_imgsubtitlecolor'));
$imgsubtitlebgcolor = esc_attr(get_option('dvteam_imgsubtitlebgcolor'));
$removezoom = esc_attr(get_option('dvteam_removezoom'));
$removetextanim = esc_attr(get_option('dvteam_removetextanim'));
$skillsize = esc_attr(get_option('dvteam_skillsize'));
$percentsize = esc_attr(get_option('dvteam_percentsize'));
$skillcolor = esc_attr(get_option('dvteam_skillcolor'));
$percentcolor = esc_attr(get_option('dvteam_percentcolor'));
$bordersize = esc_attr(get_option('dvteam_bordersize'));
$bordercolor = esc_attr(get_option('dvteam_bordercolor'));
$skillbg = esc_attr(get_option('dvteam_skillbg'));
$skillbarbg = esc_attr(get_option('dvteam_skillbarbg'));
$scrollbarcolor = esc_attr(get_option('dvteam_scrollbarcolor'));
$titlemargin = esc_attr(get_option('dvteam_titlemargin'));
$h1size = esc_attr(get_option('dvteam_h1'));
$h2size = esc_attr(get_option('dvteam_h2'));
$h3size = esc_attr(get_option('dvteam_h3'));
$h4size = esc_attr(get_option('dvteam_h4'));
$h5size = esc_attr(get_option('dvteam_h5'));
$h6size = esc_attr(get_option('dvteam_h6'));
$textsize = esc_attr(get_option('dvteam_p'));
$quotesize = esc_attr(get_option('dvteam_quote'));
$quotemarks = esc_attr(get_option('dvteam_quotemarks'));
$quotescolor = esc_attr(get_option('dvteam_quotescolor'));
$cfcolorone = esc_attr(get_option('dvteam_cfcolorone'));
$cfcolortwo = esc_attr(get_option('dvteam_cfcolortwo'));
$cfcolorthree = esc_attr(get_option('dvteam_cfcolorthree'));
$activatescroll = esc_attr(get_option('dvteam_activatescroll'));
$filterfont = esc_attr(get_option('dvteam_filterfont'));
$filterbottom = esc_attr(get_option('dvteam_filterbottom'));
$filterhorizontal = esc_attr(get_option('dvteam_filterhorizontal'));
$filtervertical = esc_attr(get_option('dvteam_filtervertical'));
$filterbgcolor = esc_attr(get_option('dvteam_filterbgcolor'));
$filtermover = esc_attr(get_option('dvteam_filtermover'));
$filteractive = esc_attr(get_option('dvteam_filteractive'));
$filterfontcolor = esc_attr(get_option('dvteam_filterfontcolor'));
$paginationfont = esc_attr(get_option('dvteam_paginationfont'));
$paginationbg = esc_attr(get_option('dvteam_paginationbg'));
$paginationbghover = esc_attr(get_option('dvteam_paginationbghover'));

ob_start("dvteamcompress");
  function dvteamcompress($buffer) {
    /* remove comments */
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    /* remove tabs, spaces, newlines, etc. */
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    return $buffer;
  }
?>
<style type="text/css">
<?php if ($activatescroll != 'true') { ?>
html,body {
margin:0px !important;
}    
<?php } ?>    
<?php if ($boldtitles == 'true') { ?>    
.dv-panel-title,.dv-panel-info,.dvteamgrid .dv-member-name,.dvcv-title,.dvcv-subtitle,.dv-panel h1,.dv-panel h2,.dv-panel h3,.dv-panel h4,.dv-panel h5,.dv-panel h6,.teamlist-popup h1,.teamlist-popup h2,.teamlist-popup h3,.teamlist-popup h4,.teamlist-popup h5,.teamlist-popup h6 {
    font-weight: 700 !important;
}
<?php } ?> 
.dv-panel h1,.dv-panel h2,.dv-panel h3,.dv-panel h4,.dv-panel h5,.dv-panel h6,.teamlist-popup h1,.teamlist-popup h2,.teamlist-popup h3,.teamlist-popup h4,.teamlist-popup h5,.teamlist-popup h6 {
    margin: 0px 0px <?php if ((!empty($titlemargin)) || ($titlemargin == '0')) { echo $titlemargin; } else { echo esc_attr("20"); } ?>px 0px;
}    
.dv-panel h1,.teamlist-popup h1 {font-size: <?php if (!empty($h1size)) { echo $h1size; } else { echo esc_attr("34"); } ?>px;}
.dv-panel h2,.teamlist-popup h2 {font-size: <?php if (!empty($h2size)) { echo $h2size; } else { echo esc_attr("28"); } ?>px;}
.dv-panel h3,.dvcv-title,.teamlist-popup h3 {font-size: <?php if (!empty($h3size)) { echo $h3size; } else { echo esc_attr("24"); } ?>px;}
.dv-panel h4,.teamlist-popup h4 {font-size: <?php if (!empty($h4size)) { echo $h4size; } else { echo esc_attr("20"); } ?>px;}
.dv-panel h5,.teamlist-popup h5 {font-size: <?php if (!empty($h5size)) { echo $h5size; } else { echo esc_attr("18"); } ?>px;}
.dv-panel h6,.dvcv-subtitle,.teamlist-popup h6 {font-size: <?php if (!empty($h6size)) { echo $h6size; } else { echo esc_attr("16"); } ?>px;} 
.dvteam-blockquote p {font-size: <?php if (!empty($quotesize)) { echo $quotesize; } else { echo esc_attr("28"); } ?>px !important;}  
.dvteam-blockquote:before,.dvteam-blockquote:after {font-size: <?php if (!empty($quotemarks)) { echo $quotemarks; } else { echo esc_attr("6"); } ?>em;color:<?php if (!empty($quotescolor)) { echo $quotescolor; } else { echo esc_attr('#414141'); } ?>;}
#dv-overlay {
    background-color: <?php if (!empty($overlaycolor)) { echo $overlaycolor; } else { echo esc_attr('#212121'); } ?>;
    opacity: <?php if ((!empty($overlayopacity)) || ($overlayopacity == '0')) { echo $overlayopacity; } else { echo esc_attr("0.7"); } ?>;
}
.dv-panel,.teamlist-popup {
    width: <?php if (!empty($panelwidth)) { echo $panelwidth; } else { echo esc_attr('640'); } ?>px;
    background-color: <?php if (!empty($panelbgcolor)) { echo $panelbgcolor; } else { echo esc_attr('#313131'); } ?>;
}
.dv-with-socialbar {
    box-shadow: inset 40px 0 0 0 <?php if (!empty($socialbgcolor)) { echo $socialbgcolor; } else { echo esc_attr('#212121'); } ?>;
}
.dv-panel,.dv-panel p, .dvcv-subtitle, .teamlist-popup, .teamlist-popup p{
    color:<?php if (!empty($ptextcolor)) { echo $ptextcolor; } else { echo esc_attr('#c7c7c7'); } ?>;
}
.dv-panel,.dv-panel p, .dv-panel input.wpcf7-form-control.wpcf7-submit,.teamlist-popup,.teamlist-popup p,.teamlist-popup input.wpcf7-form-control.wpcf7-submit{
    font-size: <?php if (!empty($textsize)) { echo $textsize; } else { echo esc_attr("14"); } ?>px;
}    
.dv-panel h1,.dv-panel h2,.dv-panel h3,.dv-panel h4,.dv-panel h5,.dv-panel h6, .dvcv-title, .dvteam-blockquote p,.teamlist-popup h1,.teamlist-popup h2,.teamlist-popup h3,.teamlist-popup h4,.teamlist-popup h5,.teamlist-popup h6{
    color:<?php if (!empty($phtitlescolor)) { echo $phtitlescolor; } else { echo esc_attr('#ffffff'); } ?>;
}    
.dv-panel hr,.teamlist-popup hr {
    margin: <?php if ((!empty($spaceinner)) || ($spaceinner == '0')) { echo $spaceinner; } else { echo esc_attr("30"); } ?>px -<?php if ((!empty($spaceinner)) || ($spaceinner == '0')) { echo $spaceinner; } else { echo esc_attr("30"); } ?>px !important;
    background-color: <?php if (!empty($pdividercolor)) { echo $pdividercolor; } else { echo esc_attr('#414141'); } ?>;
    height: <?php if (!empty($pdividersize)) { echo $pdividersize; } else { echo esc_attr('1'); } ?>px !important;
}  
.dv-panel-left {
    background-color: <?php if (!empty($socialbgcolor)) { echo $socialbgcolor; } else { echo esc_attr('#212121'); } ?>;
}  
.dv-panel-inner {
    padding: <?php if ((!empty($spaceinner)) || ($spaceinner == '0')) { echo $spaceinner; } else { echo esc_attr("30"); } ?>px;
    background-color: <?php if (!empty($panelbgcolor)) { echo $panelbgcolor; } else { echo esc_attr('#313131'); } ?>;
}
.dv-panel-title {
    font-size: <?php if (!empty($ptitlesize)) { echo $ptitlesize; } else { echo esc_attr('28'); } ?>px;
    background-color: <?php if (!empty($ptitlebgcolor)) { echo $ptitlebgcolor; } else { echo esc_attr('#eb5656'); } ?>;
    color:<?php if (!empty($ptitlecolor)) { echo $ptitlecolor; } else { echo esc_attr('#ffffff'); } ?>;
    <?php if (is_rtl()) { ?> 
    padding: 15px <?php if ((!empty($spaceinner)) || ($spaceinner == '0')) { echo $spaceinner; } else { echo esc_attr("30"); } ?>px 15px 60px;
    <?php } else { ?>
    padding: 15px 60px 15px <?php if ((!empty($spaceinner)) || ($spaceinner == '0')) { echo $spaceinner; } else { echo esc_attr("30"); } ?>px;
    <?php } ?>
}
.close-dv-panel-bt {background-image: url('<?php if (!empty($closeicon)) { echo $closeicon; } else {echo esc_attr(plugin_dir_url( __FILE__ ) .'css/icons/close.png');} ?>');background-repeat: no-repeat;background-position: center center;
} 
.dv-panel-info {
    font-size: <?php if (!empty($psubtitlesize)) { echo $psubtitlesize; } else { echo esc_attr('18'); } ?>px;
    background-color: <?php if (!empty($psubtitlebgcolor)) { echo $psubtitlebgcolor; } else { echo esc_attr('#414141'); } ?>;
    color:<?php if (!empty($psubtitlecolor)) { echo $psubtitlecolor; } else { echo esc_attr('#ffffff'); } ?>;
    padding: 15px <?php if ((!empty($spaceinner)) || ($spaceinner == '0')) { echo $spaceinner; } else { echo esc_attr("30"); } ?>px;
}  
.dvteamgrid figure a,.dvteam-thumbnails li a {
    background-color: <?php if (!empty($thumbnailoverlay)) { echo $thumbnailoverlay; } else { echo esc_attr('#212121'); } ?>;
}
.dvteam-thumbnails li a {    
    background-image: url('<?php if (!empty($smallinfoicon)) { echo $smallinfoicon; } else {echo esc_attr(plugin_dir_url( __FILE__ ) .'css/icons/s-info.png');} ?>');background-repeat: no-repeat;background-position: center center;
}
.dvteamgrid figure:hover img,.dvteam-thumbnails li a img:hover{
    opacity: <?php if ((!empty($thumbnailopacity)) || ($thumbnailopacity == '0')) { echo $thumbnailopacity; } else { echo esc_attr("0.1"); } ?>;
<?php if ($removezoom != 'true') { ?>
    transform: scale(1.2);
    -webkit-transform: scale(1.2);
    -ms-transform: scale(1.2);
<?php } ?>
}
<?php if ($removetextanim != 'true') { ?> 
<?php if (is_rtl()) { ?>    
.dvteamgrid figure:hover .dv-member-name,.dvteamgrid figure:hover .dv-member-info,.dvteamgrid figure:hover .dv-member-desc{
    -webkit-transform: translateX(100%);
	-moz-transform: translateX(100%);
	-ms-transform: translateX(100%);
	transform: translateX(100%);
}
<?php } else { ?> 
.dvteamgrid figure:hover .dv-member-name,.dvteamgrid figure:hover .dv-member-info,.dvteamgrid figure:hover .dv-member-desc{
    -webkit-transform: translateX(-100%);
	-moz-transform: translateX(-100%);
	-ms-transform: translateX(-100%);
	transform: translateX(-100%);
}    
<?php } ?>    
<?php } ?>   
.dv-member-zoom {
    background-image: url('<?php if (!empty($infoicon)) { echo $infoicon; } else {echo esc_attr(plugin_dir_url( __FILE__ ) .'css/icons/info.png');} ?>');background-repeat: no-repeat;background-position: center center;
    background-color: <?php if (!empty($infobgcolor)) { echo $infobgcolor; } else { echo esc_attr('#eb5656'); } ?>;   
}   
.dv-member-name {
    color:<?php if (!empty($imgtitlecolor)) { echo $imgtitlecolor; } else { echo esc_attr('#ffffff'); } ?>;
}
.dv-member-info {
    color:<?php if (!empty($imgsubtitlecolor)) { echo $imgsubtitlecolor; } else { echo esc_attr('#ffffff'); } ?>;
}    
.dv-member-name {
    background-color: <?php if (!empty($imgtitlebgcolor)) { echo $imgtitlebgcolor; } else { echo esc_attr('#eb5656'); } ?>;
    font-size: <?php if (!empty($imgtitlesize)) { echo $imgtitlesize; } else { echo esc_attr('18'); } ?>px;
}
.dv-member-info {
    background-color: <?php if (!empty($imgsubtitlebgcolor)) { echo $imgsubtitlebgcolor; } else { echo esc_attr('#313131'); } ?>;
    font-size: <?php if (!empty($imgsubtitlesize)) { echo $imgsubtitlesize; } else { echo esc_attr('14'); } ?>px;
}
.slidizle-next {
    right: <?php if ((!empty($spaceinner)) || ($spaceinner == '0')) { echo $spaceinner; } else { echo esc_attr("30"); } ?>px;
    background-image: url('<?php if (!empty($sliderright)) { echo $sliderright; } else {echo esc_attr(plugin_dir_url( __FILE__ ) .'css/icons/right.png');} ?>');background-repeat: no-repeat;background-position: center center;
}
.slidizle-previous {
    left: <?php if ((!empty($spaceinner)) || ($spaceinner == '0')) { echo $spaceinner; } else { echo esc_attr("30"); } ?>px;
    background-image: url('<?php if (!empty($sliderleft)) { echo $sliderleft; } else {echo esc_attr(plugin_dir_url( __FILE__ ) .'css/icons/left.png');} ?>');background-repeat: no-repeat;background-position: center center;
}  
.owl-theme .owl-controls .owl-nav .owl-prev {
    background-image: url('<?php if (!empty($carouselleft)) { echo $carouselleft; } else {echo esc_attr(plugin_dir_url( __FILE__ ) .'css/icons/c-left.png');} ?>');
    background-position: center center;background-repeat: no-repeat;
}
.owl-theme .owl-controls .owl-nav .owl-next {
    background-image: url('<?php if (!empty($carouselright)) { echo $carouselright; } else {echo esc_attr(plugin_dir_url( __FILE__ ) .'css/icons/c-right.png');} ?>');
    background-position: center center;background-repeat: no-repeat;
}  
.owl-theme .owl-controls .owl-nav {
    background-color: <?php if (!empty($cararrowbg)) { echo $cararrowbg; } else { echo esc_attr('#f5f1f0'); } ?>;
}    
.dvskillbar-title {
	font-size:<?php if (!empty($skillsize)) { echo $skillsize; } else { echo esc_attr('14'); } ?>px;
    color:<?php if (!empty($skillcolor)) { echo $skillcolor; } else { echo esc_attr('#ffffff'); } ?>;
} 
.dvskill-bar-percent {
	font-size:<?php if (!empty($percentsize)) { echo $percentsize; } else { echo esc_attr('14'); } ?>px;
    color:<?php if (!empty($percentcolor)) { echo $percentcolor; } else { echo esc_attr('#ffffff'); } ?>;
}
.dvskillbar {
    border:<?php if (!empty($bordersize)) { echo $bordersize; } else { echo esc_attr('1'); } ?>px solid <?php if (!empty($bordercolor)) { echo $bordercolor; } else { echo esc_attr('#414141'); } ?>;
}
.dvskillbar-bar {
    background-color: <?php if (!empty($skillbarbg)) { echo $skillbarbg; } else { echo esc_attr('#212121'); } ?>;
    background-image: linear-gradient(135deg, rgba(255, 255, 255, .03) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .03) 50%, rgba(255, 255, 255, .03) 75%, transparent 75%, transparent);
}
.dvskillbar-title span {
	background: <?php if (!empty($skillbg)) { echo $skillbg; } else { echo esc_attr('#212121'); } ?>;
}
.dvcv-content {
    margin: <?php if ((!empty($spaceinner)) || ($spaceinner == '0')) { echo $spaceinner; } else { echo esc_attr("30"); } ?>px 0px 0px 0px !important; 
}
.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar,.mCSB_scrollTools .mCSB_dragger:hover .mCSB_dragger_bar,.mCSB_scrollTools .mCSB_dragger:active .mCSB_dragger_bar,.mCSB_scrollTools .mCSB_dragger.mCSB_dragger_onDrag .mCSB_dragger_bar {
    background-color: <?php if (!empty($scrollbarcolor)) { echo $scrollbarcolor; } else { echo esc_attr('#ffffff'); } ?>;
}  
.dv-panel input, .dv-panel textarea,.teamlist-popup input, .teamlist-popup textarea{
    background: <?php if (!empty($cfcolorone)) { echo $cfcolorone; } else { echo esc_attr('#414141'); } ?>;
    color:<?php if (!empty($cfcolorthree)) { echo $cfcolorthree; } else { echo esc_attr('#ffffff'); } ?>;
}
.dv-panel input:focus, .dv-panel textarea:focus,.teamlist-popup input:focus, .teamlist-popup textarea:focus {
    border: 3px solid <?php if (!empty($cfcolorone)) { echo $cfcolorone; } else { echo esc_attr('#414141'); } ?>;
}
.dv-panel input.wpcf7-form-control.wpcf7-submit,.teamlist-popup input.wpcf7-form-control.wpcf7-submit{
	color:<?php if (!empty($cfcolorthree)) { echo $cfcolorthree; } else { echo esc_attr('#ffffff'); } ?> !important;
	background-color:<?php if (!empty($cfcolortwo)) { echo $cfcolortwo; } else { echo esc_attr('#eb5656'); } ?> !important;
}
.dv-panel input.wpcf7-form-control.wpcf7-submit:hover,.teamlist-popup input.wpcf7-form-control.wpcf7-submit:hover {
    color: <?php if (!empty($cfcolorone)) { echo $cfcolorone; } else { echo esc_attr('#414141'); } ?> !important;
	background-color:<?php if (!empty($cfcolorthree)) { echo $cfcolorthree; } else { echo esc_attr('#ffffff'); } ?> !important;
}
.dv-panel div.wpcf7-mail-sent-ok,.dv-panel div.wpcf7-mail-sent-ng,.dv-panel div.wpcf7-spam-blocked,.dv-panel div.wpcf7-validation-errors,.teamlist-popup div.wpcf7-mail-sent-ok,.teamlist-popup div.wpcf7-mail-sent-ng,.teamlist-popup div.wpcf7-spam-blocked,.teamlist-popup div.wpcf7-validation-errors{
    background-color: <?php if (!empty($cfcolorone)) { echo $cfcolorone; } else { echo esc_attr('#414141'); } ?>;
} 
.teamlist-popup {
    background: <?php if (!empty($panelbgcolor)) { echo $panelbgcolor; } else { echo esc_attr('#313131'); } ?>;
    width: <?php if (!empty($panelwidth)) { echo $panelwidth; } else { echo esc_attr('640'); } ?>px;
}
.mfp-bg {
    background: <?php if (!empty($overlaycolor)) { echo $overlaycolor; } else { echo esc_attr('#212121'); } ?>;
    opacity: <?php if ((!empty($overlayopacity)) || ($overlayopacity == '0')) { echo $overlayopacity; } else { echo esc_attr("0.7"); } ?>;
}
.mfp-close,.mfp-close-btn-in .mfp-close,.mfp-image-holder .mfp-close, .mfp-iframe-holder .mfp-close {
    color: <?php if (!empty($phtitlescolor)) { echo $phtitlescolor; } else { echo esc_attr('#ffffff'); } ?>;
}
.dvfilters li {font-size: <?php if (!empty($filterfont)) { echo $filterfont; } else { echo esc_attr("18"); } ?>px;padding: <?php if ((!empty($filtervertical)) || ($filtervertical == '0')) { echo $filtervertical; } else { echo esc_attr("5"); } ?>px <?php if ((!empty($filterhorizontal)) || ($filterhorizontal == '0')) { echo $filterhorizontal; } else { echo esc_attr("15"); } ?>px;}    
.dvfilters-clear {height: <?php if ((!empty($filterbottom)) || ($filterbottom == '0')) { echo $filterbottom; } else { echo esc_attr("20"); } ?>px;}
.dvfilters li {background-color: <?php if (!empty($filterbgcolor)) { echo $filterbgcolor; } else { echo esc_attr('#f5f1f0'); } ?>;color: <?php if (!empty($filtermover)) { echo $filtermover; } else { echo esc_attr('#414141'); } ?>;}   
.dvfilters li:hover {background: <?php if (!empty($filtermover)) { echo $filtermover; } else { echo esc_attr('#414141'); } ?>;color: <?php if (!empty($filterfontcolor)) { echo $filterfontcolor; } else { echo esc_attr('#fff'); } ?>;}
.dvfilters li.gridactive {background: <?php if (!empty($filteractive)) { echo $filteractive; } else { echo esc_attr('#eb5656'); } ?>;color: <?php if (!empty($filterfontcolor)) { echo $filterfontcolor; } else { echo esc_attr('#fff'); } ?>;} 
.dvteam-previous a,.dvteam-next a {
    color: <?php if (!empty($paginationfont)) { echo $paginationfont; } else { echo esc_attr('#fff'); } ?> !important;
    background-color: <?php if (!empty($paginationbg)) { echo $paginationbg; } else { echo esc_attr('#212121'); } ?>;
}
.dvteam-next a:hover,.dvteam-previous a:hover {
    color: <?php if (!empty($paginationfont)) { echo $paginationfont; } else { echo esc_attr('#fff'); } ?> !important;
    background-color: <?php if (!empty($paginationbghover)) { echo $paginationbghover; } else { echo esc_attr('#eb5656'); } ?>;
}   
</style>
<?php ob_end_flush(); ?>
<?php if (!empty($customcode)) { ?>
<style type="text/css">
<?php echo $customcode; ?>    
</style>    
<?php } ?>