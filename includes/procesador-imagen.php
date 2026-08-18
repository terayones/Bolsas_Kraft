<?php

/* ==============================
   CONFIGURACIÓN
   ============================== */

const IMAGEN_MAX_MB = 8;

const IMAGEN_GRANDE_MAX = 1200;

const IMAGEN_MINIATURA_MAX = 400;


/* ==============================
   PROCESAR IMAGEN
   ============================== */

function procesarImagen($archivo, $slug)
{

    /* ==============================
       VALIDAR ARCHIVO
       ============================== */

    if (
        !isset($archivo['error']) ||
        $archivo['error'] !== UPLOAD_ERR_OK
    ) {
        throw new Exception(
            'No se pudo subir la imagen.'
        );
    }


    /* ==============================
       VALIDAR TAMAÑO
       ============================== */

    $maxBytes =
        IMAGEN_MAX_MB * 1024 * 1024;

    if ($archivo['size'] > $maxBytes) {
        throw new Exception(
            'La imagen supera el tamaño máximo permitido de '
            . IMAGEN_MAX_MB
            . ' MB.'
        );
    }


    /* ==============================
       VALIDAR MIME REAL
       ============================== */

    $finfo = new finfo(FILEINFO_MIME_TYPE);

    $mime = $finfo->file(
        $archivo['tmp_name']
    );


    $mimePermitidos = [

        'image/jpeg' => 'jpg',

        'image/png' => 'png',

        'image/webp' => 'webp'

    ];


    if (!isset($mimePermitidos[$mime])) {
        throw new Exception(
            'El archivo no es una imagen válida.'
        );
    }


    /* ==============================
       INFORMACIÓN DE LA IMAGEN
       ============================== */

    $informacion =
        getimagesize(
            $archivo['tmp_name']
        );


    if ($informacion === false) {
        throw new Exception(
            'No se pudo leer la imagen.'
        );
    }


    $anchoOriginal =
        $informacion[0];

    $altoOriginal =
        $informacion[1];


    /* ==============================
       CREAR IMAGEN ORIGINAL EN MEMORIA
       ============================== */

    switch ($mime) {

        case 'image/jpeg':

            $imagen =
                imagecreatefromjpeg(
                    $archivo['tmp_name']
                );

            break;


        case 'image/png':

            $imagen =
                imagecreatefrompng(
                    $archivo['tmp_name']
                );

            break;


        case 'image/webp':

            $imagen =
                imagecreatefromwebp(
                    $archivo['tmp_name']
                );

            break;


        default:

            throw new Exception(
                'Formato de imagen no compatible.'
            );

    }


    if (!$imagen) {
        throw new Exception(
            'No se pudo procesar la imagen.'
        );
    }


    /* ==============================
       CREAR DIRECTORIOS
       ============================== */

    $basePath =
        dirname(__DIR__)
        . '/public/uploads/productos';


    $originalPath =
        $basePath . '/originales';

    $grandePath =
        $basePath . '/grandes';

    $miniaturaPath =
        $basePath . '/miniaturas';


    foreach ([
        $originalPath,
        $grandePath,
        $miniaturaPath
    ] as $directorio) {

        if (!is_dir($directorio)) {

            if (!mkdir(
                $directorio,
                0755,
                true
            )) {

                imagedestroy($imagen);

                throw new Exception(
                    'No se pudo crear el directorio de imágenes.'
                );
            }
        }
    }


    /* ==============================
       GENERAR NOMBRE SEGURO
       ============================== */

    $nombreBase =
        preg_replace(
            '/[^a-z0-9-]/',
            '-',
            strtolower($slug)
        );


    $nombreBase =
        trim(
            $nombreBase,
            '-'
        );


    $identificador =
        bin2hex(
            random_bytes(5)
        );


    $nombreBase .= '-' . $identificador;


    /* ==============================
       GUARDAR ORIGINAL
       ============================== */

    $extensionOriginal =
        $mimePermitidos[$mime];


    $archivoOriginal =
        $nombreBase
        . '.'
        . $extensionOriginal;


    $rutaOriginal =
        $originalPath
        . '/'
        . $archivoOriginal;


    if (
        !move_uploaded_file(
            $archivo['tmp_name'],
            $rutaOriginal
        )
    ) {

        imagedestroy($imagen);

        throw new Exception(
            'No se pudo guardar la imagen original.'
        );
    }


    /* ==============================
       FUNCIÓN PARA CALCULAR TAMAÑO
       ============================== */

    $calcularTamanio =
        function (
            $ancho,
            $alto,
            $maximo
        ) {

            if (
                $ancho <= $maximo &&
                $alto <= $maximo
            ) {

                return [
                    $ancho,
                    $alto
                ];
            }


            if ($ancho >= $alto) {

                $nuevoAncho =
                    $maximo;

                $nuevoAlto =
                    (int) round(
                        $alto *
                        ($maximo / $ancho)
                    );

            } else {

                $nuevoAlto =
                    $maximo;

                $nuevoAncho =
                    (int) round(
                        $ancho *
                        ($maximo / $alto)
                    );
            }


            return [
                $nuevoAncho,
                $nuevoAlto
            ];
        };


    /* ==============================
       FUNCIÓN PARA CREAR LIENZO BLANCO
       ============================== */

    $crearLienzoBlanco =
        function (
            $ancho,
            $alto
        ) {

            $lienzo =
                imagecreatetruecolor(
                    $ancho,
                    $alto
                );


            if (!$lienzo) {
                throw new Exception(
                    'No se pudo crear el lienzo de la imagen.'
                );
            }


            /* Fondo blanco */

            $blanco =
                imagecolorallocate(
                    $lienzo,
                    255,
                    255,
                    255
                );


            imagefill(
                $lienzo,
                0,
                0,
                $blanco
            );


            return $lienzo;
        };


    /* ==============================
       CREAR IMAGEN GRANDE
       ============================== */

    [
        $nuevoAncho,
        $nuevoAlto
    ] =
        $calcularTamanio(
            $anchoOriginal,
            $altoOriginal,
            IMAGEN_GRANDE_MAX
        );


    $imagenGrande =
        $crearLienzoBlanco(
            $nuevoAncho,
            $nuevoAlto
        );


    imagecopyresampled(
        $imagenGrande,
        $imagen,
        0,
        0,
        0,
        0,
        $nuevoAncho,
        $nuevoAlto,
        $anchoOriginal,
        $altoOriginal
    );


    /* ==============================
       ARCHIVO GRANDE WEBP
       ============================== */

    $archivoGrande =
        $nombreBase
        . '.webp';


    $rutaGrande =
        $grandePath
        . '/'
        . $archivoGrande;


    if (
        !imagewebp(
            $imagenGrande,
            $rutaGrande,
            82
        )
    ) {

        imagedestroy($imagen);

        imagedestroy($imagenGrande);

        throw new Exception(
            'No se pudo crear la imagen grande.'
        );
    }


    /* ==============================
       CREAR MINIATURA
       ============================== */

    [
        $nuevoAncho,
        $nuevoAlto
    ] =
        $calcularTamanio(
            $anchoOriginal,
            $altoOriginal,
            IMAGEN_MINIATURA_MAX
        );


    $imagenMiniatura =
        $crearLienzoBlanco(
            $nuevoAncho,
            $nuevoAlto
        );


    /*
     * IMPORTANTE:
     * La fuente es $imagen,
     * NO $imagenMiniatura.
     */

    imagecopyresampled(
        $imagenMiniatura,
        $imagen,
        0,
        0,
        0,
        0,
        $nuevoAncho,
        $nuevoAlto,
        $anchoOriginal,
        $altoOriginal
    );


    /* ==============================
       ARCHIVO MINIATURA WEBP
       ============================== */

    $archivoMiniatura =
        $nombreBase
        . '.webp';


    $rutaMiniatura =
        $miniaturaPath
        . '/'
        . $archivoMiniatura;


    if (
        !imagewebp(
            $imagenMiniatura,
            $rutaMiniatura,
            78
        )
    ) {

        imagedestroy($imagen);

        imagedestroy($imagenGrande);

        imagedestroy($imagenMiniatura);

        throw new Exception(
            'No se pudo crear la miniatura.'
        );
    }


    /* ==============================
       LIBERAR MEMORIA
       ============================== */

    imagedestroy(
        $imagen
    );

    imagedestroy(
        $imagenGrande
    );

    imagedestroy(
        $imagenMiniatura
    );


    /* ==============================
       RESULTADO
       ============================== */

    return [

        'original' =>
            'uploads/productos/originales/'
            . $archivoOriginal,

        'grande' =>
            'uploads/productos/grandes/'
            . $archivoGrande,

        'miniatura' =>
            'uploads/productos/miniaturas/'
            . $archivoMiniatura

    ];

}