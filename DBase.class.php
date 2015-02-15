<?php 
	
	/**
	 * DBase Class 
	 *
	 * A simple php class for quickly and safely connection to MySQL database using the mysqli extension, useful for simple projects.
	 *
	 * @version : v0.1.0
	 * @author : DibaCode
	 * @author URI : dibacode.wordpress.com
	 * @license : The MIT License (MIT)
	 * @license URI : http://opensource.org/licenses/MIT
	 */
	
	class DBase{
		
		// MySQL hostname.
		public $dbhost ;
		
		// The name of the database.
		public $dbname  ;
		
		// MySQL database username.
		public $dbuser ;
		
		// MySQL database password.
		public $dbpass ;
		
		// Database Charset to creating database tables.
		public $dbcharset ;
		
		// The Database Collate type.
		public $dbcollate ;
		
		
		public $connection = false;
		
		
		
		public $last_query ;
		
		
		
		public $results = array();
		
		
		
		public $rows_affected ;
		
		
		
		public $insert_id ;
		
		
		
		public $debug ;
		
		
		
		public function init( $dbhost, $dbname, $dbuser, $dbpass, $dbcharset, $dbcollate ){
			
			$this->dbhost 	 = ( !empty( $dbhost ) )	? $dbhost 	 : "localhost" ;
			$this->dbname 	 = ( !empty( $dbname ) )	? $dbname 	 : "" ;
			$this->dbuser 	 = ( !empty( $dbuser ) )	? $dbuser 	 : "root" ;
			$this->dbpass 	 = ( !empty( $dbpass ) )	? $dbpass 	 : "" ;
			$this->dbcharset = ( !empty( $dbcharset ) )	? $dbcharset : "utf8" ;
			$this->dbcollate = ( !empty( $dbcollate ) )	? $dbcollate : "utf8_general_ci" ;
			
			if( extension_loaded("mysqli") ){
				$this->connection = @mysqli_connect( $this->dbhost, $this->dbuser, $this->dbpass, $this->dbname );
				
			}else{
			
				die( "<h1 style='color:#5a5a5a;font-weight:normal' >The DBase Class needs to mysqli extension, please install it.</h1>" );
			}

			
			if( $this->connection ){
				// Sets the connection character set.
				mysqli_set_charset( $this->connection, $this->dbcharset );
			
			}
			
		}
		
		
		
		
		public function __destruct(){
			
			if( $this->connection ){
				mysqli_close( $this->connection );
				
			}
		}
		
		
		
		
		
		public function real_escape( &$str="" ){
			
			if( !is_float( $str ) && $this->connection && !empty( $str ) ){
				return $str = mysqli_real_escape_string( $this->connection, $str );
			}
			
			return false;
		}
		
		

		
		public function prepare( $query="", $args=array() ){
			
			if( empty( $query ) || !strpos( $query, "%" ) ){
				return false;
				
			}
			
			$query = str_replace( array('"%s"', "'%s'"), "%s", $query );
			$query = preg_replace( '|(?<!%)%f|' , '%F', $query ); 
			$query = preg_replace( '|(?<!%)%s|', "'%s'", $query ); 
			
			array_walk( $args, array( $this, "real_escape" ) );
			
			return @vsprintf( $query, $args );
		}
		
		
		
		
		public function query( $query_str="" ){
		
			$this->last_query = $query_str;
			$this->rows_affected = 0;
			
			if( $this->connection ){
				
				if( $stmt = mysqli_prepare( $this->connection, $query_str ) ){
					$stmt->execute();
					$this->rows_affected = $stmt->affected_rows;
					
					if( preg_match( '/^\s*(insert|delete|update|replace)\s/i', $query_str, $match ) ){
						$match = strtolower( trim( $match[0], " " ) );
						if( $match == "insert" || $match == "replace" ){
							$this->insert_id = $stmt->insert_id ;
						}
						
					}else{
						$result = $stmt->get_result();
						while ($row = $result->fetch_object()) {
							
							$this->results[] = $row;
						}
						$result->free();
					}
					
					$stmt->close();
					
				}else{
				
					return false;
				}
				
			}else{
				return false;
				
			}
			
			return $this->rows_affected;
		
		}
		
		
		
		
		public function get_results( $query="" ){
			if( $this->query( $query ) === false || empty( $query ) ){
				return null;
				
			}
			
			return $this->results;
		}
		
		
		
		
		
		public function get_charset_collate() {
			$charset_collate = '';

			if ( ! empty( $this->dbcharset ) ){
				$charset_collate = "DEFAULT CHARACTER SET " . $this->dbcharset;
			}
			
			
			if ( ! empty( $this->dbcollate ) ){
				$charset_collate .= " COLLATE " . $this->dbcollate;
			}
			
			
			return $charset_collate;
		}
		
		
		
		
		public function get_error(){
			$error = array();
			
			// The connection errors
			if( mysqli_connect_errno() ){
			
				$error['code'] 	   	= mysqli_connect_errno() ;
				$error['message'] 	= ( $this->debug )? mysqli_connect_error() : "Unknown error.";
				
			// The query errors
			}elseif( mysqli_errno( $this->connection ) ){
			
				$error['code'] 	   	= mysqli_errno( $this->connection );
				$error['query']    	= ( $this->debug )? $this->last_query : "";
				$error['message']  	= ( $this->debug )? mysqli_error( $this->connection ) : "Unknown error.";
				
			}
			
			return $error;
		}
		
		
		
		
		public function print_error(){
			
			if( $error = $this->get_error() ){
				$error_message = "<pre style='color:red' >";
				
				$error_message .= 'Error Code: ' . $error['code'] . "<br />";
				$error_message .= 'Error Message: ' . $error['message'] . "<br />";
				if( isset( $error['query'] ) ){
					$error_message .= '<span style="color:green" >Last Query: ' . $error['query'] . "</span><br />";
				}
				$error_message .= "</pre>";
				
				die( $error_message );
			}
		}
	
		
		
		
	}

	
	$dbclass = new DBase();

?>