<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pago</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        h1 { color: #007bff; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Confirmación de Pago</h1>
    <p><strong>Nombre:</strong> <?php echo e($nombre); ?></p>
    <p><strong>Apellido:</strong> <?php echo e($apellido); ?></p>
    <p><strong>Email:</strong> <?php echo e($email); ?></p>
<br>
<br>
    <h2>Detalles de la Compra</h2>
    <table>
        <thead>
            <tr>
                <th>Butaca</th>
                <th>Fila</th>
                <th>Columna</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($butacas as $butaca): ?>
            <tr>
                <td><?php echo e($butaca->id); ?></td>
                <td><?php echo e($butaca->fila); ?></td>
                <td><?php echo e($butaca->columna); ?></td>
                <td><?php echo e($butaca->precio); ?> €</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Detalles de la Sesión</h2>
    <p><strong>Película:</strong> <?php echo e($session->movie->title); ?></p>
    <p><strong>Fecha:</strong> <?php echo e($session->session_date); ?></p>
    <p><strong>Hora:</strong> <?php echo e($session->session_time); ?></p>
</body>
</html>