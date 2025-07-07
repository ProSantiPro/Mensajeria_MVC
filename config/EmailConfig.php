<?php
class EmailConfig {
    const HOST = 'smtp.gmail.com'; // Ej: smtp.gmail.com
    const USERNAME = 'strb2006@gmail.com';
    const PASSWORD = 'strb.2006';
    const SECURE = 'tls'; // o 'ssl'
    const PORT = 587; // Para TLS, 465 para SSL
    const FROM_EMAIL = '2006strb@gmail.com';
    const FROM_NAME = 'Sistema de Mensajería';
    const DEBUG = 2; // Nivel de depuración (0=off, 1=client, 2=client y server)
    const CHARSET = 'UTF-8';

}