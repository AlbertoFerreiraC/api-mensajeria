<?php

require_once "../db.php";

header("Content-Type: application/json");
session_start();

try {
    // =============================
    // VALIDAR CAMPOS
    // =============================
    if (
        empty($_POST["idModificar"]) ||
        empty($_POST["nombreModificar"])
    ) {
        http_response_code(400);
        echo json_encode(["mensaje" => "Datos incompletos"]);
        exit;
    }

    $id     = intval($_POST["idModificar"]);
    $nombre = trim($_POST["nombreModificar"]);

    // =============================
    // CONEXIÓN
    // =============================
    $db = new DB();
    $pdo = $db->connect();

    // =============================
    // VALIDAR DUPLICADO (EXCEPTO MISMO ID)
    // =============================
    $stmtExiste = $pdo->prepare("
        SELECT COUNT(*) 
        FROM usuario 
        WHERE nombre = :nombre 
        AND idusuario != :id
    ");

    $stmtExiste->bindParam(":nombre", $nombre, PDO::PARAM_STR);
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
        UPDATE usuario SET
            nombre = :nombre
        WHERE idusuario = :id
    ");

    $stmt->bindParam(":nombre", $nombre);
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
        // "error" => $e->getMessage()
    ]);
}
