<?php 

header('Content-Type: application/json');
require_once('./inc/helpers.php');




if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // https://deliciousbrains.com/wordpress-rest-api-vs-custom-request-handlers/
    // https://wordpress.stackexchange.com/questions/26254/how-to-include-wp-load-php-from-any-location
    // https://www.php.net/manual/en/features.file-upload.multiple.php
    // https://www.php.net/manual/en/function.move-uploaded-file.php
    // http://www.javascripthive.info/php/php-multiple-files-upload-validation/

    // $test = array('1' => 'test', '2' => $_POST['files']);
    $test = array('1' => 'test');

    $path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
    include($path.'wp-load.php');


    $siteurl = get_option("siteurl");

    $files = $_FILES['files'];

    $data = array('errors' => array());

    $extension = array_filter(preg_split("/[\s,]+/", get_option('supported_filetypes')), function($v){
       return trim($v);
    });

    $totalBytes = get_option('supported_filesize'); // 10mb

    $data['test'] = $extension;

    if(!empty($files))
    {
        $file_desc = reArrayFiles($files);  


        

        foreach($file_desc as $val)
        {

            $name = preg_replace('/\s+/', '_', $val['name']);
            $namewithoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $name);
            $ext = pathinfo($val['name'], PATHINFO_EXTENSION);
            $upload_path = ABSPATH.'wp-content/uploads/restricted/';
            $newname = $name;
            $full_path = $upload_path.$newname;
            // $newname = $name.'__'.date('YmdHis',time()).mt_rand().'.'.$ext;
            $upload_ok = true;

            $now = new DateTime();
            $datesent=$now->format('Y-m-d H:i:s'); 

            

            if(file_exists($full_path)){

              $err_arr = array(
                'error_msg' => 'This file already exists',
                'filename' => $val['name']
              );

              array_push($data['errors'], $err_arr);
              $upload_ok = false;
            }

            if($val['size'] > $totalBytes && !empty($totalBytes)){
                $upload_ok = false;

                $err_arr = array(
                  'error_msg' => 'The file is larger than '.($totalBytes/1000000).'mb',
                  'filename' => $val['name']
                );

                // php_round($val['size'] / 1000000, 2)
                array_push($data['errors'], $err_arr);
            }


            
            if(in_array(strtolower($ext), $extension) == false && !empty($extension)){

              $err_arr = array(
                'error_msg' => 'Extension not supported',
                'filename' => $val['name']
              );

              $upload_ok = false;
              array_push($data['errors'], $err_arr);
            }

            if($upload_ok){
              move_uploaded_file($val['tmp_name'], $full_path);
              
              global $wpdb;

              $wpdb->insert('wp_posts', array(
                  'post_author' => get_current_user_id(),
                  'post_date' => $datesent,
                  'post_date_gmt' => $datesent,
                  'post_content' => '',
                  'post_title' => $namewithoutExt,
                  'post_status' => 'inherit',
                  'comment_status' => 'open',
                  'ping_status' => 'closed',
                  'post_name' => $namewithoutExt,
                  'post_parent' => 0,
                  'guid' => $siteurl.'/wp-content/uploads/restricted/'.$name,
                  'post_type' => 'attachment',
                  'post_mime_type' => $val['type'],
                  'comment_count' => 0
              ));

              $wpdb->insert('wp_postmeta', array(
                'post_id' => $wpdb->insert_id,
                'meta_key' => '_wp_attached_file',
                'meta_value' => 'restricted/'.$name
              ));
            }
        }
    } else {
      $err_arr = array(
        'error_msg' => 'No files have been detected',
        'filename' => 'No Files Detected'
      );

      array_push($data['errors'], $err_arr);
      // $upload_ok = false; 
    }
    echo json_encode($data);
    exit;
}



?>