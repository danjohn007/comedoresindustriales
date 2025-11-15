-- ====================================================================
-- SQL Script - Sample Financial Transactions Data
-- Fecha: 2025-11-15
-- Descripción: Script para insertar datos de ejemplo de transacciones financieras
-- ====================================================================

USE comedores_industriales;

-- Insertar transacciones financieras de ejemplo
-- Este script genera transacciones para los últimos 6 meses

-- Primero, obtenemos los IDs de comedores y categorías
SET @comedor1 = (SELECT id FROM comedores WHERE activo = 1 ORDER BY id LIMIT 1);
SET @comedor2 = (SELECT id FROM comedores WHERE activo = 1 ORDER BY id LIMIT 1 OFFSET 1);
SET @comedor3 = (SELECT id FROM comedores WHERE activo = 1 ORDER BY id LIMIT 1 OFFSET 2);
SET @usuario = (SELECT id FROM usuarios WHERE rol = 'admin' LIMIT 1);

-- Categorías de ingresos
SET @cat_subsidio = (SELECT id FROM categorias_financieras WHERE nombre = 'Subsidio Gubernamental' LIMIT 1);
SET @cat_venta = (SELECT id FROM categorias_financieras WHERE nombre = 'Venta de Servicios' LIMIT 1);
SET @cat_donaciones = (SELECT id FROM categorias_financieras WHERE nombre = 'Donaciones' LIMIT 1);
SET @cat_otros_ing = (SELECT id FROM categorias_financieras WHERE nombre = 'Otros Ingresos' LIMIT 1);

-- Categorías de egresos
SET @cat_ingredientes = (SELECT id FROM categorias_financieras WHERE nombre = 'Compra de Ingredientes' LIMIT 1);
SET @cat_salarios = (SELECT id FROM categorias_financieras WHERE nombre = 'Salarios' LIMIT 1);
SET @cat_servicios = (SELECT id FROM categorias_financieras WHERE nombre = 'Servicios Públicos' LIMIT 1);
SET @cat_mantenimiento = (SELECT id FROM categorias_financieras WHERE nombre = 'Mantenimiento' LIMIT 1);
SET @cat_equipo = (SELECT id FROM categorias_financieras WHERE nombre = 'Equipo y Utensilios' LIMIT 1);
SET @cat_transporte = (SELECT id FROM categorias_financieras WHERE nombre = 'Transporte' LIMIT 1);
SET @cat_otros_egr = (SELECT id FROM categorias_financieras WHERE nombre = 'Otros Gastos' LIMIT 1);

-- ====================================================================
-- INGRESOS - Mes actual
-- ====================================================================

-- Subsidios mensuales
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'ingreso', 'Subsidio Gubernamental - Noviembre 2025', 150000.00, @cat_subsidio, '2025-11-01', 'Apoyo mensual del gobierno estatal para operación del comedor', @usuario),
    (@comedor2, 'ingreso', 'Subsidio Gubernamental - Noviembre 2025', 120000.00, @cat_subsidio, '2025-11-01', 'Apoyo mensual del gobierno estatal para operación del comedor', @usuario),
    (@comedor3, 'ingreso', 'Subsidio Gubernamental - Noviembre 2025', 135000.00, @cat_subsidio, '2025-11-01', 'Apoyo mensual del gobierno estatal para operación del comedor', @usuario);

-- Ventas de servicios (comidas vendidas)
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'ingreso', 'Venta de comidas - Semana 1', 25340.50, @cat_venta, '2025-11-08', 'Ingresos por venta de comidas primera semana de noviembre', @usuario),
    (@comedor1, 'ingreso', 'Venta de comidas - Semana 2', 27850.75, @cat_venta, '2025-11-15', 'Ingresos por venta de comidas segunda semana de noviembre', @usuario),
    (@comedor2, 'ingreso', 'Venta de comidas - Semana 1', 18920.00, @cat_venta, '2025-11-08', 'Ingresos por venta de comidas primera semana de noviembre', @usuario),
    (@comedor2, 'ingreso', 'Venta de comidas - Semana 2', 21340.50, @cat_venta, '2025-11-15', 'Ingresos por venta de comidas segunda semana de noviembre', @usuario),
    (@comedor3, 'ingreso', 'Venta de comidas - Semana 1', 22450.25, @cat_venta, '2025-11-08', 'Ingresos por venta de comidas primera semana de noviembre', @usuario),
    (@comedor3, 'ingreso', 'Venta de comidas - Semana 2', 24680.00, @cat_venta, '2025-11-15', 'Ingresos por venta de comidas segunda semana de noviembre', @usuario);

