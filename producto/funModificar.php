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
        empty($_POST["categoriaModificar"]) ||
        empty($_POST["descripcionModificar"]) ||
        empty($_POST["precio_listaModificar"])
    ) {
        http_response_code(400);
        echo json_encode(["mensaje" => "Datos incompletos"]);
        exit;
    }

    $id = intval($_POST["idModificar"]);
    $categoria = trim($_POST["categoriaModificar"]);
    $descripcion = trim($_POST["descripcionModificar"]);
    $precio_lista = floatval($_POST["precio_listaModificar"]);

    // =============================
    // CONEXIÓN
    // =============================
    $db = new DB();
    $pdo = $db->connect();

    // =============================
    // VALIDAR CÓDIGO DUPLICADO (EXCEPTO EL MISMO ID)
    // =============================
    $stmtExiste = $pdo->prepare("
    SELECT COUNT(*) 
    FROM producto 
    WHERE descripcion = :descripcion 
    AND categoria = :categoria
    AND idproducto != :id
    ");

    $stmtExiste->bindParam(":descripcion", $descripcion);
    $stmtExiste->bindParam(":categoria", $categoria);
    $stmtExiste->bindParam(":id", $id);

    if ($stmtExiste->fetchColumn() > 0) {
        echo json_encode(["mensaje" => "repetido"]);
        exit;
    }

    // =============================
    // OBTENER IMAGEN ACTUAL
    // =============================
    $stmtImagen = $pdo->prepare("
        SELECT url_imagen 
        FROM producto 
        WHERE idproducto = :id
    ");

    $stmtImagen->bindParam(":id", $id);
    $stmtImagen->execute();
    $productoActual = $stmtImagen->fetch(PDO::FETCH_ASSOC);

    $rutaGuardar = $productoActual["url_imagen"];

    // =============================
    // SI SE SUBE NUEVA IMAGEN
    // =============================
    if (isset($_FILES["imagenModificar"]) && $_FILES["imagenModificar"]["error"] == 0) {

        $permitidos = ["jpg", "jpeg", "png", "webp"];
        $extension = strtolower(pathinfo($_FILES["imagenModificar"]["name"], PATHINFO_EXTENSION));

        if (!in_array($extension, $permitidos)) {
            echo json_encode(["mensaje" => "formato_invalido"]);
            exit;
        }

        $directorio = $_SERVER["DOCUMENT_ROOT"] . "/mensajeria-app/adm-mensajeria/vistas/img/productos/";

        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $nombreImagen = "producto_" . time() . "_" . rand(1000, 9999) . "." . $extension;
        $rutaFisica = $directorio . $nombreImagen;

        if (!move_uploaded_file($_FILES["imagenModificar"]["tmp_name"], $rutaFisica)) {
            echo json_encode(["mensaje" => "nok"]);
            exit;
        }

        // Eliminar imagen anterior si existe
        if (!empty($productoActual["url_imagen"])) {
            $rutaAnterior = $_SERVER["DOCUMENT_ROOT"] . "/mensajeria-app/adm-mensajeria/" . $productoActual["url_imagen"];
            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }
        }

        $rutaGuardar = "vistas/img/productos/" . $nombreImagen;
    }

    // =============================
    // ACTUALIZAR PRODUCTO
    // =============================
    $stmt = $pdo->prepare("
        UPDATE producto SET
            categoria = :categoria,
            descripcion = :descripcion,
            precio_lista = :precio_lista,
            url_imagen = :url_imagen
        WHERE idproducto = :id
    ");

    $stmt->bindParam(":categoria", $categoria);
    $stmt->bindParam(":descripcion", $descripcion);
    $stmt->bindParam(":precio_lista", $precio_lista);
    $stmt->bindParam(":url_imagen", $rutaGuardar);
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
