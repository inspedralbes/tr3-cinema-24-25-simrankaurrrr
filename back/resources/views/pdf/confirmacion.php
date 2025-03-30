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
            font-size: 12px;
        }
        
        .container {
            width: 100%;
            padding: 15px;
            background-color: var(--white);
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--medium);
        }
        
        .logo {
            font-size: 18px;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        h1 { 
            color: var(--primary-dark);
            margin: 0;
            font-size: 20px;
            letter-spacing: -0.5px;
        }
        
        h2 {
            color: var(--dark);
            font-size: 16px;
            margin-top: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid var(--light);
        }
        
        .info-section {
            background-color: var(--light);
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            font-size: 11px;
        }
        
        .info-item {
            margin-bottom: 3px;
        }
        
        .info-item strong {
            color: var(--dark);
            display: inline-block;
            min-width: 60px;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 15px 0;
            font-size: 10px;
        }
        
        th, td { 
            padding: 8px;
            text-align: center;
            border: 1px solid var(--light);
        }
        
        th { 
            background-color: var(--dark); 
            color: var(--white);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 10px;
        }
        
        tr:nth-child(even) {
            background-color: var(--light);
        }
        
        .total-section {
            text-align: right;
            margin-top: 15px;
            padding: 10px;
            background-color: var(--dark);
            color: var(--white);
            border-radius: 5px;
            font-size: 12px;
        }
        
        .total-price {
            font-size: 16px;
            color: var(--white);
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid var(--light);
            color: var(--medium);
            font-size: 10px;
        }
        
        .movie-title {
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 11px;
        }

        /* Estilos para los QR */
        .qr-section {
            margin-top: 15px;
            padding: 10px;
            text-align: center;
        }

        .qr-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: var(--primary-dark);
            font-size: 12px;
        }

        .qr-container {
            display: inline-block;
            margin: 5px;
            text-align: center;
            vertical-align: top;
            page-break-inside: avoid;
        }

        .qr-code {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            display: block;
            border: 1px solid #ddd;
            padding: 3px;
            background: white;
        }

        .qr-info {
            margin-top: 5px;
            font-size: 9px;
            color: var(--dark);
            max-width: 100px;
        }
        
        .qr-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 5px;
        }
        
        @page {
            size: A4;
            margin: 10mm;
        }
        
        @media print {
            body {
                font-size: 10pt;
            }
            .container {
                width: 100%;
                padding: 0;
                margin: 0;
                box-shadow: none;
            }
            .qr-code {
                width: 70px;
                height: 70px;
            }
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
            
            <div class="qr-row">
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
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="footer">
            <p>Gracias por tu compra. Presenta este código QR en taquilla para canjear tus entradas.</p>
            <p>© <?php echo e(date('Y')); ?> MDVD Cinema - Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>