<?php 
 use Clses\ImagesUploaderCron ;

function getLinksForImageUploading($limit )
{

        
          $cron = ImagesUploaderCron::get_instance() ;
          $cron->upload_image_scheduled_function($limit) ;
         
}





function autoImageUploader(WP_REST_Request $request)
{ 
    
  $limit =  (int) esc_sql($request['limit']);

    
  try{ 
     
      getLinksForImageUploading($limit) ; 
     
    }catch(Error $e )
    {
        
        
    
    
        error_log(' Error:  ' .  $e->getMessage() . "at line " . $e->getLine() . "  time: " . current_time('mysql'));
     
        return new WP_Error( 'ERROR', __( $e->getMessage() . "at line " . $e->getLine() ),
                             array( 
                                 'status'  => 436 , 
                                   ) );
        
        
    } catch (Exception $e) 
    {
   
    
    
     error_log(' Error:  ' .  $e->getMessage()  . "at line " . $e->getLine() . "  time: "  . current_time('mysql'));
     
        return new WP_Error( 'ERROR', __( $e->getMessage() . "at line " . $e->getLine() ."  time : " . current_time('mysql') ),
                             array( 
                                 'status'  => 436 , 
                                   ) );
    
    
    
    }
    
     
     
   
     
     
        
    
}





