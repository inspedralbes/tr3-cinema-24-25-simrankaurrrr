<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Entrada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .valid {
            color: green;
            font-size: 24px;
            margin: 20px 0;
        }
        .invalid {
            color: red;
            font-size: 24px;
            margin: 20px 0;
        }
        .info {
            text-align: left;
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #d80032;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Verificación de Entrada</h1>
    
    @if($valido)
        <div class="valid">✓ ENTRADA VÁLIDA</div>
    @else
        <div class="invalid">✗ ENTRADA INVÁLIDA</div>
    @endif
    
    <div class="info">
        <p><strong>Película:</strong> {{ $pelicula }}</p>
        <p><strong>Fecha:</strong> {{ $fecha }}</p>
        <p><strong>Hora:</strong> {{ $hora }}</p>
        <p><strong>Asiento:</strong> {{ $asiento }}</p>
        <p><strong>Cliente:</strong> {{ $cliente }}</p>
        <p><strong>Precio:</strong> {{ $precio }} €</p>
        <p><strong>Código:</strong> {{ $codigo }}</p>
    </div>

    @if($valido)
        <a href="{{ $pdf_url }}" class="btn">Descargar PDF Completo</a>
    @endif
    
    <a href="{{ url('/') }}" class="btn">Volver al Inicio</a>
</body>
</html>