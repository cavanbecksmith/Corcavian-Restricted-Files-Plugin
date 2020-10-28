# Corcavian-Restricted-Files-Plugin
Redirect logged out users who try access restricted folder
## Install
Firstly we want to edit our htaccess and add the following code
```
# Only allows logged in users to view wp-uploads/restricted content
RewriteCond %{REQUEST_FILENAME} -s
RewriteRule ^wp-content/uploads/(restricted/.*)$ wp-content/plugins/corcavian-restricted-files/wp-uploads-restriction.php?file=$1 [QSA,L]
Options -Indexes
```
This rewrites the url so that it is pointing at our php file and not our restricted folder.
Now simply just upload your plugin to ```wp-content/plugins``` and activate in your admin panel.
## Robots.txt
I would also advise that on your robots.txt that you include this
```
# Custom Wordpress Robots
Disallow: /wp-User-agent: *
admin/
Disallow: /wp-content/uploads/restricted/
Allow: /wp-admin/admin-ajax.php
```
## Uploading files
If you have successfully activated the plugin and noticed a new menu item in your admin dashboard that says "Restricted Files". 
Click this menu item and you'll be taken to a page with only one setting atm and below that is a blue upload box.
Drag your files into this upload box and they'll be uploaded so you can access them from the media library
