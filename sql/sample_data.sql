-- Sample Data for Sistema de Gestión para Comedores Industriales
-- Estado: Querétaro, México

USE comedores_industriales;

-- Insert default admin user (password: admin123)
INSERT INTO usuarios (username, email, password, nombre_completo, rol, activo) VALUES
('admin', 'admin@comedores.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador del Sistema', 'admin', 1),
('coord_mat', 'coord.matutino@comedores.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María García López', 'coordinador', 1),
('coord_vesp', 'coord.vespertino@comedores.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan Pérez Martínez', 'coordinador', 1),
('chef_principal', 'chef@comedores.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos Rodríguez Sánchez', 'chef', 1),
('operativo1', 'operativo1@comedores.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana López Torres', 'operativo', 1);

-- Insert dining halls in Querétaro
INSERT INTO comedores (nombre, ubicacion, ciudad, estado, capacidad_total, turnos_activos, activo) VALUES
('Comedor Central Querétaro', 'Parque Industrial Bernardo Quintana', 'Querétaro', 'Querétaro', 500, 'matutino,vespertino,nocturno', 1),
('Comedor Parque Aeroespacial', 'Parque Aeroespacial de Querétaro', 'Querétaro', 'Querétaro', 350, 'matutino,vespertino', 1),
('Comedor El Marqués', 'Zona Industrial El Marqués', 'El Marqués', 'Querétaro', 400, 'matutino,vespertino,nocturno', 1),
('Comedor San Juan del Río', 'Parque Industrial San Juan del Río', 'San Juan del Río', 'Querétaro', 300, 'matutino,vespertino', 1);

-- Insert shifts
INSERT INTO turnos (nombre, hora_inicio, hora_fin, descripcion, activo) VALUES
('Matutino', '06:00:00', '14:00:00', 'Turno de la mañana - Desayuno y Comida', 1),
('Vespertino', '14:00:00', '22:00:00', 'Turno de la tarde - Comida y Cena', 1),
('Nocturno', '22:00:00', '06:00:00', 'Turno nocturno - Cena y Desayuno', 1);

-- Insert service lines
INSERT INTO lineas_servicio (nombre, descripcion, orden_visualizacion, activo) VALUES
('Línea Caliente', 'Platillos calientes principales', 1, 1),
('Línea Fría', 'Ensaladas, guarniciones frías y postres', 2, 1),
('Línea Grill', 'Carnes y pescados a la parrilla', 3, 1),
('Línea Vegetariana', 'Opciones vegetarianas y veganas', 4, 1);

-- Insert sample ingredients
INSERT INTO ingredientes (nombre, unidad_medida, costo_unitario, proveedor, activo) VALUES
('Pechuga de Pollo', 'kg', 85.00, 'Carnes y Aves del Bajío', 1),
('Crema para batir', 'l', 65.00, 'Lácteos Querétaro', 1),
('Chile Chipotle', 'kg', 120.00, 'Especias del Centro', 1),
('Cebolla blanca', 'kg', 18.00, 'Verduras Frescas QRO', 1),
('Ajo', 'kg', 45.00, 'Verduras Frescas QRO', 1),
('Arroz blanco', 'kg', 22.00, 'Abarrotes Mayoristas', 1),
('Frijoles negros', 'kg', 28.00, 'Abarrotes Mayoristas', 1),
('Jitomate', 'kg', 20.00, 'Verduras Frescas QRO', 1),
('Lechuga romana', 'kg', 25.00, 'Verduras Frescas QRO', 1),
('Aguacate', 'kg', 75.00, 'Frutas y Verduras del Bajío', 1),
('Tortilla de maíz', 'kg', 18.00, 'Tortillería La Queretana', 1),
('Aceite vegetal', 'l', 35.00, 'Abarrotes Mayoristas', 1),
('Sal', 'kg', 8.00, 'Abarrotes Mayoristas', 1),
('Pimienta negra', 'kg', 150.00, 'Especias del Centro', 1),
('Bistec de res', 'kg', 145.00, 'Carnes y Aves del Bajío', 1),
('Pescado tilapia', 'kg', 95.00, 'Pescados y Mariscos QRO', 1),
('Papa blanca', 'kg', 15.00, 'Verduras Frescas QRO', 1),
('Zanahoria', 'kg', 16.00, 'Verduras Frescas QRO', 1),
('Calabaza', 'kg', 18.00, 'Verduras Frescas QRO', 1),
('Elote', 'pzas', 8.00, 'Verduras Frescas QRO', 1);

-- Insert sample recipes
INSERT INTO recetas (nombre, linea_servicio_id, descripcion, porciones_base, tiempo_preparacion, activo) VALUES
('Pollo en Crema de Chipotle', 1, 'Pechuga de pollo en salsa cremosa de chipotle', 100, 45, 1),
('Bistec a la Mexicana', 3, 'Bistec de res con jitomate, cebolla y chile', 100, 30, 1),
('Pescado a la Plancha', 3, 'Filete de tilapia sazonado a la plancha', 100, 25, 1),
('Ensalada Fresca Mixta', 2, 'Lechuga, jitomate, zanahoria y aguacate', 100, 15, 1),
('Arroz Blanco', 2, 'Arroz blanco cocido tradicional', 100, 30, 1),
('Frijoles de la Olla', 2, 'Frijoles negros cocidos', 100, 90, 1);

