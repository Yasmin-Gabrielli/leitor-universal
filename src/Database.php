<?php
// /src/Database.php

require_once __DIR__ . '/../config/config.php';

class Database {
    private static $instance = null;

    // Construtor privado para evitar que a classe seja instanciada com "new"
    private function __construct() {}

    public static function getConnection() {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Mostra erros detalhados (essencial para debug)
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna os dados como array associativo puro
                    PDO::ATTR_EMULATE_PREPARES   => false,                  // Força o MySQL a preparar as queries (mais segurança contra SQL Injection)
                ];

                self::$instance = new PDO($dsn, DB_USER, DB_PASS, options: $options);
            } catch (PDOException $e) {
                // Em produção, você salvaria isso em um log e não mostraria na tela
                die("Erro fatal de conexão com o banco: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}