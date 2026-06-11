<?php
$host = '127.0.0.1';
$port = 3306;
$user = 'root';
$pass = '';
$db = 'cargamesta';
$base = __DIR__ . '/../database/';
$files = [
    'ms-auth.sql',
    'ms-conductores.sql',
    'ms-rutas.sql',
    'ms-vehiculos.sql',
    'ms-viajes.sql'
];

function logmsg($m) { echo $m . PHP_EOL; }

$mysqli = @new mysqli($host, $user, $pass, '', $port);
if ($mysqli->connect_errno) {
    logmsg("ERROR: No se pudo conectar a MySQL: ({$mysqli->connect_errno}) {$mysqli->connect_error}");
    exit(1);
}

// create database if not exists
if (!$mysqli->query("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    logmsg("ERROR creating database: " . $mysqli->error);
    exit(1);
}
logmsg("Database ensured: {$db}");

foreach ($files as $f) {
    $path = $base . $f;
    if (!file_exists($path)) {
        logmsg("SKIP: file not found: {$path}");
        continue;
    }

    $sql = file_get_contents($path);
    if ($sql === false) {
        logmsg("ERROR reading file: {$path}");
        continue;
    }

    // switch to target db
    $mysqli->select_db($db);

    // run multi query
    if ($mysqli->multi_query($sql)) {
        do {
            if ($res = $mysqli->store_result()) {
                $res->free();
            }
        } while ($mysqli->more_results() && $mysqli->next_result());

        if ($mysqli->errno) {
            logmsg("ERROR importing {$f}: ({$mysqli->errno}) {$mysqli->error}");
        } else {
            logmsg("OK imported: {$f}");
        }
    } else {
        logmsg("ERROR running sql for {$f}: ({$mysqli->errno}) {$mysqli->error}");
    }
}

$mysqli->close();
logmsg("Done.");
