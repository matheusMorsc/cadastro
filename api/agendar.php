<?php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/funcoes_db.php';
require_once dirname(__DIR__) . '/config.php';

$con = bd_Conexao(DB_HOST, DB_USER, DB_PASS, DB_NAME);

mysqli_report(MYSQLI_REPORT_OFF);

$pessoa_id      = trim($_POST['pessoa_id'] ?? '');
$titulo         = trim($_POST['titulo'] ?? '');
$descricao      = trim($_POST['descricao'] ?? '');
$data_consulta  = trim($_POST['data_consulta'] ?? '');
$hora_inicio    = trim($_POST['hora_inicio'] ?? '');
$hora_fim       = trim($_POST['hora_fim'] ?? '');
$erros          = [];

if (empty($pessoa_id) || !is_numeric($pessoa_id)) {
    $erros[] = 'Pessoa inválida.';
}

if (strlen($titulo) < 3) {
    $erros[] = 'Título deve ter ao menos 3 caracteres.';
}

if (empty($data_consulta)) {
    $erros[] = 'Data da consulta é obrigatória.';
}

if (empty($hora_inicio)) {
    $erros[] = 'Hora de início é obrigatória.';
}

if (empty($hora_fim)) {
    $erros[] = 'Hora de fim é obrigatória.';
}

// Validar formato da data (esperado YYYY-MM-DD)
if (!empty($data_consulta) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_consulta)) {
    $erros[] = 'Formato de data inválido. Use YYYY-MM-DD.';
}

// Validar se hora fim é depois da hora início
if (!empty($hora_inicio) && !empty($hora_fim) && $hora_fim <= $hora_inicio) {
    $erros[] = 'Hora de fim deve ser depois da hora de início.';
}

$data_consulta_esc  = mysqli_real_escape_string($con, $data_consulta);
$hora_inicio_esc    = mysqli_real_escape_string($con, $hora_inicio);
$hora_fim_esc       = mysqli_real_escape_string($con, $hora_fim);


if (empty($erros)) {

    $sql_conflito = "
        SELECT id 
        FROM agendamentos 
        WHERE 
            data_consulta = '{$data_consulta_esc}' 
            AND status = 0
            AND(
                (hora_inicio < '{$hora_fim_esc}' AND hora_fim > '{$hora_inicio_esc}')
            )";

    $res_conflito = mysqli_query(mysql: $con, query: $sql_conflito);
    
    if ($res_conflito === false) {
        $erros[] = 'Erro ao verificar conflito de horário: ' . mysqli_error($con); 
    } elseif (mysqli_num_rows($res_conflito) > 0) {
        $erros[] = 'Já existe uma consulta agendada neste horário.';
    }
}

if ($erros) {
    echo json_encode(['error' => true, 'error_msg' => implode(' ', $erros)]);
    exit;
}

$pessoa_id_esc      = mysqli_real_escape_string($con, $pessoa_id);
$titulo_esc         = mysqli_real_escape_string($con, $titulo);
$descricao_esc      = mysqli_real_escape_string($con, $descricao);

abrir();

$sql = "INSERT INTO agendamentos (pessoa_id, titulo, descricao, data_consulta, hora_inicio, hora_fim)
        VALUES ('$pessoa_id_esc', '$titulo_esc', '$descricao_esc', '$data_consulta_esc', '$hora_inicio_esc', '$hora_fim_esc')";

$res = mysqli_query($con, $sql);

if (!$res) {
    rollback();
    
    echo json_encode(['error' => true, 'error_msg' => 'Erro ao agendar consulta: ' . mysqli_error($con)]);
    exit;
}

commit();

echo json_encode([
    'error' => false, 
    'msg'   => 'Consulta agendada com sucesso.', 
    'id'    => mysqli_insert_id($con)
]);
?>