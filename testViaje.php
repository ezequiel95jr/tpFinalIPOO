<?php
// script viaje:
//Implementar dentro de la clase TestViajes una operación que permita ingresar, modificar y eliminar la información de la empresa de viajes.
include_once 'BaseDatos.php';
include_once 'Empresa.php';
include_once 'ResponsableV.php';
include_once 'Pasajero.php';
include_once 'Viaje.php';
$empresa = new Empresa();
$viaje1 = new Viaje();
$responsable1 = new ResponsableV();

menuMain($viaje1,$responsable1,$empresa);

function menuMain($viaje1,$responsable1,$empresa){
    $corta = false;
    while(!$corta){
        echo "------------------------ BIENVENIDOS --------------------------\n";
        echo "1) Empresa \n";
        echo "2) Viaje \n";
        echo "---------------------------------------------------------------\n";
        $respMain = trim(fgets(STDIN));
        switch ($respMain){
            case '1':
                echo "---------------------------EMPRESA-----------------------------\n";
                echo "1) Crear una empresa nueva\n"; //
                echo "2) Modificar datos de una empresa existente -\n";
                echo "---------------------------------------------------------------\n";
                $respuesta = trim(fgets(STDIN));
                if($respuesta == "1"){
                    echo "1) Ingresar informacion \n";
                    informacionEmpresa($respuesta,$empresa,1);
                }
                elseif($respuesta == "2"){
                    echo "- Modificar datos de una empresa existente - \n";
                    $arrayEmpresa = $empresa->listar();
                    muestraViajes($arrayEmpresa);
                    echo "Ingrese el id de la empresa con la que quiere trabajar: ";
                    $idEmpresa1 = trim(fgets(STDIN));
                    if($empresa->Buscar($idEmpresa1)){
                        echo "- la empresa existe -\n"."\n";
                        echo "---------------------------------------------------------------\n";
                        echo "a) Modificar \n";
                        echo "b) Eliminar \n";
                        echo "c) Mostrar \n";
                        echo "seleccione una opcion: ";
                        $resp1 = trim(fgets(STDIN));
                        echo "---------------------------------------------------------------\n";
                        informacionEmpresa($resp1,$empresa,$idEmpresa1);
                    }
                    else{
                        echo "no existe la empresa con id ".$idEmpresa1."\n";
                    }
                }
                else{
                    echo "opcion inválida \n";
                }
                break;
            case '2':
                $arrayEmpresa = $empresa->listar();
                muestraViajes($arrayEmpresa);
                echo "ingrese el id empresa: ";
                $idempresa2 = trim(fgets(STDIN));
                //la empresa existe:
                if($empresa->Buscar($idempresa2)){
                    echo "---------------------------------------------------------------\n";
                    echo "              - Informacion del viaje -\n";
                    echo "1) Crear un nuevo viaje o cargar pasajeros \n";
                    echo "2) Modificar \n";
                    echo "3) Eliminar \n";
                    echo "4) Mostrar viajes\n";
                    echo "---------------------------------------------------------------\n";
                    $respuesta = trim(fgets(STDIN));
                    menuRespuestaViaje($respuesta,$viaje1,$responsable1,$empresa);
                }
                break;
        }
        echo "- ingresar otra consulta? - (si/no): ";
        $repetirMenu = trim(fgets(STDIN));
        if($repetirMenu != "si"){
            $corta = true;
        }
    }
}
function menuRespuestaViaje($r,$viaje1,$responsable1,$empresa){

    switch($r){ //main
        case '1':
            echo "---------------------------------------------------------------\n";
            echo "1) Agregar un nuevo viaje\n";
            echo "2) Agregar pasajeros a un viaje existente\n";
            echo "---------------------------------------------------------------\n";
            $respuesta = trim(fgets(STDIN));
            switch($respuesta){
                case '1':
                //el idViaje será autoincrement, destino, cantMax, importe
                    echo "1) Ingrese información del viaje: \n";
                    echo "Destino: ";
                    $destino = trim(fgets(STDIN));
                    echo "Cantidad Maxima: ";
                    $cantMax = trim(fgets(STDIN));
                    echo "Importe: $";
                    $importe = trim(fgets(STDIN));
                    echo "Responsable a cargo: \n";
                    //aca deberia figurar un array de responsables pre cargados o la opcion de agregar uno nuevo
                    echo "---------------------------------------------------------------\n";
                    //*******************CORRECCION de la presentacion *******************
                    $responsableSeleccionado = elegirResponsable($responsable1);
                    $idEmpresa = $empresa->getIdEmpresa();
                    if($viaje1->insertar($destino,$cantMax,$importe,$empresa,$responsable1)){
                        echo "Viaje ".$viaje1->getIdViaje()." cargado exitosamente \n";
                    }
                        echo "desea agregar pasajeros a este viaje? (si/no): ";
                        $respuestaPasajeros = trim(fgets(STDIN));
                        if($respuestaPasajeros == "si"){
                            //cargo datos de pasajeros
                            cargarPasajeros($viaje1);
                        }
                break; //case 1
                case '2':
                    $array_viajes = $viaje1->listar("idempresa = ".$empresa->getIdEmpresa());
                    echo "--------------------- VIAJES EXISTENTES -----------------------\n";
                    muestraViajes($array_viajes);
                    echo "---------------------------------------------------------------\n";
                    echo "ingrese el idViaje: ";
                    $respuestaViaje = trim(fgets(STDIN));
                    if($viaje1->Buscar($respuestaViaje)){
                        //el viaje existe
                        echo "- viaje encontrado -\n";
                        cargarPasajeros($viaje1);
                    }
                    else{
                        echo "- el viaje no existe -\n";
                    }
                    break;
                default:
                    echo "respuesta invalida\n";
                break;
            }
            break;
            // FUNCIONA CORRECTAMENTE
        case '2':
            //modificar la informacion del viaje:
            echo "2) Modifica información del viaje: \n";
            echo "\n";
            $array_viaje = $viaje1->listar();
            muestraViajes($array_viaje);
            echo "ingrese el id del viaje que quiere modificar: ";
            $idViaje = trim(fgets(STDIN));
            if($viaje1->Buscar($idViaje)){
                //el viaje existe, el viaje tiene un responsable (obj) y una col de pasajeros
                $opcion = true;
                echo "---------------------------------------------------------------\n";
                echo "- viaje encontrado -\n";
                while($opcion){
                    echo "a) Nuevo Destino\n";
                    echo "b) Nueva cantidad maxima\n";
                    echo "c) Nuevo importe\n";
                    echo "d) Responsable: \n";
                    echo "e) Modificar informacion de un pasajero \n";
                    echo "---------------------------------------------------------------\n";
                    echo "- seleccione una opcion: \n";
                    $seleccion = trim(fgets(STDIN));
                    switch($seleccion){
                        case 'a':
                            echo "- Destino -\n";
                            $newDestino = trim(fgets(STDIN));
                            $viaje1->setDestino($newDestino);   
                            echo "Nuevo destino: ".$newDestino."\n";
                            break;
                        case 'b':
                            echo "- cantidad maxima -\n";
                            $newcantmax = trim(fgets(STDIN));
                            $viaje1->setCantPasajeros($newcantmax);
                            echo "Nueva Cantidad máxima de pasajeros: ".$newcantmax."\n";
                            break;
                        case 'c':
                            echo "- importe -\n";
                            $newimporte = trim(fgets(STDIN));
                            $viaje1->setImporte($newimporte);
                            echo "Nuevo importe: $".$newimporte."\n";
                            break;
                        case 'd':
                            echo "- responsable -\n";
                            modificarResposable($viaje1);
                            break;
                        case 'e':
                            echo " - pasajeros - \n";
                                $opcion = true;
                                $pasajerosG = new Pasajero();
                                $array_pasajeros = $pasajerosG->listar("idviaje = ".$idViaje);
                                if(count($array_pasajeros)>0){
                                    while($opcion){
                                        echo "ingrese el dni del pasajero: ";
                                        $dniBuscado = trim(fgets(STDIN));
                                        if(existe($dniBuscado,$idViaje,$pasajerosG)){
                                            //el dni existe dentro del viaje
                                            modificarPasajero($viaje1,$dniBuscado,$pasajerosG);
                                        }
                                        echo "modificar otro pasajero? (s/n): ";
                                        echo "\n";
                                        $respPasajero = trim(fgets(STDIN));
                                        if($respPasajero != 's'){
                                            $opcion = false;
                                        }
                                }
                                }
                                else{
                                    echo "no hay pasajeros cargados en este viaje \n";
                                }
                                
                        default:
                        echo "respuesta invalida! \n";
                        break;
                    }
                    echo "seguir modificando el viaje? (s/n): ";
                    echo "\n";
                    $respmodificar = trim(fgets(STDIN));
                    if($respmodificar != "s"){
                        $opcion = false;
                        if($viaje1->modificar()){
                            echo "Las modificaciones se realizaron con éxito \n";
                        }
                    }
                }
            }
            else{
                echo "El viaje ingresado no existe! \n";
            }
            break;
        case '3':
            //ELIMINA DATOS
            echo "1) eliminar viaje \n";
            echo "2) eliminar pasajeros\n";
            echo "3) eliminar responsable \n";
            $respeliminar = trim(fgets(STDIN));
                //si el viaje existe entro al switch
                switch($respeliminar){
                    case '1': //eliminar viaje
                        echo "Ingrese el IdViaje que quiere eliminar: ";
                        $idViaje = trim(fgets(STDIN));
                        if($viaje1->Buscar($idViaje)){
                            if(count($viaje1->listar("idempresa = ".$empresa->getIdEmpresa())) != 0){
                                echo "- Ha ocurrido un error. El viaje que intenta eliminar tiene pasajeros - \n";
                            }
                            else{
                                $viaje1->eliminar();
                                echo "- viaje eliminado - \n";
                            }
                        }
                        break;
                    case '2':
                        //eliminar pasajeros
                        $pasajeroN = new Pasajero();
                            echo "ingrese el dni del pasajero que desea eliminar: ";
                            $dniEliminar = trim(fgets(STDIN));
                            if($pasajeroN->Buscar($dniEliminar)){
                                echo "- existe -\n";
                                if($pasajeroN->eliminar()){
                                    echo "pasajero eliminado \n";
                                }
                                else{
                                    echo "No hubo delete. Ha ocurrido un error \n";
                                }
                            }
                            else{
                                echo "el dni ".$dniEliminar." no existe\n";
                            }
                        break;
                    case '3':
                        //echo $viaje1->getResponsable()->getNroEmpleado();
                        //echo $responsable1->listar("rnumeroempleado = ".$viaje1->getResponsable())[0];
                        echo "ingrese el Nro Empleado que desea eliminar: ";
                        $NroEmpleadoEliminar = trim(fgets(STDIN));
                        
                        if($responsable1->Buscar($NroEmpleadoEliminar)){
                            //el responsable existe pero es responsable por algun viaje?
                            //echo $NroEmpleadoEliminar." es igual a ".$responsable1->listar("rnumeroempleado = ".$viaje1->getResponsable())[0]."?\n";
                            $viajesProvisorio = $viaje1->listar("rnumeroempleado = ".$NroEmpleadoEliminar);
                            if($viajesProvisorio != null){
                                //el responsable tiene al menos un viaje a cargo
                                echo "No es posible eliminar al responsable ".$NroEmpleadoEliminar. " por que tiene viajes asociados \n";
                            }
                            elseif($responsable1->eliminar()){
                                echo "El responsable con NroEmpleado ".$NroEmpleadoEliminar. " ha sido eliminado \n";
                            }
                        }
                        else{
                            echo "El responsable con NroEmpleado ".$NroEmpleadoEliminar." no existe \n";
                        }
                        default:
                        echo "respuesta invalida \n";
                        break;
                     }
                        break;
            // FUNCIONA CORRECTAMENTE
        case '4':
            //MUESTRA LOS VIAJES
                $array = $viaje1->listar("idempresa = ".$empresa->getIdEmpresa());
                muestraViajes($array);
    }
}
function informacionEmpresa($r1,$empresa,$idEmpresa){

    switch($r1){
        case'1': 
            // ingresa informacion
            echo "---------------------------------------------------------------\n";
            echo "- ingrese los datos - \n";
            echo "Direccion: ";
            $direccionEmpresa = trim(fgets(STDIN));
            echo "Nombre: ";
            $nombreEmpresa = trim(fgets(STDIN));
            $empresa->insertar($nombreEmpresa,$direccionEmpresa);
            echo "Informacion cargada \n";
        break;
        case 'a':
            // modifica información
            $opcion1 = true;
            while($opcion1){
                echo "- modificar datos de la empresa -\n";
                echo "Direccion: ";
                $direccionEmpresa = trim(fgets(STDIN));
                echo "Nombre: ";
                $nombreEmpresa = trim(fgets(STDIN));
                $empresa->setDireccion($direccionEmpresa);
                $empresa->setNombre($nombreEmpresa);
                echo "seguir modificando? (s/n): ";
                $respOpcion1 = trim(fgets(STDIN));
                if($respOpcion1 != 's'){
                    $opcion1 = false;
                    if($empresa->modificar()){
                        echo "Informacion modificada \n";
                    }
                    else{
                        echo "error";
                    }
                }
                else{
                    echo "Ingrese el id de la empresa con la que quiere trabajar: ";
                    $idEmpresa1 = trim(fgets(STDIN));
                    if($empresa->Buscar($idEmpresa1)){
                        echo "la empresa existe \n";
                    }
                    else{
                        echo "la empresa no existe \n";
                        $opcion1 = false;
                    }
                }
            }
        break;
        case 'b':
            // elimina información
            echo "- eliminar datos -\n";
            $viaje1 = new Viaje();
            if(count($viaje1->listar("idempresa = ".$empresa->getIdEmpresa())) != 0 ){
                //no puede eliminar
                echo "# ERROR # \nLa empresa con id ".$empresa->getIdEmpresa()." tiene viajes asociados.\n";
                }
            else{
                $empresa->eliminar();
                echo "Empresa N".$idEmpresa." eliminada \n";
            }
        break;
        case 'c':
        // muestra informacion
        echo " - lista - \n";
            echo $empresa->__toString();
    }
}
function validarRespuesta(){
    $bandera = true;
    while($bandera){
        echo "Ingrese su respuesta: ";
        $resp = trim(fgets(STDIN));
        if($resp <= 3 && $resp >= 1){
            $bandera = false;
        }
        else{
            echo "respuesta incorrecta \n";
        }
    }
    return $resp;
}
function cargarPasajeros($viaje1){
    $continuar = true;
    $pasajeroN = new Pasajero();

    while($continuar){
        $arrayConPasajeros = $pasajeroN->listar("idviaje = ".$viaje1->getIdViaje());
        echo "existen actualmente ".count($arrayConPasajeros). " pasajeros cargados de ".$viaje1->getCantPasajeros()." \n";
        if(count($arrayConPasajeros)<($viaje1->getCantPasajeros())){
            echo "- cargue informacion del pasajero -\n";
            echo "DNI: ";
            $dni = trim(fgets(STDIN));
            if(existe($dni,$viaje1->getIdViaje(),$pasajeroN)){
                echo "- el dni ".$dni." ya existe -\n";
                break;
            }
            echo "Nombre: ";
            $nom = trim(fgets(STDIN));
            echo "Apellido: ";
            $apellido = trim(fgets(STDIN));
            echo "telefono: ";
            $telefono = trim(fgets(STDIN));
            //$id = $viaje1->getIdViaje();
            //en vez de mandar el id viaje, mandar el obj viaje 
            if($pasajeroN->insertar($dni,$nom,$apellido,$telefono,$viaje1)){
                echo "pasajero cargado exitosamente \n";
            }
            else{
                echo "ha ocurrido un error \n";
            }
            echo "ingresar otro pasajero? (si/no): ";
            $respuesta = trim(fgets(STDIN));
            if($respuesta != "si"){
                $continuar = false;
            }
        }
        else{
            echo "Lo sentimos, no hay mas asientos disponibles \n";
            $continuar = false;
        }
    }
}
function fueModificado($boolean){
    if($boolean){
        echo "modificado \n";
    }
    else{
        echo "error \n";
    }
}
function modificarResposable($viaje1){
        $opcion = true;
        $responsableQ = $viaje1->getResponsable();
        while($opcion){
            
            echo "a) nombre \n";
            echo "b) apellido \n";
            echo "c) nro licencia \n";
            echo "- seleccione una opcion: ";
            $resp = trim(fgets(STDIN));
            switch($resp){
                case 'a':
                    echo "nuevo nombre: ";
                    $newNombre = trim(fgets(STDIN));
                    $responsableQ->setNombre($newNombre);
                break;
                case 'b':
                    echo "nuevo apellido: ";
                    $newApellido = trim(fgets(STDIN));
                    $responsableQ->setApellido($newApellido);
                break;
                case 'c':
                    echo "nuevo nro licencia: ";
                    $newNroLic = trim(fgets(STDIN));
                    $responsableQ->setNroLicencia($newNroLic);
                break;        
        }
            echo "seguir modificando el responsable? (s/n): ";
            echo "\n";
            $resp = trim(fgets(STDIN));
            if($resp != 's'){
                $opcion = false;
                if($responsableQ->modificar()){
                    $viaje1->setResponsable($responsableQ);
                    echo "las modificaciones del resposable se realizaron con éxito \n";
                }
                else{
                    echo "error 505 \n";
                }
            }
        }
}
function elegirResponsable($r1){
    echo "a) Elegir un responsable ya cargado \n";
    echo "b) Crear un nuevo responsable \n";
    echo "su eleccion: ";
    $eleccion = trim(fgets(STDIN));
    echo "---------------------------------------------------------------\n";

    switch($eleccion){
        case 'a':
            //muestra el array de responsables cargados
            $array_responsable = $r1->listar();
            echo "-----------------------RESPONSABLES CARGADOS -------------------\n";
            echo "\n";
            muestraViajes($array_responsable);
            echo "seleccione un responsable: ";
            $responsable_seleccionado = trim(fgets(STDIN));
            if($r1->Buscar($responsable_seleccionado)){
                //el responsable existe
                if($r1->insertar($r1->getNroLicencia(),$r1->getNombre(),$r1->getApellido())){
                    echo "Responsable cargado exitosamente \n";
                }
                else{
                    echo "error: Responsable no cargado \n";
                }
            }
            else{
                echo "Hubo un error al asignar el responsable \n";
            }
            break;
        case 'b':
                    //informacion sobre el responsable a cargo
                    //nroEmpleado se autogenera
                    echo "Agregue los datos del nuevo responsable: \n";
                    echo "Número de licencia: ";
                    $numLicencia = trim(fgets(STDIN));
                    echo "Nombre: ";
                    $rnombre = trim(fgets(STDIN));
                    echo "Apellido: ";
                    $rapellido = trim(fgets(STDIN));
                    if($r1->insertar($numLicencia,$rnombre,$rapellido)){
                        echo "Responsable cargado exitosamente \n";
                    }
                    else{
                        echo "error: Responsable no cargado";
                    }
            break;          
    }
}
function existe($dni,$idViaje,$pasajero1){
    $array = $pasajero1->listar("idviaje = ".$idViaje);
    $continua = true;
    $i=0;
    $existe = false;
    while($i<count($array) && $continua){
        if($array[$i]->getDni() == $dni){
            $continua = false;
            $existe = true;
        }
        else{
            $i=$i+1;
            $existe = false;
        }
    }
    return $existe;
}
function buscarDni($array,$dni){
    $encuentra = false;
    $i = 0;
    while(!$encuentra && $i < count($array)){
        if($array[$i]->getDni() == $dni){
            $encuentra = true;
        }
        else{
            $i = $i+1;
        }
    }
    return $i;
}
function modificarPasajero($viaje1,$dni,$pasajero){
    $opcion = true;
    $p1 = new Pasajero();
    $pasajeros = $p1->listar("idviaje = ".$viaje1->getIdViaje());
    $i = buscarDni($pasajeros,$dni);
    $PasajeroActual = $pasajeros[$i];
    if(count($pasajeros)!= 0){
        echo "El pasajero que usted esta intentando modificar se encuentra en la posicion: ".$i."\n";
        echo "El array tiene ".count($pasajeros)." Pasajeros cargados \n";
    }
    else{
        echo "vacio \n";
    }
    while($opcion){
        echo "a) Nombre \n";
        echo "b) Apellido \n";
        echo "c) Telefono \n";
        echo "- seleccione una opción: ";
        $seleccion = trim(fgets(STDIN));
        switch($seleccion){
            case 'a':
                echo "ingrese el nuevo nombre: ";
                $newname = trim(fgets(STDIN));
                $pasajeros[$i]->setNombre($newname);
                echo "nuevo nombre: ".$pasajeros[$i]->getNombre()."\n";
            break;
            case 'b':
                echo "Ingrese el nuevo apellido: ";
                $newapellido = trim(fgets(STDIN));
                $pasajeros[$i]->setApellido($newapellido);
                echo "nuevo apellido: ".$pasajeros[$i]->getApellido()."\n";
            break;
            case 'c':
                echo "Ingrese el nuevo numero de telefono: ";
                $newtelefono = trim(fgets(STDIN));
                $pasajeros[$i]->setTelefono($newtelefono);
                echo "nuevo telefono: ".$pasajeros[$i]->getTelefono()."\n";
            break;
        }
        echo "seguir modificando? (s/n): ";
        echo "\n";
        $resp = trim(fgets(STDIN));
        if($resp != 's'){
            $opcion = false;
        }
        $viaje1->setPasajeros($pasajeros);
        if($pasajeros[$i]->modificar()){
            echo "el pasajero con dni ".$dni." ha sido modificado \n";
        }
    }
}
function muestraViajes($r1){
    for($i=0;$i<count($r1);$i++){
        echo $r1[$i] . "\n";
    }
}