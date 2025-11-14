<?php
/**
 * Front Controller - Entry point for all requests
 * Sistema de Gestión para Comedores Industriales
 */

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Load core classes
require_once APP_PATH . '/Router.php';
require_once APP_PATH . '/Controller.php';

// Initialize router
$router = new Router();

// ========================================
// AUTHENTICATION ROUTES
// ========================================
$router->get('/', 'AuthController', 'index');
$router->get('/login', 'AuthController', 'login');
$router->post('/login', 'AuthController', 'doLogin');
$router->get('/logout', 'AuthController', 'logout');

// ========================================
// DASHBOARD ROUTES
// ========================================
$router->get('/dashboard', 'DashboardController', 'index');
$router->get('/dashboard/stats', 'DashboardController', 'stats');

// ========================================
// ATTENDANCE ROUTES (REQ-ASIST-001, REQ-DATA-001)
// ========================================
$router->get('/attendance', 'AttendanceController', 'index');
$router->get('/attendance/history', 'AttendanceController', 'history');
$router->get('/attendance/record', 'AttendanceController', 'recordForm');
$router->post('/attendance/record', 'AttendanceController', 'saveRecord');
$router->get('/attendance/projections', 'AttendanceController', 'projections');
$router->post('/attendance/calculate-projection', 'AttendanceController', 'calculateProjection');

// ========================================
// ATYPICAL SITUATIONS ROUTES (REQ-AJUSTES-001)
// ========================================
$router->get('/situations', 'SituationsController', 'index');
$router->get('/situations/create', 'SituationsController', 'create');
$router->post('/situations/create', 'SituationsController', 'store');
$router->get('/situations/edit/:id', 'SituationsController', 'edit');
$router->post('/situations/update/:id', 'SituationsController', 'update');
$router->post('/situations/delete/:id', 'SituationsController', 'delete');

// ========================================
// PRODUCTION ORDERS ROUTES (REQ-PRODUCCION-001)
// ========================================
$router->get('/production', 'ProductionController', 'index');
$router->get('/production/create', 'ProductionController', 'create');
$router->post('/production/create', 'ProductionController', 'store');
$router->get('/production/view/:id', 'ProductionController', 'view');
$router->get('/production/edit/:id', 'ProductionController', 'edit');
$router->post('/production/update/:id', 'ProductionController', 'update');
$router->post('/production/calculate-ingredients', 'ProductionController', 'calculateIngredients');
$router->get('/production/print/:id', 'ProductionController', 'print');

// ========================================
// RECIPES ROUTES
// ========================================
$router->get('/recipes', 'RecipesController', 'index');
$router->get('/recipes/create', 'RecipesController', 'create');
$router->post('/recipes/create', 'RecipesController', 'store');
$router->get('/recipes/view/:id', 'RecipesController', 'view');
$router->get('/recipes/edit/:id', 'RecipesController', 'edit');
$router->post('/recipes/update/:id', 'RecipesController', 'update');

// ========================================
// REPORTS ROUTES (REQ-REPORTES-001)
// ========================================
$router->get('/reports', 'ReportsController', 'index');
$router->get('/reports/attendance', 'ReportsController', 'attendance');
$router->get('/reports/deviation', 'ReportsController', 'deviation');
$router->get('/reports/production', 'ReportsController', 'production');
$router->get('/reports/costs', 'ReportsController', 'costs');
$router->post('/reports/generate', 'ReportsController', 'generate');
$router->get('/reports/export/:type', 'ReportsController', 'export');

// ========================================
// SETTINGS ROUTES (REQ-CONFIG-001)
// ========================================
$router->get('/settings', 'SettingsController', 'index');
$router->post('/settings/update', 'SettingsController', 'update');
$router->get('/settings/users', 'SettingsController', 'users');
$router->get('/settings/comedores', 'SettingsController', 'comedores');
$router->get('/settings/ingredients', 'SettingsController', 'ingredients');

// ========================================
// API ROUTES (REQ-API-001)
// ========================================
$router->post('/api/attendance/sync', 'ApiController', 'syncAttendance');
$router->post('/api/projections/get', 'ApiController', 'getProjections');
$router->post('/api/production/export', 'ApiController', 'exportProduction');
$router->get('/api/employees/sync', 'ApiController', 'syncEmployees');

// Dispatch the request
$router->dispatch();
