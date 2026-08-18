<?php

/* ==============================
   CONEXIÓN
   ============================== */

require_once __DIR__ . '/../includes/db.php';


/* ==============================
   OBTENER PRODUCTO
   ============================== */

$slug = trim(
    $_GET['producto'] ?? ''
);


$producto = null;


if ($slug !== '') {

    $stmt = $pdo->prepare("
        SELECT
            id,
            nombre,
            slug
        FROM productos
        WHERE slug = ?
        AND estado = 1
        LIMIT 1
    ");

    $stmt->execute([
        $slug
    ]);

    $producto = $stmt->fetch();

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

    <link
        rel="stylesheet"
        href="/assets/css/style.css"
    >

    <title>
        Solicitar cotización | <?= APP_NAME ?>
    </title>

</head>

<body>


    <!-- ==============================
         HEADER
         ============================== -->

    <header class="header">

        <div class="container header-content">

            <a
                href="/"
                class="logo"
            >
                BOLSAS KRAFT
            </a>


            <nav class="nav">

                <a href="/">
                    Inicio
                </a>

                <a href="/#productos">
                    Productos
                </a>

                <a href="/#contacto">
                    Contacto
                </a>

            </nav>


            <a
                href="/#contacto"
                class="btn-contacto"
            >
                Contactar
            </a>

        </div>

    </header>


    <!-- ==============================
         COTIZACIÓN
         ============================== -->

    <main>

        <section class="cotizacion">

            <div class="container">


                <div class="cotizacion-contenido">


                    <h1>
                        Solicitar cotización
                    </h1>


                    <p class="cotizacion-intro">

                        Completa tus datos y cuéntanos
                        qué necesitas. Te contactaremos
                        para brindarte una cotización.

                    </p>


                    <!-- ==============================
                         PRODUCTO
                         ============================== -->

                    <?php if ($producto): ?>

                        <div class="cotizacion-producto">

                            <span>
                                Producto seleccionado
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $producto['nombre']
                                ) ?>

                            </strong>

                        </div>

                    <?php endif; ?>


                    <!-- ==============================
                         FORMULARIO
                         ============================== -->

                    <form
                        class="formulario-cotizacion"
                        method="POST"
                        action="#"
                    >


                        <!-- NOMBRE -->

                        <div class="campo">

                            <label for="nombre">
                                Nombre
                            </label>

                            <input
                                type="text"
                                name="nombre"
                                id="nombre"
                                placeholder="Tu nombre"
                                required
                            >

                        </div>


                        <!-- TELÉFONO -->

                        <div class="campo">

                            <label for="telefono">
                                Teléfono / WhatsApp
                            </label>

                            <input
                                type="tel"
                                name="telefono"
                                id="telefono"
                                placeholder="Ejemplo: 999 999 999"
                                required
                            >

                        </div>


                        <!-- CORREO -->

                        <div class="campo">

                            <label for="correo">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                name="correo"
                                id="correo"
                                placeholder="tu@email.com"
                            >

                        </div>


                        <!-- CANTIDAD -->

                        <div class="campo">

                            <label for="cantidad">
                                Cantidad aproximada
                            </label>

                            <input
                                type="text"
                                name="cantidad"
                                id="cantidad"
                                placeholder="Ejemplo: 5 millares"
                            >

                        </div>


                        <!-- MENSAJE -->

                        <div class="campo">

                            <label for="mensaje">
                                Mensaje
                            </label>

                            <textarea
                                name="mensaje"
                                id="mensaje"
                                rows="5"
                                placeholder="Cuéntanos qué necesitas..."
                            ></textarea>

                        </div>


                        <!-- BOTÓN -->

                        <button
                            type="submit"
                            class="btn-principal"
                        >
                            Solicitar cotización
                        </button>


                    </form>


                </div>

            </div>

        </section>

    </main>


    <!-- ==============================
         FOOTER
         ============================== -->

    <footer class="footer">

        <div class="container">

            <p>

                &copy;

                <?= date('Y') ?>

                <?= APP_NAME ?>

            </p>

        </div>

    </footer>


</body>

</html>