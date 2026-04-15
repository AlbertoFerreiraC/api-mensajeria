<?php

require_once "../db.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (
    empty($data["id"]) ||
    empty($data["estado"])
) {
    http_response_code(400);
    echo json_encode(["mensaje" => "Datos incompletos"]);
    exit;
}

$id = intval($data["id"]);
$estado = trim($data["estado"]);
$observacion = isset($data["observacion"]) ? trim($data["observacion"]) : "";

try {

    $db = new DB();
    $pdo = $db->connect();

    $stmt = $pdo->prepare("
        UPDATE historial_pedidos SET
            estado = :estado,
            observacion = :observacion,
            fecha_procesamiento = NOW()
        WHERE idhistorial_pedidos = :id
    ");

    $stmt->bindParam(":estado", $estado);
    $stmt->bindParam(":observacion", $observacion);
    $stmt->bindParam(":id", $id);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "ok"]);
    } else {
        echo json_encode(["mensaje" => "nok"]);
    }

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "mensaje" => "Error interno"
    ]);
}