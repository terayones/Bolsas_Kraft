<?php

/* ==============================
   CONEXIÓN
   ============================== */

require_once __DIR__ . '/../includes/db.php';


/* ==============================
   OBTENER CATEGORÍAS
   ============================== */

$stmt = $pdo->query("
    SELECT *
    FROM categorias
    ORDER BY id DESC
");

$categorias = $stmt->fetchAll();

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
        Categorías | Administración
    </title>

</head>

<body>


    <!-- ==============================
         HEADER
         ============================== -->

    <header>

        <h1>
            Categorías
        </h1>

        <a href="index.php">
            ← Volver al panel
        </a>

    </header>


    <!-- ==============================
         CONTENIDO
         ============================== -->

    <main>

        <h2>
            Lista de categorías
        </h2>


        <!-- ==============================
             AGREGAR CATEGORÍA
             ============================== -->

        <p>

            <a href="categoria-crear.php">
                + Agregar categoría
            </a>

        </p>


        <!-- ==============================
             LISTADO
             ============================== -->

        <?php if ($categorias): ?>

            <table border="1" cellpadding="10">

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Nombre
                        </th>

                        <th>
                            Slug
                        </th>

                        <th>
                            Descripción
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


                    <?php foreach ($categorias as $categoria): ?>

                        <tr>


                            <!-- ID -->

                            <td>
                                <?= $categoria['id'] ?>
                            </td>


                            <!-- NOMBRE -->

                            <td>
                                <?= htmlspecialchars(
                                    $categoria['nombre']
                                ) ?>
                            </td>


                            <!-- SLUG -->

                            <td>
                                <?= htmlspecialchars(
                                    $categoria['slug']
                                ) ?>
                            </td>


                            <!-- DESCRIPCIÓN -->

                            <td>
                                <?= htmlspecialchars(
                                    $categoria['descripcion']
                                    ?? ''
                                ) ?>
                            </td>


                            <!-- ESTADO -->

                            <td>

                                <?php if ($categoria['estado']): ?>

                                    Activa

                                <?php else: ?>

                                    Inactiva

                                <?php endif; ?>

                            </td>


                            <!-- ACCIONES -->

                            <td>

                                <a
                                    href="categoria-editar.php?id=<?= $categoria['id'] ?>"
                                >
                                    Editar
                                </a>

                                |

                                <a
                                    href="categoria-eliminar.php?id=<?= $categoria['id'] ?>"
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
                No hay categorías registradas.
            </p>

        <?php endif; ?>


    </main>


</body>

</html>