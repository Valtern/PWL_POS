<html>
<head>
    <title>Penjualan Profile</title>
</head>
<body>
    <h1>Penjualan Profile</h1>
    <p>ID: {{ $penjualan->penjualan_id }}</p>
    <p>User ID: {{ $penjualan->user_id }}</p>
    <p>Pembeli: {{ $penjualan->pembeli }}</p>
    <p>Kode Penjualan: {{ $penjualan->penjualan_kode }}</p>
    <p>Tanggal Penjualan: {{ $penjualan->penjualan_tanggal }}</p>
    <a href="{{ url('/penjualan') }}">Back to Penjualan</a>
</body>
</html>
