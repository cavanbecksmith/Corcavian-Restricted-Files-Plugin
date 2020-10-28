# Corcavian-Restricted-Files-Plugin
Redirect logged out users who try access restricted folder

## Install
Firstly we want to edit our htaccess on the root directory of your wordpress site and add the following code.
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
Drag your files into this upload box and they'll be uploaded so you can access them from the media library.
## Restricted file location
All uploads from this plugins menu page will upload to this directory
```
/wp-content/uploads/restricted/
```
## Updating the code for your usage
Currently I am only supporting few filetypes and have the upload size capped at 10mb.

(Going to at some point create these into options in the admin menu.)

However you can change these settings by going to ```plugins/corcavian-restricted-files/upload.php```

Add a list of your supported filetypes ```$extension = array("jpeg","jpg","png","gif",'pdf','txt')```

Update the filesize -> ```$totalBytes = 10000000; // 10mb```
