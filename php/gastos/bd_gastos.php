<?php

	require_once("../bd_api.php");

	

	class bd_Gastos extends BD_API {

		public function analiza_method() {
			$func = $this->function;
			$this->$func(); // espera que la URL sea del estilo : ..../personal/bd_personal.php/login
		}

		

		private function findGastosFilter() {
				$id=( isset($_REQUEST['id'])?$_REQUEST['id']:NULL);
			    $fechaInicio=(isset($_REQUEST['fechaInicio'])?$_REQUEST['fechaInicio']:NULL);
	            $fechaFin=( isset($_REQUEST['fechaFin'])?$_REQUEST['fechaFin']:NULL);
	            $descripcion=( isset($_REQUEST['descripcion'])?$_REQUEST['descripcion']:NULL);

				$sql = "select * from gastos where  " .
				    " id is not null " .

					($id != NULL ? " AND (id = :id)  " : "").
					($descripcion != NULL ? " AND (descripcion like :descripcion)  " : "") .
	                ($fechaInicio != NULL ? " AND (fecha >= :fechaInicio)  " : "") .
	                ($fechaFin != NULL ? " AND (fecha <= :fechaFin)  " : "") .

				$sql .= " ORDER BY id"; 

				$stmt = ($this->link)->prepare($sql);

				if (strpos($sql, ':id') !== false) $stmt->bindParam(':id',  $id);
				if (strpos($sql, ':descripcion') !== false) $stmt->bindValue(':descripcion',  '%'.$descripcion.'%');//jv.uso bindvalue porque asigno una expresion
                if (strpos($sql, ':fechaInicio') !== false) $stmt->bindValue(':fechaInicio',  $fechaInicio);
                if (strpos($sql, ':fechaFin') !== false) $stmt->bindValue(':fechaFin',  $fechaFin);

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
	       $sql = "delete from gastos where id = :id";
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
		        $sql="select id from gastos where id=:id";
	            $stmt = ($this->link)->prepare($sql);
	            $stmt->bindParam(':id',  $id);
	            if  ($stmt->execute()) {
	                $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
	                if (count($result)<=0)  {// no existe , lo creo
	                    $sql="insert into gastos ";
	                    $where="";
	                } else { // si que existe, actualizo
	                    $sql="update gastos ";
	                    $where=" where id=:id ";
	                }
	            }
	                
				$sql = $sql.
	                "set fecha=:fecha, descripcion=:descripcion, cantidad=:cantidad, factura=:factura, tipoGasto=:tipoGasto ".
                    $where;            
				$stmt = ($this->link)->prepare($sql);

	            if (isset($_REQUEST['id'])) $stmt->bindValue(':id',  $id);
				$stmt->bindValue(':fecha',  $_REQUEST['fecha']);
				$stmt->bindValue(':descripcion',  $_REQUEST['descripcion']);
				$stmt->bindValue(':cantidad',  $_REQUEST['cantidad']);
				$stmt->bindValue(':factura',  $_REQUEST['factura']);
				$stmt->bindValue(':tipoGasto',  $_REQUEST['tipoGasto']);

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

	

	$api = new bd_Gastos();

	$api->analiza_method();

?>