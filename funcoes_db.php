<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function htmlspecialchars_recursive($data): mixed {
    if (is_array(value: $data)) {
        $out = [];
        foreach ($data as $k => $v) {
            $out[$k] = htmlspecialchars_recursive(data: $v);
        }
        return $out;
    }
    if (is_string(value: $data)) {
        return htmlspecialchars(string: $data, flags: ENT_QUOTES | ENT_SUBSTITUTE, encoding: 'UTF-8');
    }
    return $data;
}

/* Conecta ao banco de dados*/
function bd_Conexao($database_server, $database_username, $database_password, $database_name): bool|mysqli {
    $conexao = mysqli_connect(hostname: $database_server, username: $database_username, password: $database_password, database: $database_name)
        or exit("Erro ao tentar se conectar ao Banco de Dados: " . mysqli_connect_error());

    mysqli_set_charset(mysql: $conexao, charset: "utf8") or exit("Erro ao setar charset.");

    mysqli_select_db(mysql: $conexao, database: $database_name)
        or exit("Erro ao usar o BD: " . mysqli_error(mysql: $conexao));

    $_SESSION['conexao'] = $conexao;
    return $conexao;
}

/* Executa uma query*/
function bd_query($str_sql, $str_conexao, $ver_dados = 0): bool|mysqli_result|string {
    $qry = mysqli_query(mysql: $str_conexao, query: $str_sql);

    if ($ver_dados == 1) {
        $erro = mysqli_error(mysql: $str_conexao);
        echo '<br>[Query = ' . $str_sql . '] [Erro = ' . $erro . ']';
    } else if ($ver_dados == 2) {
        $erro = mysqli_error(mysql: $str_conexao);
        return '[Query = ' . $str_sql . '] [Erro = ' . $erro . ']';
    } else if ($ver_dados == 3) {
        $erro = mysqli_error(mysql: $str_conexao);
        return '[Erro = ' . $erro . ']';
    }
    return $qry;
}

/* Funções de fetch*/
function bd_fetch_row($res): array|bool|null {
    return mysqli_fetch_row(result: $res);
}

function bd_fetch_array($res): mixed {
    return htmlspecialchars_recursive(data: mysqli_fetch_array(result: $res));
}

function bd_fetch_assoc($res): mixed {
    return htmlspecialchars_recursive(data: mysqli_fetch_assoc(result: $res));
}

function bd_fetch_array_assoc($res): array|bool|null {
    return mysqli_fetch_assoc(result: $res);
}

function bd_num_rows($res): int|string {
    return mysqli_num_rows(result: $res);
}

/* Transações*/
function abrir(): void {
    mysqli_begin_transaction(mysql: $_SESSION['conexao'], flags: MYSQLI_TRANS_START_READ_WRITE);
}

function commit(): void {
    mysqli_commit(mysql: $_SESSION['conexao']);
}

function rollback(): void {
    mysqli_rollback(mysql: $_SESSION['conexao']);
}

/* Converte resultado SQL em array associativo*/
function bd_sql_to_array_assoc($conexao, $sql, $mostarDados = 0): array {
    $array = array();

    $query = bd_query(str_sql: $sql, str_conexao: $conexao, ver_dados: $mostarDados);
    if ($query === false) return $array;

    while ($row = mysqli_fetch_assoc($query)) {
        $array[] = htmlspecialchars_recursive($row);
    }

    return $array;
}
/*Converte resultado em array*/
function bd_to_array($resultado): array {
    $array = array();
    if ($resultado === false) return $array;

    while ($row = mysqli_fetch_assoc(result: $resultado)) {
        $array[] = $row;
    }

    return $array;
}
function bd_affect_rows(): int|string {
    return mysqli_affected_rows(mysql: $_SESSION['conexao']);
}
