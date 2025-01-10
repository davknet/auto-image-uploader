<?php
namespace Clses ;
use Tra\ActivatePluginTablesFunctions;
/**
 * CONF Auto Image Uploader .
 *
 * @package  Auto Image Uploader 
 * @author   David Kahadze 
 * @license  Private 
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

class AutoImageUploader 
{
    use ActivatePluginTablesFunctions ;
    
    
     /**
     * Plugin require database tables
     *
     * @since   1.0.0
     *
     * @var     array 
     */
     protected $tables;
     
     /**
      * 
      * 
      * 
      * 
      * 
      */
     
     protected $pref_x; 
     
     /**
      * 
      * 
      * 
      * 
      * 
      */
     
     
     protected $customerTable_exists;
     /**
      * 
      * 
      * 
      * 
      * 
      */
     protected $meta_orderTable_exists;
     
     
     /**
      * 
      * 
      * 
      * 
      * 
      */
     
     private array $databases = array(
         
         'fileType' , 
         'awaiting_file_links' ,

        );
    
    
    private array $procedures = array(
        
        
           'insertInFileRef' ,
        
        );
    
    
    
    
    
    

    /**
     * Plugin version name
     *
     * @since   1.0.0
     *
     * @var     string
     */
    private static $VERSION_NAME = 'auto_image_uploader_version';

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   1.0.0
     *
     * @var     string
     */
    private static $VERSION = '1.0.0';

    /**
     * Unique identifier for your plugin.
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * plugin file.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    private static $PLUGIN_SLUG = 'auto-image-uploader';

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since     1.0.0
     */
    private function __construct()
    {
        $this->tables = $this->getAllTables();
        $this->pref_x = $this->getPrefix();
        
        // Load plugin text domain
        add_action('init', array($this, 'load_plugin_textdomain'));

        // Activate plugin when new blog is added
        add_action('wpmu_new_blog', array($this, 'activate_new_site'));
        
         if (!is_null($this->tables)) 
         {
            $this->install_AutoImageUploader_Required_Tables();
         }
         
         if( count($this->procedures) > 0 )
         {
             foreach($this->procedures as $key => $name )
             {
                if(!$this->ensureProcedureExists( $name ) )
                {
                    $function_name = 'create_'.$name .'_procedure' ; 
                    $this->$function_name($name);
                }
             }
         }

    }
    

    /**
     * Return the plugin slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_plugin_slug()
    {
        return self::$PLUGIN_SLUG;
    }

    /**
     * Return the plugin version.
     *
     * @since    1.0.0
     *
     * @return    Plugin version variable.
     */
    public function get_plugin_version()
    {
        return self::$VERSION;
    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance()
    {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Fired when the plugin is activated.
     *
     * @since    1.0.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Activate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       activated on an individual blog.
     */
    public static function activate($network_wide)
    {

        if (function_exists('is_multisite') && is_multisite()) {

            if ($network_wide) 
            {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) 
                {

                    switch_to_blog($blog_id);
                    self::single_activate();
                }

                restore_current_blog();

            }else 
            {
                self::single_activate();
            }

        } else
        {
            
             if (version_compare(get_bloginfo('version'), '6.1', '<')) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
                deactivate_plugins(plugin_basename(__FILE__));
                error_log('Activations Of Auto Image Uploader Plugin : ' . current_time('This plugin requires WordPress 6.1 or greater. Sorry about that.'));
                wp_die(__('This plugin requires WordPress 6.1 or greater. Sorry about that.', 'Auto Image Uploader'));
                return;
            }
    
            if (version_compare(phpversion(), '7.0.0', '<')) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
                deactivate_plugins(plugin_basename(__FILE__));
                error_log('Activations Of Auto Image Uploader Plugin : ' . current_time('This plugin requires PHP 7.0.0 or greater. Sorry about that.'));
                wp_die(__('This plugin requires PHP 7.0.0 or greater. Sorry about that.', 'Auto Image Uploader'));
                return;
            } 

       
            self::single_activate();
        }

    }

    /**
     * Fired when the plugin is deactivated.
     *
     * @since    1.0.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Deactivate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       deactivated on an individual blog.
     */
    public static function deactivate($network_wide)
    {

        if (function_exists('is_multisite') && is_multisite()) 
        {

                if ($network_wide) 
                {
                    // Get all blog ids
                    $blog_ids = self::get_blog_ids();
                    foreach ($blog_ids as $blog_id)
                    {
    
                        switch_to_blog($blog_id);
                        self::single_deactivate();
    
                    }
    
                    restore_current_blog();
    
                }else 
                {
                    self::single_deactivate();
                }
    
        }else 
        {
            
  
            
            
            
            
            
                self::single_deactivate();
        }

    }

    /**
     * Fired when a new site is activated with a WPMU environment.
     *
     * @since    1.0.0
     *
     * @param    int    $blog_id    ID of the new blog.
     */
    public function activate_new_site($blog_id)
    {

        if (1 !== did_action('wpmu_new_blog')) {
            return;
        }

        switch_to_blog($blog_id);
        self::single_activate();
        restore_current_blog();

    }

    /**
     * Get all blog ids of blogs in the current network that are:
     * - not archived
     * - not spam
     * - not deleted
     *
     * @since    1.0.0
     *
     * @return   array|false    The blog ids, false if no matches.
     */
    private static function get_blog_ids()
    {

        global $wpdb;

        // get an array of blog ids
        $sql = "SELECT blog_id FROM $wpdb->blogs
            WHERE archived = '0' AND spam = '0'
            AND deleted = '0'";

        return $wpdb->get_col($sql);

    }

    /**
     * Fired for each blog when the plugin is activated.
     *
     * @since    1.0.0
     */
    private static function single_activate()
    {
        update_option(self::$VERSION_NAME, self::$VERSION);

        // @TODO: Define activation functionality here
    }
    
    
    
     public   function install_AutoImageUploader_Required_Tables(bool $prefix = false)
    {
        foreach ($this->databases as $name) {
            if ($this->ensure_not_exists($name, $prefix)) {
                    $name_new = ($prefix) ? $this->pref_x . $name : $name;
                    $function_name = 'create_' . $name ."_table";
                    if (method_exists($this, $function_name)) {
                        $this->$function_name($name_new);
                    } else {
                        error_log("Method $function_name does not exist");
                    }
            } 
        }
    }
    
    public function ensure_not_exists(string $name, bool $prefix = false)
    {
        $name = ($prefix) ? $this->pref_x . $name : $name;
        return array_search($name, $this->tables) === false;
    }
    
    

    /**
     * Fired for each blog when the plugin is deactivated.
     *
     * @since    1.0.0
     */
    private static function single_deactivate()
    {
        // @TODO: Define deactivation functionality here
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain()
    {

        $domain = self::$PLUGIN_SLUG;
        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, false, basename(plugin_dir_path(dirname(__FILE__))) . '/languages/');

    }
    
    

}
