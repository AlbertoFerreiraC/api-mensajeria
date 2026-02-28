<?php

require_once "../db.php";

header("Content-Type: application/json");
session_start();

try {

    // =============================
    // VALIDAR SESIÓN
    // =============================
    if (!isset($_SESSION["iniciarSesion"]) || $_SESSION["iniciarSesion"] != "ok") {
        http_response_code(401);
        echo json_encode(["mensaje" => "No autorizado"]);
        exit;
    }

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
    // ACTUALIZAR ESTADO A INACTIVO
    // =============================
    $stmt = $pdo->prepare("
        UPDATE usuario
        SET estado = 'inactivo'
        WHERE idusuario = :id
    ");

    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

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
