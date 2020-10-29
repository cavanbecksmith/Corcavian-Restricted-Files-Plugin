<?php

/*
Plugin Name: Corcavian Restricted Files Plugin
Description: Restricted files inside /wp-content/uploads/restricted
Version:     0.1
Author:      Cavan Becksmith
Author URI:  http://www.corcavian.site/
Text Domain: corcavian_restricted_files_plugin
Domain Path: /languages/
License:     GPL v2 or later
*/


// https://gist.github.com/hakre/1552239

include_once 'inc/create_meta_box.php';
include_once 'inc/helpers.php';

if ( __FILE__ == $_SERVER['SCRIPT_FILENAME'] )
  die();
  




class CORCAVIAN_RESTRICTED_FILES_PLUGIN {

	public function __construct() {

    $this->pluginDir = get_option('siteurl').'/wp-content/plugins/corcavian-restricted-files';
    $this->uploadFile = $this->pluginDir . '/upload.php';

    if(get_option('hide_attachment_page')){
      add_action( 'template_redirect', array($this, 'redirect_attachment_page'));
    }

    add_action('admin_menu', array($this, 'create_seo_page'));
    add_action( 'admin_init', array($this, 'corcavian_register_settings') );
	}

  public function redirect_attachment_page() {
    if ( is_attachment() ) {
      global $post;
      if ( $post && $post->post_parent ) {
        wp_redirect( esc_url( get_permalink( $post->post_parent ) ), 301 );
        exit;
      } else {
        wp_redirect( esc_url( home_url( '/' ) ), 301 );
        exit;
      }
    }
  }

	public function create_seo_page() {

    $capability  = apply_filters( 'corcavian_required_capabilities', 'manage_options' );
    $parent_slug = 'corcavian_restricted_files';

    add_menu_page( __( 'Corcavian Restricted Files', 'corcavian-seo-plugin' ), __( 'Restricted Files', 'corcavian-seo-plugin' ), $capability, $parent_slug, array($this, 'corcavian_restricted_files_settings'), 'dashicons-forms' );
  }
  
  public function corcavian_restricted_files_settings() {
    ?>


    <style>
      .droppable {
        background: #08c;
        color: #fff;
        padding: 20px 0;
        text-align: center;
        width: 100%;
      }

      .droppable.dragover {
        background: #00CC71;
      }

      .droppable_files {
        display: table;
      }

      .droppable_files_cell {
        display: table-cell;
        text-align: center;
      }

      .errors{
        
      }

      .alert.alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
      }

      .alert{
        margin-top: 1rem;
        position: relative;
        padding: .75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: .25rem;
      }
    </style>


    <script>
    
    function createFormData (filesArr) {

      let formData = new FormData();

      for (let i = 0; i < filesArr.length; i++){
        let fileList = filesArr[i];
        for (let z =0; z < filesArr[i].length; z++){
          let file = filesArr[i][z];
          formData.append("files[]", file, file.name);
        }
      }
      // for(var key of formData.entries()){console.log(key[0] + ', ' + key[1]);}
      return formData;
    }
    // https://wordpress.stackexchange.com/questions/273868/form-data-is-empty-while-posting-form-through-ajax-using-jquery-in-wordpress

    // http://youmightnotneedjquery.com/
    Element.prototype.parent = function(){
        return this.parentNode;   
    };

    Element.prototype.find = function (el) {
        return this.querySelector(el);
    };

    Element.prototype.findAll = function (el) {
        return this.querySelectorAll(el);
    };

    function round(value, precision) {
      var multiplier = Math.pow(10, precision || 0);
      return Math.round(value * multiplier) / multiplier;
    }

    </script>

    <script type="text/javascript" src="<?php echo $this->pluginDir ?>/js/droppable.js"></script>

    <script type="text/javascript">