-- Donaciones
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'ingreso', 'Donación empresa local', 15000.00, @cat_donaciones, '2025-11-05', 'Donación de empresa manufacturera local para mejoras', @usuario),
    (@comedor3, 'ingreso', 'Donación fundación', 8000.00, @cat_donaciones, '2025-11-12', 'Donación de fundación comunitaria', @usuario);

-- ====================================================================
-- EGRESOS - Mes actual
-- ====================================================================

-- Compra de ingredientes (pagos semanales)
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'egreso', 'Compra de carnes y pescados', 35680.50, @cat_ingredientes, '2025-11-02', 'Compra semanal de proteínas animales', @usuario),
    (@comedor1, 'egreso', 'Compra de verduras y frutas', 18420.75, @cat_ingredientes, '2025-11-04', 'Compra de verduras frescas y frutas de temporada', @usuario),
    (@comedor1, 'egreso', 'Compra de abarrotes', 22450.00, @cat_ingredientes, '2025-11-06', 'Compra de arroz, frijol, aceite y otros básicos', @usuario),
    (@comedor1, 'egreso', 'Compra de lácteos', 12850.25, @cat_ingredientes, '2025-11-08', 'Compra de leche, quesos y yogurt', @usuario),
    (@comedor1, 'egreso', 'Compra de carnes y pescados', 38920.00, @cat_ingredientes, '2025-11-09', 'Compra semanal de proteínas animales', @usuario),
    (@comedor1, 'egreso', 'Compra de verduras y frutas', 19680.50, @cat_ingredientes, '2025-11-11', 'Compra de verduras frescas y frutas de temporada', @usuario),
    
    (@comedor2, 'egreso', 'Compra de carnes y pescados', 28450.00, @cat_ingredientes, '2025-11-02', 'Compra semanal de proteínas animales', @usuario),
    (@comedor2, 'egreso', 'Compra de verduras y frutas', 14320.75, @cat_ingredientes, '2025-11-04', 'Compra de verduras frescas y frutas de temporada', @usuario),
    (@comedor2, 'egreso', 'Compra de abarrotes', 18750.00, @cat_ingredientes, '2025-11-06', 'Compra de arroz, frijol, aceite y otros básicos', @usuario),
    (@comedor2, 'egreso', 'Compra de carnes y pescados', 31200.50, @cat_ingredientes, '2025-11-09', 'Compra semanal de proteínas animales', @usuario),
    (@comedor2, 'egreso', 'Compra de verduras y frutas', 15840.25, @cat_ingredientes, '2025-11-11', 'Compra de verduras frescas y frutas de temporada', @usuario),
    
    (@comedor3, 'egreso', 'Compra de carnes y pescados', 32150.00, @cat_ingredientes, '2025-11-02', 'Compra semanal de proteínas animales', @usuario),
    (@comedor3, 'egreso', 'Compra de verduras y frutas', 16890.50, @cat_ingredientes, '2025-11-04', 'Compra de verduras frescas y frutas de temporada', @usuario),
    (@comedor3, 'egreso', 'Compra de abarrotes', 20340.75, @cat_ingredientes, '2025-11-06', 'Compra de arroz, frijol, aceite y otros básicos', @usuario),
    (@comedor3, 'egreso', 'Compra de carnes y pescados', 34680.00, @cat_ingredientes, '2025-11-09', 'Compra semanal de proteínas animales', @usuario),
    (@comedor3, 'egreso', 'Compra de verduras y frutas', 17420.25, @cat_ingredientes, '2025-11-11', 'Compra de verduras frescas y frutas de temporada', @usuario);

