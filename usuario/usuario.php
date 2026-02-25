<?php

include_once '../db.php';

class Usuario extends DB{


    function listar(){
        $query = $this->connect()->prepare("select * from usuario where estado = 'activo'");
        if($query->execute()){
          return $query->fetchAll();
        }else{
          return null;		
        }
    }


    function verificar_existencia_cedula($item){
        $query = $this->connect()->prepare("select * from usuario where estado = 'activo' and 
        cedula = :cedula");
        $query->bindParam(":cedula", $item['cedula'], PDO::PARAM_STR);
        if($query->execute()){
          return $query->fetchAll();
        }else{
          return null;		
        }
    }

    function verificar_existencia_nic($item){
      $query = $this->connect()->prepare("select * from usuario where estado = 'activo' and 
      nic = :nic");
      $query->bindParam(":nic", $item['nic'], PDO::PARAM_STR);
      if($query->execute()){
        return $query->fetchAll();
      }else{
        return null;		
      }
  }

    function agregar($item){
        $query = $this->connect()->prepare("insert into usuario(rol,cedula,nombre,telefono,correo,nic,pass,pre_aprueba,aprueba,estado,fecha_creacion) values(:rol,:cedula,:nombre,:telefono,:correo,:nic,'MTIzNDU=',:preAprueba,:aprueba,'activo',now())");
        $query->bindParam(":rol", $item['rol'], PDO::PARAM_STR);
        $query->bindParam(":cedula", $item['cedula'], PDO::PARAM_STR);
        $query->bindParam(":nombre", $item['nombre'], PDO::PARAM_STR);
        $query->bindParam(":nic", $item['nic'], PDO::PARAM_STR);
        $query->bindParam(":telefono", $item['telefono'], PDO::PARAM_STR);
        $query->bindParam(":correo", $item['correo'], PDO::PARAM_STR);
        $query->bindParam(":preAprueba", $item['preAprueba'], PDO::PARAM_STR);
        $query->bindParam(":aprueba", $item['aprueba'], PDO::PARAM_STR);

        if($query->execute()){
            return "ok";
          }else{
            return "nok";		
          }
    }

    function obtenerDatosParaModificar($item){
      $query = $this->connect()->prepare("select * from usuario where estado = 'activo' and 
      idusuario = :id");
      $query->bindParam(":id", $item['id'], PDO::PARAM_STR);
      if($query->execute()){
        return $query->fetchAll();
      }else{
        return null;		
      }
    }

    function modificar($item){
      $query = $this->connect()->prepare("update usuario set 
          rol = :rol,   
          cedula = :cedula, 
          nombre = :nombre,
          nic = :nic,
          telefono = :telefono,
          correo = :correo,
          pre_aprueba = :preAprueba,
          aprueba = :aprueba
         where idusuario = :id and estado = 'activo'");
          $query->bindParam(":id", $item['id'], PDO::PARAM_STR);
          $query->bindParam(":rol", $item['rol'], PDO::PARAM_STR);
          $query->bindParam(":cedula", $item['cedula'], PDO::PARAM_STR);
          $query->bindParam(":nombre", $item['nombre'], PDO::PARAM_STR);
          $query->bindParam(":nic", $item['nic'], PDO::PARAM_STR);
          $query->bindParam(":telefono", $item['telefono'], PDO::PARAM_STR);
          $query->bindParam(":correo", $item['correo'], PDO::PARAM_STR);
          $query->bindParam(":preAprueba", $item['preAprueba'], PDO::PARAM_STR);
          $query->bindParam(":aprueba", $item['aprueba'], PDO::PARAM_STR);
      if($query->execute()){
          return "ok";
        }else{
          return "nok";		
        }
    }

    function eliminar($item){
      $query = $this->connect()->prepare("update usuario set estado = 'inactivo' where idusuario = :id");
      $query->bindParam(":id", $item['id'], PDO::PARAM_STR);
      if($query->execute()){
          return "ok";
        }else{
          return "nok";		
        }
    }

    function resetearPass($item){
      $query = $this->connect()->prepare("update usuario set pass = 'MTIzNDU=' where idusuario = :id");
      $query->bindParam(":id", $item['id'], PDO::PARAM_STR);
      if($query->execute()){
          return "ok";
        }else{
          return "nok";		
        }
    }

       
}



?>