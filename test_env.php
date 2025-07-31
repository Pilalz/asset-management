<?php
require __DIR__.'/vendor/autoload.php';

// Load environment variables from .env file
if (file_exists(__DIR__.'/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

echo "DB_HOST: " . env('DB_HOST') . "\n";
echo "DB_PORT: " . env('DB_PORT') . "\n";
echo "DB_DATABASE: " . env('DB_DATABASE') . "\n";
echo "DB_USERNAME: " . env('DB_USERNAME') . "\n";
echo "DB_PASSWORD (length " . strlen(env('DB_PASSWORD')) . "): '" . env('DB_PASSWORD') . "'\n";
echo "DB_ENCRYPT: " . env('DB_ENCRYPT') . "\n";
echo "DB_TRUST_SERVER_CERTIFICATE: " . env('DB_TRUST_SERVER_CERTIFICATE') . "\n";
?>