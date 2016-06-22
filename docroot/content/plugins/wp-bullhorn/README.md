WP-Bullhorn-Rest
================

Bullhorn Integration Plugin for Wordpress

### Features

 1. Custom post type for Bullhorn **Job Order** REST object
 2. CRON job to sync the Bullhorn REST objects periodically
 3. Custom taxonomy for Bullhorn **Category** REST object
 4. Settings page for managing Bullhorn API credentials.
 5. Extensible - Our flexible system provides an interface class for Read/Write on all REST Entities (Candidates, Files, JobSubmissions, CorporateUsers, etc)

Note: Ability for users to apply to jobs is supported by, but not included in this plugin. Due to the nature of form plugins (Gravity Forms, Contact Form 7, Formidable, etc) mapping the data provided by the user in the form to the candidate record is a very manual process. This plugin provides an Entity interface class that streamlines pushing data through our plugin into Bullhorn, however the use of this feature usually requires a developer.

Examples may be able to be furnished for specific form plugins upon request.


#### Installation

 - Download the latest release, and place the `wp-bullhorn` folder in your `wp-plugins` directory.
 - Activate the plugin.
 - Enter your Bullhorn API credentials and Software License Key in the Bullhorn Settings Page.
 - Go through the first-time Bullhorn setup (accepting the Bullhorn API Terms and Conditions)
 - Install a system CRON w/i your hosting panel. Generally, the command will look something like this: `php /PATH/TO/wp-content/plugins/wp-bullhorn/cron.php`. The interval is up to you, but we suggest daily during non-peak hours.
 

#### First Time Bullhorn Setup

Note: Bullhorn has some weirdness with their API. You need to manually accept the terms of service for the api before using my plugin. Its a button push at the following link, you will need to get the credentials from your BH representative.

    https://auth.bullhornstaffing.com/oauth/authorize?client_id=YOUR_CLIENT_ID&response_type=code&action=Login&username=YOUR_USERNAME&password=YOUR_PASSWORD

If you run into any issues with the above link post in the Bullhorn forum. If you

