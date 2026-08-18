<?php


/* ==============================
   CONEXIÓN
   ============================== */

require_once __DIR__ . '/../includes/db.php';

require_once __DIR__ . '/../includes/procesador-imagen.php';





/* ==============================
   VALIDAR PRODUCTO
   ============================== */


$producto_id = (int)(
    $_POST['producto_id'] ?? 0
);



if ($producto_id <= 0) {

    die('Producto inválido.');

}






/* ==============================
   VALIDAR IMAGEN
   ============================== */


if (
    !isset($_FILES['imagen'])
    ||
    $_FILES['imagen']['error'] !== UPLOAD_ERR_OK
) {


    header(
        "Location: producto-editar.php?id=".$producto_id."&mensaje=error_imagen"
    );

    exit;

}






/* ==============================
   OBTENER PRODUCTO
   ============================== */


$stmt = $pdo->prepare("
    SELECT 
        slug,
        nombre
    FROM productos
    WHERE id = ?
    LIMIT 1
");


$stmt->execute([

    $producto_id

]);


$producto = $stmt->fetch();





if (!$producto) {

    die('Producto no encontrado.');

}







/* ==============================
   PROCESAR IMAGEN
   ============================== */


$imagenes = procesarImagen(

    $_FILES['imagen'],

    $producto['slug']

);







/* ==============================
   VERIFICAR IMAGEN PRINCIPAL
   ============================== */


$stmt = $pdo->prepare("

    SELECT COUNT(*)

    FROM imagenes_productos

    WHERE producto_id = ?
    AND principal = 1

");



$stmt->execute([

    $producto_id

]);



$tienePrincipal = $stmt->fetchColumn();




/*
   Si no existe imagen principal,
   esta será la principal
*/


$principal = $tienePrincipal == 0 ? 1 : 0;








/* ==============================
   GUARDAR IMAGEN
   ============================== */


$stmt = $pdo->prepare("

    INSERT INTO imagenes_productos
    (

        producto_id,
        archivo_original,
        archivo_grande,
        archivo_miniatura,
        alt,
        orden,
        principal

    )

    VALUES

    (

        ?,
        ?,
        ?,
        ?,
        ?,
        0,
        ?

    )

");





$stmt->execute([


    $producto_id,


    $imagenes['original'],


    $imagenes['grande'],


    $imagenes['miniatura'],


    $producto['nombre'],


    $principal


]);








/* ==============================
   REDIRECCIÓN
   ============================== */


header(

    "Location: producto-editar.php?id=".$producto_id."&mensaje=imagen_ok"

);


exit;