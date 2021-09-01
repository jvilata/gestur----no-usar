<?php
 	require_once("../bd_api.php");
 	
	class bd_clientes extends BD_API {
	
   
		
		public function analiza_method() {
			$func = $this->function;
			$this->$func(); // espera que la URL sea del estilo : ..../personal/bd_personal.php/login
		}


			private function findFilter() {
	            $id=( isset($_REQUEST['id'])?$_REQUEST['id']:$this->key);
	            $nombre=( isset($_REQUEST['nombre'])?$_REQUEST['nombre']:NULL);
	            $email=( isset($_REQUEST['email'])?$_REQUEST['email']:NULL);
	            $telefono=( isset($_REQUEST['telefono'])?$_REQUEST['telefono']:NULL);
	            $dni=( isset($_REQUEST['dni'])?$_REQUEST['dni']:NULL);
	            $servPeriodico=(isset($_REQUEST['servPeriodico'])?$_REQUEST['servPeriodico']:NULL);
	            $matricula=(isset($_REQUEST['matricula'])?$_REQUEST['matricula']:NULL);
	            $sql = "select *,concat(nombre,' ',if(isnull(apellido1),'',apellido1),' ',if(isnull(apellido2),'',apellido2)) as nombreCompleto ".
	                " from clientes ".
	                " where  " .
	                " id is not null " .
	                ($id != NULL ? " AND (id = :id )  " : "") .
	                ($nombre != NULL ? " AND (nombre like :nombre OR apellido1 like :nombre OR apellido2 like :nombre or concat(nombre, ' ',apellido1) LIKE :nombre)  " : "") .
	                ($email != NULL ? " AND (email like :email)  " : "") .
	                ($telefono != NULL ? " AND (telefonos like :telefono)  " : "") .
	                ($dni != NULL ? " AND (nroDoc like :dni)  " : "")
	                ;
	                ($matricula != NULL ? " AND (matricula like :matricula)  " : "")
	                ;
	                ($servPeriodico != NULL ? " AND (tipoServicioPeriodico = :servPeriodico)  " : "")
	                ;
	                
	                $sql .= " ORDER BY nombre";
	                $stmt = ($this->link)->prepare($sql);
	                
	                if (strpos($sql, ':nombre') !== false) $stmt->bindValue(':nombre',  '%'.$nombre.'%');//jv.uso bindvalue porque asigno una expresion
	                if (strpos($sql, ':id ') !== false) $stmt->bindValue(':id',  $id);
	                if (strpos($sql, ':email') !== false) $stmt->bindValue(':email',  '%'.$email.'%');
	                if (strpos($sql, ':telefono') !== false) $stmt->bindValue(':telefono',  '%'.$telefono.'%');
	                if (strpos($sql, ':dni') !== false) $stmt->bindValue(':dni',  '%'.$dni.'%');
	                if (strpos($sql, ':matricula') !== false) $stmt->bindValue(':matricula',  '%'.$matricula.'%');
	                if (strpos($sql, ':servPeriodico') !== false) $stmt->bindValue(':servPeriodico',  '%'.$servPeriodico.'%');
	                
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
	  
	        private function comboListaClientes() {
	            $id=( isset($_REQUEST['id'])?$_REQUEST['id']:$this->key);
	            $sql = "select id, concat(nombre,' ',ifnull(apellido1,''),' ',ifnull(apellido2,'')) as nombre ".
	                " from clientes ".
	                " where  " .
	                " id is not null and nombre is not null" .
	                ($id != NULL ? " AND (id = :id )  " : "") ;
	                $sql .= " ORDER BY nombre";
	                $stmt = ($this->link)->prepare($sql);
	                
	                if (strpos($sql, ':id ') !== false) $stmt->bindValue(':id',  $id);
	                
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
	       $sql = "delete from clientes where id = :id";
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
		    
		    
	            $sql="select id from clientes where id=:id";
	            $stmt = ($this->link)->prepare($sql);
	            $stmt->bindParam(':id',  $_REQUEST['id']);
	            if  ($stmt->execute()) {
	                $result= $stmt->fetchAll(PDO::FETCH_ASSOC);
	                if (count($result)<=0)  {// no existe , lo creo
	                    $sql="insert into clientes ";
	                    $where="";
	                } else { // si que existe, actualizo
	                    $sql="update clientes ";
	                    $where=" where id=:id ";
	                }
	                
	                $sql = $sql.
	                " set  nombre=:nombre, apellido1=:apellido1, apellido2=:apellido2, email=:email,".
	                " matricula=:matricula,direccion=:direccion,cpostal=:cpostal,poblacion=:poblacion,provincia=:provincia, ".
	                " pais=:pais,telefonos=:telefonos,tipoDoc=:tipoDoc,nroDoc=:nroDoc,fechaExpedicion=:fechaExpedicion,fechaNacimiento=:fechaNacimiento, ".
	                " nacionalidad=:nacionalidad, fechaValidez=:fechaValidez, tipoServicioPeriodico=:tipoServicioPeriodico, precio=:precio, tipoFacturacion=:tipoFacturacion, ".
	                " codBanco=:codBanco, codSucursal=:codSucursal, digitosControl=:digitosControl, cuentaBancaria=:cuentaBancaria " .
	                " ,user='".(isset($_SESSION['login'])?$_SESSION['login']:'system')."',ts=now() ".
	                $where;
	                
	                
	                $stmt = ($this->link)->prepare($sql);
	                if (strpos($sql, ':id') !== false) $stmt->bindValue(':id',  $_REQUEST['id']);
	                $stmt->bindValue(':nombre',  $_REQUEST['nombre']);
	                $stmt->bindValue(':apellido1',  $_REQUEST['apellido1']);
	                $stmt->bindValue(':apellido2',  $_REQUEST['apellido2']);
	                $stmt->bindValue(':email',  $_REQUEST['email']);
	                $stmt->bindValue(':matricula',  $_REQUEST['matricula']);
	                $stmt->bindValue(':direccion',  $_REQUEST['direccion']);
	                $stmt->bindValue(':cpostal',  $_REQUEST['cpostal']);
	                $stmt->bindValue(':poblacion',  $_REQUEST['poblacion']);
	                $stmt->bindValue(':provincia',  $_REQUEST['provincia']);
	                $stmt->bindValue(':pais',  $_REQUEST['pais']);
	                $stmt->bindValue(':telefonos',  $_REQUEST['telefonos']);
	                $stmt->bindValue(':tipoDoc',  $_REQUEST['tipoDoc']);
	                $stmt->bindValue(':nroDoc',  $_REQUEST['nroDoc']);
	                $stmt->bindValue(':fechaExpedicion',  $_REQUEST['fechaExpedicion']);
	                $stmt->bindValue(':fechaNacimiento',  $_REQUEST['fechaNacimiento']);
	                $stmt->bindValue(':nacionalidad',  $_REQUEST['nacionalidad']);
	                $stmt->bindValue(':fechaValidez',  $_REQUEST['fechaValidez']);
	                $stmt->bindValue(':tipoServicioPeriodico',  $_REQUEST['tipoServicioPeriodico']);
	                $stmt->bindValue(':precio',  $_REQUEST['precio']);
	                $stmt->bindValue(':tipoFacturacion',  $_REQUEST['tipoFacturacion']);
	                $stmt->bindValue(':codBanco',  $_REQUEST['codBanco']);
	                $stmt->bindValue(':codSucursal',  $_REQUEST['codSucursal']);
	                $stmt->bindValue(':digitosControl',  $_REQUEST['digitosControl']);
	                $stmt->bindValue(':cuentaBancaria',  $_REQUEST['cuentaBancaria']);

	                
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
	
	} // fin de class
	
	// Initiiate Library
	$api = new bd_clientes();
	
	$api->analiza_method();

?>