-- Salarios (pago quincenal)
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'egreso', 'Pago de nómina - Primera quincena', 45000.00, @cat_salarios, '2025-11-01', 'Pago de salarios primera quincena de noviembre', @usuario),
    (@comedor1, 'egreso', 'Pago de nómina - Segunda quincena', 45000.00, @cat_salarios, '2025-11-15', 'Pago de salarios segunda quincena de noviembre', @usuario),
    (@comedor2, 'egreso', 'Pago de nómina - Primera quincena', 38000.00, @cat_salarios, '2025-11-01', 'Pago de salarios primera quincena de noviembre', @usuario),
    (@comedor2, 'egreso', 'Pago de nómina - Segunda quincena', 38000.00, @cat_salarios, '2025-11-15', 'Pago de salarios segunda quincena de noviembre', @usuario),
    (@comedor3, 'egreso', 'Pago de nómina - Primera quincena', 42000.00, @cat_salarios, '2025-11-01', 'Pago de salarios primera quincena de noviembre', @usuario),
    (@comedor3, 'egreso', 'Pago de nómina - Segunda quincena', 42000.00, @cat_salarios, '2025-11-15', 'Pago de salarios segunda quincena de noviembre', @usuario);

-- Servicios públicos
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'egreso', 'Pago de electricidad - Octubre', 8450.50, @cat_servicios, '2025-11-03', 'Consumo de energía eléctrica del mes de octubre', @usuario),
    (@comedor1, 'egreso', 'Pago de agua - Octubre', 3280.00, @cat_servicios, '2025-11-05', 'Consumo de agua potable del mes de octubre', @usuario),
    (@comedor1, 'egreso', 'Pago de gas - Octubre', 5670.25, @cat_servicios, '2025-11-07', 'Consumo de gas LP del mes de octubre', @usuario),
    (@comedor2, 'egreso', 'Pago de electricidad - Octubre', 7120.75, @cat_servicios, '2025-11-03', 'Consumo de energía eléctrica del mes de octubre', @usuario),
    (@comedor2, 'egreso', 'Pago de agua - Octubre', 2850.00, @cat_servicios, '2025-11-05', 'Consumo de agua potable del mes de octubre', @usuario),
    (@comedor2, 'egreso', 'Pago de gas - Octubre', 4890.50, @cat_servicios, '2025-11-07', 'Consumo de gas LP del mes de octubre', @usuario),
    (@comedor3, 'egreso', 'Pago de electricidad - Octubre', 7890.25, @cat_servicios, '2025-11-03', 'Consumo de energía eléctrica del mes de octubre', @usuario),
    (@comedor3, 'egreso', 'Pago de agua - Octubre', 3120.00, @cat_servicios, '2025-11-05', 'Consumo de agua potable del mes de octubre', @usuario),
    (@comedor3, 'egreso', 'Pago de gas - Octubre', 5340.75, @cat_servicios, '2025-11-07', 'Consumo de gas LP del mes de octubre', @usuario);

-- Mantenimiento
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'egreso', 'Reparación de estufa industrial', 4500.00, @cat_mantenimiento, '2025-11-03', 'Reparación de quemadores y válvulas de estufa', @usuario),
    (@comedor1, 'egreso', 'Mantenimiento de refrigeradores', 2800.00, @cat_mantenimiento, '2025-11-10', 'Limpieza y mantenimiento preventivo de equipos de refrigeración', @usuario),
    (@comedor2, 'egreso', 'Pintura de comedor', 8500.00, @cat_mantenimiento, '2025-11-05', 'Pintura interior del área de comedor', @usuario),
    (@comedor3, 'egreso', 'Reparación de plomería', 3200.00, @cat_mantenimiento, '2025-11-08', 'Reparación de fugas en área de cocina', @usuario);

