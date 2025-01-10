<?php 
use Clses\AutoImageUploader ;



 /*
Plugin Name: Auto Image Uploader
Plugin URI: http://example.com/wordpress-plugins/auto-image-uploader
Description: A powerful and efficient tool designed for internal company use, streamlining the automatic upload and management of images within WordPress. This plugin enhances productivity by integrating seamlessly with the existing infrastructure of App-Impuls.
Version: 1.0
Author: David Kahadze
Author URI: http://example.com
*/


// plugins_url() — Full plugins directory URL 
// includes_url() — Full includes directory URL 
// content_url() — Full content directory URL 
// admin_url() — Full admin URL (for example, 
// site_url() — Site URL for the current site 
// home_url() — Home URL for the current site 


if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define('AUTO_CHEMA_NAME' , 'payitbackend' );
define('AUTO_IMAGE_PLUGIN_DIR_PATH' , plugin_dir_path( __FILE__ ) );
require( AUTO_IMAGE_PLUGIN_DIR_PATH .'functions.php' ) ; 
require( AUTO_IMAGE_PLUGIN_DIR_PATH .'routes.php' ) ; 
require( AUTO_IMAGE_PLUGIN_DIR_PATH .'route_callbackes.php' ) ; 

  $imageUploader = AutoImageUploader::get_instance() ;
  register_activation_hook(__FILE__, array( $imageUploader  , 'activate'));
  


