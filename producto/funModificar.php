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
        empty($_POST["idModificar"]) ||
        empty($_POST["categoriaModificar"]) ||
        empty($_POST["tipo_productoModificar"]) ||
        empty($_POST["codigoModificar"]) ||
        empty($_POST["descripcionModificar"]) ||
        empty($_POST["precio_listaModificar"]) ||
        empty($_POST["existenciaModificar"]) ||
        empty($_POST["estadoModificar"])
    ) {
        http_response_code(400);
        echo json_encode(["mensaje" => "Datos incompletos"]);
        exit;
    }

    $id             = intval($_POST["idModificar"]);
    $categoria      = trim($_POST["categoriaModificar"]);
    $tipo_producto  = trim($_POST["tipo_productoModificar"]);
    $codigo         = trim($_POST["codigoModificar"]);
    $descripcion    = trim($_POST["descripcionModificar"]);
    $precio_lista   = floatval($_POST["precio_listaModificar"]);
    $existencia     = intval($_POST["existenciaModificar"]);
    $estado         = trim($_POST["estadoModificar"]);

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
        WHERE codigo = :codigo 
        AND idproducto != :id
    ");

    $stmtExiste->bindParam(":codigo", $codigo);
    $stmtExiste->bindParam(":id", $id);
    $stmtExiste->execute();

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
            tipo_producto = :tipo_producto,
            codigo = :codigo,
            descripcion = :descripcion,
            precio_lista = :precio_lista,
            existencia = :existencia,
            url_imagen = :url_imagen,
            estado = :estado
        WHERE idproducto = :id
    ");

    $stmt->bindParam(":categoria", $categoria);
    $stmt->bindParam(":tipo_producto", $tipo_producto);
    $stmt->bindParam(":codigo", $codigo);
    $stmt->bindParam(":descripcion", $descripcion);
    $stmt->bindParam(":precio_lista", $precio_lista);
    $stmt->bindParam(":existencia", $existencia);
    $stmt->bindParam(":url_imagen", $rutaGuardar);
    $stmt->bindParam(":estado", $estado);
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
