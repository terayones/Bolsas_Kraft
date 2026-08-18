<?php
if (!isset($titulo_admin)) {
    $titulo_admin = 'Administración';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
<?= htmlspecialchars($titulo_admin) ?>
</title>

<link rel="stylesheet" href="/assets/css/admin.css">

</head>


<body>


<header>

<h1>
<?= htmlspecialchars($titulo_admin) ?>
</h1>


<nav>

<a href="/admin/productos.php">
Productos
</a>

</nav>


</header>


<main>