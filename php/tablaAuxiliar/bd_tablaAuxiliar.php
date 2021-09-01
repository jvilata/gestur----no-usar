<?php

	require_once("../bd_api.php");

	

	class bd_tablaAuxiliar extends BD_API {

		public function analiza_method() {
			$func = $this->function;
			$this->$func(); // espera que la URL sea del estilo : ..../personal/bd_personal.php/login
		}

		

		private function findTablaAuxFilter() {
		       $codTabla=( isset($_REQUEST['codTabla'])?$_REQUEST['codTabla']:NULL);
				$id=( isset($_REQUEST['id'])?$_REQUEST['id']:NULL);
				$codElemento=( isset($_REQUEST['codElemento'])?$_REQUEST['codElemento']:NULL);

				$sql = "select * from tablaAuxiliar where  " .
				    " id is not null " .

				    ($codTabla != NULL ? " AND (codTabla = :codTabla)  " : "") .
					($id != NULL ? " AND (id = :id)  " : "") .
					($codElemento != NULL ? " AND (codElemento = :codElemento)  " : "") 
					;

				$sql .= " ORDER BY codElemento"; 

				$stmt = ($this->link)->prepare($sql);

				if (strpos($sql, ':codTabla') !== false) $stmt->bindParam(':codTabla',  $codTabla);
				if (strpos($sql, ':id') !== false) $stmt->bindParam(':id',  $id);
				if (strpos($sql, ':codElemento') !== false) $stmt->bindParam(':codElemento',  $codElemento);

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
	       $sql = "delete from tablaAuxiliar where id = :id";
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
		        $sql="select id from tablaAuxiliar where id=:id";
	            $stmt = ($this->link)->prepare($sql);
	            $stmt->bindParam(':id',  $id);
	            if  ($stmt->execute()) {
	                $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
	                if (count($result)<=0)  {// no existe , lo creo
	                    $sql="insert into tablaAuxiliar ";
	                    $where="";
	                } else { // si que existe, actualizo
	                    $sql="update tablaAuxiliar ";
	                    $where=" where id=:id ";
	                }
	            }
	                
				$sql = $sql.
	                "set codTabla=:codTabla, codElemento=:codElemento, valor1=:valor1".
                    $where;            
				$stmt = ($this->link)->prepare($sql);

	            if (isset($_REQUEST['id'])) $stmt->bindValue(':id',  $id);
				$stmt->bindValue(':codTabla',  $_REQUEST['codTabla']);
				$stmt->bindValue(':codElemento',  $_REQUEST['codElemento']);
				$stmt->bindValue(':valor1',  $_REQUEST['valor1']);

				try {
					$stmt->execute() ;
	                $lastId= $this->link->lastInsertId();
	                echo "{\"id\":".$lastId."}";
				}
				catch ( PDOException $Exception)  {
					die( $Exception->getMessage( )  );
				}		
		}
	}

	

	// Initiiate Library

	

	$api = new bd_tablaAuxiliar();

	$api->analiza_method();

?>