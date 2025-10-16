<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * Conecta ao banco de dados MySQL.
 * @param string $host 
 * @param string $user 
 * @param string $pass 
 * @param string $db 
 * @return bool|mysqli 
 */
function bd_Conexao(string $host, string $user, string $pass, string $db): bool|mysqli {
    
    $con = mysqli_connect($host, $user, $pass, $db);
    
    if (!$con) {
        return false;
    }
   
    mysqli_set_charset($con, 'utf8mb4');
    
    $_SESSION['conexao'] = $con;
    
    return $con;
}

/**
 * Executa uma query SQL no banco de dados.
 * @param mysqli $con 
 * @param string $sql 
 * @return bool|mysqli_result 
 */
function bd_query(string $sql, mysqli $con): bool|mysqli_result {
    $res = mysqli_query(mysql: $con, query: $sql);
    return $res;
}

/**
 * @param mysqli $con 
 * @param string $sql 
 * @return array 
 */
function bd_sql_to_array_assoc(mysqli $con, string $sql): array {
    $res = bd_query($sql, $con); 
    $arr = [];
    
    if ($res) {
        while ($row = mysqli_fetch_assoc(result: $res)) {
            $arr[] = $row;
        }
    }
    return $arr;
}


function abrir(): void { 
    mysqli_begin_transaction(mysql: $_SESSION['conexao']); 
}

function commit(): void { 
    mysqli_commit(mysql: $_SESSION['conexao']); 
}

function rollback(): void { 
    mysqli_rollback(mysql: $_SESSION['conexao']); 
}