<?php

	require_once("../bd_api.php");

	

	class bd_viajeros extends BD_API {

		public function analiza_method() {
			$func = $this->function;
			$this->$func(); // espera que la URL sea del estilo : ..../personal/bd_personal.php/login
		}

		

		private function findViajerosFilter() {
				$id=( isset($_REQUEST['id'])?$_REQUEST['id']:NULL);
				$idEstancia=( isset($_REQUEST['idEstancia'])?$_REQUEST['idEstancia']:NULL);
				$sql = "select * from viajeros where  " .
				    " id is not null " .
				    
				($id != NULL ? " AND (id = :id )  " : "") .
				($idEstancia != NULL ? " AND (idEstancia = :idEstancia )  " : "") ;

				$sql .= " ORDER BY id"; 

				$stmt = ($this->link)->prepare($sql);
				if (strpos($sql, ':id ') !== false) $stmt->bindValue(':id',  $id);
	            if (strpos($sql, ':idEstancia ') !== false) $stmt->bindValue(':idEstancia',  $idEstancia);

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
	       $sql = "delete from viajeros where id = :id";
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
	        $sql="select id from viajeros where id=:id";
            $stmt = ($this->link)->prepare($sql);
            $stmt->bindParam(':id',  $id);
            if  ($stmt->execute()) {
                $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($result)<=0)  {// no existe , lo creo
                    $sql="insert into viajeros ";
                    $where="";
                } else { // si que existe, actualizo
                    $sql="update viajeros ";
                    $where=" where id=:id ";
                }
            }
                
			$sql = $sql.
                "set idEstancia=:idEstancia, dni=:dni, pasaporte=:pasaporte, TipoDoc=:TipoDoc, FechaExp=:FechaExp, PrimerApellido=:PrimerApellido, ".
                    "SegundoApellido=:SegundoApellido, Nombre=:Nombre, sexo=:sexo, FechaNac=:FechaNac, PaisNac=:PaisNac, FechaEntrada=:FechaEntrada " .
                 // ",user='".(isset($_SESSION['login'])?$_SESSION['login']:'system')."',ts=now() ".
                $where;            
			$stmt = ($this->link)->prepare($sql);

            if (isset($_REQUEST['id'])) $stmt->bindValue(':id',  $id);
            $stmt->bindValue(':idEstancia',  $_REQUEST['idEstancia']);
			$stmt->bindValue(':dni',  $_REQUEST['dni']);
			$stmt->bindValue(':pasaporte',  $_REQUEST['pasaporte']);
			$stmt->bindValue(':TipoDoc',  $_REQUEST['TipoDoc']);
			$stmt->bindValue(':FechaExp',  $_REQUEST['FechaExp']);
			$stmt->bindValue(':PrimerApellido',  $_REQUEST['PrimerApellido']);
			$stmt->bindValue(':SegundoApellido',  $_REQUEST['SegundoApellido']);
			$stmt->bindValue(':Nombre',  $_REQUEST['Nombre']);
			$stmt->bindValue(':sexo',  $_REQUEST['sexo']);
			$stmt->bindValue(':FechaNac',  $_REQUEST['FechaNac']);
			$stmt->bindValue(':PaisNac',  $_REQUEST['PaisNac']);
			$stmt->bindValue(':FechaEntrada',  $_REQUEST['FechaEntrada']);

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

	

	$api = new bd_viajeros();

	$api->analiza_method();

?>