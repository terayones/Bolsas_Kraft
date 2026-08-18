<?php

/* ==============================
   CONEXIÓN
   ============================== */

require_once __DIR__ . '/../includes/db.php';


/* ==============================
   DATOS DEL FORMULARIO
   ============================== */

$nombre = '';
$slug = '';
$descripcion = '';

$mensaje = '';


/* ==============================
   GUARDAR CATEGORÍA
   ============================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');

    $slug = trim($_POST['slug'] ?? '');

    $descripcion = trim($_POST['descripcion'] ?? '');


    /* ==============================
       VALIDAR
       ============================== */

    if ($nombre === '' || $slug === '') {

        $mensaje = 'Completa los campos obligatorios.';

    } else {


        /* ==============================
           COMPROBAR SLUG
           ============================== */

        $stmt = $pdo->prepare("
            SELECT id
            FROM categorias
            WHERE slug = ?
            LIMIT 1
        ");

        $stmt->execute([$slug]);

        $slugExiste = $stmt->fetch();


        if ($slugExiste) {

            $mensaje = 'Ese slug ya existe. Utiliza otro.';

        } else {


            /* ==============================
               INSERTAR CATEGORÍA
               ============================== */

            $stmt = $pdo->prepare("
                INSERT INTO categorias (
                    nombre,
                    slug,
                    descripcion,
                    estado
                )
                VALUES (?, ?, ?, 1)
            ");

            $stmt->execute([
                $nombre,
                $slug,
                $descripcion
            ]);


            /* ==============================
               REDIRIGIR
               ============================== */

            header('Location: categorias.php');

            exit;

        }

    }

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
        Agregar categoría | Administración
    </title>

</head>

<body>


    <!-- ==============================
         HEADER
         ============================== -->

    <header>

        <h1>
            Agregar categoría
        </h1>

        <a href="categorias.php">
            ← Volver a categorías
        </a>

    </header>


    <!-- ==============================
         FORMULARIO
         ============================== -->

    <main>


        <?php if ($mensaje !== ''): ?>

            <p>
                <?= htmlspecialchars($mensaje) ?>
            </p>

        <?php endif; ?>


        <form method="POST">


            <!-- ==============================
                 NOMBRE
                 ============================== -->

            <div>

                <label for="nombre">
                    Nombre
                </label>

                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    value="<?= htmlspecialchars($nombre) ?>"
                    required
                >

            </div>


            <!-- ==============================
                 SLUG
                 ============================== -->

            <div>

                <label for="slug">
                    Slug
                </label>

                <input
                    type="text"
                    name="slug"
                    id="slug"
                    value="<?= htmlspecialchars($slug) ?>"
                    required
                >

            </div>


            <!-- ==============================
                 DESCRIPCIÓN
                 ============================== -->

            <div>

                <label for="descripcion">
                    Descripción
                </label>

                <textarea
                    name="descripcion"
                    id="descripcion"
                    rows="5"
                ><?= htmlspecialchars($descripcion) ?></textarea>

            </div>


            <!-- ==============================
                 BOTÓN
                 ============================== -->

            <div>

                <button type="submit">
                    Guardar categoría
                </button>

            </div>


        </form>

    </main>


    <!-- ==============================
         GENERADOR DE SLUG
         ============================== -->

    <script>

        const nombreInput = document.getElementById('nombre');

        const slugInput = document.getElementById('slug');


        nombreInput.addEventListener('input', function () {

            let slug = this.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');

            slugInput.value = slug;

        });

    </script>


</body>

</html>