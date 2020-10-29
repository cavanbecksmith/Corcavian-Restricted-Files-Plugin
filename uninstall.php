<?php

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
// $option_name = 'blog_homepage';
 
delete_option('hide_attachment_page');
delete_option('supported_filetypes');
delete_option('supported_filesize');
// delete_option('show_page_title');
// delete_option('generic_homepage_title');

// global $wpdb;

// $wpdb->query( 
//     $wpdb->prepare( 
//         "
//                     DELETE FROM $wpdb->postmeta
//             WHERE meta_key = %s
//             OR meta_key = %s
//         ",
//           '_meta_title', '_meta_description'
//         )
// );


?>