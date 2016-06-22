<div class="wrap">

<?php settings_errors(); ?>

<h1><?php echo __("WP Bullhorn", WP_Bullhorn::TEXTDOMAIN); ?></h1>
<p><?php echo __("Thanks for choosing to use the WordPress Bullhorn Plugin.", WP_Bullhorn::TEXTDOMAIN); ?></p>

<form method="post" action="options.php"> 
<?php @settings_fields(WP_Bullhorn_Settings::OPTIONS_GROUP); ?>
<?php @do_settings_sections("bullhorn"); ?>
    
<?php @submit_button(); ?>
</form>
</div>
