<html>
<head>
    <title>Barang Profile</title>
</head>
<body>
    <h1>Barang Profile</h1>
    <p>ID: {{ $barang->barang_id }}</p>
    <p>Code: {{ $barang->barang_kode }}</p>
    <p>Name: {{ $barang->barang_nama }}</p>
    <p>Category: {{ $barang->kategori->kategori_nama }}</p>
    <p>Purchase Price: {{ number_format($barang->harga_beli, 2) }}</p>
    <p>Selling Price: {{ number_format($barang->harga_jual, 2) }}</p>
    <a href="{{ url('/barang') }}">Back to Barang</a>
</body>
</html>
