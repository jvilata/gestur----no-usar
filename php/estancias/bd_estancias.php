<?php
 	require_once("../bd_api.php");
 	
	class bd_estancias extends BD_API {
	
   
		
		public function analiza_method() {
				$func = $this->function;
    			$this->$func(); // espera que la URL sea del estilo : ..../personal/bd_personal.php/login
		}


			private function findEstanciasFilter() {
	            $id=( isset($_REQUEST['id'])?$_REQUEST['id']:NULL);
	            $nombre=( isset($_REQUEST['nombre'])?$_REQUEST['nombre']:NULL);
	            $NroFactura=( isset($_REQUEST['NroFactura'])?$_REQUEST['NroFactura']:NULL);
	            $facturadas=( isset($_REQUEST['facturadas'])?$_REQUEST['facturadas']:NULL);
	            $tipoEstancia=( isset($_REQUEST['tipoEstancia'])?$_REQUEST['tipoEstancia']:NULL);
	            $fechaEntrada=( isset($_REQUEST['fechaEntrada'])?$_REQUEST['fechaEntrada']:NULL);
	            $fechaEntradaDesde=( isset($_REQUEST['fechaEntradaDesde'])?$_REQUEST['fechaEntradaDesde']:NULL);
	            $fechaEntradaHasta=( isset($_REQUEST['fechaEntradaHasta'])?$_REQUEST['fechaEntradaHasta']:NULL);
	            $fechaSalida=( isset($_REQUEST['fechaSalida'])?$_REQUEST['fechaSalida']:NULL);
	            $fechaFacturaDesde=( isset($_REQUEST['fechaFacturaDesde'])?$_REQUEST['fechaFacturaDesde']:NULL);
	            $fechaFacturaHasta=( isset($_REQUEST['fechaFacturaHasta'])?$_REQUEST['fechaFacturaHasta']:NULL);
	            
	            $sql = "select e.*, concat(c.nombre,' ',if(isnull(c.apellido1),'',c.apellido1),' ',if(isnull(c.apellido2),'',c.apellido2)) as nombre ".
	                " from estancias e left join clientes c on e.idCliente = c.id ".
	                " where  " .
	                "  e.id is not null " .
	                ($id != NULL ? " AND (e.id = :id )  " : "") .
	                ($tipoEstancia != NULL ? " AND (e.tipoEstancia = :tipoEstancia )  " : "") .
	                ($NroFactura != NULL ? " AND (e.NroFactura = :NroFactura )  " : "") .
	                ($facturadas === '1' ? " AND (e.NroFactura <> '' or e.NroFactura is not null)  " : "") .
	                ($facturadas === '0' ? " AND (e.NroFactura = '' or e.NroFactura is null)  " : "") .
	                ($nombre != NULL ? " AND (c.nombre like :nombre OR c.apellido1 like :nombre OR c.apellido2 like :nombre)  " : "") .
	                ($fechaEntrada != NULL ? " AND (e.fechaEntrada >= :fechaEntrada )  " : "") .
	                ($fechaEntradaDesde != NULL ? " AND (e.fechaEntrada >= :fechaEntradaDesde )  " : "") .
	                ($fechaEntradaHasta != NULL ? " AND (e.fechaEntrada <= :fechaEntradaHasta )  " : "") .
	                ($fechaSalida != NULL ? " AND (e.fechaSalida <= :fechaSalida)  " : "") .
	                ($fechaFacturaDesde != NULL ? " AND (e.fechaFactura>= :fechaFacturaDesde)  " : "").
	                ($fechaFacturaHasta != NULL ? " AND (e.fechaFactura <= :fechaFacturaHasta)  " : "")
	                ;
	                
	                if ($fechaFacturaDesde != NULL) $sql .= " order by NroFactura ";
	                else $sql .= " order by fechaEntrada  ";
	                $sql .= "  LIMIT 1000";
	                $stmt = ($this->link)->prepare($sql);
	                
	                if (strpos($sql, ':nombre') !== false) $stmt->bindValue(':nombre',  '%'.$nombre.'%');//jv.uso bindvalue porque asigno una expresion
	                if (strpos($sql, ':id ') !== false) $stmt->bindValue(':id',  $id);
	                if (strpos($sql, ':tipoEstancia ') !== false) $stmt->bindValue(':tipoEstancia',  $tipoEstancia);
	                if (strpos($sql, ':NroFactura ') !== false) $stmt->bindValue(':NroFactura',  $NroFactura);
	                if (strpos($sql, ':fechaEntrada ') !== false) $stmt->bindValue(':fechaEntrada',  $fechaEntrada);
	                if (strpos($sql, ':fechaEntradaDesde') !== false) $stmt->bindValue(':fechaEntradaDesde',  $fechaEntradaDesde);
	                if (strpos($sql, ':fechaEntradaHasta') !== false) $stmt->bindValue(':fechaEntradaHasta',  $fechaEntradaHasta);
	                if (strpos($sql, ':fechaSalida') !== false) $stmt->bindValue(':fechaSalida',  $fechaSalida);
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
	        private function exportarExcelEstanciasFilter() {
	            $id=( isset($_REQUEST['id'])?$_REQUEST['id']:NULL);
	            $nombre=( isset($_REQUEST['nombre'])?$_REQUEST['nombre']:NULL);
	            $NroFactura=( isset($_REQUEST['NroFactura'])?$_REQUEST['NroFactura']:NULL);
	            $facturadas=( isset($_REQUEST['facturadas'])?$_REQUEST['facturadas']:NULL);
	            $tipoEstancia=( isset($_REQUEST['tipoEstancia'])?$_REQUEST['tipoEstancia']:NULL);
	            $fechaEntrada=( isset($_REQUEST['fechaEntrada'])?$_REQUEST['fechaEntrada']:NULL);
	            $fechaEntradaDesde=( isset($_REQUEST['fechaEntradaDesde'])?$_REQUEST['fechaEntradaDesde']:NULL);
	            $fechaEntradaHasta=( isset($_REQUEST['fechaEntradaHasta'])?$_REQUEST['fechaEntradaHasta']:NULL);
	            $fechaSalida=( isset($_REQUEST['fechaSalida'])?$_REQUEST['fechaSalida']:NULL);
	            $fechaFacturaDesde=( isset($_REQUEST['fechaFacturaDesde'])?$_REQUEST['fechaFacturaDesde']:NULL);
	            $fechaFacturaHasta=( isset($_REQUEST['fechaFacturaHasta'])?$_REQUEST['fechaFacturaHasta']:NULL);
	            
	            $sql = "select e.NroFactura, concat(c.nombre,' ',if(isnull(c.apellido1),'',c.apellido1),' ',if(isnull(c.apellido2),'',c.apellido2)) as nombre, c.nroDoc, e.FechaFactura, e.base, ".
	               "(SELECT GROUP_CONCAT(d.pIva SEPARATOR ', ') FROM `cdesgloseIvaFactura` d WHERE piva is not null and id=e.id) as pIva, ".
	               "e.totalIva, e.totalEstancia as totalFactura, e.porRetencion,e.Retencion ".
	                " from estancias e left join clientes c on e.idCliente = c.id ".
	                " where  " .
	                "  e.id is not null " .
	                ($id != NULL ? " AND (e.id = :id )  " : "") .
	                ($tipoEstancia != NULL ? " AND (e.tipoEstancia = :tipoEstancia )  " : "") .
	                ($NroFactura != NULL ? " AND (e.NroFactura = :NroFactura )  " : "") .
	                ($facturadas === '1' ? " AND (e.NroFactura <> '' or e.NroFactura is not null)  " : "") .
	                ($facturadas === '0' ? " AND (e.NroFactura = '' or e.NroFactura is null)  " : "") .
	                ($nombre != NULL ? " AND (c.nombre like :nombre OR c.apellido1 like :nombre OR c.apellido2 like :nombre)  " : "") .
	                ($fechaEntrada != NULL ? " AND (e.fechaEntrada >= :fechaEntrada )  " : "") .
	                ($fechaEntradaDesde != NULL ? " AND (e.fechaEntrada >= :fechaEntradaDesde )  " : "") .
	                ($fechaEntradaHasta != NULL ? " AND (e.fechaEntrada <= :fechaEntradaHasta )  " : "") .
	                ($fechaSalida != NULL ? " AND (e.fechaSalida <= :fechaSalida)  " : "") .
	                ($fechaFacturaDesde != NULL ? " AND (e.fechaFactura>= :fechaFacturaDesde)  " : "").
	                ($fechaFacturaHasta != NULL ? " AND (e.fechaFactura <= :fechaFacturaHasta)  " : "")
	                ;
	                
	                if ($fechaFacturaDesde != NULL) $sql .= " order by NroFactura ";
	                else $sql .= " order by fechaEntrada  ";
	                $sql .= "  LIMIT 1000";
	                $stmt = ($this->link)->prepare($sql);
	                
	                if (strpos($sql, ':nombre') !== false) $stmt->bindValue(':nombre',  '%'.$nombre.'%');//jv.uso bindvalue porque asigno una expresion
	                if (strpos($sql, ':id ') !== false) $stmt->bindValue(':id',  $id);
	                if (strpos($sql, ':tipoEstancia ') !== false) $stmt->bindValue(':tipoEstancia',  $tipoEstancia);
	                if (strpos($sql, ':NroFactura ') !== false) $stmt->bindValue(':NroFactura',  $NroFactura);
	                if (strpos($sql, ':fechaEntrada ') !== false) $stmt->bindValue(':fechaEntrada',  $fechaEntrada);
	                if (strpos($sql, ':fechaEntradaDesde') !== false) $stmt->bindValue(':fechaEntradaDesde',  $fechaEntradaDesde);
	                if (strpos($sql, ':fechaEntradaHasta') !== false) $stmt->bindValue(':fechaEntradaHasta',  $fechaEntradaHasta);
	                if (strpos($sql, ':fechaSalida') !== false) $stmt->bindValue(':fechaSalida',  $fechaSalida);
	                if (strpos($sql, ':fechaFacturaDesde') !== false) $stmt->bindValue(':fechaFacturaDesde',  $fechaFacturaDesde);
	                if (strpos($sql, ':fechaFacturaHasta') !== false) $stmt->bindValue(':fechaFacturaHasta',  $fechaFacturaHasta);
	                
	                try {
	                    if  ($stmt->execute()) {
	                        $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
	                        
	                        $this->devolverResultadosExcel($result);
	                    }
	                }
	                catch ( PDOException $Exception)  {
	                    die( $Exception->getMessage( )  );
	                }
	        }
	        
	        protected function devolverResultadosExcel($result) {
    			$filename = $_REQUEST['nompdf']; // File Name
    			// Download file
    			header("Content-Disposition: attachment; filename=\"$filename\"");
    			header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    			$flag = false;
    			foreach ($result as $row) {
    				if (!$flag) {
    					// display field/column names as first row
    					echo implode("\t", array_keys($row)) . "\r\n";
    					$flag = true;
    				}
    				
    				// echo implode("\t", array_values($row)) . "\r\n";		
    				$line = '';
    				foreach($row as $value){
    				    if(!isset($value) || $value == ""){				        $value = "\t";
    				    }else{
    				        if (is_numeric($value)) $value = str_replace('.', ',', $value);
    				        else  $value = str_replace('"', '""', $value);
    				        $value = '"' . $value . '"' . "\t";
    				    }
    				    $line .= $value;
    				}
    				echo trim($line)."\r\n";
    			}
    		}
	        
	        public function findLinEstanciasFilter() {
		    $idEstancia=( isset($_REQUEST['idEstancia'])?$_REQUEST['idEstancia']:$this->key);
		    $sql = "select * from reservas where " .
		  		    ($idEstancia != NULL ? "  (idEstancia = :idEstancia)  " : " idEstancia = -1 ") ;
		  		    $sql .= " ORDER BY id";
		  		    
		  		    $stmt = ($this->link)->prepare($sql);
		  		    if (strpos($sql, ':idEstancia') !== false) $stmt->bindValue(':idEstancia',  $idEstancia);
		  		    
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
	       $sql = "delete from estancias where id = :id";
	       $stmt = ($this->link)->prepare($sql);
	       $stmt->bindValue(':id',  $_REQUEST['id']);
	       
	       try {
	            $stmt->execute() ;
	            // borro las lineas
                $stmt = ($this->link)->prepare("delete from reservas where idEstancia=:id");
                $stmt->bindValue(':id',  $_REQUEST['id']);
                $stmt->execute();
	            echo '{ "result": "OK" }';
	       }   
            catch ( PDOException $Exception)  {
                die( $Exception->getMessage( )  );
            }
	   }
	   
   	    private function guardarBD() {
	        $id = (isset($_REQUEST['id']) ? $_REQUEST['id'] : -1);
            $sql="select id from estancias where id=:id";
            $stmt = ($this->link)->prepare($sql);
            $stmt->bindParam(':id',  $id);
            if  ($stmt->execute()) {
                $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($result)<=0)  {// no existe , lo creo
                    $sql="insert into estancias ";
                    $where="";
                } else { // si que existe, actualizo
                    $sql="update estancias ";
                    $where=" where id=:id ";
                }
                
                $sql = $sql. " set ";
                $campos = "".
                (isset($_REQUEST['fechaEntrada']) ? ",fechaEntrada=:fechaEntrada" : "").
                (isset($_REQUEST['fechaSalida']) ? ",fechaSalida=:fechaSalida" : "").
                (isset($_REQUEST['idCliente']) ?  " ,idCliente=:idCliente" : "") .
                (isset($_REQUEST['tipoEstancia']) ? ",tipoEstancia=:tipoEstancia" : "").
                (isset($_REQUEST['tipoTarifa']) ? ",tipoTarifa=:tipoTarifa" : "").
                (isset($_REQUEST['observaciones']) ? ",observaciones=:observaciones" : "").
                (isset($_REQUEST['base']) ? ",base=:base" : "").
                (isset($_REQUEST['totalIva']) ? ",totalIva=:totalIva" : "").
                (isset($_REQUEST['totalEstancia']) ? ",totalEstancia=:totalEstancia" : "").
                (isset($_REQUEST['ACuenta']) ? ",ACuenta=:ACuenta" : "").
                (isset($_REQUEST['NroFactura']) ? ",NroFactura=:NroFactura": "").
                (isset($_REQUEST['FechaFactura']) ? ",FechaFactura=:FechaFactura": "").
                (isset($_REQUEST['PorBanco']) ? ",PorBanco=:PorBanco" : "").
                (isset($_REQUEST['matricula']) ? ",matricula=:matricula" : "").
                (isset($_REQUEST['PorDatafono']) ? ",PorDatafono=:PorDatafono" : "").
                (isset($_REQUEST['Fianza']) ? ",Fianza=:Fianza" : "").
                (isset($_REQUEST['porRetencion']) ? ",porRetencion=:porRetencion" : "").
                (isset($_REQUEST['Retencion']) ? ",Retencion=:Retencion" : "") .
                (isset($_REQUEST['NroTiket']) ? ",NroTiket=:NroTiket" : "").
                ",user='".(isset($_SESSION['login'])?$_SESSION['login']:'system')."',ts=now() ";
                
                $sql = $sql . substr($campos, 1) . $where; // quito la primera ,
                
                
                $stmt = ($this->link)->prepare($sql);
                if (strpos($sql, ':id') !== false) $stmt->bindValue(':id',  $id);
                if (isset($_REQUEST['idCliente'])) $stmt->bindValue(':idCliente',  $_REQUEST['idCliente']);
                if (isset($_REQUEST['tipoEstancia'])) $stmt->bindValue(':tipoEstancia',  $_REQUEST['tipoEstancia']);
                if (isset($_REQUEST['tipoTarifa'])) $stmt->bindValue(':tipoTarifa',  $_REQUEST['tipoTarifa']);
                if (isset($_REQUEST['fechaEntrada'])) $stmt->bindValue(':fechaEntrada',  $_REQUEST['fechaEntrada']);
                if (isset($_REQUEST['fechaSalida']))  $stmt->bindValue(':fechaSalida',  $_REQUEST['fechaSalida']);
                if (isset($_REQUEST['observaciones'])) $stmt->bindValue(':observaciones',  $_REQUEST['observaciones']);
                if (isset($_REQUEST['base'])) $stmt->bindValue(':base',  $_REQUEST['base']);
                if (isset($_REQUEST['totalIva'])) $stmt->bindValue(':totalIva',  $_REQUEST['totalIva']);
                if (isset($_REQUEST['totalEstancia'])) $stmt->bindValue(':totalEstancia',  $_REQUEST['totalEstancia']);
                if (isset($_REQUEST['ACuenta'])) $stmt->bindValue(':ACuenta',  $_REQUEST['ACuenta']);
                if (isset($_REQUEST['NroFactura'])) $stmt->bindValue(':NroFactura',  $_REQUEST['NroFactura']);
                if (isset($_REQUEST['FechaFactura'])) $stmt->bindValue(':FechaFactura',  $_REQUEST['FechaFactura']);
                if (isset($_REQUEST['PorBanco'])) $stmt->bindValue(':PorBanco',  $_REQUEST['PorBanco']);
                if (isset($_REQUEST['matricula'])) $stmt->bindValue(':matricula',  $_REQUEST['matricula']);
                if (isset($_REQUEST['PorDatafono'])) $stmt->bindValue(':PorDatafono',  $_REQUEST['PorDatafono']);
                if (isset($_REQUEST['Fianza'])) $stmt->bindValue(':Fianza',  $_REQUEST['Fianza']);
                if (isset($_REQUEST['porRetencion'])) $stmt->bindValue(':porRetencion',  $_REQUEST['porRetencion']);
                if (isset($_REQUEST['Retencion'])) $stmt->bindValue(':Retencion',  $_REQUEST['Retencion']);
                if (isset($_REQUEST['NroTiket'])) $stmt->bindValue(':NroTiket',  $_REQUEST['NroTiket']);

                
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
            
            
		private function guardarReservasBD() {
            $id = (isset($_REQUEST['id']) ? $_REQUEST['id'] : -1);
            $sql="select id from reservas where id=:id";
            $stmt = ($this->link)->prepare($sql);
            $stmt->bindValue(':id',  $id);
            if  ($stmt->execute()) {
                $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($result)<=0)  {// no existe , lo creo
                    $sql="insert into reservas ";
                    $where="";
                } else { // si que existe, actualizo
                    $sql="update reservas ";
                    $where=" where id=:id ";
                }
                
                $sql = $sql. " set ";
                $campos = "".
                (isset($_REQUEST['idEstancia']) ?  ",idEstancia=:idEstancia" : "") .
                (isset($_REQUEST['fechaInicio']) ? ",fechaInicio=:fechaInicio" : "").
                (isset($_REQUEST['fechaFin']) ? ",fechaFin=:fechaFin" : "").
                (isset($_REQUEST['idCliente']) ?  " ,idCliente=:idCliente" : "") .
                (isset($_REQUEST['idServicio']) ?  " ,idServicio=:idServicio" : "") .
                (isset($_REQUEST['comentarios']) ? ",comentarios=:comentarios" : "").
                (isset($_REQUEST['Numero']) ? ",Numero=:Numero" : "").
                (isset($_REQUEST['noches']) ? ",noches=:noches" : "").
                (isset($_REQUEST['cantidad']) ? ",cantidad=:cantidad": "").
                (isset($_REQUEST['tarifa']) ? ",tarifa=:tarifa": "").
                (isset($_REQUEST['tipoIva']) ? ",tipoIva=:tipoIva" : "").
                (isset($_REQUEST['dto']) ? ",dto=:dto" : "").
                (isset($_REQUEST['base']) ? ",base=:base" : "").
                (isset($_REQUEST['total']) ? ",total=:total" : "").
                (isset($_REQUEST['dudoso']) ? ",dudoso=:dudoso" : "").
                ",user='".(isset($_SESSION['login'])?$_SESSION['login']:'system')."',ts=now() ";
                
                $sql = $sql . substr($campos, 1) . $where; // quito la primera ,
                
                $stmt = ($this->link)->prepare($sql);
                if (strpos($sql, ':id ') !== false) $stmt->bindValue(':id',  $id);
                if (isset($_REQUEST['idCliente'])) $stmt->bindValue(':idCliente',  $_REQUEST['idCliente']);
                if (isset($_REQUEST['idEstancia'])) $stmt->bindValue(':idEstancia',  $_REQUEST['idEstancia']);
                if (isset($_REQUEST['fechaInicio'])) $stmt->bindValue(':fechaInicio',  $_REQUEST['fechaInicio']);
                if (isset($_REQUEST['fechaFin'])) $stmt->bindValue(':fechaFin',  $_REQUEST['fechaFin']);
                if (isset($_REQUEST['idServicio']))  $stmt->bindValue(':idServicio',  $_REQUEST['idServicio']);
                if (isset($_REQUEST['comentarios'])) $stmt->bindValue(':comentarios',  $_REQUEST['comentarios']);
                if (isset($_REQUEST['Numero'])) $stmt->bindValue(':Numero',  $_REQUEST['Numero']);
                if (isset($_REQUEST['noches'])) $stmt->bindValue(':noches',  $_REQUEST['noches']);
                if (isset($_REQUEST['cantidad'])) $stmt->bindValue(':cantidad',  $_REQUEST['cantidad']);
                if (isset($_REQUEST['tarifa'])) $stmt->bindValue(':tarifa',  $_REQUEST['tarifa']);
                if (isset($_REQUEST['tipoIva'])) $stmt->bindValue(':tipoIva',  $_REQUEST['tipoIva']);
                if (isset($_REQUEST['dto'])) $stmt->bindValue(':dto',  $_REQUEST['dto']);
                if (isset($_REQUEST['base'])) $stmt->bindValue(':base',  $_REQUEST['base']);
                if (isset($_REQUEST['total'])) $stmt->bindValue(':total',  $_REQUEST['total']);
                if (isset($_REQUEST['dudoso'])) $stmt->bindValue(':dudoso',  $_REQUEST['dudoso']);

                
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
        private function deleteReservasBD() {
	       $sql = "delete from reservas where id = :id";
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
	   
	   // GENERAR FACTURA
	   private function generarFactura() {
	       $idEstancia = $_REQUEST['id'];
	       $ejercicio = (isset($_REQUEST['fechaSalida']) ? substr($_REQUEST['fechaSalida'],0,4) : date('Y'));
	       echo $ejercicio;
	       $this->guardarBD();
	        $sql = "update estancias set nrofactura=(select case when isnull(max(nrofactura)) then 1 else max(nrofactura) end from estancias where year(fechafactura)=year(curdate()))+1, tipoestancia=3, fechaFactura=curdate() ".
	            " where id=:id";
	       $stmt = ($this->link)->prepare($sql);
	       $stmt->bindValue(':id',  $idEstancia);
	       // $stmt->bindValue(':ejercicio',  $ejercicio);
	       
	       try {
	            $stmt->execute() ;
	            
	            echo '{ "result": "OK" }';
	       }   
            catch ( PDOException $Exception)  {
                die( $Exception->getMessage( )  );
            }
	   }
	   
	   // ESTADISTICAS
	   private function findcEvolucionFacturacionMes() {
	    $sql="SELECT date_format(FechaFactura,'%Y') as serie, date_format(FechaFactura,'%m') as etiquetavalor, sum(totalEstancia) as valor FROM estancias  ".
	        "WHERE NroFactura>0 and FechaFactura>=concat(year(curdate())-3 ,'-01-01') ".
	        "GROUP BY date_format(FechaFactura,'%Y'), date_format(FechaFactura,'%m') ".
	        "ORDER BY etiquetavalor,serie";
	    
	    
	    $stmt = ($this->link)->prepare($sql);
	    // $stmt->bindValue(':codEmpresa',  $_REQUEST['codEmpresa']);
	    
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
	   
	   private function findcEvolucionFacturacionAnyos() {
	    $sql="SELECT 'Facturacion' as serie, date_format(FechaFactura,'%Y') as etiquetavalor, sum(totalEstancia) as valor FROM estancias  ".
	        "WHERE NroFactura>0 and FechaFactura>=concat(year(curdate())-10 ,'-01-01') ".
	        "GROUP BY date_format(FechaFactura,'%Y') ".
	        "ORDER BY etiquetavalor,serie";
	    
	    
	    $stmt = ($this->link)->prepare($sql);
	    // $stmt->bindValue(':codEmpresa',  $_REQUEST['codEmpresa']);
	    
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
	  
	  private function findGridEstadis() {
	    $sql="SELECT estancias.id, estancias.idCliente, estancias.NroFactura, estancias.FechaFactura, 
	            upper(clientes.provincia) as provincia, upper(clientes.pais) as pais, 
	            reservas.idServicio, reservas.Numero, reservas.fechaInicio, reservas.fechaFin, reservas.noches, reservas.cantidad,
	            servicios.descripcionCorta,servicios.tipoServicio, tabAux.valor1 as descTipoServ,
	            (select count(1) from viajeros where idEstancia=estancias.id) as persBunga
            FROM estancias 
                left join reservas on estancias.id = reservas.idEstancia
        		left join clientes on estancias.idCliente = clientes.id
        		left join servicios on servicios.id = reservas.idServicio
        		left join tablaAuxiliar tabAux on tabAux.codTabla=4 and tabAux.codElemento=servicios.tipoServicio
            WHERE servicios.tipoServicio in (1,2,4,6,7) and not servicios.id in (11,12,13,14) and 
                  reservas.fechaInicio<=:fechaHasta and reservas.fechaFin>=:fechaDesde
            ORDER BY clientes.pais, clientes.provincia, reservas.fechaInicio	";
	    
	    
	    $stmt = ($this->link)->prepare($sql);
	    $stmt->bindValue(':fechaDesde',  $_REQUEST['fechaDesde']);
	    $stmt->bindValue(':fechaHasta',  $_REQUEST['fechaHasta']);
	    
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

	
	} // fin de class
	
	// Initiiate Library
	$api = new bd_estancias();
	
	$api->analiza_method();

?>