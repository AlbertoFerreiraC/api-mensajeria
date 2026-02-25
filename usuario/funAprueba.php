<?php

include_once 'apiUsuario.php';
$api = new ApiUsuario();
$datosRecibidos = file_get_contents("php://input");
$datos = json_decode($datosRecibidos);
$api->listarApruebaApi();
