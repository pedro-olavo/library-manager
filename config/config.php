<?php

/**
 * Configurações gerais da aplicação.
 * Os valores são lidos de variáveis de ambiente (definidas no docker-compose.yml
 * ou em um arquivo .env local), com valores padrão para desenvolvimento.
 */
return [
    'db' => [
        'host'     => getenv('DB_HOST') ?: 'db',
        'port'     => getenv('DB_PORT') ?: '5432',
        'database' => getenv('DB_DATABASE') ?: 'biblioteca',
        'username' => getenv('DB_USERNAME') ?: 'biblioteca_user',
        'password' => getenv('DB_PASSWORD') ?: 'biblioteca_pass',
    ],
];
