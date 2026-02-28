<?php

require_once "../db.php";
header("Content-Type: application/json");

// Leer JSON
$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data["numero_o_id"]) ||
    !isset($data["contacto"]) ||
    !isset($data["mensaje_entrada"]) ||
    !isset($data["mensaje_respuesta"]) ||
    !isset($data["tipo_secuencia"])
) {
    http_response_code(400);
    echo json_encode(["mensaje" => "Datos incompletos"]);
    exit;
}

$numero_o_id      = $data["numero_o_id"];
$contacto         = $data["contacto"];
$mensaje_entrada  = $data["mensaje_entrada"];
$mensaje_respuesta = $data["mensaje_respuesta"];
$tipo_secuencia   = $data["tipo_secuencia"];
$fecha            = date("Y-m-d H:i:s");

try {

    $db = new DB();
    $pdo = $db->connect();

    $stmt = $pdo->prepare("
        INSERT INTO historial_conversaciones
        (
            fecha,
            numero_o_id,
            contacto,
            mensaje_entrada,
            mensaje_respuesta,
            tipo_secuencia
        )
        VALUES
        (
            :fecha,
            :numero_o_id,
            :contacto,
            :mensaje_entrada,
            :mensaje_respuesta,
            :tipo_secuencia
        )
    ");

    $stmt->bindParam(":fecha", $fecha);
    $stmt->bindParam(":numero_o_id", $numero_o_id);
    $stmt->bindParam(":contacto", $contacto);
    $stmt->bindParam(":mensaje_entrada", $mensaje_entrada);
    $stmt->bindParam(":mensaje_respuesta", $mensaje_respuesta);
    $stmt->bindParam(":tipo_secuencia", $tipo_secuencia);

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
