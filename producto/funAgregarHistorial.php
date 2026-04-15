<?php

require_once "../db.php";

header("Content-Type: application/json");

try {

    // =============================
    // VALIDAR DESCRIPCIÓN
    // =============================
    
    $data = $_POST;

    // Si viene vacío, intentar leer JSON
    if (empty($data)) {
        $data = json_decode(file_get_contents("php://input"), true);
    }

    if (
        !isset($data["contactoNro"]) || trim($data["contactoNro"]) === "" ||
        !isset($data["descripcionContacto"]) || trim($data["descripcionContacto"]) === "" ||
        !isset($data["descripcionProducto"]) || trim($data["descripcionProducto"]) === "" ||
        !isset($data["precioProducto"]) || $data["precioProducto"] === ""
    ) {
        http_response_code(400);
        echo json_encode(["mensaje" => "Datos incompletos"]);
        exit;
    }

    $contactoNro = trim($data["contactoNro"]);
    $descripcionContacto = trim($data["descripcionContacto"]);
    $descripcionProducto = trim($data["descripcionProducto"]);
    $precioProducto = trim($data["precioProducto"]);

    // =============================
    // CONEXIÓN
    // =============================
    $db = new DB();
    $pdo = $db->connect();

   // =============================
    // INSERTAR
    // =============================
    $stmt = $pdo->prepare("
        INSERT INTO historial_pedidos (fecha_levantamiento,contacto_nro,contacto_desripcion,descripcion_producto,precio_producto,estado)
        VALUES (now(),:contactoNro,:descripcionContacto,:descripcionProducto,:precioProducto, 'pendiente')
    ");

    $stmt->bindParam(":contactoNro", $contactoNro, PDO::PARAM_STR);
    $stmt->bindParam(":descripcionContacto", $descripcionContacto, PDO::PARAM_STR);
    $stmt->bindParam(":descripcionProducto", $descripcionProducto, PDO::PARAM_STR);
    $stmt->bindParam(":precioProducto", $precioProducto, PDO::PARAM_STR);

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
