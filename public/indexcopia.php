<?php

/* ==============================
   CONEXIÓN Y CATEGORÍAS
   ============================== */

require_once __DIR__ . '/../includes/db.php';


/* ==============================
   OBTENER CATEGORÍAS
   ============================== */

$stmt = $pdo->query("
    SELECT *
    FROM categorias
    WHERE estado = 1
    ORDER BY nombre ASC
");

$categorias = $stmt->fetchAll();


/* ==============================
   WHATSAPP
   ============================== */

$whatsappNumero = '51912188520';

$mensajeWhatsApp =
    'Hola, quisiera solicitar información y cotización sobre las bolsas de papel Kraft.';

$urlWhatsApp =
    'https://wa.me/'
    . $whatsappNumero
    . '?text='
    . rawurlencode($mensajeWhatsApp);

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


    <!-- ==============================
         SEO
         ============================== -->

    <meta
        name="description"
        content="Bolsas de papel Kraft para negocios y empresas. Conoce nuestros productos, medidas y presentaciones y solicita una cotización por WhatsApp."
    >

    <meta
        name="robots"
        content="index, follow"
    >

    <link
        rel="canonical"
        href="http://bolsas-kraft.test/"
    >


    <!-- ==============================
         CSS
         ============================== -->

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <!-- ==============================
         TÍTULO
         ============================== -->

    <title>
        Bolsas de papel Kraft para negocios | <?= htmlspecialchars(APP_NAME) ?>
    </title>

</head>

<body>


    <!-- ==============================
         HEADER
         ============================== -->

    <?php
        require_once __DIR__ . '/../includes/header.php';
    ?>
        <!-- ==============================
             FIN HEADER
        ============================== -->

        <!-- ==============================
         CONTENIDO PRINCIPAL
         ============================== -->

    <main>


        <!-- ==============================
             HERO
             ============================== -->

        <section class="hero">

            <div class="container">

                <h1>
                    Bolsas de papel Kraft para negocios y empresas
                </h1>

                <p>
                    Soluciones en bolsas de papel para diferentes tipos de negocios.
                </p>

                <a
                    href="#productos"
                    class="btn-principal"
                >
                    Ver productos
                </a>

            </div>

        </section>


        <!-- ==============================
             PRODUCTOS
             ============================== -->

        <section
            id="productos"
            class="productos"
        >

            <div class="container">

                <h2>
                    Nuestros productos
                </h2>


                <?php foreach ($categorias as $categoria): ?>

                    <div class="categoria">


                        <!-- ==============================
                             CATEGORÍA
                             ============================== -->

                        <h3>

                            <?= htmlspecialchars(
                                $categoria['nombre']
                            ) ?>

                        </h3>


                        <p>

                            <?= htmlspecialchars(
                                $categoria['descripcion']
                            ) ?>

                        </p>


                        <?php

                        /* ==============================
                           PRODUCTOS DE LA CATEGORÍA
                           ============================== */

                        $stmtProductos = $pdo->prepare("
                            SELECT
                                productos.*,

                                (
                                    SELECT
                                        imagenes_productos.archivo_miniatura

                                    FROM imagenes_productos

                                    WHERE
                                        imagenes_productos.producto_id = productos.id

                                    ORDER BY
                                        imagenes_productos.principal DESC,
                                        imagenes_productos.orden ASC,
                                        imagenes_productos.id ASC

                                    LIMIT 1

                                ) AS archivo_miniatura

                            FROM productos

                            WHERE productos.categoria_id = ?

                            AND productos.estado = 1

                            ORDER BY productos.nombre ASC
                        ");


                        $stmtProductos->execute([
                            $categoria['id']
                        ]);


                        $productos =
                            $stmtProductos->fetchAll();

                        ?>


                        <?php if ($productos): ?>

                            <div class="productos-grid">


                                <?php foreach (
                                    $productos
                                    as $producto
                                ): ?>


                                    <!-- ==============================
                                         TARJETA DEL PRODUCTO
                                         ============================== -->

                                    <article class="producto-card">


                                        <!-- ==============================
                                             IMAGEN
                                             ============================== -->

                                        <div class="producto-card-imagen">


                                            <?php if (
                                                !empty(
                                                    $producto['archivo_miniatura']
                                                )
                                            ): ?>

                                                <img
                                                    src="/<?= htmlspecialchars(
                                                        $producto['archivo_miniatura']
                                                    ) ?>"
                                                    alt="<?= htmlspecialchars(
                                                        $producto['nombre']
                                                    ) ?>"
                                                    loading="lazy"
                                                >

                                            <?php else: ?>

                                                <span>
                                                    Sin imagen
                                                </span>

                                            <?php endif; ?>


                                        </div>


                                        <!-- ==============================
                                             NOMBRE
                                             ============================== -->

                                        <h4>

                                            <?= htmlspecialchars(
                                                $producto['nombre']
                                            ) ?>

                                        </h4>


                                        <!-- ==============================
                                             DESCRIPCIÓN
                                             ============================== -->

                                        <?php if (
                                            !empty(
                                                $producto['descripcion']
                                            )
                                        ): ?>

                                            <p>

                                                <?= htmlspecialchars(
                                                    $producto['descripcion']
                                                ) ?>

                                            </p>

                                        <?php endif; ?>


                                        <!-- ==============================
                                             ENLACE
                                             ============================== -->

                                        <a
                                            href="/producto.php?slug=<?= urlencode(
                                                $producto['slug']
                                            ) ?>"
                                            class="btn-principal"
                                        >
                                            Ver producto
                                        </a>


                                    </article>


                                <?php endforeach; ?>


                            </div>

                        <?php endif; ?>


                    </div>

                <?php endforeach; ?>


            </div>

        </section>


        <!-- ==============================
             CONTACTO
             ============================== -->

        <section
            id="contacto"
            class="contacto"
        >

            <div class="container">

                <h2>
                    ¿Necesitas bolsas para tu negocio?
                </h2>

                <p>
                    Solicita información y cotización directamente por WhatsApp.
                </p>

                <a
                    href="<?= htmlspecialchars(
                        $urlWhatsApp,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    class="btn-principal"
                    target="_blank"
                    rel="noopener"
                >
                    Solicitar cotización
                </a>

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

                <?= htmlspecialchars(APP_NAME) ?>

            </p>

        </div>

    </footer>


</body>

</html>