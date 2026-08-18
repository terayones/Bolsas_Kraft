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
   DATOS DEL FORMULARIO
   ============================== */

$nombre = $categoria['nombre'];
$slug = $categoria['slug'];
$descripcion = $categoria['descripcion'];
$estado = $categoria['estado'];

$mensaje = '';


/* ==============================
   ACTUALIZAR CATEGORÍA
   ============================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');

    $slug = trim($_POST['slug'] ?? '');

    $descripcion = trim($_POST['descripcion'] ?? '');

    $estado = (int) ($_POST['estado'] ?? 0);


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
            AND id != ?
            LIMIT 1
        ");

        $stmt->execute([
            $slug,
            $id
        ]);

        $slugExiste = $stmt->fetch();


        if ($slugExiste) {

            $mensaje = 'Ese slug ya pertenece a otra categoría.';

        } else {


            /* ==============================
               ACTUALIZAR
               ============================== */

            $stmt = $pdo->prepare("
                UPDATE categorias
                SET
                    nombre = ?,
                    slug = ?,
                    descripcion = ?,
                    estado = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $nombre,
                $slug,
                $descripcion,
                $estado,
                $id
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
        Editar categoría | Administración
    </title>

</head>

<body>


    <!-- ==============================
         HEADER
         ============================== -->

    <header>

        <h1>
            Editar categoría
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
                 ESTADO
                 ============================== -->

            <div>

                <label for="estado">
                    Estado
                </label>

                <select
                    name="estado"
                    id="estado"
                >

                    <option
                        value="1"
                        <?= $estado == 1 ? 'selected' : '' ?>
                    >
                        Activa
                    </option>

                    <option
                        value="0"
                        <?= $estado == 0 ? 'selected' : '' ?>
                    >
                        Inactiva
                    </option>

                </select>

            </div>


            <!-- ==============================
                 BOTÓN
                 ============================== -->

            <div>

                <button type="submit">
                    Guardar cambios
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