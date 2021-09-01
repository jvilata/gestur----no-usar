<?php

	require_once("../bd_api.php");

	

	class bd_servicios extends BD_API {

		public function analiza_method() {
			$func = $this->function;
			$this->$func(); // espera que la URL sea del estilo : ..../personal/bd_personal.php/login
		}

		

		private function findServiciosFilter() {
		        $descripcionCorta=( isset($_REQUEST['descripcionCorta'])?$_REQUEST['descripcionCorta']:NULL);
		        $descripcionLarga=( isset($_REQUEST['descripcionLarga'])?$_REQUEST['descripcionLarga']:NULL);
				$id=( isset($_REQUEST['id'])?$_REQUEST['id']:NULL);
                $tipoServicio=( isset($_REQUEST['tipoServicio'])?$_REQUEST['tipoServicio']:NULL);
				$sql = "select * from servicios where  " .
				    " id is not null " .

				    ($tipoServicio != NULL ? " AND (tipoServicio = :tipoServicio)  " : "") .
					($id != NULL ? " AND (id = :id)  " : "") .
					($descripcionCorta != NULL ? " AND (descripcionCorta like :descripcionCorta)  " : "") .
					($descripcionLarga != NULL ? " AND (descripcionLarga like :descripcionLarga)  " : "")
					;

				$sql .= " ORDER BY descripcionCorta"; 

				$stmt = ($this->link)->prepare($sql);

				if (strpos($sql, ':tipoServicio') !== false) $stmt->bindValue(':tipoServicio',  $tipoServicio);
				if (strpos($sql, ':id') !== false) $stmt->bindParam(':id',  $id);
				if (strpos($sql, ':descripcionCorta') !== false) $stmt->bindValue(':descripcionCorta',  '%'.$descripcionCorta.'%');
				if (strpos($sql, ':descripcionLarga') !== false) $stmt->bindValue(':descripcionLarga',  '%'.$descripcionLarga.'%');

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
	       $sql = "delete from servicios where id = :id";
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
		        $sql="select id from servicios where id=:id";
	            $stmt = ($this->link)->prepare($sql);
	            $stmt->bindParam(':id',  $id);
	            if  ($stmt->execute()) {
	                $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
	                if (count($result)<=0)  {// no existe , lo creo
	                    $sql="insert into servicios ";
	                    $where="";
	                } else { // si que existe, actualizo
	                    $sql="update servicios ";
	                    $where=" where id=:id ";
	                }
	            }
	                
				$sql = $sql.
	                "set tipoServicio=:tipoServicio, descripcionCorta=:descripcionCorta, descripcionLarga=:descripcionLarga, tipoIva=:tipoIva ".
                    $where;            
				$stmt = ($this->link)->prepare($sql);

	            if (isset($_REQUEST['id'])) $stmt->bindValue(':id',  $id);
				$stmt->bindValue(':tipoServicio',  $_REQUEST['tipoServicio']);
				$stmt->bindValue(':descripcionCorta',  $_REQUEST['descripcionCorta']);
				$stmt->bindValue(':descripcionLarga',  $_REQUEST['descripcionLarga']);
				$stmt->bindValue(':tipoIva',  $_REQUEST['tipoIva']);

				try {
					$stmt->execute() ;
	                $lastId= $this->link->lastInsertId();
	                echo "{\"id\":".$lastId."}";
				}
				catch ( PDOException $Exception)  {
					die( $Exception->getMessage( )  );
				}		
		}
		
		private function calcularTarifaServicio () { // parametros: $idServicio, $tipoServ, $tipoTarifa (alta,media,baja)
		    $vTarifa = 0 ;
		    $mayorTarifa = 0;
		    $idServicio = (isset($_REQUEST['idServicio']) ? $_REQUEST['idServicio'] : -1);
		    $tipoServ = (isset($_REQUEST['tipoServ']) ? $_REQUEST['tipoServ'] : -1);
		    $tipoTarifa = (isset($_REQUEST['tipoTarifa']) ? $_REQUEST['tipoTarifa'] : -1);
		    
	        $sql="select * from tarifas where idServicio=:idServicio";
            $stmt = ($this->link)->prepare($sql);
            $stmt->bindParam(':idServicio', $idServicio);
            $stmt->execute();
            $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($result)>0) { //  existe
              $i=0;
              while ($i<count($result)) {
               if ($result[$i]['Importe'] > $mayorTarifa) { $mayorTarifa = $result[$i]['Importe']; } // ' siempre me quedo la mayor
               if ($result[$i]['tipoTarifa'] === $tipoTarifa) { // encuentro tarifa temp alta, media, baja
                 $vTarifa = $result[$i]['Importe'];
               }   
               $i++;
              }
            } else {
              $sql="select * from tarifas where (idServicio is null or idServicio=0) and tipoServicio=:tipoServ";
              $stmt = ($this->link)->prepare($sql);
              $stmt->bindParam(':tipoServ', $tipoServ);
              $stmt->execute();
              $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
              if (count($result)>0) { //  existe   
                  $i=0;
                  while ($i<count($result)) {
                   if ($result[$i]['Importe'] > $mayorTarifa) { $mayorTarifa = $result[$i]['Importe']; } // ' siempre me quedo la mayor
                   if ($result[$i]['tipoTarifa'] === $tipoTarifa) { // encuentro tarifa temp alta, media, baja
                     $vTarifa = $result[$i]['Importe'];
                   }   
                   $i++;
                  }
              } else { // no existe
                  $vTarifa = 0;
              }
            }
            if ($vTarifa <= 0) { // mo hemos encontrado la exacta
                $vTarifa = $mayorTarifa;
            }
            echo '[{ "tarifa": '.$vTarifa." }]";
            return $vTarifa;
		}

	}

	

	// Initiiate Library

	

	$api = new bd_servicios();

	$api->analiza_method();

?>