<?php
 	require_once("../bd_api.php");
 	
	class bd_personal extends BD_API {
	
   	
		
		public function analiza_method() {
			$func = $this->function;
			$this->$func(); // espera que la URL sea del estilo : ..../personal/bd_personal.php/login
		}


		private function login() {
				$sql = "select * from personal where login=:login and password=:password" ;

				$stmt = ($this->link)->prepare($sql);

				$stmt->bindParam(':login',  $_REQUEST['login']);
				$stmt->bindParam(':password',  $_REQUEST['password']);
				$_SESSION['login']=$_REQUEST['login'];
				try {
					if  ($stmt->execute()) {
						$result= $stmt->fetchAll(PDO::FETCH_ASSOC);
						echo json_encode($result);
					}
				}
				catch ( PDOException $Exception)  {
					die( $Exception->getMessage( )  );
				}
		}
		
		private function logout() {
		    unset($_SESSION['login']) ;
		    session_destroy();
		    session_commit();
		    echo "{ \"result\": \"OK\"}";
		}
		
		private function findUsuariosFilter() {
		        $nombre=( isset($_REQUEST['nombre'])?$_REQUEST['nombre']:NULL);
				$id=( isset($_REQUEST['id'])?$_REQUEST['id']:NULL);
				$sql = "select * from personal where  " .
				    " id is not null " .

					($id != NULL ? " AND (id = :id)  " : "") .
					($nombre != NULL ? " AND (nombre like :nombre)  " : "")
					;

				$sql .= " ORDER BY id"; 

				$stmt = ($this->link)->prepare($sql);

				if (strpos($sql, ':id') !== false) $stmt->bindValue(':id',  $id);
				if (strpos($sql, ':nombre') !== false) $stmt->bindValue(':nombre',  '%'.$nombre.'%');

				try {

					if  ($stmt->execute()) {
						$result= $stmt->fetchAll(PDO::FETCH_ASSOC);
                        echo json_encode($result);
					}
				}
				catch ( PDOException $Exception)  {
					die( $Exception->getMessage( )  );
				}
		}
		
        private function deleteBD() {
	       $sql = "delete from personal where id = :id";
	       $stmt = ($this->link)->prepare($sql);
	       $stmt->bindValue(':id',  $_REQUEST['id']);
	       
	       try {
	            $stmt->execute() ;
	            echo '{ "result": "OK" }';
	       }   
            catch ( PDOException $Exception)  {
                die( $Exception->getMessage( )  );
            }
	    }
	    
		private function guardarBD() {
		        $id = (isset($_REQUEST['id']) ? $_REQUEST['id'] : -1);
		        $sql="select id from personal where id=:id";
	            $stmt = ($this->link)->prepare($sql);
	            $stmt->bindParam(':id',  $id);
	            if  ($stmt->execute()) {
	                $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
	                if (count($result)<=0)  {// no existe , lo creo
	                    $sql="insert into personal ";
	                    $where="";
	                } else { // si que existe, actualizo
	                    $sql="update personal ";
	                    $where=" where id=:id ";
	                }
	            }
	                
				$sql = $sql.
	                "set nombre=:nombre, login=:login, password=:password".
                    $where;            
				$stmt = ($this->link)->prepare($sql);

	            if (isset($_REQUEST['id'])) $stmt->bindValue(':id',  $id);
				$stmt->bindValue(':nombre',  $_REQUEST['nombre']);
				$stmt->bindValue(':login',  $_REQUEST['login']);
				$stmt->bindValue(':password',  $_REQUEST['password']);

				try {
					$stmt->execute() ;
	                $lastId= $this->link->lastInsertId();
	                echo "{\"id\":".$lastId."}";
				}
				catch ( PDOException $Exception)  {
					die( $Exception->getMessage( )  );
				}		
		}
		

	} // fin de class
	
	

	// Initiiate Library
	$api = new bd_personal();
	
	$api->analiza_method();

?>