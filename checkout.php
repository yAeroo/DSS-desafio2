<?php
session_start();

// Verificar si el carrito está vacío, si es así redirigir a la página principal
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header('Location: index.php');
    exit;
}

$errores = [];
if (isset($_SESSION['errors'])) {
    $errores = $_SESSION['errors'];
    unset($_SESSION['errors']);
}

// Obtener el carrito de la sesión
$carrito = $_SESSION['carrito'];
$total = 0;
foreach ($carrito as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validationErrors = [];

    $nombre = htmlspecialchars(trim($_POST['nombre']) ?? '');
    $dui = htmlspecialchars(trim($_POST['dui']) ?? '');
    $numCard = htmlspecialchars(trim($_POST['numCard']) ?? '');
    $email = htmlspecialchars(trim($_POST['email']) ?? '');
    $direccion = htmlspecialchars(trim($_POST['direccion']) ?? '');
    $fechaVen = $_POST['fechaVen'] ?? '';

    if (!preg_match("/^[A-Za-zÁÉÍÓÚÑáéíóúñ\s]{3,}$/", $nombre)) {
        $validationErrors[] = "El nombre debe tener solo letras y al menos 3 caracteres.";
    }

    if (!preg_match("/^\d{8}-\d{1}$/", $dui)) {
        $validationErrors[] = "El DUI debe tener el formato 00000000-0.";
    }

    if (!preg_match("/^\d{16}$/", $numCard)) {
        $validationErrors[] = "El número de tarjeta debe tener exactamente 16 dígitos.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validationErrors[] = "El correo electrónico no es válido.";
    }

    if (strlen($direccion) < 10) {
        $validationErrors[] = "La dirección debe tener al menos 10 caracteres.";
    }

    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fechaVen) || strtotime($fechaVen) < strtotime(date('Y-m-d'))) {
        $validationErrors[] = "La fecha de vencimiento no es válida o es anterior a hoy.";
    }

    if(count($validationErrors) === 0) {
        unset($_SESSION['carrito']);
        $_SESSION['success'] = "Compra realizada con éxito. Gracias por su compra.";
        header('Location: index.php');
        exit;
    }else{
        $_SESSION['errors'] = $validationErrors;
        header('Location: checkout.php');
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body class="bg-gray-100 min-h-screen">
        <!-- NAVBAR -->
        <nav class="bg-white shadow p-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">Desafío 2 DSS</h1>

            <a href="index.php" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-lg shadow">
                Ir a Inicio
            </a>
    </nav>

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
        <form method="POST" action="checkout.php" class="bg-white rounded-xl shadow p-6 space-y-4">
            <h2 class="text-xl font-semibold">Datos del Cliente</h2>

            <?php if (!empty($errores)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="font-bold flex"><i class='bx bx-error text-xl mr-1'></i> ¡Error de Validación!</span>
                    <?php foreach ($errores as $error): ?>
                        <span class="block">• <?= $error ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre completo</label>
                <input type="text" name="nombre" id="nombre" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring focus:border-blue-400">
            </div>

            <div>
                <label for="dui" class="block text-sm font-medium text-gray-700">DUI</label>
                <input type="text" name="dui" id="dui" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring focus:border-blue-400">
            </div>

            <div>
                <label for="numCard" class="block text-sm font-medium text-gray-700">Número de Tarjeta</label>
                <input type="text" name="numCard" id="numCard" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring focus:border-blue-400">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                <input type="email" name="email" id="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring focus:border-blue-400">
            </div>

            <div>
                <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección de envío</label>
                <textarea name="direccion" id="direccion" required rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring focus:border-blue-400"></textarea>
            </div>

            <div>
                <label for="fechaVen" class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                <input type="date" name="fechaVen" id="fechaVen" required rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring focus:border-blue-400"></textarea>
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
