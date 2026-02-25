<?php

    include_once 'apiSesiones.php';

    $api = new ApiSesiones();
    $datosRecibidos = file_get_contents("php://input");
    $datos = json_decode($datosRecibidos);
    $item = array(
        'nroSesion' => $datos->nroSesion
    );
    $api -> finzalizarSesion($item);
?>