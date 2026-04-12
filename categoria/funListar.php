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
            idcategoria AS id,
            descripcion,
            estado
        FROM categoria
        where estado = 'activo'
        ORDER BY idcategoria DESC
    ");

    $stmt->execute();

    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($categorias);
} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "mensaje" => "Error interno"
        // "error" => $e->getMessage() // solo en desarrollo
    ]);
}
