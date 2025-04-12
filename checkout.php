<?php
session_start();

// Verificar si el carrito está vacío, si es así redirigir a la página principal
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header('Location: index.php');
    exit;
}

// Obtener el carrito de la sesión
$carrito = $_SESSION['carrito'];
$total = 0;
foreach ($carrito as $item) {
    $total += $item['precio'] * $item['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Finalizar Compra</h1>

        <!-- Carrito Resumen -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Resumen del Carrito</h2>
            <table class="w-full text-sm">
                <thead class="text-gray-600 border-b">
                    <tr>
                        <th class="text-left">Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carrito as $item): ?>
                        <tr class="border-b">
                            <td class="py-2">
                                <div class="flex items-center gap-2">
                                    <img src="<?= htmlspecialchars($item['imagen']) ?>" class="w-12 h-12 object-cover rounded" alt="">
                                    <span><?= htmlspecialchars($item['nombre']) ?></span>
                                </div>
                            </td>
                            <td class="text-center"><?= $item['cantidad'] ?></td>
                            <td class="text-center">$<?= number_format($item['precio'], 2) ?></td>
                            <td class="text-center font-medium">
                                $<?= number_format($item['precio'] * $item['cantidad'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="text-right mt-4 text-lg font-semibold">
                Total: $<?= number_format($total, 2) ?>
            </div>
        </div>

        <!-- Formulario de Pago -->
        <form action="procesar_compra.php" method="POST" class="bg-white rounded-xl shadow p-6 space-y-4">
            <h2 class="text-xl font-semibold">Datos del Cliente</h2>

            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre completo</label>
                <input type="text" name="nombre" id="nombre" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring focus:border-blue-400">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                <input type="email" name="email" id="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring focus:border-blue-400">
            </div>

            <div>
                <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección de envío</label>
                <textarea name="direccion" id="direccion" required rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring focus:border-blue-400"></textarea>
            </div>

            <div class="text-right pt-4">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow">
                    Confirmar Compra
                </button>
            </div>
        </form>
    </div>
</body>

</html>
