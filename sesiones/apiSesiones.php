<?php

include_once 'sesiones.php';

class ApiSesiones{
   
    function login($array){
        $trabajador = new Sesion();       
        $ingreso = $trabajador->controlUsuario($array);  
        if(!empty($ingreso)){     
            $passIngresado = base64_decode($array['pass']);
            $passBd = base64_decode($ingreso[0]['pass']);
            if($passBd == $passIngresado){
                $generarCodigoCarga = $trabajador->generarCodigoSesion();  
                $codigoCarga = $generarCodigoCarga[0]['codigo'];
                $item = array(
                    'idUsuario'=> $ingreso[0]['idusuario'],
                    'nombre'=> $ingreso[0]['nombre'],
                    'nic'=> $ingreso[0]['nic'],
                    'nroSesion'=> $codigoCarga,
                    'rol'=> $ingreso[0]['rol'],
                    'mensaje'=> 'ok'
                );
                $trabajador->cargaSesion($item);  
                printJSON($item);
            }else{
                error("passInvalido");
                header("HTTP/1.1 401 Unauthorized");
            }    
        }else{
            error("noUsuario");
            header("HTTP/1.1 401 Unauthorized");
        }
    } 

    function finzalizarSesion($array){
        $herramienta = new Sesion();        
        $eliminar = $herramienta->cerrarSesion($array);
            if($eliminar == "ok"){
                exito("ok");
            }else{
                exito("nok");
            }        
    }


    function actualizarDatosUsuario($array){
        $herramienta = new Sesion();        
        $verificarId = $herramienta->verificarId($array);      
        if(!empty($verificarId)){
            $passActual = base64_decode($verificarId[0]['pass']);
            if($passActual == base64_decode($array['passActual'])){
                if($array['nuevoPass'] == $array['repitaNuevoPass']){
                    $eliminar = $herramienta->actualizarDatosUsuario($array);
                        if($eliminar == "ok"){
                            exito("ok");
                        }else{
                            exito("nok");
                        } 
                }else{
                    error("pi");
                }
            }else{
                error("npa");
            }
        }       
    }

    

} //FIN API SESIONES

    function error($mensaje){
        echo json_encode(array('mensaje' => $mensaje)); 
    }

    function exito($mensaje){
        echo json_encode(array('mensaje' => $mensaje)); 
    }

    function printJSON($array){
        echo json_encode($array);
    }

?>