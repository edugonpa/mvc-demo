<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CRUD Productos</title>
        <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background-color: #f4f4f4; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Gestión de Productos</h1>

    @if(session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    @yield('content')
</body>
</html>
