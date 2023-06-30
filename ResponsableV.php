<?php
include_once 'BaseDatos.php';
Class ResponsableV{
    //el número de empleado, número de licencia, nombre y apellido
    private $nroEmpleado;
    private $nroLicencia;
    private $nombre;
    private $apellido;

    public function __construct(){
        $this->nroEmpleado = "";
        $this->nroLicencia = "";
        $this->nombre = "";
        $this->apellido = "";
    }

    public function getNroEmpleado(){
        return $this->nroEmpleado;
    }
    public function getNroLicencia(){
        return $this->nroLicencia;
    }
    public function getNombre(){
        return $this->nombre;
    }
    public function getApellido(){
        return $this->apellido;
    }
    public function setNroEmpleado($ne){
        $this->nroEmpleado = $ne;
    }
    public function setNroLicencia($nl){
        $this->nroLicencia = $nl;
    }
    public function setNombre($n){
        $this->nombre = $n;
    }
    public function setApellido($a){
        $this->apellido = $a;
    }

//----------------------------------------------------------------------------------------------------------------------//

    public function __toString(){
        return "Nombre: ".$this->getNombre()."\n"."Apellido: ".$this->getApellido()."\n"."Número de empleado: ".$this->getNroEmpleado()."\n"."Número de Licencia: ".$this->getNroLicencia()."\n";
    }
//----------------------------------------------------------------------------------------------------------------------//    

    public function cargar($nroEmp,$nroL,$nomb,$apel){		
		$this->setNroEmpleado($nroEmp);
		$this->setNroLicencia($nroL);
		$this->setNombre($nomb);
		$this->setApellido($apel);
    }
//----------------------------------------------------------------------------------------------------------------------//  
    	/**
	 * Recupera los datos de una persona por dni
	 * @param int $dni
	 * @return true en caso de encontrar los datos, false en caso contrario 
	 */		
    public function Buscar($nroEmpleadop){
		$base = new BaseDatos();
		$consultaResponsable = "Select * from responsable where rnumeroempleado = ".$nroEmpleadop;
		$resp = false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaResponsable)){
				if($row2 = $base->Registro()){					
				    //$this->setNroEmpleado($nroEmpleadop);
                    //$this->setNroLicencia($row2['rnumerolicencia']);
					//$this->setNombre($row2['rnombre']);
					//$this->setApellido($row2['rapellido']);
					// CORRECCION DE PRESENTACION
					$rnumerolicencia = $row2['rnumerolicencia'];
					$rnombre = $row2['rnombre'];
					$rapellido = $row2['rapellido'];
					$this->cargar($nroEmpleadop,$rnumerolicencia,$rnombre,$rapellido);
					$resp= true;
				}				
		 	}	else {
		 			$this->setmensajeoperacion($base->getError());
		 		
			}
		 }	else {
		 		$this->setmensajeoperacion($base->getError());
		 	
		 }		
		 return $resp;
	}	
	public function listar($condicion = ""){
		$arregloResp = null;
		$base = new BaseDatos();
		$consultaResp = "Select * from responsable ";
		if ($condicion != ""){
			$consultaResp = $consultaResp.' where '.$condicion;
		}
		if($base->Iniciar()){
			if($base->Ejecutar($consultaResp)){				
				$arregloResp = array();
				while($row2 = $base->Registro()){
					
					$nroEmpleado = $row2['rnumeroempleado'];
					$rnumerolic = $row2['rnumerolicencia'];
					$rnombre = $row2['rnombre'];
					$rapellido = $row2['rapellido'];
					$ObjResponsable = new ResponsableV();
					$ObjResponsable->cargar($nroEmpleado,$rnumerolic,$rnombre,$rapellido);
					array_push($arregloResp,$ObjResponsable);
				}
			 }	else {
					 $this->setmensajeoperacion($base->getError());
			}
		 }	else {
				 $this->setmensajeoperacion($base->getError());
		 }	
		 return $arregloResp;
	}	
//----------------------------------------------------------------------------------------------------------------------//
    public function insertar($nroL,$rNom,$rAp){
        $base = new BaseDatos();
        $resp = false;
        $consultaInsertar = "INSERT INTO responsable (rnumerolicencia, rnombre, rapellido) 
                VALUES ('$nroL','$rNom','$rAp')";

        if($base->Iniciar()){
            if($numEmpleado = $base->devuelveIDinsercion($consultaInsertar)){
                $resp=  true;
				$this->cargar($numEmpleado,$nroL,$rNom,$rAp);
            }	else {
                    $this->setmensajeoperacion($base->getError());
            }
        } else {
                $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }
//----------------------------------------------------------------------------------------------------------------------//
    public function modificar(){
	    $resp = false; 
	    $base = new BaseDatos();
		$consultaModifica = "UPDATE responsable SET rapellido = '".$this->getApellido()."', rnombre = '".$this->getNombre()."'
                           , rnumerolicencia = '".$this->getNroLicencia()."' WHERE rnumeroempleado = ". $this->getNroEmpleado();
		if($base->Iniciar()){
			if($base->Ejecutar($consultaModifica)){
			    $resp=  true;
		}   else{
				$this->setmensajeoperacion($base->getError());
			}
		}   else{
				$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}

//----------------------------------------------------------------------------------------------------------------------//
    public function eliminar(){
		$base = new BaseDatos();
		$resp = false;
		if($base->Iniciar()){
				$consultaBorra = "DELETE FROM responsable WHERE rnumeroempleado = ".$this->getNroEmpleado();
				if($base->Ejecutar($consultaBorra)){
				    $resp=  true;
				}else{
						$this->setmensajeoperacion($base->getError());
					
				}
		}else{
				$this->setmensajeoperacion($base->getError());
			
		}
		return $resp; 
	}    
}