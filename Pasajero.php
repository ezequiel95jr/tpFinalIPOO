<?php
//De los pasajeros se conoce su nombre, apellido, número de documento y teléfono
include_once 'BaseDatos.php';
Class Pasajero{
    private $dni;	//PRIMARY KEY
    private $nombre;
    private $apellido;
    private $telefono;
	private $viaje; // obj viaje
    public function __construct(){
        $this->dni = "";
        $this->nombre = "";
        $this->apellido = "";
        $this->telefono = "";
		$this->viaje = null;
    }
    
    public function getNombre(){
        return $this->nombre;
    }
    public function getApellido(){
        return $this->apellido;
    }
    public function getDni(){
        return $this->dni;
    }
    public function getTelefono(){
        return $this->telefono;
    }
	public function getViaje(){
		return $this->viaje;
	}
    public function getmensajeoperacion(){
		return $this->mensajeoperacion ;
	}

    public function setNombre($nom){
        $this->nombre = $nom;
    }
    public function setApellido($ap){
        $this->apellido = $ap;
    }
    public function setDni($nroDoc){
        $this->dni = $nroDoc;
    }
    public function setTelefono($t){
        $this->telefono = $t;
    }
	public function setViaje($v1){
		$this->viaje = $v1;
	}
    public function setmensajeoperacion($mensajeoperacion){
		$this->mensajeoperacion=$mensajeoperacion;
	}

//------------------------------------------------------------------------------------------------//
    public function __toString(){
        return "Nombre: ".$this->getNombre()."\n"."Apellido: ".$this->getApellido()."\n"."Dni: ".$this->getDni()."\n"."Telefono: ".$this->getTelefono()."\n"."Viaje asociado: ".$this->getViaje()->getIdViaje()."\n";
    }
//------------------------------------------------------------------------------------------------//
    public function cargar($NroD,$Nom,$Ape,$tel,$viaje){		
		$this->setDni($NroD);
		$this->setNombre($Nom);
		$this->setApellido($Ape);
		$this->setTelefono($tel);
		$this->setViaje($viaje);
		//el param idviaje viene cuando se ejecuta el metodo en otro script
    }
//------------------------------------------------------------------------------------------------//
    	/**
	 * Recupera los datos de una persona por dni
	 * @param int $dni
	 * @return true en caso de encontrar los datos, false en caso contrario 
	 */		
    public function Buscar($dni){
		$base = new BaseDatos();
		$consultaPersona = "Select * from pasajero where pdocumento = ".$dni;
		$resp = false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaPersona)){
				if($row2=$base->Registro()){					
				    $this->setDni($dni);
					//$this->setNombre($row2['pnombre']);
					//$this->setApellido($row2['papellido']);
					//$this->setTelefono($row2['ptelefono']);
					//$this->setViaje($viaje); //CONSULTAR, MODIFICADO
					// CORRECCION DE PRESENTACION
					$pnombre = $row2['pnombre'];
					$papellido = $row2['papellido'];
					$ptelefono = $row2['ptelefono'];
					$viaje = new Viaje();
					$viaje->Buscar($row2['idviaje']);
					$this->cargar($dni,$pnombre,$papellido,$ptelefono,$viaje);
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

//------------------------------------------------------------------------------------------------//
    public function listar($condicion = ""){
	    $arregloPersona = null;
		$base = new BaseDatos();
		$consultaPersonas = "Select * from pasajero ";
		if ($condicion != ""){
		    $consultaPersonas = $consultaPersonas.' where '.$condicion;
		}
		//$consultaPersonas.= " order by apellido ";
		if($base->Iniciar()){
			if($base->Ejecutar($consultaPersonas)){				
				$arregloPersona= array();
				while($row2 = $base->Registro()){
					
                    $NroDoc = $row2['pdocumento'];
					$Nombre = $row2['pnombre'];
					$Apellido = $row2['papellido'];
					$Tel = $row2['ptelefono'];
					$viaje = new Viaje();
					$viaje->Buscar($row2['idviaje']);
					
					$perso = new Pasajero();
					$perso->cargar($NroDoc,$Nombre,$Apellido,$Tel,$viaje);
					array_push($arregloPersona,$perso);
				}
		 	}	else {
		 			$this->setmensajeoperacion($base->getError());
			}
		 }	else {
		 		$this->setmensajeoperacion($base->getError());
		 }	
		 return $arregloPersona;
	}	
//------------------------------------------------------------------------------------------------//

    public function insertar($pDni,$pNom,$pApe,$pTel,$viaje){
        $base = new BaseDatos();
        $resp = false;
		$id = $viaje->getIdViaje();
        $consultaInsertar = "INSERT INTO pasajero (pdocumento, pnombre, papellido ,ptelefono,idViaje) 
                VALUES ('$pDni','$pNom','$pApe','$pTel',' $id ' )";

        if($base->Iniciar()){

            if($base->Ejecutar($consultaInsertar)){
                $resp=  true;
				$this->cargar($pDni,$pNom,$pApe,$pTel,$viaje);
            }	else {
                    $this->setmensajeoperacion($base->getError());
            }
        } else {
                $this->setmensajeoperacion($base->getError());
        }
        return $resp;
    }

//------------------------------------------------------------------------------------------------//
    public function modificar(){
	    $resp = false; 
	    $base = new BaseDatos();
		$consultaModifica = "UPDATE pasajero SET 
			papellido = '".$this->getApellido()."',
			pnombre = '".$this->getNombre()."',
			ptelefono = '".$this->getTelefono()."',
			idviaje = '".$this->getViaje()->getIdViaje()."' 
			WHERE pdocumento = ". $this->getDni();
		if($base->Iniciar()){
			if($base->Ejecutar($consultaModifica)){
			    $resp=  true;
			}else{
				$this->setmensajeoperacion($base->getError());
			}
		}else{
				$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}

//------------------------------------------------------------------------------------------------//
	public function modificaNombre($newNombre){
		$resp = false; 
	    $base = new BaseDatos();
		$consultaModifica = "UPDATE pasajero SET pnombre='".$newNombre."' WHERE pdocumento=". $this->getDni();
		if($base->Iniciar()){
			if($base->Ejecutar($consultaModifica)){
			    $resp=  true;
			}else{
				$this->setmensajeoperacion($base->getError());
			}
		}else{
				$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}
//------------------------------------------------------------------------------------------------//
	public function modificaApellido($newApellido){
		$resp = false; 
	    $base = new BaseDatos();
		$consultaModifica = "UPDATE pasajero SET papellido ='".$newApellido."' WHERE pdocumento=". $this->getDni();
		if($base->Iniciar()){
			if($base->Ejecutar($consultaModifica)){
			    $resp=  true;
			}else{
				$this->setmensajeoperacion($base->getError());
			}
		}else{
				$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}
//------------------------------------------------------------------------------------------------//
	public function modificaTelefono($newTelefono){
		$resp = false; 
	    $base = new BaseDatos();
		$consultaModifica = "UPDATE pasajero SET telefono ='".$newTelefono."' WHERE pdocumento=". $this->getDni();
		if($base->Iniciar()){
			if($base->Ejecutar($consultaModifica)){
			    $resp=  true;
			}else{
				$this->setmensajeoperacion($base->getError());
			}
		}else{
				$this->setmensajeoperacion($base->getError());
		}
		return $resp;
	}
//------------------------------------------------------------------------------------------------//
    public function eliminar(){
		$base = new BaseDatos();
		$resp = false;
		if($base->Iniciar()){
				$consultaBorra="DELETE FROM pasajero WHERE pdocumento = ".$this->getDni();
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