-- Insert recipe ingredients (gramajes)
-- Pollo en Crema de Chipotle (para 100 porciones)
INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad, notas) VALUES
(1, 1, 12.000, 'kg', 'Pechuga sin hueso'), -- Pollo
(1, 2, 1.500, 'l', 'Crema para batir'), -- Crema
(1, 3, 0.200, 'kg', 'Chiles chipotles adobados'), -- Chipotle
(1, 4, 1.000, 'kg', 'Picada finamente'), -- Cebolla
(1, 5, 0.100, 'kg', 'Ajo picado'), -- Ajo
(1, 12, 0.200, 'l', 'Para sofreír'), -- Aceite
(1, 13, 0.050, 'kg', 'Al gusto'), -- Sal
(1, 14, 0.020, 'kg', 'Molida'); -- Pimienta

-- Bistec a la Mexicana (para 100 porciones)
INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad, notas) VALUES
(2, 15, 15.000, 'kg', 'Bistec en trozos'), -- Bistec
(2, 8, 2.000, 'kg', 'En cubos'), -- Jitomate
(2, 4, 1.500, 'kg', 'Picada'), -- Cebolla
(2, 5, 0.100, 'kg', 'Picado'), -- Ajo
(2, 12, 0.300, 'l', 'Para cocinar'), -- Aceite
(2, 13, 0.080, 'kg', 'Al gusto'); -- Sal

-- Pescado a la Plancha (para 100 porciones)
INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad, notas) VALUES
(3, 16, 15.000, 'kg', 'Filetes'), -- Pescado
(3, 5, 0.100, 'kg', 'Molido'), -- Ajo
(3, 12, 0.200, 'l', 'Para plancha'), -- Aceite
(3, 13, 0.060, 'kg', 'Al gusto'), -- Sal
(3, 14, 0.030, 'kg', 'Al gusto'); -- Pimienta

-- Ensalada Fresca Mixta (para 100 porciones)
INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad, notas) VALUES
(4, 9, 5.000, 'kg', 'Desinfectada y cortada'), -- Lechuga
(4, 8, 3.000, 'kg', 'En cubos'), -- Jitomate
(4, 18, 2.000, 'kg', 'Rallada'), -- Zanahoria
(4, 10, 2.000, 'kg', 'En cubos'); -- Aguacate

-- Arroz Blanco (para 100 porciones)
INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad, notas) VALUES
(5, 6, 8.000, 'kg', 'Arroz grano largo'), -- Arroz
(5, 4, 0.500, 'kg', 'Picada'), -- Cebolla
(5, 5, 0.050, 'kg', 'Picado'), -- Ajo
(5, 12, 0.400, 'l', 'Para sofreír'), -- Aceite
(5, 13, 0.100, 'kg', 'Al gusto'); -- Sal

-- Frijoles de la Olla (para 100 porciones)
INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad, notas) VALUES
(6, 7, 10.000, 'kg', 'Frijoles negros'), -- Frijoles
(6, 4, 0.500, 'kg', 'Entera'), -- Cebolla
(6, 5, 0.100, 'kg', 'Entero'), -- Ajo
(6, 13, 0.150, 'kg', 'Al gusto'); -- Sal

-- Insert historical attendance data (last 30 days)
INSERT INTO asistencia_diaria (comedor_id, turno_id, fecha, comensales_proyectados, comensales_reales, porcentaje_asistencia, registrado_por) VALUES
-- Week 1
(1, 1, DATE_SUB(CURDATE(), INTERVAL 30 DAY), 180, 175, 97.22, 2),
(1, 2, DATE_SUB(CURDATE(), INTERVAL 30 DAY), 150, 148, 98.67, 3),
(1, 3, DATE_SUB(CURDATE(), INTERVAL 30 DAY), 100, 95, 95.00, 2),
(1, 1, DATE_SUB(CURDATE(), INTERVAL 29 DAY), 180, 182, 101.11, 2),
(1, 2, DATE_SUB(CURDATE(), INTERVAL 29 DAY), 150, 145, 96.67, 3),
(1, 3, DATE_SUB(CURDATE(), INTERVAL 29 DAY), 100, 98, 98.00, 2),
-- Week 2
(1, 1, DATE_SUB(CURDATE(), INTERVAL 23 DAY), 180, 178, 98.89, 2),
(1, 2, DATE_SUB(CURDATE(), INTERVAL 23 DAY), 150, 152, 101.33, 3),
(1, 3, DATE_SUB(CURDATE(), INTERVAL 23 DAY), 100, 97, 97.00, 2),
(1, 1, DATE_SUB(CURDATE(), INTERVAL 22 DAY), 180, 176, 97.78, 2),
(1, 2, DATE_SUB(CURDATE(), INTERVAL 22 DAY), 150, 149, 99.33, 3),
(1, 3, DATE_SUB(CURDATE(), INTERVAL 22 DAY), 100, 99, 99.00, 2),
-- Week 3
(1, 1, DATE_SUB(CURDATE(), INTERVAL 16 DAY), 180, 181, 100.56, 2),
(1, 2, DATE_SUB(CURDATE(), INTERVAL 16 DAY), 150, 147, 98.00, 3),
(1, 3, DATE_SUB(CURDATE(), INTERVAL 16 DAY), 100, 96, 96.00, 2),
(1, 1, DATE_SUB(CURDATE(), INTERVAL 15 DAY), 180, 179, 99.44, 2),
(1, 2, DATE_SUB(CURDATE(), INTERVAL 15 DAY), 150, 151, 100.67, 3),
(1, 3, DATE_SUB(CURDATE(), INTERVAL 15 DAY), 100, 98, 98.00, 2),
-- Week 4 (current week)
(1, 1, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 180, 177, 98.33, 2),
(1, 2, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 150, 150, 100.00, 3),
(1, 3, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 100, 97, 97.00, 2),
(1, 1, DATE_SUB(CURDATE(), INTERVAL 6 DAY), 180, 180, 100.00, 2),
(1, 2, DATE_SUB(CURDATE(), INTERVAL 6 DAY), 150, 148, 98.67, 3),
(1, 3, DATE_SUB(CURDATE(), INTERVAL 6 DAY), 100, 99, 99.00, 2);

