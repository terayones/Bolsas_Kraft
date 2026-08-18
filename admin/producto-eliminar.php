<?php

/* ==============================
   CONEXIÓN
   ============================== */

require_once __DIR__ . '/../includes/db.php';


/* ==============================
   OBTENER ID
   ============================== */

$id = (int) ($_GET['id'] ?? 0);


if ($id <= 0) {

    die('Producto no especificado.');

}


/* ==============================
   OBTENER PRODUCTO
   ============================== */

$stmt = $pdo->prepare("
    SELECT *
    FROM productos
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$producto = $stmt->fetch();


if (!$producto) {

    die('Producto no encontrado.');

}


/* ==============================
   ELIMINAR PRODUCTO
   ============================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        DELETE FROM productos
        WHERE id = ?
    ");

    $stmt->execute([$id]);


    /* ==============================
       VOLVER AL LISTADO
       ============================== */

    header('Location: productos.php');

    exit;

}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Eliminar producto | Administración
    </title>

</head>

<body>


    <!-- ==============================
         CONFIRMACIÓN
         ============================== -->

    <main>

        <h1>
            Eliminar producto
        </h1>


        <p>
            ¿Estás seguro de que quieres eliminar este producto?
        </p>


        <h2>
            <?= htmlspecialchars($producto['nombre']) ?>
        </h2>


        <!-- ==============================
             FORMULARIO DE ELIMINACIÓN
             ============================== -->

        <form method="POST">

            <button type="submit">
                Sí, eliminar producto
            </button>

            <a href="productos.php">
                Cancelar
            </a>

        </form>


    </main>


</body>

</html>