<?php

require_once "../db.php";
header("Content-Type: application/json");

try {

    $db = new DB();
    $pdo = $db->connect();

    // Total conversaciones
    $total = $pdo->query("SELECT COUNT(*) FROM historial_conversaciones")->fetchColumn();

    // Contactos únicos
    $contactos = $pdo->query("
        SELECT COUNT(DISTINCT numero_o_id)
        FROM historial_conversaciones
    ")->fetchColumn();

    // Conversaciones hoy
    $hoy = $pdo->query("
        SELECT COUNT(*)
        FROM historial_conversaciones
        WHERE DATE(fecha) = CURDATE()
    ")->fetchColumn();

    echo json_encode([
        "total_conversaciones" => $total,
        "total_contactos" => $contactos,
        "conversaciones_hoy" => $hoy
    ]);
} catch (Exception $e) {

    http_response_code(500);
    echo json_encode(["mensaje" => "Error interno"]);
}
