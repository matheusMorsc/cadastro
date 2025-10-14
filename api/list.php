<?php
header(header: 'Content-Type: application/json; charset=utf-8');
require_once dirname(path: __DIR__) . '/funcoes_db.php';
require_once dirname(path: __DIR__) . '/config.php';


$con = bd_Conexao(database_server: DB_HOST, database_username: DB_USER, database_password: DB_PASS, database_name: DB_NAME);

$sql = "SELECT id, nome, email, telefone, foto_perfil, nome_documento, created_at
        FROM pessoas
        ORDER BY id DESC";

$dados = bd_sql_to_array_assoc(conexao: $con, sql: $sql);

echo json_encode(value: ['error' => false, 'data' => $dados]);