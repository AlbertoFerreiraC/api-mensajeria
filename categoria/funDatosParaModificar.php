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
    // LEER JSON ENVIADO DESDE JS
    // =============================
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["id"]) || empty($data["id"])) {
        http_response_code(400);
        echo json_encode(["mensaje" => "Datos incompletos"]);
        exit;
    }

    $id = $data["id"];

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
        WHERE idcategoria = :id
        LIMIT 1
    ");

    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $categoria = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($categoria);
} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "mensaje" => "Error interno"
        // "error" => $e->getMessage() // solo en desarrollo
    ]);
}
