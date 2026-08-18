<?php

/* ==============================
   ERRORES (TEMPORAL)
   ============================== */

error_reporting(E_ALL);
ini_set('display_errors', 1);


/* ==============================
   CONEXIÓN
   ============================== */

require_once __DIR__ . '/../includes/db.php';



/* ==============================
   OBTENER ID PRODUCTO
   ============================== */

$id = (int)(
    $_GET['id'] ?? 0
);


if ($id <= 0) {

    die('Producto no válido.');

}



/* ==============================
   OBTENER PRODUCTO
   ============================== */

$stmt = $pdo->prepare("
    SELECT *
    FROM productos
    WHERE id = ?
    LIMIT 1
");


$stmt->execute([
    $id
]);


$producto = $stmt->fetch();


if (!$producto) {

    die('Producto no encontrado.');

}



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
   OBTENER IMÁGENES
   ============================== */

$stmt = $pdo->prepare("
    SELECT *
    FROM imagenes_productos
    WHERE producto_id = ?
    ORDER BY principal DESC, orden ASC
");


$stmt->execute([
    $id
]);


$imagenes = $stmt->fetchAll();



/* ==============================
   VARIABLES
   ============================== */

$mensaje = '';


if (
    isset($_GET['mensaje'])
) {


    switch ($_GET['mensaje']) {


        case 'imagen_ok':

            $mensaje = 'Imagen subida correctamente.';

        break;



        case 'actualizado':

            $mensaje = 'Producto actualizado correctamente.';

        break;


    }

}



/* ==============================
   GUARDAR CAMBIOS PRODUCTO
   ============================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $categoria_id = (int)(
        $_POST['categoria_id'] ?? 0
    );


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


    $material = trim(
        $_POST['material'] ?? ''
    );


    $gramaje = trim(
        $_POST['gramaje'] ?? ''
    );


    $medidas = trim(
        $_POST['medidas'] ?? ''
    );


    $presentacion = trim(
        $_POST['presentacion'] ?? ''
    );


    $personalizable = isset(
        $_POST['personalizable']
    ) ? 1 : 0;


    $precio = trim(
        $_POST['precio'] ?? ''
    );


    $precio = $precio === ''
        ? null
        : (float)$precio;



    $estado = isset(
        $_POST['estado']
    ) ? 1 : 0;




    if (
        $nombre === '' ||
        $slug === '' ||
        $categoria_id <= 0
    ) {


        $mensaje =
            'Completa los campos obligatorios.';


    } else {


        $stmt = $pdo->prepare("
            UPDATE productos SET

                categoria_id = ?,
                nombre = ?,
                slug = ?,
                descripcion = ?,
                seo_titulo = ?,
                seo_descripcion = ?,
                seo_keywords = ?,
                material = ?,
                gramaje = ?,
                medidas = ?,
                presentacion = ?,
                personalizable = ?,
                precio = ?,
                estado = ?

            WHERE id = ?

        ");



        $stmt->execute([


            $categoria_id,

            $nombre,

            $slug,

            $descripcion,

            $seo_titulo,

            $seo_descripcion,

            $seo_keywords,

            $material,

            $gramaje,

            $medidas,

            $presentacion,

            $personalizable,

            $precio,

            $estado,

            $id

        ]);



        header(
            "Location: /admin/productos.php?mensaje=actualizado"
            );

            exit;


    }


}


?>

<?php

        $titulo_admin = "Editar producto";

        require_once __DIR__ . '/../includes/admin-header.php';

        ?>

        <main>


        <h2>
            Editar producto
        </h2>



        <?php if ($mensaje !== ''): ?>

            <p>
                <?= htmlspecialchars($mensaje) ?>
            </p>

        <?php endif; ?>



        <form method="POST">



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


        <?php foreach ($categorias as $categoria): ?>


        <option
        value="<?= $categoria['id'] ?>"
        <?= $producto['categoria_id'] == $categoria['id']
            ? 'selected'
            : ''
        ?>
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

        <label>
            Nombre
        </label>


        <input
        type="text"
        name="nombre"
        id="nombre"
        value="<?= htmlspecialchars(
            $producto['nombre']
        ) ?>"
        required
        >


        </div>





        <!-- ==============================
             SLUG
             ============================== -->


        <div>

        <label>
            Slug
        </label>


        <input
        type="text"
        name="slug"
        id="slug"
        value="<?= htmlspecialchars(
            $producto['slug']
        ) ?>"
        required
        >


        </div>





        <!-- ==============================
             DESCRIPCIÓN
             ============================== -->


        <div>

        <label>
            Descripción
        </label>


        <textarea
        name="descripcion"
        rows="5"
        ><?= htmlspecialchars(
            $producto['descripcion'] ?? ''
        ) ?></textarea>


        </div>





        <!-- ==============================
             SEO
             ============================== -->


        <div>

        <label>
            SEO Título
        </label>


        <input
        type="text"
        name="seo_titulo"
        value="<?= htmlspecialchars(
            $producto['seo_titulo'] ?? ''
        ) ?>"
        >


        </div>



        <div>

        <label>
            SEO Descripción
        </label>


        <textarea
        name="seo_descripcion"
        rows="4"
        ><?= htmlspecialchars(
            $producto['seo_descripcion'] ?? ''
        ) ?></textarea>


        </div>




        <div>

        <label>
            Keywords SEO
        </label>


        <input
        type="text"
        name="seo_keywords"
        value="<?= htmlspecialchars(
            $producto['seo_keywords'] ?? ''
        ) ?>"
        >


        </div>





        <!-- ==============================
             CARACTERÍSTICAS
             ============================== -->


        <div>

        <label>
            Material
        </label>


        <input
        type="text"
        name="material"
        value="<?= htmlspecialchars(
            $producto['material'] ?? ''
        ) ?>"
        >


        </div>




        <div>

        <label>
            Gramaje
        </label>


        <input
        type="text"
        name="gramaje"
        value="<?= htmlspecialchars(
            $producto['gramaje'] ?? ''
        ) ?>"
        >


        </div>




        <div>

        <label>
            Medidas
        </label>


        <input
        type="text"
        name="medidas"
        value="<?= htmlspecialchars(
            $producto['medidas'] ?? ''
        ) ?>"
        >


        </div>




        <div>

        <label>
            Presentación
        </label>


        <input
        type="text"
        name="presentacion"
        value="<?= htmlspecialchars(
            $producto['presentacion'] ?? ''
        ) ?>"
        >


        </div>





        <!-- ==============================
             PERSONALIZABLE
             ============================== -->


        <div>


        <label>


        <input
        type="checkbox"
        name="personalizable"
        value="1"

        <?= $producto['personalizable']
            ? 'checked'
            : ''
        ?>

        >


        Producto personalizable


        </label>


        </div>





        <!-- ==============================
             PRECIO
             ============================== -->


        <div>

        <label>
            Precio
        </label>


        <input
        type="number"
        name="precio"
        step="0.01"
        value="<?= htmlspecialchars(
            $producto['precio'] ?? ''
        ) ?>"
        >


        </div>





        <!-- ==============================
             ESTADO
             ============================== -->


        <div>


        <label>


        <input
        type="checkbox"
        name="estado"
        value="1"

        <?= $producto['estado']
            ? 'checked'
            : ''
        ?>

        >


        Activo


        </label>


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

<?php

/* ==============================
   IMÁGENES DEL PRODUCTO
   ============================== */

    ?>


    <section>


    <h2>
        Imágenes del producto
    </h2>



    <?php if ($imagenes): ?>


    <div>


    <?php foreach ($imagenes as $imagen): ?>


    <div>


    <img
    src="/<?= htmlspecialchars(
        $imagen['archivo_miniatura']
    ) ?>"
    width="120"
    alt="<?= htmlspecialchars(
        $imagen['alt'] ?? ''
    ) ?>"
    >


    <?php if ($imagen['principal']): ?>

    <p>
    Imagen principal
    </p>

    <?php endif; ?>


    </div>



    <?php endforeach; ?>


    </div>


    <?php else: ?>


    <p>
    No hay imágenes registradas.
    </p>


    <?php endif; ?>





    <h3>
        Subir nueva imagen
    </h3>



    <form
    action="producto-imagen-subir.php"
    method="POST"
    enctype="multipart/form-data"
    >



    <input
    type="hidden"
    name="producto_id"
    value="<?= $id ?>"
    >



    <input
    type="file"
    name="imagen"
    accept="image/jpeg,image/png,image/webp"
    required
    >



    <button type="submit">

    Subir imagen

    </button>



    </form>



    </section>





    </main>





    <script>


    const nombreInput =
    document.getElementById('nombre');


    const slugInput =
    document.getElementById('slug');



    if(nombreInput && slugInput){


    nombreInput.addEventListener(
    'input',
    function(){


    let slug = this.value
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


    }



    </script>




    <?php

    require_once __DIR__ . '/../includes/admin-footer.php';

    ?>