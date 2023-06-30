<?php
include_once 'BaseDatos.php';
Class Viaje{
    
    private $idViaje;       //no se modifica   PRIMARY KEY
    private $destino;
    private $cantPasajeros;
    private $importe;   
    private $pasajeros; //coleccion de obj pasajeros // no se contempla en bd, no se modifica ni elimina
    private $empresa;   //objEmpresa | referencia a la clase empresa
    private $responsable;//nro empleado| referencia a objResponsable ES UN OBJ

    public function __construct(){
        $this->idViaje = "";
        $this->destino = "";
        $this->cantPasajeros = "";
        $this->importe = "";
        $this->empresa = null ;
        $this->pasajeros[] = new Pasajero();
        $this->responsable = null;
    }

    public function getIdViaje(){
        return $this->idViaje;
    }
    public function getDestino(){
        return $this->destino;
    }
    public function getCantPasajeros(){
        return $this->cantPasajeros;
    }
    public function getImporte(){
        return $this->importe;
    }
    public function getPasajeros(){
        return $this->pasajeros;
    }
    public function getResponsable(){
        return $this->responsable;
    }
    public function getEmpresa(){
        return $this->empresa;
    }

    public function setIdViaje($idv){
        $this->idViaje = $idv;
    }
    public function setDestino($d){
        $this->destino = $d;
    }
    public function setCantPasajeros($cp){
        $this->cantPasajeros = $cp;
    }
    public function setImporte($i){
        $this->importe = $i;
    }
    public function setPasajeros($pasaj){
        $this->pasajeros = $pasaj;
    }
    public function setResponsable($resp){
        $this->responsable = $resp;
    }
    public function setEmpresa($emp){
        $this->empresa = $emp;
    }

//------------------------------------------------------------------------------------------------//

    public function showPasajeros(){
        $cadena = "";
        if($this->listarPasajeros()){
            $array = $this->getPasajeros();
            for($i = 0; $i<count($array);$i++){
                $cadena = $cadena . $array[$i]->__toString()."\n";
            }
        }
        else{
            $cadena.= " no se encontraron pasajeros\n";
        }
        return $cadena;
    }
//------------------------------------------------------------------------------------------------//
    //ahora cada viaje tendrá su propia cantmax de pasajeros
    public function __toString(){
        return "---------------  Viaje N° ".$this->getIdViaje()."--------------- \n"."- Empresa ".$this->getEmpresa()->getNombre()." - \n"."- Destino: ".$this->getDestino()."- \n".
        "- Cantidad de pasajeros: ".$this->getCantPasajeros()."- \n"."- Importe: $".$this->getImporte()." -\n"."---------------  Resposable ---------------- \n".$this->getResponsable().
        "\n"."---------------  Pasajeros --------------- \n". $this->showPasajeros()."\n";
    }
//------------------------------------------------------------------------------------------------//
    public function listarPasajeros(){
        $resp = false;
        $objPasajero = new Pasajero();
        $array = $objPasajero->listar("idviaje = ".$this->getIdViaje());
        if($array != null){
            $this->setPasajeros($array);
            $resp = true;
        }
        return $resp;
    }
//------------------------------------------------------------------------------------------------//
public function listar($condicion = ""){
    $arregloViaje = null;
    $base = new BaseDatos();
    $consultaViaje = "Select * from viaje ";
    if ($condicion != ""){
        $consultaViaje = $consultaViaje.' where '.$condicion;
    }
    if($base->Iniciar()){
        if($base->Ejecutar($consultaViaje)){				
            $arregloViaje = array();
            while($row2 = $base->Registro()){
                
                $idv = $row2['idviaje'];
                $destino = $row2['vdestino'];
                $vcantmax = $row2['vcantmaxpasajeros'];
                $objEmpresa = new Empresa();
                $objEmpresa->Buscar($row2['idempresa']);
                $this->setEmpresa($objEmpresa);    //obj Empresa
                $empleado = $row2['rnumeroempleado'];
                $importe = $row2['vimporte'];
                $objViaje = new Viaje();
                $objResponsable = new ResponsableV();
                $objResponsable->Buscar($empleado);
                $objViaje->cargar($idv,$destino,$vcantmax,$importe,$objResponsable,$objEmpresa);
                array_push($arregloViaje,$objViaje);
            }
         }	else {
                 $this->setmensajeoperacion($base->getError());
        }
     }	else {
             $this->setmensajeoperacion($base->getError());
     }	
     return $arregloViaje;
}	
//------------------------------------------------------------------------------------------------//
    public function cargar($id,$dest,$cantP,$imp,$respV,$emp){	
        $this->setIdViaje($id);
        $this->setDestino($dest);
        $this->setCantPasajeros($cantP);
        $this->setImporte($imp);
        $this->setResponsable($respV);
        $this->setEmpresa($emp);
    }
//------------------------------------------------------------------------------------------------//
    /**
	 * Recupera los datos de un viaje por idViaje
	 * @param int $idViaje
	 * @return true en caso de encontrar los datos, false en caso contrario 
	 */		
    public function Buscar($id){
		$base = new BaseDatos();
		$consultaViaje = "Select * from viaje where idviaje = ".$id;
		$resp = false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaViaje)){
				if($row2 = $base->Registro()){					
				    //$this->setIdViaje($id); 
                    //$this->setDestino($row2['vdestino']);
                    //$this->setCantPasajeros($row2['vcantmaxpasajeros']);
                    //$this->setImporte($row2['vimporte']);
                    // $this->setEmpresa($objEmpresa);    //obj Empresa
                    // buscar con el nroempleado y crear el obj responsable
                    //$this->setResponsable($respons);    //obj Responsable
                    // CORRECCION DE PRESENTACION
                    $objEmpresa = new Empresa();
                    $objEmpresa->Buscar($row2['idempresa']);
                    $respons = new ResponsableV();
                    $respons->Buscar($row2['rnumeroempleado']);
                    $vdestino = $row2['vdestino'];
                    $vcantmaxpasajeros = $row2['vcantmaxpasajeros'];
                    $vimporte = $row2['vimporte'];
                    $this->cargar($id,$vdestino,$vcantmaxpasajeros,$vimporte,$respons,$objEmpresa);
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
    public function insertar($vdestino,$vcantmax,$vimporte,$objempresa,$objResponsable){
        $base = new BaseDatos();
        $resp = false;
        $consultaInsertar = "INSERT INTO viaje (vdestino, vcantmaxpasajeros, rnumeroempleado, idempresa, vimporte) 
                VALUES ('".$vdestino."','".$vcantmax."','".$objResponsable->getNroEmpleado()."','".$objempresa->getIdEmpresa()."','".$vimporte."')";

        if($base->Iniciar()){

            if($id = $base->devuelveIDInsercion($consultaInsertar)){
                $resp=  true;
                $this->cargar($id, $vdestino,$vcantmax,$vimporte,$objResponsable,$objempresa);
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
		$consultaModifica = "UPDATE viaje SET 
                vdestino = '".$this->getDestino()."',
                vcantmaxpasajeros = '".$this->getCantPasajeros()."', 
                idempresa = '".$this->getEmpresa()->getIdEmpresa()."', 
                rnumeroempleado = '".$this->getResponsable()->getNroEmpleado()."', 
                vimporte = '".$this->getImporte()."' 
                WHERE idviaje = ". $this->getIdViaje();
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
				$consultaBorra="DELETE FROM viaje WHERE idviaje = ".$this->getIdViaje();
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