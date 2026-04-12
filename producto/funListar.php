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
            idproducto AS id,
            categoria,
            tipo_producto,
            codigo,
            descripcion,
            precio_lista,
            existencia,
            url_imagen,
            estado
        FROM producto
        where estado = 'activo'
        ORDER BY idproducto DESC
    ");

    $stmt->execute();

    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($productos);
} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "mensaje" => "Error interno"
        // "error" => $e->getMessage() // solo en desarrollo
    ]);
}
