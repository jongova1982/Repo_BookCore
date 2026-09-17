<?php
// Configuración de la base de datos para AlwaysData.
// REEMPLAZA los valores entre << >> por las credenciales reales de tu cuenta.

const DB_HOST = 'mysql-<<TU_SERVIDOR>>.alwaysdata.net';
const DB_NAME = '<<TU_BASE_DE_DATOS>>';
const DB_USER = '<<TU_USUARIO>>';
const DB_PASS = '<<TU_CLAVE>>';
const DB_CHARSET = 'utf8mb4';

// Zona horaria de Colombia.
date_default_timezone_set('America/Bogota');

// Cuenta administrativa inicial. Cambia estos datos antes de publicar.
const ADMIN_EMAIL = 'admin@biblioteca.local';
const ADMIN_PASSWORD = 'Admin123*';
