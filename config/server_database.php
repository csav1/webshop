<?php

return [
    'host' => 'db.htl-md.dev', // oder 'localhost', falls db.htl-md.dev nicht geht
    'dbname' => 'lab03',       // Datenbankname entspricht meist der Lab-Nummer
    'user' => 'lab03',         // Benutzername
    'password' => 'RbcHkGHsjQNJwEe', // Dein Passwort
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
