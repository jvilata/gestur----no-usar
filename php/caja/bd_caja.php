<?php

	require_once("../bd_api.php");

	

	class bd_caja extends BD_API {

		public function analiza_method() {
			$func = $this->function;
			$this->$func(); // espera que la URL sea del estilo : ..../personal/bd_personal.php/login
		}

		

		private function findCajaFilter() {
				$id=( isset($_REQUEST['id'])?$_REQUEST['id']:NULL);
				$fechaInicial=( isset($_REQUEST['fechaInicial'])?$_REQUEST['fechaInicial']:NULL);
				$fechaCierre=( isset($_REQUEST['fechaCierre'])?$_REQUEST['fechaCierre']:NULL);
				$observaciones=( isset($_REQUEST['observaciones'])?$_REQUEST['observaciones']:NULL);
				$sql = "select * from cuadreCaja where  " .
				    " id is not null " .

					($id != NULL ? " AND (id = :id)  " : "").
					($fechaInicial != NULL ? " AND (fechaInicial >= :fechaInicial)  " : "") .
					($fechaCierre != NULL ? " AND (fechaCierre <= :fechaCierre)  " : "") .
					($observaciones != NULL ? " AND (observaciones like :observaciones)  " : "") ;

				$sql .= " ORDER BY id"; 

				$stmt = ($this->link)->prepare($sql);

				if (strpos($sql, ':id') !== false) $stmt->bindParam(':id',  $id);
				if (strpos($sql, ':observaciones') !== false) $stmt->bindValue(':observaciones',  '%'.$observaciones.'%');//jv.uso bindvalue porque asigno una expresion
				if (strpos($sql, ':fechaInicial') !== false) $stmt->bindValue(':fechaInicial',  $fechaInicial);
				if (strpos($sql, ':fechaCierre') !== false) $stmt->bindValue(':fechaCierre',  $fechaCierre);

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
	       $sql = "delete from cuadreCaja where id = :id";
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
		        $sql="select id from cuadreCaja where id=:id";
	            $stmt = ($this->link)->prepare($sql);
	            $stmt->bindParam(':id',  $id);
	            if  ($stmt->execute()) {
	                $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
	                if (count($result)<=0)  {// no existe , lo creo
	                    $sql="insert into cuadreCaja ";
	                    $where="";
	                } else { // si que existe, actualizo
	                    $sql="update cuadreCaja ";
	                    $where=" where id=:id ";
	                }
	            }
	                
				$sql = $sql.
	                "set fechaCierre=:fechaCierre, fechaInicial=:fechaInicial, facturacionPeriodo=:facturacionPeriodo, recaudacionCaja=:recaudacionCaja, cajaPendiente=:cajaPendiente, gastosCajaPeriodo=:gastosCajaPeriodo, observaciones=:observaciones ".
	                 ",user='".(isset($_SESSION['login'])?$_SESSION['login']:'system')."',ts=now() ".
                    $where;            
				$stmt = ($this->link)->prepare($sql);

	            if (isset($_REQUEST['id'])) $stmt->bindValue(':id',  $id);
				$stmt->bindValue(':fechaCierre',  $_REQUEST['fechaCierre']);
				$stmt->bindValue(':fechaInicial',  $_REQUEST['fechaInicial']);
				$stmt->bindValue(':facturacionPeriodo',  $_REQUEST['facturacionPeriodo']);
				$stmt->bindValue(':recaudacionCaja',  $_REQUEST['recaudacionCaja']);
				$stmt->bindValue(':cajaPendiente',  $_REQUEST['cajaPendiente']);
				$stmt->bindValue(':gastosCajaPeriodo',  $_REQUEST['gastosCajaPeriodo']);
				$stmt->bindValue(':observaciones',  $_REQUEST['observaciones']);

				try {
					$stmt->execute() ;
	                $lastId= $this->link->lastInsertId();
	                echo "{\"id\":".$lastId."}";
				}
				catch ( PDOException $Exception)  {
					die( $Exception->getMessage( )  );
				}		
		}
		
		private function cuadreCajaCierre() {
	            $fechaFacturaDesde=( isset($_REQUEST['fechaInicial'])?$_REQUEST['fechaInicial']:NULL);
	            $fechaFacturaHasta=( isset($_REQUEST['fechaCierre'])?$_REQUEST['fechaCierre']:NULL);
	            $sql = "select e.totFactura, e.totalCaja, (".
	            " select Sum(cantidad) AS gastosCaja FROM gastos WHERE (TipoGasto='C')  ".
	              ($fechaFacturaDesde != NULL ? " AND (fecha>= :fechaFacturaDesde)  " : "").
	              ($fechaFacturaHasta != NULL ? " AND (fecha <= :fechaFacturaHasta)  " : "").
	            ") as gastosCaja  from (".
				 "select sum(totalEstancia) as totFactura, sum(acuenta) as totalCaja ".
	                " from estancias".
	                " where  " .
	                "  id is not null and nroFactura>0 " .
	                ($fechaFacturaDesde != NULL ? " AND (fechaFactura>= :fechaFacturaDesde)  " : "").
	                ($fechaFacturaHasta != NULL ? " AND (fechaFactura <= :fechaFacturaHasta)  " : "")
	                ;
               $sql = $sql . ") e";
 
  				$stmt = ($this->link)->prepare($sql);

				if (strpos($sql, ':fechaFacturaDesde') !== false) $stmt->bindValue(':fechaFacturaDesde',  $fechaFacturaDesde);
	            if (strpos($sql, ':fechaFacturaHasta') !== false) $stmt->bindValue(':fechaFacturaHasta',  $fechaFacturaHasta);

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
		private function cuadreCajaTotal() {
	            $fechaFacturaDesde=( isset($_REQUEST['fechaInicial'])?$_REQUEST['fechaInicial']:NULL);
	            $fechaFacturaHasta=( isset($_REQUEST['fechaCierre'])?$_REQUEST['fechaCierre']:NULL);
				$sql = "select sum(e.recaudacionCaja)- sum(e.cajaPendiente) - sum(e.gastosCajaPeriodo) as cuadreTotalCaja ".
	                " from cuadreCaja e".
	                " where  " .
	                "  e.id is not null " .
	                ($fechaFacturaDesde != NULL ? " AND (e.fechaInicial>= :fechaFacturaDesde)  " : "").
	                ($fechaFacturaHasta != NULL ? " AND (e.fechaCierre <= :fechaFacturaHasta)  " : "")
	                ;

				$stmt = ($this->link)->prepare($sql);

				if (strpos($sql, ':fechaFacturaDesde') !== false) $stmt->bindValue(':fechaFacturaDesde',  $fechaFacturaDesde);
	            if (strpos($sql, ':fechaFacturaHasta') !== false) $stmt->bindValue(':fechaFacturaHasta',  $fechaFacturaHasta);

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

	}

	

	// Initiiate Library

	

	$api = new bd_caja();

	$api->analiza_method();

?>