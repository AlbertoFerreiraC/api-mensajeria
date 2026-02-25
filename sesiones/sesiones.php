<?php

include_once '../db.php';

class Sesion extends DB{


  function controlUsuario($item){
        $query = $this->connect()->prepare("select * from usuario where estado = 'activo' and nic = :nic");
    $query->bindParam(":nic", $item['nic'], PDO::PARAM_STR);
    if($query->execute()){
      return $query->fetchAll();
    }else{
      return null;		
    }
  }

  function verificarId($item){
    $query = $this->connect()->prepare("select * from usuario where estado = 'activo' and idusuario = :idUsuario");
    $query->bindParam(":idUsuario", $item['idUsuario'], PDO::PARAM_STR);
    if($query->execute()){
      return $query->fetchAll();
    }else{
      return null;		
    }
  }

  function generarCodigoSesion(){
    $query = $this->connect()->prepare("select DATE_FORMAT(now(),'%Y%m%d%H%i%S') codigo");
    if($query->execute()){
      return $query->fetchAll();
    }else{
      return null;		
    }
  }

  function cargaSesion($item){
    $query = $this->connect()->prepare("insert into sesiones(usuario,codigo_sesion,entrada) values(:idUsuario,:nroSesion,now())");
    $query->bindParam(":idUsuario", $item['idUsuario'], PDO::PARAM_STR);
    $query->bindParam(":nroSesion", $item['nroSesion'], PDO::PARAM_STR);
    if($query->execute()){
        return "ok";
      }else{
        return "nok";		
      }
  }

  function cerrarSesion($item){
    $query = $this->connect()->prepare("update sesiones set salida = now() where codigo_sesion = :nroSesion");
    $query->bindParam(":nroSesion", $item['nroSesion'], PDO::PARAM_STR);
    if($query->execute()){
        return "ok";
      }else{
        return "nok";		
      }
  }

  function actualizarDatosUsuario($item){
    $query = $this->connect()->prepare("update usuario set 
    pass = :nuevoPass where idusuario = :idUsuario and estado = 'activo'");
    $query->bindParam(":idUsuario", $item['idUsuario'], PDO::PARAM_STR);
    $query->bindParam(":nuevoPass", $item['nuevoPass'], PDO::PARAM_STR);
    if($query->execute()){
        return "ok";
      }else{
        return "nok";		
      }
  }


       
}



?>