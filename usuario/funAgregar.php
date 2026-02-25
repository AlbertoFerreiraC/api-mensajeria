<?php

    include_once 'apiUsuario.php';

    $api = new ApiUsuario();
    $datosRecibidos = file_get_contents("php://input");
    $datos = json_decode($datosRecibidos);
    $item = array(
        'rol' => $datos->rol,
        'cedula' => $datos->cedula,
        'nombre' => $datos->nombre,
        'nic' => $datos->nic,
        'telefono' => $datos->telefono,       
        'correo' => $datos->correo,
        'preAprueba' => $datos->preAprueba,
        'aprueba' => $datos->aprueba
    );
    $api -> agregarApi($item);
?>