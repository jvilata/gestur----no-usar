<?php

	require_once("../bd_api.php");

	

	class bd_guardiac extends BD_API {

		public function analiza_method() {
			$func = $this->function;
			$this->$func(); // espera que la URL sea del estilo : ..../personal/bd_personal.php/login
		}

		

		private function findGCTipo1Filter() {
				$id=( isset($_REQUEST['id'])?$_REQUEST['id']:NULL);
				$sql = "select * from GCTipo1 where  " .
				    " id is not null " ;

				$sql .= " ORDER BY id"; 

				$stmt = ($this->link)->prepare($sql);

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
		
		private function findGCTipoReg2Filter() {
			$sql = "select * from GCTipoReg2 where  " .
			    " id is not null " ;

			$sql .= " ORDER BY id"; 

			$stmt = ($this->link)->prepare($sql);

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
		
        private function deleteBDTipoReg2() {
	       $sql = "delete from GCTipoReg2 where id = :id";
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
	    
		private function guardarBDTipo1() {
	        $id = (isset($_REQUEST['id']) ? $_REQUEST['id'] : -1);
	        $sql="select id from GCTipo1 where id=:id";
            $stmt = ($this->link)->prepare($sql);
            $stmt->bindParam(':id',  $id);
            if  ($stmt->execute()) {
                $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($result)<=0)  {// no existe , lo creo
                    $sql="insert into GCTipo1 ";
                    $where="";
                } else { // si que existe, actualizo
                    $sql="update GCTipo1 ";
                    $where=" where id=:id ";
                }
            }
                
			$sql = $sql.
                "set tipoReg=:tipoReg, codEstablecimiento=:codEstablecimiento, nombreEstablecimiento=:nombreEstablecimiento, fechaEnvio=:fechaEnvio, HoraEnvio=:HoraEnvio, NumRegistrosTipo2=:NumRegistrosTipo2 ".
                 // ",user='".(isset($_SESSION['login'])?$_SESSION['login']:'system')."',ts=now() ".
                $where;            
			$stmt = ($this->link)->prepare($sql);

            if (isset($_REQUEST['id'])) $stmt->bindValue(':id',  $id);
			$stmt->bindValue(':tipoReg',  $_REQUEST['tipoReg']);
			$stmt->bindValue(':codEstablecimiento',  $_REQUEST['codEstablecimiento']);
			$stmt->bindValue(':nombreEstablecimiento',  $_REQUEST['nombreEstablecimiento']);
			$stmt->bindValue(':fechaEnvio',  $_REQUEST['fechaEnvio']);
			$stmt->bindValue(':HoraEnvio',  $_REQUEST['HoraEnvio']);
			$stmt->bindValue(':NumRegistrosTipo2',  $_REQUEST['NumRegistrosTipo2']);

			try {
				$stmt->execute() ;
                $lastId= $this->link->lastInsertId();
                echo "{\"id\":".$lastId."}";
			}
			catch ( PDOException $Exception)  {
				die( $Exception->getMessage( )  );
			}		
		}
		
		private function guardarBDTipoReg2() {
	        $id = (isset($_REQUEST['id']) ? $_REQUEST['id'] : -1);
	        $sql="select id from GCTipoReg2 where id=:id";
            $stmt = ($this->link)->prepare($sql);
            $stmt->bindParam(':id',  $id);
            if  ($stmt->execute()) {
                $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($result)<=0)  {// no existe , lo creo
                    $sql="insert into GCTipoReg2 ";
                    $where="";
                } else { // si que existe, actualizo
                    $sql="update GCTipoReg2 ";
                    $where=" where id=:id ";
                }
            }
                
			$sql = $sql.
                "set TipoRegistro=:TipoRegistro, dni=:dni, pasaporte=:pasaporte, TipoDoc=:TipoDoc, FechaExp=:FechaExp, PrimerApellido=:PrimerApellido, ".
                    "SegundoApellido=:SegundoApellido, Nombre=:Nombre, sexo=:sexo, FechaNac=:FechaNac, PaisNac=:PaisNac, FechaEntrada=:FechaEntrada " .
                 // ",user='".(isset($_SESSION['login'])?$_SESSION['login']:'system')."',ts=now() ".
                $where;            
			$stmt = ($this->link)->prepare($sql);

            if (isset($_REQUEST['id'])) $stmt->bindValue(':id',  $id);
			$stmt->bindValue(':TipoRegistro',  $_REQUEST['TipoRegistro']);
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
		
		private function quitarCar($s) {
		   $s = strtoupper($s);
           $s1 = "";
           for($i = 0; $i<strlen($s); $i++) {
             if ((substr($s, $i, 1) >= "0" && substr($s, $i, 1) <= "9") ||
               (ord(substr($s, $i, 1)) >= ord("A") && ord(substr($s, $i, 1)) <= ord("Z")) ||
               (substr($s, $i, 1) == " ") || (substr($s, $i, 1) == "|") || (substr($s, $i, 1) == "\r") || (substr($s, $i, 1) == "\n")) {
                $s1 = $s1 . substr($s, $i, 1);
             }
           }
           return $s1 ;  
		}
		
		private function generarArchivoGC() {
		    $dirbase = $_SERVER['DOCUMENT_ROOT'].'/gestur/tmp/';
		    
		    // registro tipo1 -  cabecera
		    $sql="select * from GCTipo1";
            $stmt = ($this->link)->prepare($sql);
            if  ($stmt->execute()) {
                $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
                $nomArchivo=$result[0]['nomArchivo'];
                $archivo = fopen($dirbase.$nomArchivo,'w');
                
                // registros tipo 2 - usuarios
                $sql="select * from GCTipoReg2";
                $stmt = ($this->link)->prepare($sql);
                if  ($stmt->execute()) {
                    $resultLin= $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $i=0;
                    $strLin = "";
                    while ($i<count($resultLin))  {
                        $strLin=$strLin . $resultLin[$i]['TipoRegistro'].'|'.$resultLin[$i]['dni'].'|'.$resultLin[$i]['pasaporte'].'|'.$resultLin[$i]['TipoDoc'].'|'.
                            date_create_from_format("Y-m-d H:i:s", $resultLin[$i]['FechaExp'])->format("Ymd").'|'.
                            $resultLin[$i]['PrimerApellido'].'|'.$resultLin[$i]['SegundoApellido'].'|'.$resultLin[$i]['Nombre'].'|'.$resultLin[$i]['sexo'].'|'.
                            date_create_from_format("Y-m-d H:i:s", $resultLin[$i]['FechaNac'])->format("Ymd").'|'.
                            $resultLin[$i]['PaisNac'].'|'.
                            date_create_from_format("Y-m-d H:i:s", $resultLin[$i]['FechaEntrada'])->format("Ymd").
                            "\r\n";
                        $i++;
                    }
                }
                $numLin = $i;
                // ahora que ya tengo el numero de lineas calcula la cabecera 
                $i=0;
                while ($i<count($result))  {
                    $str1=$result[0]['tipoReg'].'|'.$result[0]['codEstablecimiento'].'|'.$result[0]['nombreEstablecimiento'].'|'.
                        date_create_from_format("Y-m-d H:i:s", $result[0]['fechaEnvio'])->format("Ymd").'|'.date_create_from_format("Y-m-d H:i:s", $result[0]['fechaEnvio'])->format("Hi").
                        '|'.$numLin ;
                    fputs($archivo,$this->quitarCar($str1)."\r\n");
                    $i++;
                }
                // lineas      
                fputs($archivo,$this->quitarCar($strLin));
                
                fclose($archivo);
            }
            // echo "{\"nombreArchivo\":\"".$nomArchivo."\"}";
            
            // devolver archivo
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Transfer-Encoding: binary');
            header("Content-Length: ".filesize($dirbase.$nomArchivo));
            header("Content-Disposition: attachment; filename=" .$nomArchivo);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($dirbase.$nomArchivo);
            unlink($dirbase.$nomArchivo);
            exit;
		    
		}
		
		private function generarListaGC() {
		    
		    $FechaInicialEntrada = (isset($_REQUEST['FechaInicialEntrada']) ? $_REQUEST['FechaInicialEntrada'] : null);
		    $FechaFinalEntrada = (isset($_REQUEST['FechaFinalEntrada']) ? $_REQUEST['FechaFinalEntrada'] : null);
		    
		   	$sql="delete from GCTipoReg2";
		    $stmt = ($this->link)->prepare($sql);
	    	try {
				$stmt->execute() ;
			}
			catch ( PDOException $Exception)  {
				die( $Exception->getMessage( )  );
			}
		    
	        $sql="INSERT INTO GCTipoReg2 ( TipoRegistro, dni, pasaporte, TipoDoc, FechaExp, PrimerApellido, SegundoApellido, Nombre, sexo, FechaNac, PaisNac, FechaEntrada ) ".
                "SELECT '2' AS Expr2, if( tipoDoc <>1,if(isnull( nroDoc ),'111', nroDoc ),'') AS mdni, If( tipoDoc =1,if(isnull( nroDoc) ,'111', nroDoc ),'') AS mpas, ".
                "If( tipoDoc =1,'P',If( tipoDoc =2,'D','C')) AS TipoD, if(isnull( fechaExpedicion) ,curdate(), fechaExpedicion ) AS mfechaexped, ".
                "if(isnull(apellido1),upper( nombre ),upper( apellido1 )) AS mape1, upper( apellido2 ) AS mape2, upper( nombre ) AS mnombre, 'M' AS mSexo, ".
                "if(isnull( fechaNacimiento ),curdate(), fechaNacimiento ) AS mFechaNac, if(isnull( pais ),'ESP',upper(pais)) AS mPaisNac,  fechaEntrada  AS mFechaentrada ".
                "FROM clientes INNER JOIN estancias ON clientes.id = estancias.idCliente ".
                "WHERE estancias.fechaEntrada Between :FechaInicialEntrada And :FechaFinalEntrada";
	        
            $stmt = ($this->link)->prepare($sql);
            
            $stmt->bindValue(':FechaInicialEntrada',  $FechaInicialEntrada);
            $stmt->bindValue(':FechaFinalEntrada',  $FechaFinalEntrada);
            
        	try {
				$stmt->execute() ;
                $numberAffected= $stmt->rowCount();
			}
			catch ( PDOException $Exception)  {
				die( $Exception->getMessage( )  );
			}
			
			 $sql="INSERT INTO GCTipoReg2 ( TipoRegistro, dni, pasaporte, TipoDoc, FechaExp, PrimerApellido, SegundoApellido, Nombre, sexo, FechaNac, PaisNac, FechaEntrada ) ".
                "SELECT '2' AS Expr2, dni,pasaporte, TipoDoc, FechaExp, PrimerApellido, SegundoApellido, Nombre, sexo, FechaNac, PaisNac, estancias.FechaEntrada ".
                "FROM viajeros INNER JOIN estancias ON viajeros.idEstancia = estancias.id ".
                "WHERE estancias.fechaEntrada Between :FechaInicialEntrada And :FechaFinalEntrada";
	        
            $stmt = ($this->link)->prepare($sql);
            
            $stmt->bindValue(':FechaInicialEntrada',  $FechaInicialEntrada);
            $stmt->bindValue(':FechaFinalEntrada',  $FechaFinalEntrada);
            
        	try {
				$stmt->execute() ;
                $numberAffected= $numberAffected + $stmt->rowCount();
			}
			catch ( PDOException $Exception)  {
				die( $Exception->getMessage( )  );
			}
			
			
		    $sql="update GCTipo1 set numArchivo=(select valor1 from tablaAuxiliar where codTabla=5 and codElemento=2), nomarchivo=concat((select valor1 from tablaAuxiliar where codTabla=5 and codElemento=3),'.',(select valor1 from tablaAuxiliar where codTabla=5 and codElemento=2)),".
		       "fechaEnvio=now(),numRegistrosTipo2=:numberAffected";
		    $stmt = ($this->link)->prepare($sql);
		    $stmt->bindValue(':numberAffected',  $numberAffected);
	    	try {
				$stmt->execute() ;
				echo "{\"result\": \"OK\" }";
			}
			catch ( PDOException $Exception)  {
				die( $Exception->getMessage( )  );
			}
			// incrementa contador
			$sql="update tablaAuxiliar set valor1=(select valor1 from tablaAuxiliar where codTabla=5 and codElemento=2)+1 ".
			    "where  codTabla=5 and codElemento=2 ";
		    $stmt = ($this->link)->prepare($sql);
		    $stmt->bindValue(':numberAffected',  $numberAffected);
	    	try {
				$stmt->execute() ;
				echo "{\"result\": \"OK\" }";
			}
			catch ( PDOException $Exception)  {
				die( $Exception->getMessage( )  );
			}
		    
		}
	
	}

	

	// Initiiate Library

	

	$api = new bd_guardiac();

	$api->analiza_method();

?>