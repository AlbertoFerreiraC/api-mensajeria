<?php

require_once "../db.php";
header("Content-Type: application/json");

try {

    $db = new DB();
    $pdo = $db->connect();

    // Filtro opcional por número
    $numero = isset($_GET["numero_o_id"]) ? $_GET["numero_o_id"] : null;

    if ($numero) {

        $stmt = $pdo->prepare("
            SELECT *
            FROM historial_conversaciones
            WHERE numero_o_id = :numero
            ORDER BY fecha ASC
        ");

        $stmt->bindParam(":numero", $numero);
        $stmt->execute();

    } else {

        $stmt = $pdo->prepare("
            SELECT *
            FROM historial_conversaciones
            ORDER BY fecha DESC
        ");

        $stmt->execute();
    }

    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultado);

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "mensaje" => "Error interno"
    ]);
}