<?php 
header(header: 'Content-Type: application/json; charset=utf-8');
require_once dirname(path: __DIR__) . '/funcoes_db.php';
require_once dirname(path: __DIR__) . '/config.php';

$con = bd_Conexao(host: DB_HOST, user: DB_USER, pass: DB_PASS, db: DB_NAME);

$sql = "SELECT id, nome, email, telefone, foto_perfil, nome_documento, created_at
        FROM pessoas
        ORDER BY id DESC";

$dados = bd_sql_to_array_assoc(con: $con, sql: $sql);

echo json_encode(value: ['error' => false, 'data' => $dados]);
