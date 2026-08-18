<?php

/* ==============================
   CONEXIÓN
   ============================== */

require_once __DIR__ . '/../includes/db.php';


/* ==============================
   OBTENER PRODUCTOS
   ============================== */

$stmt = $pdo->query("
    SELECT

        productos.*,

        categorias.nombre AS categoria_nombre,

        imagenes_productos.archivo_miniatura AS imagen_principal


    FROM productos


    LEFT JOIN categorias
        ON productos.categoria_id = categorias.id


    LEFT JOIN imagenes_productos
        ON imagenes_productos.producto_id = productos.id
        AND imagenes_productos.principal = 1


    ORDER BY productos.id DESC
");

$productos = $stmt->fetchAll();
$mensaje = $_GET['mensaje'] ?? '';

?>

<?php

$titulo_admin = "Productos";

require_once __DIR__ . '/../includes/admin-header.php';

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Productos | Administración
    </title>


    <link rel="stylesheet" href="../assets/css/admin.css">

</head>

<body>


    <!-- ==============================
         HEADER
         ============================== -->

    <!-- ==============================
         CONTENIDO
         ============================== -->

    <main>

        <?php if ($mensaje === 'actualizado'): ?>

            <div class="alerta exito">
                ✅ Producto actualizado correctamente
            </div>

        <?php endif; ?>

        <h2>
            Lista de productos
        </h2>


        <!-- ==============================
             AGREGAR PRODUCTO
             ============================== -->

        <p>

            <a href="producto-crear.php">
                + Agregar producto
            </a>

        </p>


        <!-- ==============================
             LISTADO
             ============================== -->

        <?php if ($productos): ?>

            <table border="1" cellpadding="10">

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Imagen
                        </th>

                        <th>
                            Nombre
                        </th>

                        <th>
                            Categoría
                        </th>

                        <th>
                            Descripción
                        </th>

                        <th>
                            Precio
                        </th>

                        <th>
                            Estado
                        </th>

                        <th>
                            Acciones
                        </th>

                    </tr>

                </thead>

                <tbody>


                    <?php foreach ($productos as $producto): ?>

                        <tr>


                            <!-- ID -->

                            <td>
                                <?= $producto['id'] ?>
                            </td>


                            <!-- IMAGEN -->

                            <td>

                                <?php if (!empty($producto['imagen_principal'])): ?>

                                    <img
                                        class="imagen-producto"
                                        src="/<?= htmlspecialchars(
                                            $producto['imagen_principal']
                                        ) ?>"
                                        alt="<?= htmlspecialchars(
                                            $producto['nombre']
                                        ) ?>"
                                    >

                                <?php else: ?>

                                    Sin imagen

                                <?php endif; ?>

                            </td>


                            <!-- NOMBRE -->

                            <td>
                                <?= htmlspecialchars(
                                    $producto['nombre']
                                ) ?>
                            </td>


                            <!-- CATEGORÍA -->

                            <td>
                                <?= htmlspecialchars(
                                    $producto['categoria_nombre']
                                    ?? 'Sin categoría'
                                ) ?>
                            </td>


                            <!-- DESCRIPCIÓN -->

                            <td>
                                <?= htmlspecialchars(
                                    $producto['descripcion']
                                    ?? ''
                                ) ?>
                            </td>


                            <!-- PRECIO -->

                            <td>

                                <?php if (
                                    $producto['precio'] !== null
                                ): ?>

                                    S/
                                    <?= number_format(
                                        $producto['precio'],
                                        2
                                    ) ?>

                                <?php else: ?>

                                    Sin precio

                                <?php endif; ?>

                            </td>


                            <!-- ESTADO -->

                            <td>

                            <?php if ($producto['estado']): ?>

                                <span class="estado activo">
                                    Activo
                                </span>

                            <?php else: ?>

                                <span class="estado inactivo">
                                    Inactivo
                                </span>

                            <?php endif; ?>

                            </td>


                            <!-- ACCIONES -->

                            <td>
                                <a
                                class="btn editar"
                                href="producto-editar.php?id=<?= $producto['id'] ?>"
                                >
                                Editar
                                </a>


                                <a
                                class="btn eliminar"
                                href="producto-eliminar.php?id=<?= $producto['id'] ?>"
                                >
                                Eliminar
                                </a>
                            </td>


                        </tr>

                    <?php endforeach; ?>


                </tbody>

            </table>


        <?php else: ?>

            <p>
                No hay productos registrados.
            </p>

        <?php endif; ?>


    </main>


<?php

require_once __DIR__ . '/../includes/admin-footer.php';

?>