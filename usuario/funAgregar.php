<?php

require_once "../db.php";

header("Content-Type: application/json");
session_start();

try {
    // =============================
    // VALIDAR CAMPOS
    // =============================
    if (
        empty($_POST["nombreAgregar"])
    ) {
        http_response_code(400);
        echo json_encode(["mensaje" => "Datos incompletos"]);
        exit;
    }

    $nombre = trim($_POST["nombreAgregar"]);

    // =============================
    // CONEXIÓN
    // =============================
    $db = new DB();
    $pdo = $db->connect();

    // =============================
    // VALIDAR USUARIO DUPLICADO
    // =============================
    $stmtExiste = $pdo->prepare("
        SELECT COUNT(*) 
        FROM usuario 
        WHERE nombre = :nombre
    ");

    $stmtExiste->bindParam(":nombre", $nombre, PDO::PARAM_STR);
    $stmtExiste->execute();

    if ($stmtExiste->fetchColumn() > 0) {
        echo json_encode(["mensaje" => "repetido"]);
        exit;
    }

    // =============================
    // CONTRASEÑA POR DEFECTO
    // =============================
    $passwordDefault = password_hash("12345", PASSWORD_DEFAULT);

    // =============================
    // INSERTAR
    // =============================
    $stmt = $pdo->prepare("
        INSERT INTO usuario (nombre, pass, estado)
        VALUES (:nombre, :pass, 'activo')
    ");

    $stmt->bindParam(":nombre", $nombre);
    $stmt->bindParam(":pass", $passwordDefault);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "ok"]);
    } else {
        echo json_encode(["mensaje" => "nok"]);
    }
} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "mensaje" => "Error interno"
        // "error" => $e->getMessage()
    ]);
}
