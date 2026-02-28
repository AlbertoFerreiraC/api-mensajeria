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
    // VALIDAR CAMPOS
    // =============================
    if (
        empty($_POST["categoria"]) ||
        empty($_POST["tipo_producto"]) ||
        empty($_POST["codigo"]) ||
        empty($_POST["descripcion"]) ||
        empty($_POST["precio_lista"]) ||
        empty($_POST["existencia"]) ||
        empty($_POST["estado"])
    ) {
        http_response_code(400);
        echo json_encode(["mensaje" => "Datos incompletos"]);
        exit;
    }

    $categoria      = trim($_POST["categoria"]);
    $tipo_producto  = trim($_POST["tipo_producto"]);
    $codigo         = trim($_POST["codigo"]);
    $descripcion    = trim($_POST["descripcion"]);
    $precio_lista   = floatval($_POST["precio_lista"]);
    $existencia     = intval($_POST["existencia"]);
    $estado         = trim($_POST["estado"]);

    // =============================
    // CONEXIÓN
    // =============================
    $db = new DB();
    $pdo = $db->connect();

    // =============================
    // VALIDAR CÓDIGO DUPLICADO
    // =============================
    $stmtExiste = $pdo->prepare("
        SELECT COUNT(*) 
        FROM producto 
        WHERE codigo = :codigo
    ");

    $stmtExiste->bindParam(":codigo", $codigo, PDO::PARAM_STR);
    $stmtExiste->execute();

    if ($stmtExiste->fetchColumn() > 0) {
        echo json_encode(["mensaje" => "registro_existente"]);
        exit;
    }

    // =============================
    // VALIDAR IMAGEN
    // =============================
    if (!isset($_FILES["imagen"]) || $_FILES["imagen"]["error"] != 0) {
        echo json_encode(["mensaje" => "nok"]);
        exit;
    }

    $permitidos = ["jpg", "jpeg", "png", "webp"];
    $extension = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));

    if (!in_array($extension, $permitidos)) {
        echo json_encode(["mensaje" => "formato_invalido"]);
        exit;
    }

    // =============================
    // RUTA ABSOLUTA SEGURA
    // =============================
    $directorio = $_SERVER["DOCUMENT_ROOT"] . "/mensajeria-app/adm-mensajeria/vistas/img/productos/";

    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }

    // =============================
    // GENERAR NOMBRE ÚNICO
    // =============================
    $nombreImagen = "producto_" . time() . "_" . rand(1000, 9999) . "." . $extension;
    $rutaFisica = $directorio . $nombreImagen;

    if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaFisica)) {
        echo json_encode(["mensaje" => "nok"]);
        exit;
    }

    // Ruta relativa para guardar en BD (para mostrar en navegador)
    $rutaGuardar = "vistas/img/productos/" . $nombreImagen;

    // =============================
    // INSERTAR EN BASE DE DATOS
    // =============================
    $stmt = $pdo->prepare("
        INSERT INTO producto 
        (categoria, tipo_producto, codigo, descripcion, precio_lista, existencia, url_imagen, estado)
        VALUES
        (:categoria, :tipo_producto, :codigo, :descripcion, :precio_lista, :existencia, :url_imagen, :estado)
    ");

    $stmt->bindParam(":categoria", $categoria);
    $stmt->bindParam(":tipo_producto", $tipo_producto);
    $stmt->bindParam(":codigo", $codigo);
    $stmt->bindParam(":descripcion", $descripcion);
    $stmt->bindParam(":precio_lista", $precio_lista);
    $stmt->bindParam(":existencia", $existencia);
    $stmt->bindParam(":url_imagen", $rutaGuardar);
    $stmt->bindParam(":estado", $estado);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "ok"]);
    } else {
        echo json_encode(["mensaje" => "nok"]);
    }
} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "mensaje" => "Error interno"
        // "error" => $e->getMessage() // activar solo en desarrollo
    ]);
}
