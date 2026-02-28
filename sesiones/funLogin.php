<?php

require_once "../db.php";

header("Content-Type: application/json");
session_start();

// Leer JSON enviado desde JS
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["usuario"]) || !isset($data["pass"])) {
    http_response_code(400);
    echo json_encode(["mensaje" => "Datos incompletos"]);
    exit;
}

$usuario = $data["usuario"];
$password = base64_decode($data["pass"]);

try {

    // 🔥 Crear conexión correctamente
    $db = new DB();
    $pdo = $db->connect();

    $stmt = $pdo->prepare("
        SELECT idusuario, nombre, pass, estado
        FROM usuario
        WHERE nombre = :usuario
        LIMIT 1
    ");

    $stmt->bindParam(":usuario", $usuario, PDO::PARAM_STR);
    $stmt->execute();

    $respuesta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$respuesta) {
        http_response_code(401);
        echo json_encode(["mensaje" => "Usuario no existe"]);
        exit;
    }

    if ($respuesta["estado"] != "activo") {
        http_response_code(401);
        echo json_encode(["mensaje" => "Usuario inactivo"]);
        exit;
    }

    // =============================
    // VALIDAR CONTRASEÑA
    // =============================

    $hashGuardado = $respuesta["pass"];

    // Si el password guardado parece un hash moderno
    if (password_get_info($hashGuardado)["algo"] !== 0) {

        if (!password_verify($password, $hashGuardado)) {
            http_response_code(401);
            echo json_encode(["mensaje" => "Contraseña incorrecta"]);
            exit;
        }
    } else {
        // Compatibilidad con usuarios antiguos (texto plano)

        if ($hashGuardado !== $password) {
            http_response_code(401);
            echo json_encode(["mensaje" => "Contraseña incorrecta"]);
            exit;
        }
    }

    // Crear sesión
    $_SESSION["iniciarSesion"] = "ok";
    $_SESSION["idusuario"] = $respuesta["idusuario"];
    $_SESSION["nombre"] = $respuesta["nombre"];

    http_response_code(200);
    echo json_encode([
        "idusuario" => $respuesta["idusuario"],
        "nombre" => $respuesta["nombre"]
    ]);
} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "mensaje" => "Error interno",
        "error" => $e->getMessage() // puedes quitar esto en producción
    ]);
}
