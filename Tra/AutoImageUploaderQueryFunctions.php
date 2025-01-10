<?php 
 namespace Tra ;
 
/**
 * CONF Auto Image Uploader .
 *
 * @package   Auto Image Uploader 
 * @author    CONF_David_Kahadze 
 * @license   Private 
 * @link      CONF_Author_Link
 * @copyright CONF_Plugin_Copyright
 */

/**
 *-----------------------------------------
 * Do not delete this line
 * Added for security reasons: http://codex.wordpress.org/Theme_Development#Template_Files
 *-----------------------------------------
 */
defined('ABSPATH') or die("Direct access to the script does not allowed");
/*-----------------------------------------*/
 
 
 trait AutoImageUploaderQueryFunctions
 {
     
     public function getAllImageLinks(int $limit = 50 )
     {
         global $wpdb ; 
         $sql           = ' SELECT * FROM awaiting_file_links WHERE status = 0  LIMIT %d ;' ;
         $query         = $wpdb->prepare($sql , $limit ) ;
        return  $result = $wpdb->get_results($query ) ;
     }
     
     
     
     function insert_ImageToFileRef_table( array $data  )
     {
         global $wpdb ;
         $sql           =  "Call insertInFileRef(  %d ,  %s ,  %s ,  %d , %d ,  1 , @error_id ,  @error_message );" ;
         $query    = $wpdb->prepare($sql , $data['id'] , $data['fileLink'] , $data['type'] , $data['ref'] , $data['fileTypeRef'] ) ;
         $result        = $wpdb->query($query );  
         $error         = $wpdb->get_row(" SELECT @error_id as error_id ,  @error_message  AS error_message ; ") ;
         
         if( !is_null( $error->error_id ) )
         {
             
              throw new Exception( "error_id :" . $error->error_id . " message : " . $error->error_message  );
              
         }
                  
          return  $result ;        
         
         
     }
     
 }