<?php
namespace Clses;
use Tra\AutoImageUploaderQueryFunctions ;

class ImagesUploaderCron
{
    use AutoImageUploaderQueryFunctions ;
    /**
     * Instance of this class.
     *
     * @since 1.0.0
     * @var object|null
     */
    protected static $instance = null;
    
    protected static  bool $permisson ;

    /**
     * Constructor: Adds the required actions and filters.
     */
    public function __construct(bool $permission_to_use_wordpress_built_in_cron = false )
    {
        self::$permisson =  $permission_to_use_wordpress_built_in_cron ;
        
        if( self::$permisson == true  ){
        // add_action('upload_image_cron_hook', array($this, 'upload_image_scheduled_function'));
        // add_filter('cron_schedules', array($this, 'five_minutes_cron_schedule'));
        }
    }

    /**
     * The function to be executed on the cron schedule.
     * 
     * @since 1.0.0
     */
    public function upload_image_scheduled_function( int $limit = 50 )
    {
        // Your code here
        
        
        $all_links = $this->getAllImageLinks($limit);
        
        if( !is_null($all_links) && count($all_links) > 0  )
        {
            foreach($all_links as $key => $url )
            {
                $local_url =  $this->getGestImage( $url->fileLink ) ;
                $imgData   = array(
                        "id"          => $url->id    ,
                        "fileLink"    => $local_url  ,
                        "type"        => $url->type  ,
                        "ref"         => $url->ref   ,
                        "fileTypeRef" => $url->fileTypeRef );        
                $last = $this->insert_ImageToFileRef_table( $imgData );  
                
                // echo '<br><pre> ' ;
                
                // var_dump(  $last ) ; 
                
                // echo   '</pre><br>' ;
            }
        }
    }

    /**
     * Returns an instance of this class (Singleton Pattern).
     *
     * @since 1.0.0
     * @return object A single instance of this class.
     */
    public static function get_instance(bool $permission = false )
    {
        // If the single instance hasn't been set, set it now.
        if (null === self::$instance) 
        {
            self::$instance = new self($permission);
        }

        return self::$instance;
    }
    

    /**
     * Schedules the cron event for uploading images.
     *
     * @since 1.0.0
     */
    public static function activate()
    {
        if (!wp_next_scheduled('upload_image_cron_hook')) {
            // wp_schedule_event(time(), 'every_five_minutes', 'upload_image_cron_hook');
            // error_log('Activation of Auto Image Uploader Class: ' . current_time('mysql'));
        }
    }

    /**
     * Adds a custom schedule interval of five minutes.
     *
     * @since 1.0.0
     * @param array $schedules Existing cron schedules.
     * @return array Modified cron schedules.
     */
    public function five_minutes_cron_schedule($schedules)
    {
        // $schedules['every_five_minutes'] = array(
        //     'interval' => 300, // Time in seconds (5 minutes)
        //     'display'  => __('Every Five Minutes'),
        // );
        // return $schedules;
    }

    /**
     * Unschedules the cron event on deactivation.
     *
     * @since 1.0.0
     */
    public static function deactivate()
    {
      if( self::$permisson == true  )
      {
        // $timestamp = wp_next_scheduled('upload_image_cron_hook');
        // if ($timestamp) {
        //     wp_unschedule_event($timestamp, 'upload_image_cron_hook');
        //     error_log('Deactivation of Auto Image Uploader Class: ' . current_time('mysql'));
        // }
      }
    }
    
    
    
    public function  getGestImage($url)
    {
		// If the function it's not available, require it.
            	if(!function_exists('download_url'))
            	{
            		require_once ABSPATH . 'wp-admin/includes/file.php';
            	}
            
                //echo "manageGuestImage2 $url";
                $timeout_seconds = 15;
                $info = pathinfo($url);
                $ext  = empty($info['extension'] ) ? '' : '.' . $info['extension'];
                $name = basename(md5(rand()), $ext);
            
                // Download file to temp dir
                $temp_file = download_url( $url, $timeout_seconds );
                //echo "temp_file $temp_file";
                if ( !is_wp_error( $temp_file ) ) {
                    // Array based on $_FILE as seen in PHP file uploads
                    $file = array(
                        'name'     =>  $name  . $ext, // ex: wp-header-logo.png
                        'type'     => 'image/png',
                        'tmp_name' => $temp_file,
                        'error'    => 0,
                        'size'     => filesize($temp_file),
                    );
                
                    $overrides = array(
                        // Tells WordPress to not look for the POST form
                        // fields that would normally be present as
                        // we downloaded the file from a remote server, so there
                        // will be no form fields
                        // Default is true
                        'test_form' => false,
                
                        // Setting this to false lets WordPress allow empty files, not recommended
                        // Default is true
                        'test_size' => true,
                    );
                
                    // Move the temporary file into the uploads directory
                    $results = wp_handle_sideload( $file, $overrides );
                    
                    if(!empty( $results['error']))
                    {
                        // Insert any error handling here
                        throw new Exception($results['error']);
                    } else 
                    {
                        $filename  = $results['file']; // Full path to the file
                        $local_url = $results['url'];  // URL to the file in the uploads dir
                        $type      = $results['type']; // MIME type of the file
                        return $local_url;
                        // Perform any actions here based in the above results
                    }
                }
  }
  
  
  
  
  
  
}

