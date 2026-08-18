<?php

/* ==============================
   CONEXIÓN
   ============================== */

require_once __DIR__ . '/../includes/db.php';


/* ==============================
   PROCESADOR DE IMÁGENES
   ============================== */

require_once __DIR__ . '/../includes/procesador-imagen.php';



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
   DATOS DEL FORMULARIO
   ============================== */

$nombre = '';

$slug = '';

$descripcion = '';

$seo_titulo = '';

$seo_descripcion = '';

$seo_keywords = '';

$precio = '';

$categoria_id = 0;

$mensaje = '';



/* ==============================
   GUARDAR PRODUCTO
   ============================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    /* ==============================
       RECIBIR DATOS
       ============================== */


    $nombre = trim(
        $_POST['nombre'] ?? ''
    );


    $slug = trim(
        $_POST['slug'] ?? ''
    );


    $descripcion = trim(
        $_POST['descripcion'] ?? ''
    );


    $seo_titulo = trim(
        $_POST['seo_titulo'] ?? ''
    );


    $seo_descripcion = trim(
        $_POST['seo_descripcion'] ?? ''
    );


    $seo_keywords = trim(
        $_POST['seo_keywords'] ?? ''
    );


    $precio = trim(
        $_POST['precio'] ?? ''
    );


    $categoria_id = (int) (
        $_POST['categoria_id'] ?? 0
    );



    /* ==============================
       VALIDAR CAMPOS
       ============================== */


    if (
        $nombre === '' ||
        $slug === '' ||
        $categoria_id <= 0
    ) {


        $mensaje =
            'Completa los campos obligatorios.';



    } else {



        /* ==============================
           COMPROBAR SLUG
           ============================== */


        $stmt = $pdo->prepare("
            SELECT id
            FROM productos
            WHERE slug = ?
            LIMIT 1
        ");


        $stmt->execute([
            $slug
        ]);


        $slugExiste = $stmt->fetch();



        if ($slugExiste) {


            $mensaje =
                'Ese slug ya existe. Utiliza otro.';



        } else {



            /* ==============================
               VALIDAR CATEGORÍA
               ============================== */


            $stmt = $pdo->prepare("
                SELECT id
                FROM categorias
                WHERE id = ?
                AND estado = 1
                LIMIT 1
            ");


            $stmt->execute([
                $categoria_id
            ]);


            $categoriaExiste =
                $stmt->fetch();



            if (!$categoriaExiste) {


                $mensaje =
                    'La categoría seleccionada no es válida.';



            } else {



                /* ==============================
                   CONVERTIR PRECIO
                   ============================== */


                $precio =
                    $precio === ''
                    ? null
                    : (float) $precio;



                /* ==============================
                   TRANSACCIÓN
                   ============================== */


                $pdo->beginTransaction();



                try {



                    /* ==============================
                       INSERTAR PRODUCTO
                       ============================== */


                    $stmt = $pdo->prepare("
                        INSERT INTO productos (
                            categoria_id,
                            nombre,
                            slug,
                            descripcion,
                            seo_titulo,
                            seo_descripcion,
                            seo_keywords,
                            precio,
                            estado
                        )

                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
                    ");



                    $stmt->execute([


                        $categoria_id,

                        $nombre,

                        $slug,

                        $descripcion,

                        $seo_titulo,

                        $seo_descripcion,

                        $seo_keywords,

                        $precio


                    ]);



                    $productoId =
                        $pdo->lastInsertId();

                    /* ==============================
                       PROCESAR IMAGEN
                       ============================== */


                    if (
                        isset($_FILES['imagen'])
                        &&
                        $_FILES['imagen']['error']
                        !== UPLOAD_ERR_NO_FILE
                    ) {



                        $imagenes =
                            procesarImagen(
                                $_FILES['imagen'],
                                $slug
                            );



                        /* ==============================
                           GUARDAR IMAGEN EN MYSQL
                           ============================== */


                        $stmt = $pdo->prepare("
                            INSERT INTO imagenes_productos (
                                producto_id,
                                archivo_original,
                                archivo_grande,
                                archivo_miniatura,
                                alt,
                                orden,
                                principal
                            )

                            VALUES (?, ?, ?, ?, ?, 0, 1)
                        ");



                        $stmt->execute([


                            $productoId,


                            $imagenes['original'],


                            $imagenes['grande'],


                            $imagenes['miniatura'],


                            $nombre


                        ]);

                    }



                    /* ==============================
                       CONFIRMAR
                       ============================== */


                    $pdo->commit();



                    /* ==============================
                       REDIRECCIÓN
                       ============================== */


                    header(
                        'Location: productos.php'
                    );


                    exit;



                } catch (Throwable $e) {



                    /* ==============================
                       CANCELAR TRANSACCIÓN
                       ============================== */


                    if (
                        $pdo->inTransaction()
                    ) {

                        $pdo->rollBack();

                    }



                    $mensaje =
                        'ERROR: '
                        . $e->getMessage()
                        . ' | Archivo: '
                        . $e->getFile()
                        . ' | Línea: '
                        . $e->getLine();

                }

            }

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
        Agregar producto | Administración
    </title>

</head>


<body>


<header>

    <h1>
        Agregar producto
    </h1>


    <a href="productos.php">
        ← Volver a productos
    </a>

</header>



<main>


<?php if ($mensaje !== ''): ?>

    <p>
        <?= htmlspecialchars($mensaje) ?>
    </p>

<?php endif; ?>



<form
    method="POST"
    enctype="multipart/form-data"
>



<!-- ==============================
     CATEGORÍA
     ============================== -->


<div>

<label for="categoria_id">
    Categoría
</label>


<select
    name="categoria_id"
    id="categoria_id"
    required
>


<option value="">
    Seleccionar categoría
</option>



<?php foreach ($categorias as $categoria): ?>


<option
    value="<?= $categoria['id'] ?>"
    <?= $categoria_id == $categoria['id']
        ? 'selected'
        : '' ?>
>


<?= htmlspecialchars(
    $categoria['nombre']
) ?>


</option>


<?php endforeach; ?>


</select>


</div>



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
     SEO TÍTULO
     ============================== -->


<div>

<label for="seo_titulo">
    SEO Título
</label>


<input
    type="text"
    name="seo_titulo"
    id="seo_titulo"
    value="<?= htmlspecialchars($seo_titulo) ?>"
>


</div>




<!-- ==============================
     SEO DESCRIPCIÓN
     ============================== -->


<div>


<label for="seo_descripcion">
    SEO Descripción
</label>


<textarea
    name="seo_descripcion"
    id="seo_descripcion"
    rows="4"
><?= htmlspecialchars($seo_descripcion) ?></textarea>


</div>




<!-- ==============================
     SEO KEYWORDS
     ============================== -->


<div>


<label for="seo_keywords">
    Palabras clave SEO
</label>


<input
    type="text"
    name="seo_keywords"
    id="seo_keywords"
    value="<?= htmlspecialchars($seo_keywords) ?>"
>


</div>




<!-- ==============================
     PRECIO
     ============================== -->


<div>


<label for="precio">
    Precio
</label>


<input
    type="number"
    name="precio"
    id="precio"
    value="<?= htmlspecialchars($precio) ?>"
    step="0.01"
>


</div>




<!-- ==============================
     IMAGEN
     ============================== -->


<div>


<label for="imagen">
    Imagen del producto
</label>


<input
    type="file"
    name="imagen"
    id="imagen"
    accept="image/jpeg,image/png,image/webp"
>


</div>




<!-- ==============================
     BOTÓN
     ============================== -->


<div>


<button type="submit">
    Guardar producto
</button>


</div>



</form>


</main>




<!-- ==============================
     GENERADOR DE SLUG
     ============================== -->


<script>


const nombreInput =
    document.getElementById('nombre');


const slugInput =
    document.getElementById('slug');



nombreInput.addEventListener(
    'input',
    function () {


        let slug =
            this.value
            .toLowerCase()
            .normalize('NFD')
            .replace(
                /[\u0300-\u036f]/g,
                ''
            )
            .replace(
                /[^a-z0-9]+/g,
                '-'
            )
            .replace(
                /^-+|-+$/g,
                ''
            );


        slugInput.value = slug;


    }
);



</script>



</body>

</html>