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
    // CONSULTA SOLO ACTIVAS
    // =============================
    $stmt = $pdo->prepare("
        SELECT 
            idcategoria AS id,
            descripcion
        FROM categoria
        WHERE estado = 'activo'
        ORDER BY descripcion ASC
    ");

    $stmt->execute();

    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($categorias);
} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "mensaje" => "Error interno"
        // "error" => $e->getMessage()
    ]);
}
