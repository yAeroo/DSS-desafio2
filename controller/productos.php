<?php
require_once __DIR__ . '/../database/conexion.php';

function obtenerProductosAgrupados($pdo) {
    $productos = [];

    $sql = "SELECT p.id, c.nombre AS categoria, p.nombre AS producto, p.precio, p.stock, p.imagen_url
            FROM productos p
            JOIN categorias c ON p.categoria_id = c.id
            ORDER BY c.nombre, p.precio";

    $stmt = $pdo->query($sql);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cat = $row['categoria'];
        $productos[$cat][] = [
            'id' => $row['id'],
            'nombre' => $row['producto'],
            'precio' => $row['precio'],
            'stock' => $row['stock'],
            'imagen' => $row['imagen_url'] ,
        ];
        
    }
    return $productos;
}
?>
