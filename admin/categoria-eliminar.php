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

    die('Categoría no especificada.');

}


/* ==============================
   OBTENER CATEGORÍA
   ============================== */

$stmt = $pdo->prepare("
    SELECT *
    FROM categorias
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$categoria = $stmt->fetch();


if (!$categoria) {

    die('Categoría no encontrada.');

}


/* ==============================
   CONTAR PRODUCTOS
   ============================== */

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM productos
    WHERE categoria_id = ?
");

$stmt->execute([$id]);

$totalProductos = (int) $stmt->fetchColumn();


/* ==============================
   ELIMINAR
   ============================== */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && $totalProductos === 0
) {

    $stmt = $pdo->prepare("
        DELETE FROM categorias
        WHERE id = ?
    ");

    $stmt->execute([$id]);


    /* ==============================
       VOLVER AL LISTADO
       ============================== */

    header('Location: categorias.php');

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
        Eliminar categoría | Administración
    </title>

</head>

<body>


    <!-- ==============================
         CONTENIDO
         ============================== -->

    <main>

        <h1>
            Eliminar categoría
        </h1>


        <h2>
            <?= htmlspecialchars($categoria['nombre']) ?>
        </h2>


        <?php if ($totalProductos > 0): ?>


            <!-- ==============================
                 CATEGORÍA CON PRODUCTOS
                 ============================== -->

            <p>

                No se puede eliminar esta categoría porque
                tiene <?= $totalProductos ?> producto(s) asociado(s).

            </p>


            <p>

                Primero debes cambiar los productos a otra
                categoría o desactivarla.

            </p>


            <a href="categorias.php">
                ← Volver a categorías
            </a>


        <?php else: ?>


            <!-- ==============================
                 CONFIRMACIÓN
                 ============================== -->

            <p>

                Esta categoría no tiene productos asociados.

            </p>


            <p>

                ¿Estás seguro de que quieres eliminarla?

            </p>


            <form method="POST">

                <button type="submit">
                    Sí, eliminar categoría
                </button>

                <a href="categorias.php">
                    Cancelar
                </a>

            </form>


        <?php endif; ?>


    </main>


</body>

</html>