<?php
$produk = [
    [
        "thumb" => "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=300&fit=crop",
        "title" => "Laptop Pro 15",
        "desc"  => "Prosesor cepat, layar 15 inci, cocok untuk kerja dan kuliah.",
        "price" => 8_500_000,
    ],
    [
        "thumb" => "https://images.unsplash.com/photo-1540574163026-643ea20ade25?w=400&h=300&fit=crop",
        "title" => "Meja Belajar Kayu",
        "desc"  => "Meja minimalis dari kayu jati, kuat dan tahan lama.",
        "price" => 750_000,
    ],
    [
        "thumb" => "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=300&fit=crop",
        "title" => "Headphone Wireless",
        "desc"  => "Suara jernih, noise cancelling, baterai tahan 30 jam.",
        "price" => 350_000,
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-bold mb-12 text-center text-gray-800">Katalog Produk</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($produk as $item): ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <!-- Thumbnail -->
                    <img src="<?= htmlspecialchars($item['thumb']) ?>" 
                         alt="<?= htmlspecialchars($item['title']) ?>" 
                         class="w-full h-48 object-cover">
                    
                    <!-- Content -->
                    <div class="p-6">
                        <h2 class="text-xl font-bold mb-2 text-gray-800"><?= htmlspecialchars($item['title']) ?></h2>
                        <p class="text-gray-600 text-sm mb-4"><?= htmlspecialchars($item['desc']) ?></p>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-green-600">
                                Rp <?= number_format($item['price'], 0, ',', '.') ?>
                            </span>
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                                Beli
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>