-- Equipo y utensilios
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'egreso', 'Compra de ollas industriales', 6800.00, @cat_equipo, '2025-11-04', 'Compra de 3 ollas industriales de 40 litros', @usuario),
    (@comedor2, 'egreso', 'Compra de platos y cubiertos', 3450.00, @cat_equipo, '2025-11-06', 'Compra de vajilla para 100 personas', @usuario),
    (@comedor3, 'egreso', 'Compra de mesas y sillas', 12500.00, @cat_equipo, '2025-11-09', 'Compra de 5 mesas y 20 sillas para área de comedor', @usuario);

-- Transporte
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'egreso', 'Combustible - Noviembre', 2450.00, @cat_transporte, '2025-11-10', 'Combustible para vehículo de abastecimiento', @usuario),
    (@comedor2, 'egreso', 'Combustible - Noviembre', 1980.00, @cat_transporte, '2025-11-10', 'Combustible para vehículo de abastecimiento', @usuario),
    (@comedor3, 'egreso', 'Combustible - Noviembre', 2150.00, @cat_transporte, '2025-11-10', 'Combustible para vehículo de abastecimiento', @usuario);

-- Otros gastos
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'egreso', 'Productos de limpieza', 1850.00, @cat_otros_egr, '2025-11-07', 'Compra de detergentes, cloro, desinfectantes', @usuario),
    (@comedor1, 'egreso', 'Material de oficina', 680.00, @cat_otros_egr, '2025-11-12', 'Compra de papelería y material de oficina', @usuario),
    (@comedor2, 'egreso', 'Productos de limpieza', 1540.00, @cat_otros_egr, '2025-11-07', 'Compra de detergentes, cloro, desinfectantes', @usuario),
    (@comedor3, 'egreso', 'Productos de limpieza', 1690.00, @cat_otros_egr, '2025-11-07', 'Compra de detergentes, cloro, desinfectantes', @usuario),
    (@comedor3, 'egreso', 'Material de oficina', 520.00, @cat_otros_egr, '2025-11-12', 'Compra de papelería y material de oficina', @usuario);

-- ====================================================================
-- TRANSACCIONES DEL MES ANTERIOR (OCTUBRE 2025)
-- ====================================================================

-- Ingresos de Octubre
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'ingreso', 'Subsidio Gubernamental - Octubre 2025', 150000.00, @cat_subsidio, '2025-10-01', 'Apoyo mensual del gobierno estatal', @usuario),
    (@comedor2, 'ingreso', 'Subsidio Gubernamental - Octubre 2025', 120000.00, @cat_subsidio, '2025-10-01', 'Apoyo mensual del gobierno estatal', @usuario),
    (@comedor3, 'ingreso', 'Subsidio Gubernamental - Octubre 2025', 135000.00, @cat_subsidio, '2025-10-01', 'Apoyo mensual del gobierno estatal', @usuario),
    
    (@comedor1, 'ingreso', 'Venta de comidas - Octubre', 98450.75, @cat_venta, '2025-10-31', 'Ingresos totales por venta de comidas en octubre', @usuario),
    (@comedor2, 'ingreso', 'Venta de comidas - Octubre', 75680.00, @cat_venta, '2025-10-31', 'Ingresos totales por venta de comidas en octubre', @usuario),
    (@comedor3, 'ingreso', 'Venta de comidas - Octubre', 87320.50, @cat_venta, '2025-10-31', 'Ingresos totales por venta de comidas en octubre', @usuario);

