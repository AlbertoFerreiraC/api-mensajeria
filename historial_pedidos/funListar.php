<?php

require_once "../db.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$estado = isset($data["estado"]) ? $data["estado"] : "";
$desde = isset($data["desde"]) ? $data["desde"] : "";
$hasta = isset($data["hasta"]) ? $data["hasta"] : "";

try {

    $db = new DB();
    $pdo = $db->connect();

    $sql = "SELECT * FROM historial_pedidos WHERE 1=1 ";

    if (!empty($estado)) {
        $sql .= " AND estado = :estado ";
    }

    if (!empty($desde)) {
        $sql .= " AND DATE(fecha_levantamiento) >= :desde ";
    }

    if (!empty($hasta)) {
        $sql .= " AND DATE(fecha_levantamiento) <= :hasta ";
    }

    $sql .= " ORDER BY fecha_levantamiento DESC";

    $stmt = $pdo->prepare($sql);

    if (!empty($estado)) {
        $stmt->bindParam(":estado", $estado);
    }

    if (!empty($desde)) {
        $stmt->bindParam(":desde", $desde);
    }

    if (!empty($hasta)) {
        $stmt->bindParam(":hasta", $hasta);
    }

    $stmt->execute();

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultados);

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "mensaje" => "Error interno"
    ]);
}