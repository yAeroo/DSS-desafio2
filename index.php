<?php
session_start();

// 🔌 Conexión a la base de datos
$host = 'localhost';
$dbname = 'desafio2_dss';
$user = 'root';
$pass = ''; // cambia esto si tenés contraseña

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// 📦 Traer las categorías y productos desde la base de datos
$productos = [];

$sql = "SELECT c.nombre AS categoria, p.nombre AS producto, p.precio, p.stock, p.imagen_url
        FROM productos p
        JOIN categorias c ON p.categoria_id = c.id
        ORDER BY c.nombre, p.precio";

$stmt = $pdo->query($sql);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cat = $row['categoria'];
    $productos[$cat][] = [
        'nombre' => $row['producto'],
        'precio' => $row['precio'],
        'stock' => $row['stock'],
        'imagen' => $row['imagen_url'] ?? 'https://via.placeholder.com/150',
    ];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tienda E-Commerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <!-- 🔝 NAVBAR -->
    <nav class="bg-white shadow p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Mi Tienda</h1>
        <div class="relative">
            <button id="carritoBtn" class="relative">
                🛒
                <span class="bg-red-500 text-white text-xs rounded-full px-2 absolute -top-2 -right-3">0</span>
            </button>
            <div id="carritoDropdown"
                class="hidden absolute right-0 mt-2 w-64 bg-white border rounded-lg shadow-lg z-50 p-4">
                <p class="text-sm text-gray-600">Tu carrito está vacío</p>
            </div>
        </div>
    </nav>

    <!-- 🛍️ LANDING PAGE -->
    <div class="max-w-7xl mx-auto mt-10 p-4">
        <?php foreach ($productos as $categoria => $lista): ?>
            <h2 class="text-2xl font-semibold mb-4 text-gray-800"><?= htmlspecialchars($categoria) ?></h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-10">
                <?php
                usort($lista, fn($a, $b) => $a['precio'] <=> $b['precio']);
                foreach ($lista as $producto):
                ?>
                    <div class="bg-white p-4 rounded-xl shadow hover:shadow-md transition">
                        <img src="<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>"
                            class="w-full h-40 object-cover rounded-md mb-2">
                        <h3 class="font-semibold text-lg"><?= htmlspecialchars($producto['nombre']) ?></h3>
                        <p class="text-gray-700">Precio: $<?= number_format($producto['precio'], 2) ?></p>
                        <p class="text-gray-500 text-sm">Stock: <?= $producto['stock'] ?></p>
                        <button class="mt-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">Agregar al carrito</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Script para mostrar el carrito -->
    <script>
    const btn = document.getElementById('carritoBtn');
    const dropdown = document.getElementById('carritoDropdown');
    btn.addEventListener('click', () => dropdown.classList.toggle('hidden'));
    </script>

</body>
</html>