-- Egresos de Octubre
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'egreso', 'Compra de ingredientes - Octubre', 125680.50, @cat_ingredientes, '2025-10-31', 'Total de compras de ingredientes en octubre', @usuario),
    (@comedor2, 'egreso', 'Compra de ingredientes - Octubre', 98450.75, @cat_ingredientes, '2025-10-31', 'Total de compras de ingredientes en octubre', @usuario),
    (@comedor3, 'egreso', 'Compra de ingredientes - Octubre', 112340.25, @cat_ingredientes, '2025-10-31', 'Total de compras de ingredientes en octubre', @usuario),
    
    (@comedor1, 'egreso', 'Pago de nómina - Octubre', 90000.00, @cat_salarios, '2025-10-31', 'Pago total de salarios en octubre', @usuario),
    (@comedor2, 'egreso', 'Pago de nómina - Octubre', 76000.00, @cat_salarios, '2025-10-31', 'Pago total de salarios en octubre', @usuario),
    (@comedor3, 'egreso', 'Pago de nómina - Octubre', 84000.00, @cat_salarios, '2025-10-31', 'Pago total de salarios en octubre', @usuario),
    
    (@comedor1, 'egreso', 'Servicios públicos - Octubre', 16890.50, @cat_servicios, '2025-10-31', 'Total de servicios públicos en octubre', @usuario),
    (@comedor2, 'egreso', 'Servicios públicos - Octubre', 14320.75, @cat_servicios, '2025-10-31', 'Total de servicios públicos en octubre', @usuario),
    (@comedor3, 'egreso', 'Servicios públicos - Octubre', 15680.25, @cat_servicios, '2025-10-31', 'Total de servicios públicos en octubre', @usuario);

-- ====================================================================
-- TRANSACCIONES ANTERIORES (SEPTIEMBRE - JUNIO 2025)
-- ====================================================================

-- Septiembre 2025
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'ingreso', 'Subsidio Gubernamental - Septiembre', 150000.00, @cat_subsidio, '2025-09-01', 'Apoyo mensual del gobierno estatal', @usuario),
    (@comedor1, 'ingreso', 'Venta de comidas - Septiembre', 95320.50, @cat_venta, '2025-09-30', 'Ingresos totales del mes', @usuario),
    (@comedor1, 'egreso', 'Compra de ingredientes - Septiembre', 120450.75, @cat_ingredientes, '2025-09-30', 'Total de compras del mes', @usuario),
    (@comedor1, 'egreso', 'Pago de nómina - Septiembre', 90000.00, @cat_salarios, '2025-09-30', 'Pago total de salarios', @usuario),
    (@comedor1, 'egreso', 'Servicios públicos - Septiembre', 17250.00, @cat_servicios, '2025-09-30', 'Total de servicios', @usuario),
    
    (@comedor2, 'ingreso', 'Subsidio Gubernamental - Septiembre', 120000.00, @cat_subsidio, '2025-09-01', 'Apoyo mensual del gobierno estatal', @usuario),
    (@comedor2, 'ingreso', 'Venta de comidas - Septiembre', 72890.25, @cat_venta, '2025-09-30', 'Ingresos totales del mes', @usuario),
    (@comedor2, 'egreso', 'Compra de ingredientes - Septiembre', 95680.00, @cat_ingredientes, '2025-09-30', 'Total de compras del mes', @usuario),
    (@comedor2, 'egreso', 'Pago de nómina - Septiembre', 76000.00, @cat_salarios, '2025-09-30', 'Pago total de salarios', @usuario),
    (@comedor2, 'egreso', 'Servicios públicos - Septiembre', 13890.50, @cat_servicios, '2025-09-30', 'Total de servicios', @usuario),
    
    (@comedor3, 'ingreso', 'Subsidio Gubernamental - Septiembre', 135000.00, @cat_subsidio, '2025-09-01', 'Apoyo mensual del gobierno estatal', @usuario),
    (@comedor3, 'ingreso', 'Venta de comidas - Septiembre', 84560.75, @cat_venta, '2025-09-30', 'Ingresos totales del mes', @usuario),
    (@comedor3, 'egreso', 'Compra de ingredientes - Septiembre', 108920.50, @cat_ingredientes, '2025-09-30', 'Total de compras del mes', @usuario),
    (@comedor3, 'egreso', 'Pago de nómina - Septiembre', 84000.00, @cat_salarios, '2025-09-30', 'Pago total de salarios', @usuario),
    (@comedor3, 'egreso', 'Servicios públicos - Septiembre', 15120.25, @cat_servicios, '2025-09-30', 'Total de servicios', @usuario);

