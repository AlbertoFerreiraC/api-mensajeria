<?php

require_once "../db.php";

header("Content-Type: application/json");
session_start();

try {
    // =============================
    // CONEXIÓN
    // =============================
    $db = new DB();
    $pdo = $db->connect();

    // =============================
    // CONSULTA
    // =============================
    $stmt = $pdo->prepare("
        SELECT 
            idusuario,
            nombre,
            estado
        FROM usuario
        where estado = 'activo'
        ORDER BY idusuario DESC
    ");

    $stmt->execute();

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($usuarios);

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "mensaje" => "Error interno"
        // "error" => $e->getMessage() // usar solo en desarrollo
    ]);
}