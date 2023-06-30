<?php
include_once 'BaseDatos.php';
Class Empresa{
    private $idEmpresa;
    private $eNombre;
    private $eDireccion;
    private $viajes; //array de viajes|| 1 empresa tiene N viajes

    public function __construct(){
        $this->idEmpresa = "";
        $this->eNombre = "";
        $this->eDireccion = "";
        $this->viajes = [];
    }

    public function getIdEmpresa(){
        return $this->idEmpresa;
    }
    public function getNombre(){
        return $this->eNombre;
    }
    public function getDireccion(){
        return $this->eDireccion;
    }
    public function getViajes(){
        return $this->viajes;
    }
    public function setIdEmpresa($id){
        $this->idEmpresa = $id;
    }
    public function setNombre($n){
        $this->eNombre = $n;
    }
    public function setDireccion($d){
        $this->eDireccion = $d;
    }
    public function setViajes($a1){
        $this->viajes = $a1;
    }

    public function __toString(){

        $viaje2 = new Viaje();
        $array = $viaje2->listar("idempresa = ".$this->getIdEmpresa());
        if($array != null){
            $this->setViajes($array);
        }
        return "//// EMPRESA ".$this->getNombre()." //// \n". "Id Empresa: "
        .$this->getIdEmpresa(). " | Direccion: ".$this->getDireccion()."\n"
        ."La empresa tiene ".count($this->getViajes())." viajes \n";
    }

    public function showViajes(){
        $arrayViajes = $this->getViajes();
        $cadena = "";
        for($i=0;$i<count($arrayViajes);$i++){
                $cadena = $cadena ." ".$arrayViajes[$i]."\n";
        }
        return $cadena;
    }

//------------------------------------------------------------------------------------------------//
    public function cargar($idEm, $nom,$dire,$viajes){	
        $this->setIdEmpresa($idEm);
        $this->setNombre($nom);
        $this->setDireccion($dire);
        $this->setViajes($viajes);
}
//------------------------------------------------------------------------------------------------//
    	/**
	 * Recupera los datos de un viaje por idViaje
	 * @param int $idViaje
	 * @return true en caso de encontrar los datos, false en caso contrario 
	 */		
    public function Buscar($id){
		$base = new BaseDatos();
		$consultaEmpresa = "Select * from empresa where idempresa = ".$id;
		$resp = false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaEmpresa)){
				if($row2=$base->Registro()){					
				    //$this->setIdViaje($id); no es necesario (?) porq tiene el autoincrement
                    $this->setIdEmpresa($id);
                    $this->setNombre($row2['enombre']);
                    $this->setDireccion($row2['edireccion']);
                    $viajes = new Viaje();
                    //$array_viajes = $viajes->listar("idempresa = ".$id);
                    //$enombre = $row2['enombre'];
                    //$edireccion = $row2['edireccion'];
                    //$this->cargar($id,$enombre,$edireccion,$array_viajes);
					$resp = true;
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
    $arregloEmpresa = null;
    $base = new BaseDatos();
    $consultaPersonas = "Select * from empresa ";
    if ($condicion != ""){
        $consultaPersonas = $consultaPersonas.' where '.$condicion;
    }
    //$consultaPersonas.= " order by apellido ";
    if($base->Iniciar()){
        if($base->Ejecutar($consultaPersonas)){				
            $arregloEmpresa= array();
            while($row2 = $base->Registro()){
                
                $idEmpresa = $row2['idempresa'];
                $Nombre = $row2['enombre'];
                $direccion = $row2['edireccion'];
                $empresa = new Empresa();
                $empresa->Buscar($row2['idempresa']);
                array_push($arregloEmpresa,$empresa);
            }
         }	else {
                 $this->setmensajeoperacion($base->getError());
        }
     }	else {
             $this->setmensajeoperacion($base->getError());
     }	
     return $arregloEmpresa;
}	
//------------------------------------------------------------------------------------------------//
    public function insertar($nom,$dire){
        $base = new BaseDatos();
        $resp = false;
        $consultaInsertar = " INSERT INTO empresa (enombre ,edireccion) 
                VALUES ('$nom','$dire')";

        if($base->Iniciar()){

            if($id = $base->devuelveIDInsercion($consultaInsertar)){
                $resp=  true;
                $viaje = new Viaje();
                $arrayViajes = $viaje->listar("idempresa = ".$id);
                $this->cargar($id,$nom,$dire,$arrayViajes);
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
        $consultaModifica = "UPDATE empresa SET enombre = '".$this->getNombre()."'
                        ,edireccion ='".$this->getDireccion()."' WHERE idempresa =". $this->getIdEmpresa();
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
//----------------------------------------------------------------------------------------------------------------------//
    public function eliminar(){
        $base = new BaseDatos();
        $resp = false;
        if($base->Iniciar()){
                $consultaBorra = "DELETE FROM empresa WHERE idempresa = ".$this->getIdEmpresa();
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

}//clase main