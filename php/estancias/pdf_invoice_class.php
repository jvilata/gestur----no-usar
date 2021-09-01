    <?php 
  require_once($_SERVER['DOCUMENT_ROOT'].'/privado/php/lib/fpdf/alphapdf.php');
  require_once("../bd_api.php");
  // require_once($_SERVER['DOCUMENT_ROOT']."/privado/php/onedrive/onedrive.php");
  // require_once("bd_facturas_class.php");
  
  class pdf_invoice_class extends BD_API {
      // CARGAR DATOS
      protected $dirbase=''; //$_SERVER['DOCUMENT_ROOT'].'/wealthmgr/img/'; // directorio logos
      protected $datos_vida=[]; // datos empresa que factura
      protected $logo='';
      protected $datos_aquien=[];       // datos empresa a quien factura
      protected $datos_cabfactura=[]; //datos cab factura
      protected $datos_iva=[[]];
      protected $datos_linfactura=[[]];//datos lineas
      
      public function main_imprimirFactura($id, $aDisco) {
        $this->dirbase=$_SERVER['DOCUMENT_ROOT'].'/gestur/';
        //$this->cargarDatosMuestra();
        $this->cargarDatosFactura($id);  // $this->cargarDatosMuestra();
        $this->imprimeFactura($id,$aDisco); 
      }
      
      // cargar Datos Muestra
      protected function cargarDatosMuestra() {
          // datos empresa que factura
          $this->datos_vida=['logo'=>'camping_color.jpg','nombre'=>'VILATA DARDER GESTIÓN TURÍSTICA SL','cif'=>'B97935068',
              'direccion'=>'C/Gran Vía Marqués de Turia, 49, D705','cpostal'=>'46005','poblacion'=>'Valencia','provincia'=>'Valencia',
              'registro'=>'Inscrita en el Registro Mercantil de Valencia al tomo 8598, Folio 56, Hoja V118965, inscripción 1ª'  ];
          $this->logo='';
          if ($this->datos_vida['logo']) $this->logo=$this->dirbase."img/".$this->datos_vida['logo'];// $logo="C:/eclipse-workspace/wealthmgr/img/VIDA_color.jpg";
          
          // datos empresa a quien factura
          $this->datos_aquien=['nombre'=>'CONSUM SOCIEDAD COOP. SLP','cif'=>'B97830426','direccion'=>'C/Gran Vía Marqués de Turia, 49, D705','cpprov'=>'46005 - Valencia'];
          //datos cab factura
          $this->datos_cabfactura=['nroFactura'=>'100','fecha'=>'01/10/2018','por_retencion'=>'19','retencion'=>'9.835,34','totalFactura'=>'123.456,78'];
          $this->datos_iva=[['piva'=>'10','totalIva'=>'12.56,67','base'=>'138.567,45'],['piva'=>'21','totalIva'=>'1.560,67','base'=>'13.567,45']];
          //datos lineas
          $this->datos_linfactura=[['descripcion'=>'ESTO ES UN CONCEPTO','unidades'=>'10','precio'=>'13.567,45','pdescuento'=>'0','neto'=>'135.345,67','piva'=>'21'],
              ['descripcion'=>'ESTO ES UN CONCEPTO','unidades'=>'10','precio'=>'13.567,45','pdescuento'=>'0','neto'=>'135.345,67','piva'=>'21']];
          
      }
      // cargar datos de la factura
      private function cargarDatosFactura($id){
          // CARGAR DATOS
          $cfacturas=[];
          $cDesgloseIvaFactura=[];
          // datos factura actual
          $sql="select * from cfacturas where id=:id";
          $stmt = ($this->link)->prepare($sql);
          $stmt->bindParam(':id',  $id);
          if  ($stmt->execute()) {
              $cfacturas= $stmt->fetchAll(PDO::FETCH_ASSOC);
              if (count($cfacturas)<=0)  return;// no existe salgo
              $sql="select * from cdesgloseIvaFactura where id=:id";
              $stmt = ($this->link)->prepare($sql);
              $stmt->bindParam(':id',  $id);
              if  ($stmt->execute()) 
                  $cDesgloseIvaFactura= $stmt->fetchAll(PDO::FETCH_ASSOC);
          }
          
          // datos empresa que factura
           $this->datos_vida=['logo'=>'camping_color.jpg','nombre'=>'VILATA DARDER GESTION TURISTICA SL','cif'=>'B97935068',
                      'direccion'=>'C/Gran Vía Marqués de Turia, 49, D705','cpostal'=>'46005','poblacion'=>'Valencia','provincia'=>'Valencia',
                      'registro'=>'Inscrita en el Registro Mercantil de Valencia al tomo 8742, Folio 182, Hoja V124024, inscripción 1ª',
                      'nombreCorto' => 'VIDAGESTUR'];
           /*  $sql="select * from entidades where codEmpresa=:codEmpresa and tipoentidad='SELF'";
            $stmt = ($this->link)->prepare($sql);
            $stmt->bindValue(':codEmpresa',  $cfacturas[0]['codEmpresa']);
            if  ($stmt->execute()) {
              $cdatosEntidad= $stmt->fetchAll(PDO::FETCH_ASSOC);
              if (count($cdatosEntidad)<=0)
                  $this->datos_vida=['logo'=>'camping_color.jpg','nombre'=>'VILATA DARDER GESTION TURISTICA SL','cif'=>'B97935068',
                      'direccion'=>'C/Gran Vía Marqués de Turia, 49, D705','cpostal'=>'46005','poblacion'=>'Valencia','provincia'=>'Valencia',
                      'registro'=>'Inscrita en el Registro Mercantil de Valencia al tomo 8742, Folio 182, Hoja V124024, inscripcion 1'  ];
              else
                  $this->datos_vida=$cdatosEntidad[0]; 
            }
            $this->datos_vida['nombreCorto']="NO EXISTE";
            $sql="select * from tablaauxiliar where codTabla=8 and codElemento=:codEmpresa";
            $stmt = ($this->link)->prepare($sql);
            $stmt->bindValue(':codEmpresa',  $cfacturas[0]['codEmpresa']);
            if  ($stmt->execute()) {
                $ctabAux= $stmt->fetchAll(PDO::FETCH_ASSOC);
                $this->datos_vida['nombreCorto']=$ctabAux[0]['valor1'];
            }
            */
          $this->logo='';
          if ($this->datos_vida['logo']) $this->logo=$this->dirbase."img/".$this->datos_vida['logo'];// $logo="C:/eclipse-workspace/wealthmgr/img/VIDA_color.jpg";
          
          // datos empresa a quien factura
          $this->datos_aquien=$cfacturas[0]; //['nombre'=>$cfacturas[0]['nombre'],'cif'=>$cfacturas[0]['cif'],'direccion'=>$cfacturas[0]['direccion'],'cpprov'=>$cfacturas[0]['cpostal'].' - '.$cfacturas[0]['provincia']];
          //datos cab factura
          $this->datos_cabfactura=$cfacturas[0]; //'nroFactura'=>$cfacturas[0]['nombre'],'fecha'=>'01/10/2018','por_retencion'=>'19','retencion'=>'9.835,34','totalFactura'=>'123.456,78'];
          $this->datos_iva=$cDesgloseIvaFactura; //[['piva'=>'10','totalIva'=>'12.56,67','base'=>'138.567,45'],['piva'=>'21','totalIva'=>'1.560,67','base'=>'13.567,45']];
          //datos lineas
          $this->datos_linfactura=$cfacturas; //[['descripcion'=>'ESTO ES UN CONCEPTO','unidades'=>'10','precio'=>'13.567,45','pdescuento'=>'0','neto'=>'135.345,67','piva'=>'21'],
//              ['descripcion'=>'ESTO ES UN CONCEPTO','unidades'=>'10','precio'=>'13.567,45','pdescuento'=>'0','neto'=>'135.345,67','piva'=>'21']];
          
      }
      
      public function imprimeFactura($idCabFactura,$aDisco) {
          
          // DIBUJAR FACTURA
          $pdf=new AlphaPDF(); // uso esta clase que implementa watermarks (transparencia) para el logo del fondo pagina
          $pdf->AddPage();
          $pdf->SetLeftMargin(15);
          if ($this->logo) { 
              $pdf->Image($this->logo,16,16,40); // adding the logo to the pdf 
              $pdf->SetFont('Arial','',8); //set the font-related propert
              $pdf->SetY(40);
              $pdf->Cell(10,3,($this->datos_vida['nombre']));//'VILATA DARDER HOLDING SL');
          } else { // no hay logo
              $pdf->SetFont('Arial','B',16); //set the font-related propert
              $pdf->SetY(35);
              $pdf->MultiCell(80,5,($this->datos_vida['nombre']),0,'L');//'VILATA DARDER HOLDING SL');
              $pdf->SetY(40);
          }
          
          $pdf->SetFont('Arial','',8); //set the font-related propert
          $pdf->Ln();// Line break
          $pdf->Cell(10,3,'CIF: '.$this->datos_vida['cif']);// B97830426');
          $pdf->Ln();// Line break
          $pdf->Cell(10,3,($this->datos_vida['direccion']));//'C/Gran Vía Marqués de Turia, 49, D705');
          $pdf->Ln();// Line break
          $pdf->Cell(10,3,$this->datos_vida['cpostal'].' - '.($this->datos_vida['poblacion']));//'46005 - Valencia');
          $pdf->Ln();// Line break
          if ($this->datos_vida['poblacion']!=$this->datos_vida['provincia']) $pdf->Cell(10,3,($this->datos_vida['provincia']));//'Valencia');
          
          $pdf->SetXY(120,35);
          $pdf->SetFont('Arial','B',16); //set the font-related propert
          $pdf->SetTextColor(130,127,126); // gris
          if ($this->datos_cabfactura['nroFactura'] == '' || $this->datos_cabfactura['nroFactura'] == '0') $pdf->Cell(40,0,'ESTANCIA');
          else $pdf->Cell(40,0,'FACTURA');
          $pdf->SetTextColor(0,0,0);
          
          $pdf->SetAlpha(0.1);
          if ($this->logo) { $pdf->Image($this->logo,30,105,150); // adding the logo to the pdf
          }
          $pdf->SetAlpha(1);
          
          // apartados de FECHA y NRO FACTURA
          $pdf->SetY(55);
          $pdf->SetFont('Arial','B',10); //set the font-related propert
          $pdf->Cell(40,8,'FECHA',1);
          $pdf->SetFont('Arial','',10); //set the font-related propert
          if ($this->datos_cabfactura['nroFactura'] == '' || $this->datos_cabfactura['nroFactura'] == '0') $pdf->Cell(40,8,date("d/m/Y", strtotime($this->datos_cabfactura['fechaEntrada'])),1);
          else $pdf->Cell(40,8,date("d/m/Y", strtotime($this->datos_cabfactura['FechaFactura'])),1);
           $pdf->Ln();// Line break
          $pdf->SetFont('Arial','B',10); //set the font-related propert
          if ($this->datos_cabfactura['nroFactura'] == '' || $this->datos_cabfactura['nroFactura'] == '0') $pdf->Cell(40,8,'ESTANCIA',1);
          else $pdf->Cell(40,8,'FACTURA',1);
          $pdf->SetFont('Arial','',10); //set the font-related propert
          if ($this->datos_cabfactura['nroFactura'] == '' || $this->datos_cabfactura['nroFactura'] == '0') $pdf->Cell(40,8,$this->datos_cabfactura['id'],1);
          else $pdf->Cell(40,8,$this->datos_cabfactura['nroFactura'],1);
        
          // DATOS DEL CLIENTE
          $x=110;
          $pdf->SetXY($x,45);
          $pdf->SetFont('Arial','B',10); //set the font-related propert
          $pdf->setX($x);
          // $pdf->Cell(10,3,'CLIENTE:',0,2);
          // $pdf->Ln();// Line break
          $pdf->setX($x);
          $pdf->Cell(10,3,utf8_decode($this->datos_aquien['nombre']),0,2);
          $pdf->Ln();// Line break
          $pdf->SetFont('Arial','',10); //set the font-related propert
          $pdf->setX($x);
          $pdf->Cell(10,3,'CIF: '.$this->datos_aquien['nroDoc'],0,2);
          $pdf->Ln();// Line break
          $pdf->setX($x);
          $pdf->Cell(10,3,utf8_decode($this->datos_aquien['direccion']),0,2);
          $pdf->Ln();// Line break
          $pdf->setX($x);
          $pdf->Cell(10,3,$this->datos_aquien['cpostal'].' - '.utf8_decode($this->datos_aquien['poblacion']),0,2);
          $pdf->Ln();// Line break
          $pdf->setX($x);
          $pdf->Cell(10,3,utf8_decode($this->datos_aquien['provincia'].'   - MATRICULA:'.$this->datos_aquien['matricula']),0,2);
          
          //CUERPO DE FACTURA
          //cabecera de lineas
          $y=75;//90
          $pdf->SetY($y);
          $pdf->Cell(0,8,'',1,1);
          $pdf->setY($y); 
          $pdf->SetFont('Arial','B',8); //set the font-related propert
          $pdf->Cell(10,8,'UNID.',0,0,'C');
          $pdf->Cell(50,8,'CONCEPTO');
          $pdf->Cell(20,8,'F.ENTRADA',0,0,'C');
          $pdf->Cell(20,8,'F.SALIDA',0,0,'C');
          $pdf->Cell(15,8,'NOCHES',0,0,'C');
          $pdf->Cell(15,8,'%IVA',0,0,'C');
          $pdf->Cell(15,8,'%DTO',0,0,'C');
          $pdf->Cell(20,8,'TARIFA',0,0,'C');
          $pdf->Cell(20,8,'TOTAL',0,0,'C');
          $pdf->Ln();// Line break
          
          $pdf->SetFont('Arial','',8); //set the font-related propert
          $x=$pdf->getX();
          $y=$pdf->getY();
          $altura=65; //140
          $pdf->Cell(185,$altura,'',1); // CONCPETO
          /*$pdf->Cell(20,$altura,'',1); // UNID
          $pdf->Cell(25,$altura,'',1);//PRECIO
          $pdf->Cell(15,$altura,'',1);   //DTO
          $pdf->Cell(30,$altura,'',1); //NETO
          $pdf->Cell(15,$altura,'',1); //IVA
          */
          $pdf->SetXY($x, $y+3);
          
          // bucle repeticion de lineas
          for ($i=0;$i<count($this->datos_linfactura);$i++) {
              $pdf->Cell(10,5,$this->datos_linfactura[$i]['cantidad'],0,0,'C');
              $pdf->Cell(50,5, utf8_decode($this->datos_linfactura[$i]['descripcionCorta'].' '.($this->datos_linfactura[$i]['Numero']=='0'?'':$this->datos_linfactura[$i]['Numero']).' '.$this->datos_linfactura[$i]['comentarios']));
              $pdf->Cell(20,5,date("d/m/Y", strtotime($this->datos_linfactura[$i]['fechaInicio'])),0,0,'R');//fecha inicio
              $pdf->Cell(20,5,date("d/m/Y", strtotime($this->datos_linfactura[$i]['fechaFin'])),0,0,'R');//fecha fin
              $pdf->Cell(15,5,$this->datos_linfactura[$i]['noches'],0,0,'R');//noches
              $pdf->Cell(15,5,number_format($this->datos_linfactura[$i]['tipoIva'], 2, ',', '.'),0,0,'R'); //IVA
              $pdf->Cell(15,5,number_format($this->datos_linfactura[$i]['dto'], 2, ',', '.'),0,0,'R');   //DTO
              $pdf->Cell(20,5,number_format($this->datos_linfactura[$i]['tarifa'], 2, ',', '.'),0,0,'R');//tarifa
              $pdf->Cell(20,5,number_format($this->datos_linfactura[$i]['totallin'], 2, ',', '.'),0,0,'R'); //total
              
             $pdf->Ln();// Line break
          }
          // TOTALES
          $y=$y+$altura;
          $pdf->SetY($y);
          if (strval($this->datos_cabfactura['Fianza']) >0) $pdf->Cell(40,5,'Fianza: ' . $this->datos_cabfactura['Fianza'],0,0,'C');
          if (strval($this->datos_cabfactura['ACuenta']) >0) $pdf->Cell(40,5,'Efectivo: ' . $this->datos_cabfactura['Acuenta'],0,0,'C');
          if (strval($this->datos_cabfactura['PorBanco']) >0) $pdf->Cell(40,5,'Transfer.: ' . $this->datos_cabfactura['PorBanco'],0,0,'C');
          if (strval($this->datos_cabfactura['PorDatafono']) >0) $pdf->Cell(40,5,'Tarjeta: ' . $this->datos_cabfactura['PorDatafono'],0,0,'C');
          //cabecera totales
          $y=$y+5;
          $pdf->SetY($y);
          $pdf->Cell(0,5,'',1,1);
          $pdf->setY($y);
          $pdf->SetFont('Arial','B',8); //set the font-related propert
          $pdf->Cell(40,5,'BASE',0,0,'C');
          $pdf->Cell(20,5,'%IVA',0,0,'C');
          $pdf->Cell(35,5,'CUOTA',0,0,'C');
          $pdf->Cell(15,5,'%RET,',0,0,'C');
          $pdf->Cell(35,5,'RETENCION',0,0,'C');
          if ($this->datos_cabfactura['nroFactura'] == '' || $this->datos_cabfactura['nroFactura'] == '0') $pdf->Cell(40,5,'TOTAL ESTANCIA',0,0,'C');
          else $pdf->Cell(40,5,'TOTAL FACTURA',0,0,'C');
          
          // bucle repeticion bases
          for ($i=0; $i<count($this->datos_iva);$i++) {
              $pdf->Ln();// Line break
              $pdf->SetFont('Arial','',8); //set the font-related propert
              $pdf->Cell(40,5,number_format($this->datos_iva[$i]['base'], 2, ',', '.'),1,0,'C');
              $pdf->Cell(20,5,number_format($this->datos_iva[$i]['piva'], 2, ',', '.'),1,0,'C');
              $pdf->Cell(35,5,number_format($this->datos_iva[$i]['totalIva'], 2, ',', '.'),1,0,'C');
              $x=$pdf->getX();
              /*$pdf->Cell(15,5,'',1,0,'C');
              $pdf->Cell(35,5,'',1,0,'C');
              $pdf->Cell(40,5,'',1,0,'C');*/
          }
        
        
          // total retencion y total factura
          $pdf->setX($x);
          $pdf->Cell(15,5,number_format($this->datos_cabfactura['porRetencion'], 2, ',', '.'),1,0,'C');
          $pdf->Cell(35,5,number_format($this->datos_cabfactura['Retencion'], 2, ',', '.'),1,0,'C');
          $pdf->SetFont('Arial','B',8); //set the font-related propert
          $pdf->Cell(40,5,number_format($this->datos_cabfactura['totalEstancia'], 2, ',', '.'),1,0,'C');
          
          
          //PIE FACTURA
          /*$pdf->SetY(260);
          $pdf->SetFont('Arial','',10); //set the font-related propert
//          $pdf->SetTextColor(130,127,126); // gris
          $cuenta= $this->datos_vida['cuentaCorriente'];
          $pdf->Cell(0,5, 'IBAN: '.substr($cuenta,0,4).' '.substr($cuenta,4,4).' '.substr($cuenta,8,4).' '.substr($cuenta,12,4).' '.substr($cuenta,16,4).' '.substr($cuenta,20,4));
          */
          $pdf->setY($pdf->getY()+8);
          $pdf->SetFont('Arial','',8); //set the font-related propert
          $pdf->SetTextColor(130,127,126); // gris
          $pdf->Cell(0,5, ($this->datos_vida['registro']),0,0,'C');
          
          // intrucciones
          $pdf->setXY(0,$pdf->getY()+3);
          $pdf->SetFont('Arial','B',8); //set the font-related propert
          
          $pdf->Cell(0,5,'',0,1);
          $pdf->Cell(185,5,'INSTRUCCIONES',1,0,'C');
          $pdf->setXY(0,$pdf->getY());
          $pdf->Cell(0,5,'',0,1);
           $pdf->SetFont('Arial','',7); //set the font-related propert
          $str="-El horario de la recepción esta expuesto al publico en recepción.\n".
"-El cliente está obligado a presentar el D.N.I o pasaporte.\n".
"-Se establece una fianza de 20€ para los clientes de camping y 50€ para los clientes de bungalows que se devolverá al salir, devolver el mando a distancia y haber cumplido con las normas aquí descritas.\n".
"-El día de salida se saldrá antes de las 12 h. contabilizándose una jornada más cuando pase de dicha hora.\n".
"-Se deberá liquidar la cuenta la víspera antes de partir.\n".
"-Las mascotas siempre deben estar vigiladas por sus propietarios y “NUNCA SUELTAS”. Para sus necesidades deben salir fuera del recinto vallado. No se deben usar enseres, camas ni sillones de los bungalows para las mascotas.\n".
"-El horario de silencio se establece entre las 23 h. y las 8 h. En el horario de silencio queda prohibido circular todo tipo de vehículos por el interior del camping. El vehículo no se podrá utilizar para moverse dentro del camping , solo se podrá utilizar para entrar y salir del camping , y se estacionara en el lugar indicado para ello. La velocidad nunca deberá superar los 10 km/hora.\n".
"-Las visitas deberán dejar previamente el DNI en recepción y abonar el precio estipulado en cada momento. Las visitas no podrán pernoctar en el bungalow. No se permiten más de 5 visitas por parcela. El horario de visitas es de 8 a 23h.\n".
"-Queda prohibido atar cuerdas a los árboles. Para tender ropa deben hacerlo en los tendederos.\n".
"-Deben mantener limpia la parcela y recoger la basura en bolsas de plástico que se depositaran en los contenedores debidamente cerradas. Si la parcela queda sucia al salir se cobrará el importe de la fianza en concepto de limpieza.\n".
"-Para utilizar la lavadora consultar en recepción.\n".
"-Se reserva el derecho de admisión de aquellos clientes que perturben el orden y la convivencia entre los acampados.\n".
"\n".
"LOS  CLIENTES DE BUNGALOW ADEMÁS DEBERÁN  CUMPLIR LAS SIGUIENTES NORMAS\n".
"-El bungalow quedará limpio y recogido tal y como fue entregado, con la vajilla y menaje lavado, en  caso contrario se cobrará el importe de la fianza en concepto  de limpieza.\n".
"-Los desperfectos ocasionados por el uso inadecuado deberán abonarse aparte.\n".
"-Bajo ningún concepto se podrá sacar fuera del bungalow el mobiliario ni utensilios tales como mantas, sillas, mesas, ....etc.\n".
"-Está prohibido hacer barbacoa en el porche del bungalow.\n".
"-El volumen de la televisión, radio, etc....., se mantendrá siempre a un nivel que no se oiga desde el exterior.\n".
"-Solo esta permitido un vehículo por cada bungalow, si hubiera más de un vehículo, éste pagaría por permanecer estacionado en el aparcamiento el precio estipulado y vigente en ese momento.\n".
"\n".
"FELIZ ESTANCIA Y GRACIAS POR SU VISITA";

          $pdf->MultiCell(185,3,$str,1,'L'); // CONCPETO
          $pdf->Output();
      }
  }
  
 

?>