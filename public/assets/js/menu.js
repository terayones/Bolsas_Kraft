document.addEventListener('DOMContentLoaded', function () {

    const menuAbrir = document.getElementById('menuAbrir');
    const menuCerrar = document.getElementById('menuCerrar');
    const menuOverlay = document.getElementById('menuOverlay');
    const menuLateral = document.getElementById('menuLateral');

    if (
        !menuAbrir ||
        !menuCerrar ||
        !menuOverlay ||
        !menuLateral
    ) {
        return;
    }


    /* ==============================
       ABRIR MENÚ
       ============================== */

    function abrirMenu() {

        document.body.classList.add('menu-abierto');

        menuAbrir.setAttribute(
            'aria-expanded',
            'true'
        );

        menuLateral.setAttribute(
            'aria-hidden',
            'false'
        );

    }


    /* ==============================
       CERRAR MENÚ
       ============================== */

    function cerrarMenu() {

        document.body.classList.remove('menu-abierto');

        menuAbrir.setAttribute(
            'aria-expanded',
            'false'
        );

        menuLateral.setAttribute(
            'aria-hidden',
            'true'
        );

    }


    /* ==============================
       BOTÓN ABRIR
       ============================== */

    menuAbrir.addEventListener(
        'click',
        abrirMenu
    );


    /* ==============================
       BOTÓN CERRAR
       ============================== */

    menuCerrar.addEventListener(
        'click',
        cerrarMenu
    );


    /* ==============================
       CLIC EN EL FONDO
       ============================== */

    menuOverlay.addEventListener(
        'click',
        cerrarMenu
    );


    /* ==============================
       CERRAR AL ELEGIR UNA OPCIÓN
       ============================== */

    const enlacesMenu =
        menuLateral.querySelectorAll('a');


    enlacesMenu.forEach(function (enlace) {

        enlace.addEventListener(
            'click',
            cerrarMenu
        );

    });


    /* ==============================
       TECLA ESC
       ============================== */

    document.addEventListener(
        'keydown',
        function (evento) {

            if (
                evento.key === 'Escape'
            ) {

                cerrarMenu();

            }

        }
    );

});