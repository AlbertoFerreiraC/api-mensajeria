<?php

require_once "../db.php";

header("Content-Type: application/json");

try {

    // =============================
    // VALIDAR DESCRIPCIÓN
    // =============================
    if (!isset($_POST["descripcionAgregar"]) || empty(trim($_POST["descripcionAgregar"]))) {
        http_response_code(400);
        echo json_encode(["mensaje" => "Datos incompletos"]);
        exit;
    }

    $descripcion = trim($_POST["descripcionAgregar"]);

    // =============================
    // CONEXIÓN
    // =============================
    $db = new DB();
    $pdo = $db->connect();

    // =============================
    // VERIFICAR DUPLICADO
    // =============================
    $stmtExiste = $pdo->prepare("
        SELECT COUNT(*) 
        FROM categoria 
        WHERE descripcion = :descripcion
    ");
    $stmtExiste->bindParam(":descripcion", $descripcion, PDO::PARAM_STR);
    $stmtExiste->execute();

    if ($stmtExiste->fetchColumn() > 0) {
        echo json_encode(["mensaje" => "registro_existente"]);
        exit;
    }

    // =============================
    // INSERTAR
    // =============================
    $stmt = $pdo->prepare("
        INSERT INTO categoria (descripcion, estado)
        VALUES (:descripcion, 'activo')
    ");

    $stmt->bindParam(":descripcion", $descripcion, PDO::PARAM_STR);

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
