# BookingFor Wordpress plugin - V.3
Wordpress plugin repo for Bookingfor. BookingFor wordpress plugin is a free and open source web-based booking plugin for reservation multi hotel and multi merchant booking based on the service bookingfor. You must have a bookingfor subscription to use this open source booking plugin.

Demo: www.bookingformars.com

# This release is currently maintained and supported

# Setup
1. install Bookingfor Plugin;<br/>
3. configure Bookingfor Plugin with your Subscription code, Bookingfor Apikey and Google Maps ApiKey;<br/>
5. add Bookingfor widgets.<br/>
6. enjoy!<br />

# Requirements
Wordpress  4.8 +<br/>
PHP 5.6 +<br/>
cURL 7.40 +<br/>
OpenSSL 1.0.2 +<br/>
Bookingfor v.7.x.x

# Compatibility
Polylang v. 2.2 +<br/>
WPML v. 3.7<br/><br/>
Supported theme: <br/>
twentyseventeen<br/>
twentysixteen<br/>
twentyfifteen<br/>
Genesis (Agent Focused Pro)

# Shortcode
[bookingfor_merchants category=1019 rating=4]<br/>
[bookingfor_resources categories=6,4]<br/>
[bookingfor_onsells]<br/>
[bookingfor_onsells order=asc]<br/>
[bookingfor_tag tagid=7]<br/>
[bookingfor_merchantscarousel tags=1,32]<br/>
[bookingfor_resources condominiumid=2]

# Translations
To help in translation http://translate.bookingfor.com/collaboration/

# How to override Bookingfor template files

You can edit the markup and template structure for front-end of your site in an upgrade-safe way using overrides.

Templates files can be found within the /wp-content/plugins/bookingfor/templates/ directory.

Copy the needed file into the directory within your theme named bookingfor (create if it does not exist), keeping the same file structure but removing the templates subdirectory.

For example, if you need to overwrite file /wp-content/plugins/bookingfor/templates/thanks.php, copy this file to /themes/themexxxx/bookingfor folder:

The copied file will now override the bookingfor default template file.

Do not edit these files within the core plugin itself as they are overwritten during the upgrade process and any customizations will be lost.