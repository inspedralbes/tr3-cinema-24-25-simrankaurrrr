<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pago - Cine MDVD</title>
    <style>
        :root {
            --primary: #ef233c;
            --primary-dark: #d80032;
            --dark: #2b2d42;
            --medium: #8d99ae;
            --light: #edf2f4;
            --white: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark);
            line-height: 1.6;
            padding: 0;
            margin: 0;
            background-color: #f9f9f9;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background-color: var(--white);
            box-shadow: var(--shadow);
            border-radius: 10px;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--medium);
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        h1 { 
            color: var(--primary-dark);
            margin: 0;
            font-size: 28px;
            letter-spacing: -0.5px;
        }
        
        h2 {
            color: var(--dark);
            font-size: 20px;
            margin-top: 30px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--light);
        }
        
        .info-section {
            background-color: var(--light);
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            margin-bottom: 5px;
        }
        
        .info-item strong {
            color: var(--dark);
            display: inline-block;
            min-width: 80px;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 25px 0;
            box-shadow: var(--shadow);
            overflow: hidden;
            border-radius: 8px;
        }
        
        th, td { 
            padding: 15px;
            text-align: center;
            border: 1px solid var(--light);
        }
        
        th { 
            background-color: var(--dark); 
            color: var(--white);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        
        tr:nth-child(even) {
            background-color: var(--light);
        }
        
        tr:hover {
            background-color: #e2e7eb;
        }
        
        .total-section {
            text-align: right;
            margin-top: 30px;
            padding: 20px;
            background-color: var(--dark);
            color: var(--white);
            border-radius: 8px;
        }
        
        .total-price {
            font-size: 24px;
            color: var(--white);
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            margin-top: 90px;
            padding-top: 20px;
            border-top: 1px solid var(--light);
            color: var(--medium);
            font-size: 14px;
        }
        
        .movie-title {
            font-weight: 600;
            color: var(--primary-dark);
        }

        /* Estilos para los QR */
        .qr-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            text-align: center;
        }

        .qr-title {
            font-weight: bold;
            margin-bottom: 20px;
            color: var(--primary-dark);
        }

        .qr-container {
            display: inline-block;
            margin: 15px;
            text-align: center;
            vertical-align: top;
        }

        .qr-code {
            width: 150px;
            height: 150px;
            margin: 0 auto;
            display: block;
            border: 1px solid #ddd;
            padding: 5px;
            background: white;
        }

        .qr-info {
            margin-top: 10px;
            font-size: 14px;
            color: var(--dark);
            max-width: 150px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 15px;
            }
            
            .info-section {
                grid-template-columns: 1fr;
            }
            
            th, td {
                padding: 10px 5px;
                font-size: 14px;
            }

            .qr-container {
                margin: 10px;
            }
            
        }
        .download-btn {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 15px;
    background-color: #d80032;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
    text-align: center;
    transition: all 0.3s ease;
}

.download-btn:hover {
    background-color: #b30028;
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">MDVD CINEMA</div>
            <h1>Confirmación de Pago</h1>
        </div>

        <div class="info-section">
            <div class="info-item">
                <strong>Nombre:</strong> <?php echo e($nombre); ?>
            </div>
            <div class="info-item">
                <strong>Apellido:</strong> <?php echo e($apellido); ?>
            </div>
            <div class="info-item">
                <strong>Email:</strong> <?php echo e($email); ?>
            </div>
            <div class="info-item">
                <strong>Fecha:</strong> <?php echo e(date('d/m/Y')); ?>
            </div>
        </div>

        <h2>Detalles de tu compra</h2>
        <table>
            <thead>
                <tr>
                    <th>Película</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Asiento</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $precioTotal = 0;
                foreach ($butacas as $butaca): 
                    $precioTotal += $butaca['precio'];
                ?>
                <tr>
                    <td class="movie-title"><?php echo e($butaca['movie']['title']); ?></td>
                    <td><?php echo e(date('d/m/Y', strtotime($butaca['session']['session_date']))); ?></td>
                    <td><?php echo e($butaca['session']['session_time']); ?></td>
                    <td>Fila <?php echo e($butaca['fila']); ?>, Asiento <?php echo e($butaca['columna']); ?></td>
                    <td><?php echo e(number_format($butaca['precio'], 2)); ?> €</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            <strong>Total a pagar:</strong> <span class="total-price"><?php echo e(number_format($precioTotal, 2)); ?> €</span>
        </div>

        <h2>Códigos QR de tus entradas</h2>
        <div class="qr-section">
            <p class="qr-title">Escanea este código para verificar tu entrada</p>
            
            <div style="display: flex; flex-wrap: wrap; justify-content: center;">
            <?php foreach ($butacas as $butaca): ?>
    <?php if(isset($butaca['qr_code'])): ?>
        <div class="qr-container">
    <img src="<?php echo e($butaca['qr_code']); ?>" class="qr-code" alt="Código QR">
    <div class="qr-info">
        <?php echo e($butaca['movie']['title']); ?><br>
        Fecha: <?php echo e(date('d/m/Y', strtotime($butaca['session']['session_date']))); ?><br>
        Hora: <?php echo e($butaca['session']['session_time']); ?><br>
        Asiento: <?php echo e($butaca['fila']); ?><?php echo e($butaca['columna']); ?>
    </div>
    
    <!-- Botón de descarga con URL exacta -->
    <a href="<?php echo e(Storage::url("comprobante_{$nombre}_{$apellido}_" . time() . ".pdf")); ?>" 
       class="download-btn"
       style="display: inline-block; margin-top: 10px; padding: 8px 15px; background-color: #d80032; color: white; text-decoration: none; border-radius: 4px; font-size: 14px;">
       Descargar Entrada
    </a>
</div>
    <?php endif; ?>
   
        </div>

<?php endforeach; ?>
            

        <div class="footer">
            <p>Gracias por tu compra. Presenta este código QR en taquilla para canjear tus entradas.</p>
            <p>© <?php echo e(date('Y')); ?> MDVD Cinema - Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>