<?php

$produk = [
    ["nama" => "Laptop", "kategori" => "Elektronik", "harga" => 8_500_000],
    ["nama" => "Meja Belajar", "kategori" => "Furniture", "harga" => 750_000],
    ["nama" => "Headphone", "kategori" => "Aksesoris", "harga" => 350_000],
    ["nama" => "Smartphone", "kategori" => "Elektronik", "harga" => 5_500_000],
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold mb-8 text-center">Daftar Produk</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($produk as $item): ?>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="font-bold text-lg mb-2"><?= htmlspecialchars($item['nama']) ?></h2>
                    <p class="text-gray-600 mb-2">
                        <span class="font-semibold">Kategori:</span> <?= htmlspecialchars($item['kategori']) ?>
                    </p>
                    <p class="text-green-600 font-bold">
                        Rp <?= number_format($item['harga'], 0, ',', '.') ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>