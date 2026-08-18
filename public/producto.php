<?php

require_once __DIR__ . '/../includes/db.php';


/* ==============================
   OBTENER SLUG
   ============================== */

$slug = trim(
    $_GET['slug'] ?? ''
);


if ($slug === '') {

    http_response_code(404);

    die('Producto no especificado.');

}


/* ==============================
   BUSCAR PRODUCTO
   ============================== */

$stmt = $pdo->prepare("
    SELECT
        productos.*,
        categorias.nombre AS categoria_nombre,
        categorias.slug AS categoria_slug

    FROM productos

    LEFT JOIN categorias
        ON productos.categoria_id = categorias.id

    WHERE productos.slug = ?
    AND productos.estado = 1

    LIMIT 1
");

$stmt->execute([
    $slug
]);

$producto = $stmt->fetch();


if (!$producto) {

    http_response_code(404);

    die('Producto no encontrado.');

}


/* ==============================
   OBTENER IMAGEN PRINCIPAL
   ============================== */

$stmt = $pdo->prepare("
    SELECT
        archivo_original,
        archivo_grande,
        archivo_miniatura,
        alt

    FROM imagenes_productos

    WHERE producto_id = ?

    ORDER BY
        principal DESC,
        orden ASC,
        id ASC

    LIMIT 1
");

$stmt->execute([
    $producto['id']
]);

$imagen = $stmt->fetch();


/* ==============================
   TÍTULO DE LA PÁGINA
   ============================== */

$tituloPagina =
    $producto['nombre']
    . ' | '
    . APP_NAME;


/* ==============================
   WHATSAPP
   ============================== */

$whatsappNumero = '51912188520';

$mensajeWhatsApp =
    'Hola, quisiera consultar información sobre el producto: '
    . $producto['nombre']
    . '.';

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
        content="<?= htmlspecialchars(
            $producto['nombre']
            . ' | Bolsas de papel Kraft para negocios y empresas.'
        ) ?>"
    >

    <meta
        name="robots"
        content="index, follow"
    >

    <link
        rel="canonical"
        href="http://bolsas-kraft.test/producto.php?slug=<?= urlencode(
            $producto['slug']
        ) ?>"
    >


    <!-- ==============================
         CSS
         ============================== -->

    <link
        rel="stylesheet"
        href="/assets/css/style.css"
    >


    <!-- ==============================
         TÍTULO
         ============================== -->

    <title>
        <?= htmlspecialchars(
            $tituloPagina
        ) ?>
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
         CONTENIDO PRINCIPAL
         ============================== -->

    <main>


        <!-- ==============================
             DETALLE DEL PRODUCTO
             ============================== -->

        <section class="producto-detalle">

            <div class="container">


                <!-- ==============================
                     BREADCRUMB
                     ============================== -->

                <nav
                    class="producto-breadcrumb"
                    aria-label="Navegación"
                >

                    <a href="/">
                        Inicio
                    </a>

                    <span>
                        /
                    </span>

                    <a href="/#productos">
                        Productos
                    </a>

                    <span>
                        /
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $producto['nombre']
                        ) ?>

                    </strong>

                </nav>


                <!-- ==============================
                     PRODUCTO
                     ============================== -->

                <div class="producto-detalle-grid">


                    <!-- ==============================
                         IMAGEN DEL PRODUCTO
                         ============================== -->

                    <div class="producto-detalle-imagen">

                        <?php if (
                            $imagen &&
                            !empty(
                                $imagen['archivo_grande']
                            )
                        ): ?>

                            <img
                                src="/<?= htmlspecialchars(
                                    $imagen['archivo_grande']
                                ) ?>"
                                alt="<?= htmlspecialchars(
                                    $imagen['alt']
                                    ?: $producto['nombre']
                                ) ?>"
                            >

                        <?php else: ?>

                            <div class="producto-sin-imagen">

                                Sin imagen disponible

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- ==============================
                         INFORMACIÓN DEL PRODUCTO
                         ============================== -->

                    <div class="producto-detalle-info">


                        <!-- ==============================
                             CATEGORÍA
                             ============================== -->

                        <?php if (
                            !empty(
                                $producto['categoria_nombre']
                            )
                        ): ?>

                            <p class="producto-categoria">

                                <?= htmlspecialchars(
                                    $producto['categoria_nombre']
                                ) ?>

                            </p>

                        <?php endif; ?>


                        <!-- ==============================
                             NOMBRE
                             ============================== -->

                        <h1>

                            <?= htmlspecialchars(
                                $producto['nombre']
                            ) ?>

                        </h1>


                        <!-- ==============================
                             DESCRIPCIÓN
                             ============================== -->

                        <?php if (
                            !empty(
                                $producto['descripcion']
                            )
                        ): ?>

                            <div class="producto-descripcion">

                                <p>

                                    <?= nl2br(
                                        htmlspecialchars(
                                            $producto['descripcion']
                                        )
                                    ) ?>

                                </p>

                            </div>

                        <?php endif; ?>


                        <!-- ==============================
                             ESPECIFICACIONES
                             ============================== -->

                        <div class="producto-especificaciones">

                            <h2>
                                Especificaciones
                            </h2>


                            <div class="especificaciones-lista">


                                <!-- MATERIAL -->

                                <?php if (
                                    !empty(
                                        $producto['material']
                                    )
                                ): ?>

                                    <div class="especificacion">

                                        <span>
                                            Material
                                        </span>

                                        <strong>

                                            <?= htmlspecialchars(
                                                $producto['material']
                                            ) ?>

                                        </strong>

                                    </div>

                                <?php endif; ?>


                                <!-- GRAMAJE -->

                                <?php if (
                                    !empty(
                                        $producto['gramaje']
                                    )
                                ): ?>

                                    <div class="especificacion">

                                        <span>
                                            Gramaje
                                        </span>

                                        <strong>

                                            <?= htmlspecialchars(
                                                $producto['gramaje']
                                            ) ?>

                                        </strong>

                                    </div>

                                <?php endif; ?>


                                <!-- MEDIDAS -->

                                <?php if (
                                    !empty(
                                        $producto['medidas']
                                    )
                                ): ?>

                                    <div class="especificacion">

                                        <span>
                                            Medidas
                                        </span>

                                        <strong>

                                            <?= htmlspecialchars(
                                                $producto['medidas']
                                            ) ?>

                                        </strong>

                                    </div>

                                <?php endif; ?>


                                <!-- PRESENTACIÓN -->

                                <?php if (
                                    !empty(
                                        $producto['presentacion']
                                    )
                                ): ?>

                                    <div class="especificacion">

                                        <span>
                                            Presentación
                                        </span>

                                        <strong>

                                            <?= htmlspecialchars(
                                                $producto['presentacion']
                                            ) ?>

                                        </strong>

                                    </div>

                                <?php endif; ?>


                                <!-- PERSONALIZABLE -->

                                <div class="especificacion">

                                    <span>
                                        Personalizable
                                    </span>

                                    <strong>

                                        <?= !empty(
                                            $producto['personalizable']
                                        )
                                            ? 'Sí'
                                            : 'No'
                                        ?>

                                    </strong>

                                </div>


                            </div>

                        </div>


                        <!-- ==============================
                             CONTACTO WHATSAPP
                             ============================== -->

                        <div class="producto-contacto">

                            <p>

                                ¿Quieres conocer precios,
                                disponibilidad o pedir más información?

                            </p>


                            <a
                                href="<?= htmlspecialchars(
                                    $urlWhatsApp,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                class="btn-whatsapp"
                                target="_blank"
                                rel="noopener noreferrer"
                            >

                                Consultar por WhatsApp

                            </a>

                        </div>


                    </div>

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

                <?= htmlspecialchars(
                    APP_NAME
                ) ?>

            </p>

        </div>

    </footer>


</body>

</html>