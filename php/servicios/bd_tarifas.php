<?php

	require_once("../bd_api.php");

	class bd_tarifas extends BD_API {

		public function analiza_method() {
			$func = $this->function;
			$this->$func(); // espera que la URL sea del estilo : ..../personal/bd_personal.php/login
		}

		

		private function findTarifasFilter() {
				$id=( isset($_REQUEST['id'])?$_REQUEST['id']:NULL);
				$sql = "select * from tarifas where  " .
				    " id is not null " .

					($id != NULL ? " AND (id = :id)  " : "") 	;

				$sql .= " ORDER BY id"; 

				$stmt = ($this->link)->prepare($sql);

				if (strpos($sql, ':id') !== false) $stmt->bindParam(':id',  $id);

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
	       $sql = "delete from tarifas where id = :id";
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
		        $sql="select id from tarifas where id=:id";
	            $stmt = ($this->link)->prepare($sql);
	            $stmt->bindParam(':id',  $id);
	            if  ($stmt->execute()) {
	                $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
	                if (count($result)<=0)  {// no existe , lo creo
	                    $sql="insert into tarifas ";
	                    $where="";
	                } else { // si que existe, actualizo
	                    $sql="update tarifas ";
	                    $where=" where id=:id ";
	                }
	            }
	                
				$sql = $sql.
	                "set tipoServicio=:tipoServicio, idServicio=:idServicio, tipoTarifa=:tipoTarifa, Importe=:Importe, comentario=:comentario ".
                    $where;            
				$stmt = ($this->link)->prepare($sql);

	            if (isset($_REQUEST['id'])) $stmt->bindValue(':id',  $id);
				$stmt->bindValue(':tipoServicio',  $_REQUEST['tipoServicio']);
				$stmt->bindValue(':idServicio',  $_REQUEST['idServicio']);
				$stmt->bindValue(':tipoTarifa',  $_REQUEST['tipoTarifa']);
				$stmt->bindValue(':Importe',  $_REQUEST['Importe']);
				$stmt->bindValue(':comentario',  $_REQUEST['comentario']);

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

	

	$api = new bd_tarifas();

	$api->analiza_method();

?>