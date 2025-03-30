<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entradas de Cine - Confirmación de Compra</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #edf2f4;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #2b2d42;
        }
        .container {
            width: 90%;
            max-width: 600px;
            background: #ffffff;
            margin: 30px auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(43, 45, 66, 0.1);
            border-top: 4px solid #d80032;
        }
        h1 {
            color: #2b2d42;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        p {
            color: #2b2d42;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #8d99ae;
            border-top: 1px solid #edf2f4;
            padding-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 25px 0;
            background-color: #d80032;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(216, 0, 50, 0.2);
        }
        .btn:hover {
            background-color: #ef233c;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(216, 0, 50, 0.3);
        }
        .highlight {
            color: #d80032;
            font-weight: 600;
        }
        .movie-title {
            font-size: 20px;
            color: #2b2d42;
            margin: 20px 0 10px;
            font-weight: bold;
        }
        .butacas-list {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
    <h1><span style="color: #ef233c;">MD</span>VD</h1>

        <h1>¡Disfruta de tu película, {{ $nombre }} {{ $apellido }}!</h1>
        
        @if(isset($session) && isset($session->movie))
        <p class="movie-title">{{ $session->movie->title }}</p>
        @endif
        
        <p>Tu compra ha sido procesada con éxito. Adjuntamos tus entradas en formato PDF para que puedas presentarlas al acceder a la sala.</p>
        
        @if(isset($session))
        <p><strong>Fecha:</strong> {{ $session->session_date ?? 'No especificada' }}<br>
        <strong>Hora:</strong> {{ $session->session_time ?? 'No especificada' }}</p>
        @endif
        
        @if(isset($butacas) && count($butacas) > 0)
        <div class="butacas-list">
            <strong>Butacas:</strong>
            @foreach($butacas as $butaca)
            <div>Fila {{ $butaca['fila'] }}, Asiento {{ $butaca['columna'] }}</div>
            @endforeach
        </div>
        @endif
        
        <p>Recomendamos llegar al menos 15 minutos antes del comienzo de la sesión.</p>
        
        <a href="{{ route('download.pdf', ['pdfPath' => $pdfPath]) }}" class="btn">Descargar Comprobante</a>
        
        <p class="footer">En caso de cualquier incidencia con tu compra, contacta con nuestro equipo en <span class="highlight">gestioncine0@gmail.com</span><br>
        ¡Gracias por elegirnos y que disfrutes de la película!</p>
    </div>
</body>
</html>