-- Insert atypical situations
INSERT INTO situaciones_atipicas (comedor_id, tipo, fecha_inicio, fecha_fin, impacto_comensales, descripcion, turnos_afectados, creado_por) VALUES
(1, 'contratacion', DATE_SUB(CURDATE(), INTERVAL 15 DAY), NULL, 25, 'Contratación de nuevo turno de producción', 'matutino', 1),
(1, 'dia_festivo', DATE_ADD(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), -80, 'Día de Asueto - 16 de Septiembre', 'matutino,vespertino,nocturno', 1),
(2, 'evento_especial', DATE_ADD(CURDATE(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 50, 'Capacitación empresarial con comida incluida', 'vespertino', 1);

-- Insert projections for next week
INSERT INTO proyecciones (comedor_id, turno_id, fecha, comensales_proyectados, metodo_calculo, margen_error, creado_por) VALUES
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 185, 'historico', 5.00, 1),
(1, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 152, 'historico', 5.00, 1),
(1, 3, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 98, 'historico', 5.00, 1),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), 185, 'historico', 5.00, 1),
(1, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), 152, 'historico', 5.00, 1),
(1, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), 98, 'historico', 5.00, 1);

-- Insert system configuration
INSERT INTO configuracion_sistema (clave, valor, tipo_dato, descripcion, categoria) VALUES
('margen_error_default', '5.0', 'decimal', 'Margen de error aceptable por defecto (%)', 'proyecciones'),
('alerta_desviacion', '10.0', 'decimal', 'Porcentaje de desviación para activar alertas', 'proyecciones'),
('factor_estacional_vacaciones', '20.0', 'decimal', 'Reducción porcentual en períodos vacacionales', 'proyecciones'),
('dias_historico_calculo', '30', 'integer', 'Días de histórico para cálculo de proyecciones', 'proyecciones'),
('notificaciones_email', 'true', 'boolean', 'Activar notificaciones por email', 'sistema'),
('api_enabled', 'true', 'boolean', 'Habilitar API para integraciones externas', 'api');

-- Sample production order
INSERT INTO ordenes_produccion (numero_orden, comedor_id, turno_id, fecha_servicio, receta_id, comensales_proyectados, porciones_calcular, estado, creado_por) VALUES
('OP-2024-001', 1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 1, 185, 185, 'pendiente', 4),
('OP-2024-002', 1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 5, 185, 185, 'pendiente', 4),
('OP-2024-003', 1, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 2, 152, 152, 'pendiente', 4);

-- Calculate and insert production order ingredients
INSERT INTO orden_ingredientes (orden_produccion_id, ingrediente_id, cantidad_requerida, unidad, costo_estimado)
SELECT 1, ri.ingrediente_id, 
       ROUND((ri.cantidad / r.porciones_base) * op.porciones_calcular, 3),
       ri.unidad,
       ROUND((ri.cantidad / r.porciones_base) * op.porciones_calcular * i.costo_unitario, 2)
FROM ordenes_produccion op
JOIN recetas r ON op.receta_id = r.id
JOIN receta_ingredientes ri ON r.id = ri.receta_id
JOIN ingredientes i ON ri.ingrediente_id = i.id
WHERE op.id = 1;

-- Log sample actions
INSERT INTO logs_sistema (usuario_id, accion, modulo, descripcion, ip_address) VALUES
(1, 'login', 'auth', 'Inicio de sesión exitoso', '192.168.1.100'),
(2, 'crear_proyeccion', 'proyecciones', 'Creación de proyección para mañana', '192.168.1.101'),
(4, 'crear_orden', 'produccion', 'Orden de producción OP-2024-001 creada', '192.168.1.102');
