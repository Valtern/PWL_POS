<html>
<head>
    <title>Kategori Profile</title>
</head>
<body>
    <h1>Kategori Profile</h1>
    <p>ID: {{ $kategori->kategori_id }}</p>
    <p>Code: {{ $kategori->kategori_kode }}</p>
    <p>Name: {{ $kategori->kategori_nama }}</p>
    <a href="{{ url('/kategori') }}">Back to Kategori</a>
</body>
</html>
