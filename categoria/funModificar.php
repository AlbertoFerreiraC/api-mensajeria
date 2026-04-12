<?php

require_once "../db.php";

header("Content-Type: application/json");
session_start();

try {
    // =============================
    // VALIDAR DATOS
    // =============================
    if (
        !isset($_POST["descripcionModificar"]) ||
        !isset($_POST["estadoModificar"]) ||
        !isset($_POST["idModificar"])
    ) {
        http_response_code(400);
        echo json_encode(["mensaje" => "Datos incompletos"]);
        exit;
    }

    $descripcion = trim($_POST["descripcionModificar"]);
    $estado      = trim($_POST["estadoModificar"]);
    $id          = intval($_POST["idModificar"]);

    // =============================
    // CONEXIÓN
    // =============================
    $db = new DB();
    $pdo = $db->connect();

    // =============================
    // VALIDAR DUPLICADO (excepto el mismo registro)
    // =============================
    $stmtExiste = $pdo->prepare("
        SELECT COUNT(*) 
        FROM categoria 
        WHERE descripcion = :descripcion
        AND idcategoria != :id
    ");

    $stmtExiste->bindParam(":descripcion", $descripcion, PDO::PARAM_STR);
    $stmtExiste->bindParam(":id", $id, PDO::PARAM_INT);
    $stmtExiste->execute();

    if ($stmtExiste->fetchColumn() > 0) {
        echo json_encode(["mensaje" => "repetido"]);
        exit;
    }

    // =============================
    // ACTUALIZAR
    // =============================
    $stmt = $pdo->prepare("
        UPDATE categoria
        SET descripcion = :descripcion,
            estado = :estado
        WHERE idcategoria = :id
    ");

    $stmt->bindParam(":descripcion", $descripcion, PDO::PARAM_STR);
    $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
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
        // "error" => $e->getMessage() // solo en desarrollo
    ]);
}
