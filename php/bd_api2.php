<?php
     session_set_cookie_params(0,'/;samesite=None;Secure');
     session_start();
     
 	require_once("../bd_properties.php"); // constantes BD
 	
class BD_API  { 
		protected $method="";
		protected $input=NULL;
		protected $link=NULL;
		protected $table="";
		protected $key="";
		protected $stmt=NULL;
		protected $function="";
		
       public function __construct(){
            ini_set('display_errors', 'off');
           		
			// get the HTTP method, path and body of the request
    			$this->method = $_SERVER['REQUEST_METHOD'];
    			
  			// connect to the mysql database
			$this->link = new PDO("mysql:host=".DB_SERVER.";dbname=".DB.";charset=utf8", DB_USER, DB_PASSWORD);
			// set the PDO error mode to exception
			($this->link)->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
			($this->link)->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
			($this->link)->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
			($this->link)->exec("SET lc_time_names = 'es_ES'");

			$this->input = json_decode(file_get_contents('php://input'),true);
			// retrieve the table and key from the path. la URL debe tener el formato:  path/bd_api.php/{table}/{id}  , ejem: php/lib/bd_api.php/suscripcioncurso/
			$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
			$this->function= preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
			$this->key = array_shift($request)+0;
			if ($this->function!="login" && (!isset($_SESSION['login']) || $_SESSION['login']=="")) {
			    header('HTTP/1.1 500 La sesiиоn ha caducado. Debe volver a identificarse');
    			die("La sesion ha caducado. Debe volver a identificarse");
			}

		}
		
		 public function __destruct(){
		 // close mysql connection
		  $this->link=NULL;
		}
	}
	
	
	// tratamiento de CORS

	if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: ". $_SERVER['HTTP_ORIGIN']); // jv. 19.2.2020 para permitir conectarse desde otras URLs
	} else {
	    header("Access-Control-Allow-Origin: *");
	}
	header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
?>