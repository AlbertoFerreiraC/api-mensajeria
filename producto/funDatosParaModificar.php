<?php

require_once "../db.php";

header("Content-Type: application/json");
session_start();

try {
    // =============================
    // LEER JSON
    // =============================
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["id"]) || empty($data["id"])) {
        http_response_code(400);
        echo json_encode(["mensaje" => "Datos incompletos"]);
        exit;
    }

    $id = intval($data["id"]);

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
            descripcion,
            precio_lista,
            url_imagen
        FROM producto
        WHERE idproducto = :id
        LIMIT 1
    ");

    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $producto = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($producto);
} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "mensaje" => "Error interno"
        // "error" => $e->getMessage() // solo en desarrollo
    ]);
}
