<?php

/* ==============================
   CONEXIÓN
   ============================== */

require_once __DIR__ . '/../includes/db.php';

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Administración | <?= APP_NAME ?></title>

</head>

<body>


    <!-- ==============================
         HEADER DEL ADMIN
         ============================== -->

    <header>

        <h1>
            Panel de administración
        </h1>

        <p>
            <?= APP_NAME ?>
        </p>

    </header>


    <!-- ==============================
         MENÚ PRINCIPAL
         ============================== -->

    <main>

        <h2>
            Administración
        </h2>

        <ul>

            <li>

                <a href="productos.php">
                    Productos
                </a>

            </li>

            <li>

                <a href="#">
                    Categorías
                </a>

            </li>

        </ul>

    </main>


</body>

</html>