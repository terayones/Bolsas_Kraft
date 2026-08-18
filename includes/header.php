<header class="header">

    <div class="container header-content">

        <!-- LOGO -->

        <a
            href="/"
            class="logo"
        >
            BOLSAS KRAFT
        </a>


        <!-- MENÚ ESCRITORIO -->

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


    </div>

</header>


<!-- =========================================
     MENÚ FLOTANTE MÓVIL / TABLET
     ========================================= -->

<button
    type="button"
    class="menu-flotante"
    id="menuAbrir"
    aria-label="Abrir menú"
    aria-controls="menuLateral"
    aria-expanded="false"
>
    <span></span>
    <span></span>
    <span></span>
</button>


<!-- =========================================
     CAPA OSCURA
     ========================================= -->

<div
    class="menu-overlay"
    id="menuOverlay"
></div>


<!-- =========================================
     PANEL LATERAL
     ========================================= -->

<aside
    class="menu-lateral"
    id="menuLateral"
    aria-hidden="true"
>

    <div class="menu-lateral-header">

        <span>
            MENÚ
        </span>

        <button
            type="button"
            class="menu-cerrar"
            id="menuCerrar"
            aria-label="Cerrar menú"
        >
            &times;
        </button>

    </div>


    <nav class="menu-lateral-nav">

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

</aside>

<script src="/assets/js/menu.js"></script>