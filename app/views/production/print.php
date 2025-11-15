<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Producción - <?php echo htmlspecialchars($order['numero_orden']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
            font-size: 18px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #333;
        }
        .signature {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Cerrar
        </button>
    </div>

    <div class="header">
        <h1>ORDEN DE PRODUCCIÓN</h1>
        <p>Sistema de Gestión para Comedores Industriales</p>
    </div>

    <div class="info-section">
        <h2>Información General</h2>
        <div class="info-row">
            <div class="info-label">Número de Orden:</div>
            <div class="info-value"><?php echo htmlspecialchars($order['numero_orden']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Comedor:</div>
            <div class="info-value"><?php echo htmlspecialchars($order['comedor']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Ubicación:</div>
            <div class="info-value"><?php echo htmlspecialchars($order['ubicacion']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Fecha de Servicio:</div>
            <div class="info-value"><?php echo date('d/m/Y', strtotime($order['fecha_servicio'])); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Turno:</div>
            <div class="info-value"><?php echo htmlspecialchars($order['turno']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Línea de Servicio:</div>
            <div class="info-value"><?php echo htmlspecialchars($order['linea_servicio']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Receta:</div>
            <div class="info-value"><?php echo htmlspecialchars($order['receta']); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Comensales Proyectados:</div>
            <div class="info-value"><strong><?php echo number_format($order['comensales_proyectados']); ?></strong></div>
        </div>
    </div>

    <div class="info-section">
        <h2>Lista de Ingredientes (OPAD-025)</h2>
        <table>
            <thead>
                <tr>
                    <th>Ingrediente</th>
                    <th>Cantidad Requerida</th>
                    <th>Unidad</th>
                    <th>Costo Estimado</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $costoTotal = 0;
                foreach ($ingredientes as $ing): 
                    $costoTotal += $ing['costo_estimado'];
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($ing['ingrediente']); ?></td>
                    <td style="text-align: right;"><?php echo number_format($ing['cantidad_requerida'], 3); ?></td>
                    <td><?php echo htmlspecialchars($ing['unidad']); ?></td>
                    <td style="text-align: right;">$<?php echo number_format($ing['costo_estimado'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total">
            Costo Total Estimado: $<?php echo number_format($costoTotal, 2); ?>
        </div>
    </div>

    <?php if (!empty($order['observaciones'])): ?>
    <div class="info-section">
        <h2>Observaciones</h2>
        <p><?php echo nl2br(htmlspecialchars($order['observaciones'])); ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p><strong>Formato OPAD-007 - Orden de Producción de Alimentos</strong></p>
        <p>Fecha de Emisión: <?php echo date('d/m/Y H:i'); ?></p>
        
        <div class="signature">
            <div class="signature-box">
                <div class="signature-line">
                    Elaboró
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    Autorizó
                </div>
            </div>
        </div>
    </div>
</body>
</html>
