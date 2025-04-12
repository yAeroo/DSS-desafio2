<?php
session_start();
//session_destroy(); // ← solo descomentalo si querés resetear todo
require_once './controller/productos.php';

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// 🛒 Agregar al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'])) {
    $producto_id = $_POST['producto_id'];
    $nombre = $_POST['nombre'];
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $imagen = isset($_POST['imagen']) ? $_POST['imagen'] : '';

    // Si no existe o no es un array válido, lo inicializamos
    if (!isset($_SESSION['carrito'][$producto_id]) || !is_array($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id] = [
            'nombre' => $nombre,
            'precio' => $precio,
            'stock' => $stock,
            'imagen' => $imagen,
            'cantidad' => 1
        ];
    } else {
        // Verificamos que la clave 'cantidad' exista y sea numérica
        if (!isset($_SESSION['carrito'][$producto_id]['cantidad']) || !is_numeric($_SESSION['carrito'][$producto_id]['cantidad'])) {
            $_SESSION['carrito'][$producto_id]['cantidad'] = 1;
        }

        // Aumentamos la cantidad hasta el máximo permitido por stock
        if ($_SESSION['carrito'][$producto_id]['cantidad'] < $stock) {
            $_SESSION['carrito'][$producto_id]['cantidad']++;
        }
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
}

// 🗑️ Eliminar del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $producto_id = $_POST['eliminar_id'];

    if (isset($_SESSION['carrito'][$producto_id])) {
        if ($_SESSION['carrito'][$producto_id]['cantidad'] > 1) {
            $_SESSION['carrito'][$producto_id]['cantidad']--;
        } else {
            unset($_SESSION['carrito'][$producto_id]);
        }
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
}

$productos = obtenerProductosAgrupados($pdo);
$total_items = array_sum(array_column($_SESSION['carrito'], 'cantidad'));
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tienda E-Commerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-stone-100">

    <!-- NAVBAR -->
    <nav class="bg-white shadow p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Desafío 2 DSS</h1>
        <div class="relative">
            <button id="carritoBtn" class="relative">
                🛒
                <span class="bg-red-500 text-white text-xs rounded-full px-2 absolute -top-2 -right-3">
                    <?= $total_items ?>
                </span>
            </button>
            <div id="carritoDropdown"
                class="hidden absolute right-0 mt-2 w-[25rem] md:w-[40rem] bg-white border rounded-lg shadow-lg z-50 p-4 max-h-96 overflow-auto">

                <?php if (empty($_SESSION['carrito'])): ?>
                    <p class="text-sm text-gray-600">Tu carrito está vacío</p>
                <?php else: ?>
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="text-gray-600 border-b">
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Unidades</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['carrito'] as $id => $item): ?>
                                <tr class="border-b">
                                    <td class="flex items-center gap-2">
                                        <img src="<?= htmlspecialchars($item['imagen']) ?>" alt="img"
                                            class="w-10 h-10 object-cover rounded">
                                        <span><?= htmlspecialchars($item['nombre']) ?></span>
                                    </td>
                                    <td>$<?= number_format($item['precio'], 2) ?></td>
                                    <td>
                                        <div class="flex items-center gap-0 md:gap-2">
                                            <!-- Botón -1 -->
                                            <form method="POST">
                                                <input type="hidden" name="eliminar_id" value="<?= $id ?>">
                                                <button class="text-gray-700 font-bold text-lg px-1 border">−</button>
                                            </form>

                                            <!-- Cantidad -->
                                            <span class="px-2"><?= $item['cantidad'] ?></span>

                                            <!-- Botón +1 -->
                                            <form method="POST">
                                                <input type="hidden" name="producto_id" value="<?= $id ?>">
                                                <input type="hidden" name="nombre"
                                                    value="<?= htmlspecialchars($item['nombre']) ?>">
                                                <input type="hidden" name="precio" value="<?= $item['precio'] ?>">
                                                <input type="hidden" name="stock" value="<?= $item['stock'] ?>">
                                                <input type="hidden" name="imagen"
                                                    value="<?= htmlspecialchars($item['imagen']) ?>">
                                                <button class="text-gray-700 font-bold text-lg px-1 border">+</button>
                                            </form>
                                        </div>
                                    </td>
                                    <td>$<?= number_format($item['cantidad'] * $item['precio'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Ir al Pago -->
                    <div class="mt-4 text-right">
                        <a href="checkout.php"
                            class="inline-block bg-green-600 hover:bg-green-600 text-white text-sm px-4 py-2 rounded">
                            Ir al Pago
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- LANDING PAGE -->
    <div class="max-w-7xl mx-auto mt-10 p-4">
        <?php foreach ($productos as $categoria => $lista): ?>
            <h2 class="text-2xl font-semibold mb-4 text-gray-800"><?= htmlspecialchars($categoria) ?></h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-10">
                <?php foreach ($lista as $producto): ?>
                    <div class="bg-white p-4 rounded-xl shadow hover:shadow-md transition">
                        <img src="<?= htmlspecialchars($producto['imagen']) ?>"
                            alt="<?= htmlspecialchars($producto['nombre']) ?>" class="w-full h-60 object-cover rounded-md mb-2">
                        <h3 class="font-semibold text-lg"><?= htmlspecialchars($producto['nombre']) ?></h3>
                        <p class="text-gray-700">Precio: $<?= number_format($producto['precio'], 2) ?></p>
                        <p class="text-gray-500 text-sm">Stock: <?= $producto['stock'] ?></p>

                        <form method="POST">
                            <input type="hidden" name="producto_id" value="<?= $producto['id'] ?>">
                            <input type="hidden" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>">
                            <input type="hidden" name="precio" value="<?= $producto['precio'] ?>">
                            <input type="hidden" name="stock" value="<?= $producto['stock'] ?>">
                            <input type="hidden" name="imagen" value="<?= htmlspecialchars($producto['imagen']) ?>">
                            <button class="mt-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                                Agregar al carrito
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        const btn = document.getElementById('carritoBtn');
        const dropdown = document.getElementById('carritoDropdown');
        btn.addEventListener('click', () => dropdown.classList.toggle('hidden'));
    </script>

</body>

</html>