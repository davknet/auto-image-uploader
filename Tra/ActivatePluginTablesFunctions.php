<?php
namespace Tra ;






defined('ABSPATH') or die("Direct access to the script does not allowed");


trait ActivatePluginTablesFunctions
{
     public function getAllTables()
    {
        global $wpdb;
        $sql = 'SHOW TABLES;';
        $result = $wpdb->get_results($sql, ARRAY_A);
        $tables = array_column($result, 'Tables_in_' . DB_NAME);
        return $tables;
    }
    
    public function getPrefix():string 
    {
        global $wpdb ;
        return $wpdb->prefix ;
    }
    
    
    
       public function ensureProcedureExists(string $name): bool 
        {
            global $wpdb;
            
            $sql    = 'SHOW PROCEDURE STATUS WHERE `Name` = %s;';
            $query  = $wpdb->prepare($sql, $name);
            $result = $wpdb->get_row($query);
            
            if (is_null($result)) {
                return false;
            }
            
            return true;
        }
    

    
    public function create_salomon_table(string $name )
    {
        
        global $wpdb;
        $sql = "CREATE TABLE $name  ( id INT AUTO_INCREMENT PRIMARY KEY , name  VARCHAR(255) )";
        return  $wpdb->query($sql);   
    }
    
       public function create_fileType_table(string $name )
    {
        global $wpdb ;
        $sql = "CREATE TABLE $name (
                                id INT(11) AUTO_INCREMENT PRIMARY KEY ,
                                description	VARCHAR(255),
                                description_English	VARCHAR(255) ,	
                                status	INT(11)	,
                                oType	VARCHAR(255)  );" ;           
             return  $wpdb->query($sql);          
    }
    
    public function create_awaiting_file_links_table(string $name )
    {
        global $wpdb ;
        $sql = "CREATE TABLE $name (
                              id INT(11) AUTO_INCREMENT PRIMARY KEY ,
                              fileLink VARCHAR(2048)	            ,
                              type	VARCHAR(255)                    ,
                              ref	INT(11)	,
                              fileTypeRef INT(11) , 	
                              created	timestamp  DEFAULT current_timestamp() 	, 
                              updated	DATETIME  NULL ,
                              status	TINYINT(1) , 
                              FOREIGN KEY ( fileTypeRef )  REFERENCES fileType(id ) );" ;           
             return  $wpdb->query($sql);          
    }
    
    
    
     public function create_insertInFileRef_procedure(string $name)
            {
                global $wpdb;
            
                // Sanitize the procedure name to avoid SQL injection
                $name = preg_replace('/[^a-zA-Z0-9_]/', '', $name);
            
                $sql = "
                    CREATE PROCEDURE $name
                    (
                        IN IN_id INT,
                        IN In_fileLink VARCHAR(2048),
                        IN In_type     VARCHAR(255),
                        IN In_ref INT,
                        IN In_fileTypeRef INT,
                        IN In_status INT,
                        OUT error_id INT,
                        OUT error_message VARCHAR(255)
                    )
                    BEGIN
                        
                        DECLARE last_id INT DEFAULT NULL;
                        DECLARE EXIT HANDLER FOR SQLEXCEPTION
                        BEGIN
                            GET DIAGNOSTICS CONDITION 1 @errno = MYSQL_ERRNO, @errmsg = MESSAGE_TEXT;
                            SET error_id = @errno;
                            SET error_message = @errmsg;
                            ROLLBACK;
                            SELECT error_id, error_message;
                        END;
                    
                        START TRANSACTION;
                    
                            INSERT INTO fileRef (fileLink, type, ref, fileTypeRef, created, status)
                            VALUES (In_fileLink, In_type, In_ref, In_fileTypeRef, NOW(), In_status);
                            
                            SET last_id = LAST_INSERT_ID();
                    
                            IF (last_id > 0) THEN
                                UPDATE awaiting_file_links 
                                SET status = 1 
                                WHERE id = IN_id;
                    
                                SELECT last_id;
                                COMMIT;
                            ELSE
                                SET error_id = 341;
                                SET error_message = 'Insertion failed: No ID returned';
                                ROLLBACK;
                                SELECT error_id, error_message;
                            END IF;
                    END;
                ";
            
                // Execute the query
                $result = $wpdb->query($sql);
            
                return $result;
            }

}






