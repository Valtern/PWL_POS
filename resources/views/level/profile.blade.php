<html>
<head>
    <title>Level Profile</title>
</head>
<body>
    <h1>Level Profile</h1>
    <p>ID: {{ $level->level_id }}</p>
    <p>Code: {{ $level->level_kode }}</p>
    <p>Name: {{ $level->level_nama }}</p>
    <a href="{{ url('/level') }}">Back to Levels</a>
</body>
</html>