-- Agosto 2025
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'ingreso', 'Subsidio Gubernamental - Agosto', 150000.00, @cat_subsidio, '2025-08-01', 'Apoyo mensual', @usuario),
    (@comedor1, 'ingreso', 'Venta de comidas - Agosto', 102450.75, @cat_venta, '2025-08-31', 'Ingresos totales', @usuario),
    (@comedor1, 'egreso', 'Compra de ingredientes - Agosto', 128900.00, @cat_ingredientes, '2025-08-31', 'Total de compras', @usuario),
    (@comedor1, 'egreso', 'Pago de nómina - Agosto', 90000.00, @cat_salarios, '2025-08-31', 'Pago de salarios', @usuario),
    
    (@comedor2, 'ingreso', 'Subsidio Gubernamental - Agosto', 120000.00, @cat_subsidio, '2025-08-01', 'Apoyo mensual', @usuario),
    (@comedor2, 'ingreso', 'Venta de comidas - Agosto', 79320.50, @cat_venta, '2025-08-31', 'Ingresos totales', @usuario),
    (@comedor2, 'egreso', 'Compra de ingredientes - Agosto', 102340.75, @cat_ingredientes, '2025-08-31', 'Total de compras', @usuario),
    (@comedor2, 'egreso', 'Pago de nómina - Agosto', 76000.00, @cat_salarios, '2025-08-31', 'Pago de salarios', @usuario),
    
    (@comedor3, 'ingreso', 'Subsidio Gubernamental - Agosto', 135000.00, @cat_subsidio, '2025-08-01', 'Apoyo mensual', @usuario),
    (@comedor3, 'ingreso', 'Venta de comidas - Agosto', 91780.25, @cat_venta, '2025-08-31', 'Ingresos totales', @usuario),
    (@comedor3, 'egreso', 'Compra de ingredientes - Agosto', 115670.00, @cat_ingredientes, '2025-08-31', 'Total de compras', @usuario),
    (@comedor3, 'egreso', 'Pago de nómina - Agosto', 84000.00, @cat_salarios, '2025-08-31', 'Pago de salarios', @usuario);

-- Julio 2025
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'ingreso', 'Subsidio Gubernamental - Julio', 150000.00, @cat_subsidio, '2025-07-01', 'Apoyo mensual', @usuario),
    (@comedor1, 'ingreso', 'Venta de comidas - Julio', 97680.00, @cat_venta, '2025-07-31', 'Ingresos totales', @usuario),
    (@comedor1, 'egreso', 'Compra de ingredientes - Julio', 122890.50, @cat_ingredientes, '2025-07-31', 'Total de compras', @usuario),
    (@comedor1, 'egreso', 'Pago de nómina - Julio', 90000.00, @cat_salarios, '2025-07-31', 'Pago de salarios', @usuario),
    
    (@comedor2, 'ingreso', 'Subsidio Gubernamental - Julio', 120000.00, @cat_subsidio, '2025-07-01', 'Apoyo mensual', @usuario),
    (@comedor2, 'ingreso', 'Venta de comidas - Julio', 76540.25, @cat_venta, '2025-07-31', 'Ingresos totales', @usuario),
    (@comedor2, 'egreso', 'Compra de ingredientes - Julio', 97450.75, @cat_ingredientes, '2025-07-31', 'Total de compras', @usuario),
    (@comedor2, 'egreso', 'Pago de nómina - Julio', 76000.00, @cat_salarios, '2025-07-31', 'Pago de salarios', @usuario),
    
    (@comedor3, 'ingreso', 'Subsidio Gubernamental - Julio', 135000.00, @cat_subsidio, '2025-07-01', 'Apoyo mensual', @usuario),
    (@comedor3, 'ingreso', 'Venta de comidas - Julio', 88920.50, @cat_venta, '2025-07-31', 'Ingresos totales', @usuario),
    (@comedor3, 'egreso', 'Compra de ingredientes - Julio', 110340.00, @cat_ingredientes, '2025-07-31', 'Total de compras', @usuario),
    (@comedor3, 'egreso', 'Pago de nómina - Julio', 84000.00, @cat_salarios, '2025-07-31', 'Pago de salarios', @usuario);

