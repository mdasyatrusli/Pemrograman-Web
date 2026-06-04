<?php

$timezone = new DateTimeZone('Asia/Jakarta');
$now = new DateTime('now', $timezone);

$formatter = new IntlDateFormatter(
    'id_ID',
    IntlDateFormatter::FULL,
    IntlDateFormatter::NONE,
    'Asia/Jakarta',
    IntlDateFormatter::GREGORIAN,
    'EEEE, dd MMMM yyyy'
);

$tanggal = $formatter->format($now);
$waktu = $now->format('H:i:s');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-lg max-w-sm w-full text-center">
        <h1 class="text-xl font-semibold mb-6">Tanggal dan Waktu Sekarang</h1>
        <p class="mb-4">
            <span class="font-semibold">Tanggal:</span><br>
            <?= ucfirst($tanggal) ?>
        </p>
        <p>
            <span class="font-semibold">Waktu:</span><br>
            <?= $waktu ?> WIB
        </p>
    </div>

</body>
</html>