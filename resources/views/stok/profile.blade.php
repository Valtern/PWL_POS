<html>
<head>
    <title>Stok Profile</title>
</head>
<body>
    <h1>Stok Profile</h1>
    <p>ID: {{ $stok->stok_id }}</p>
    <p>Barang: {{ $stok->barang->barang_nama }}</p>
    <p>Quantity: {{ $stok->stok_jumlah }}</p>
    <p>Last Updated: {{ $stok->stok_tanggal ? $stok->stok_tanggal->format('d-m-Y') : 'No Date Available' }}</p>
    <a href="{{ url('/stok') }}">Back to Stok</a>
</body>
</html>
