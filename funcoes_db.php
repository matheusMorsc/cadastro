<?php
if (session_status() === PHP_SESSION_NONE) session_start();

/* Sanitiza recursivamente strings ou arrays */
function limpar($d): mixed {
    if (is_array(value: $d)) return array_map(callback: 'limpar', array: $d);
    return is_string(value: $d) ? htmlspecialchars(string: $d, flags: ENT_QUOTES | ENT_SUBSTITUTE, encoding: 'UTF-8') : $d;
}

/* Conecta ao banco */
function bd_Conexao($host, $user, $pass, $db): bool|mysqli {
    $con = mysqli_connect(hostname: $host, username: $user, password: $pass, database: $db)
        or exit("Erro ao conectar: " . mysqli_connect_error());
    mysqli_set_charset(mysql: $con, charset: "utf8");
    $_SESSION['conexao'] = $con;
    return $con;
}

/* Executa query com opção de debug */
function bd_query($sql, $con, $debug = 0): bool|mysqli_result|string {
    $q = mysqli_query(mysql: $con, query: $sql);
    $err = mysqli_error(mysql: $con);
    if ($debug == 1) echo "<br>[Query=$sql][Erro=$err]";
    if ($debug == 2) return "[Query=$sql][Erro=$err]";
    if ($debug == 3) return "[Erro=$err]";
    return $q;
}

/* Métodos auxiliares de leitura */
function bd_fetch_assoc($r): mixed { return limpar(d: mysqli_fetch_assoc(result: $r)); }
function bd_fetch_array($r): mixed { return limpar(d: mysqli_fetch_array(result: $r)); }
function bd_fetch_row($r): array|bool|null   { return mysqli_fetch_row(result: $r); }
function bd_num_rows($r): int|string    { return mysqli_num_rows(result: $r); }

/* Transações */
function abrir(): void   { mysqli_begin_transaction($_SESSION['conexao']); }
function commit(): void  { mysqli_commit(mysql: $_SESSION['conexao']); }
function rollback(): void{ mysqli_rollback(mysql: $_SESSION['conexao']); }

/* Retorna array de resultados */
function bd_sql_to_array_assoc($con, $sql, $debug = 0): array {
    $res = bd_query(sql: $sql, con: $con, debug: $debug);
    $arr = [];
    while ($row = mysqli_fetch_assoc(result: $res)) $arr[] = limpar(d: $row);
    return $arr;
}

function bd_to_array($res): array {
    $arr = [];
    while ($r = mysqli_fetch_assoc(result: $res)) $arr[] = $r;
    return $arr;
}

function bd_affect_rows(): int|string { return mysqli_affected_rows(mysql: $_SESSION['conexao']); }