-- Junio 2025
INSERT INTO transacciones_financieras (comedor_id, tipo, concepto, monto, categoria_id, fecha_transaccion, descripcion, creado_por)
VALUES 
    (@comedor1, 'ingreso', 'Subsidio Gubernamental - Junio', 150000.00, @cat_subsidio, '2025-06-01', 'Apoyo mensual', @usuario),
    (@comedor1, 'ingreso', 'Venta de comidas - Junio', 94230.75, @cat_venta, '2025-06-30', 'Ingresos totales', @usuario),
    (@comedor1, 'egreso', 'Compra de ingredientes - Junio', 118560.00, @cat_ingredientes, '2025-06-30', 'Total de compras', @usuario),
    (@comedor1, 'egreso', 'Pago de nómina - Junio', 90000.00, @cat_salarios, '2025-06-30', 'Pago de salarios', @usuario),
    (@comedor1, 'egreso', 'Servicios públicos - Junio', 18450.50, @cat_servicios, '2025-06-30', 'Total de servicios', @usuario),
    
    (@comedor2, 'ingreso', 'Subsidio Gubernamental - Junio', 120000.00, @cat_subsidio, '2025-06-01', 'Apoyo mensual', @usuario),
    (@comedor2, 'ingreso', 'Venta de comidas - Junio', 73890.50, @cat_venta, '2025-06-30', 'Ingresos totales', @usuario),
    (@comedor2, 'egreso', 'Compra de ingredientes - Junio', 93670.25, @cat_ingredientes, '2025-06-30', 'Total de compras', @usuario),
    (@comedor2, 'egreso', 'Pago de nómina - Junio', 76000.00, @cat_salarios, '2025-06-30', 'Pago de salarios', @usuario),
    (@comedor2, 'egreso', 'Servicios públicos - Junio', 14680.00, @cat_servicios, '2025-06-30', 'Total de servicios', @usuario),
    
    (@comedor3, 'ingreso', 'Subsidio Gubernamental - Junio', 135000.00, @cat_subsidio, '2025-06-01', 'Apoyo mensual', @usuario),
    (@comedor3, 'ingreso', 'Venta de comidas - Junio', 85670.25, @cat_venta, '2025-06-30', 'Ingresos totales', @usuario),
    (@comedor3, 'egreso', 'Compra de ingredientes - Junio', 106890.75, @cat_ingredientes, '2025-06-30', 'Total de compras', @usuario),
    (@comedor3, 'egreso', 'Pago de nómina - Junio', 84000.00, @cat_salarios, '2025-06-30', 'Pago de salarios', @usuario),
    (@comedor3, 'egreso', 'Servicios públicos - Junio', 16230.50, @cat_servicios, '2025-06-30', 'Total de servicios', @usuario);

-- ====================================================================
-- Actualizar presupuestos con los gastos registrados
-- ====================================================================

-- Actualizar presupuestos de noviembre basado en egresos
UPDATE presupuestos p
JOIN (
    SELECT comedor_id, SUM(monto) as total_gastado
    FROM transacciones_financieras
    WHERE tipo = 'egreso' 
    AND YEAR(fecha_transaccion) = 2025 
    AND MONTH(fecha_transaccion) = 11
    GROUP BY comedor_id
) t ON p.comedor_id = t.comedor_id
SET p.presupuesto_gastado = t.total_gastado,
    p.porcentaje_ejecutado = (t.total_gastado / p.presupuesto_asignado) * 100,
    p.estado = CASE 
        WHEN (t.total_gastado / p.presupuesto_asignado) * 100 > 100 THEN 'excedido'
        WHEN (t.total_gastado / p.presupuesto_asignado) * 100 >= 95 THEN 'cerrado'
        ELSE 'activo'
    END
WHERE p.anio = 2025 AND p.mes = 11;

-- ====================================================================
-- Mensaje de confirmación
-- ====================================================================
SELECT 'Datos de transacciones financieras insertados correctamente.' AS resultado,
       COUNT(*) as total_transacciones
FROM transacciones_financieras;

SELECT 'Resumen por tipo de transacción:' AS info;
SELECT 
    tipo,
    COUNT(*) as cantidad,
    SUM(monto) as total_monto
FROM transacciones_financieras
GROUP BY tipo;
