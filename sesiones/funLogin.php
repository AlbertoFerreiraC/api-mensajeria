<?php

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../db.php';

try {

    $usuario = trim($_POST["usuario"] ?? '');
    $password = $_POST["password"] ?? '';

    if (empty($usuario) || empty($password)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Complete todos los campos"
        ]);
        exit;
    }

    $sql = "SELECT id, id_rol, usuario, nombre, email, contrasena_hash
            FROM usuarios
            WHERE usuario = :usuario
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Nota: Si usas encriptación real cambia esto por !password_verify($password, $user["contrasena_hash"])
    if (!$user || $password !== $user["contrasena_hash"]) {

        http_response_code(401);

        echo json_encode([
            "success" => false,
            "message" => "Usuario o contraseña incorrectos"
        ]);
        exit;
    }

    /* =========================
        🔥 GUARDAR SESIÓN AQUÍ
    ========================= */
    $_SESSION["id"] = $user["id"];
    $_SESSION["id_rol"] = $user["id_rol"];
    $_SESSION["usuario"] = $user["usuario"];
    $_SESSION["nombre"] = $user["nombre"];
    $_SESSION["email"] = $user["email"];

    /* =========================================
        CÁLCULO DINÁMICO DEL DESTINO POR ROL
    ========================================= */
    $redirigirA = "inicio"; // Por defecto administrativo

    if ((int) $user["id_rol"] === 1) {
        $redirigirA = "perfil_tecnico";
    } else if ((int) $user["id_rol"] === 2) {
        $redirigirA = "inicio";
    }

    echo json_encode([
        "success" => true,
        "nombre" => $user["nombre"],
        "redirect" => $redirigirA // Ahora devuelve el string correcto para tu router
    ]);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Error interno"
    ]);
}