    document.addEventListener('DOMContentLoaded', function(){


        var frmData = null;
        var APP_URL = '<?php echo $this->uploadFile ?>'

        // $('.ajax-upload').on("click", function(event){
        //   event.preventDefault();
        //   console.log("click");
        // })

        $(document).on('click', '.ajax-upload', function (event) {
          event.preventDefault();

          console.log("submit");

          $.ajax({
              url: APP_URL,
              // dataType: "json", 
              data: frmData,
              processData: false,  // tell jQuery not to process the data
              contentType: false, // tell jQuery not to set contentType
              type: 'POST',
              success: function (data) {
                var errDiv = $('.errors');
                errDiv.html('');
                console.log("success");
                console.log(data);
                if(data.errors.length > 0){
                  console.log('have errors');
                  for(var i = 0; i < data.errors.length; i++){
                     errDiv.append('<div class="alert alert-danger">' + data.errors[i]['filename'] + ': ' + data.errors[i]['error_msg'] + '</div>');
                  }
                }
              },
              error: function (data) {
                  // var errDiv = $('.errors');
                  // errDiv.html('');
                  // console.log(errDiv);
                  // for (var item in data.responseJSON) {
                  //     for (var i = 0; i < data.responseJSON[item].length; i++) {
                  //         errDiv.append('<div class="alert alert-danger">' + data.responseJSON[item] + '</div>');
                  //     }
                  // }
              }
          });
21
        });


        $(document).on('click', '.clear-files', function (event) {
          event.preventDefault();
          frmData = null;
        });

        makeDroppable(function(data){
          frmData = data;
        });

    });

    </script>


    <h1 class="test">Private Files uploader</h1>


    <!-- Dropable field -->
    <div class="droppable">
        <div>
            <!-- <span class="fa fa-upload" style="font-size: 8em;"></span> -->
            <span class="dashicons dashicons-paperclip"></span>
        </div>
        Please drag or click to upload your files here...
        <div class="droppable_count">&nbsp;</div>
    </div>

    <div class="droppable_files"></div>

    <form action='<?php echo $this->uploadFile ?>' method="POST" class="dropform" enctype="multipart/form-data">
        <!-- <input type="text" value="something" name="sm"/> -->
        <?php   ?>
        <div class="form-group py-2" style="margin-top: 20px;">
            <input type="button" class="button button-secondary clear-files" value="Clear Files">
        </div>
        <!-- <div class="form-group">
            <input type="submit" class="btn btn-secondary ajax-upload" />
        </div> -->

        <!-- <input type="text" name="test" value="test"/> -->
        <div class="form-group py-2" style="margin-top: 20px;">
          <button type="submit" class="button button-primary ajax-upload" name="submit">Submit</button>
        <!--  -->
        </div>

        <div class="errors"></div>

    </form>



    <h1 class="test">Corcavian Restricted Files Settings</h1>
    <p>Thank you for using the corcavian Restricted Files plugin. We hope this lightweight plugin helps you with your site</p>
    <h2>Setup</h2>
    <h3>Setup .htaccess to redirect the user when not logged in</h3>
    
    <form method="post" action="options.php">
      <?php settings_fields( 'corcavian_restricted_plugin_options_group' ); ?>
      <table>
        <tr valign="top">
          <th scope="row"><label for="hide_attachment_page">Hide Attachment Page</label></th>
          <td>
            <!-- <input type="text" id="hide_attachment_page" name="hide_attachment_page" value="<?php echo get_option('hide_attachment_page'); ?>" /> -->
            <select name="hide_attachment_page" id="hide_attachment_page">
              <option selected="selected" value="<?php echo get_option('hide_attachment_page') ?>"  hidden><?php echo get_option('hide_attachment_page') ? 'true' : 'false'; ?></option>
              <option value="1">true</option>
              <option value="0">false</option>
            </select>
          </td>
          <td>*Redirects any user that visits the attachment page if true</td>
        </tr>
        <tr>
          <th><label for="supported_filetypes">Supported filetypes</label></th>
          <td><input type="text" id="supported_filetypes" name="supported_filetypes" value="<?php echo get_option('supported_filetypes'); ?>" /></td>
          <td>*Add file extensions here seperated by a space...</td>
        </tr>
        <tr>
          <th><label for="supported_filesize">Max Filesize</label></th>
          <td><input type="text" id="supported_filesize" name="supported_filesize" value="<?php echo get_option('supported_filesize'); ?>" /></td>
          <td>*Edit filesize of the file</td>
        </tr>
      </table>
      <?php  submit_button(); ?>
    </form>

    <!-- <form action=""> -->

    <!-- </form> -->
    <?php
  }

  public function corcavian_register_settings(){
    register_setting( 'corcavian_restricted_plugin_options_group', 'hide_attachment_page'); //, array($this, 'corcavian_register_settings_cb')
    register_setting( 'corcavian_restricted_plugin_options_group', 'supported_filetypes');
    register_setting( 'corcavian_restricted_plugin_options_group', 'supported_filesize');
  }

}

new CORCAVIAN_RESTRICTED_FILES_PLUGIN();