<?php
header('Content-Type: application/json; charset=utf-8');
require_once dirname(__DIR__) . '/funcoes_db.php';
require_once dirname(__DIR__) . '/config.php';

$con = bd_Conexao(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_report(MYSQLI_REPORT_OFF);

$id = trim($_POST['id'] ?? '');

if (empty($id) || !is_numeric(value: $id)) {
    echo json_encode(value: ['error' => true, 'error_msg' => 'ID de agendamento inválido.']);
    exit;
}

$id_esc = mysqli_real_escape_string(mysql: $con, string: $id);

abrir();

// Atualiza o status para 1 (Realizada). Assumindo status 0 = Pendente/Agendada
$sql = "UPDATE agendamentos SET status = 1 WHERE id = '$id_esc'";

$res = mysqli_query($con, $sql);

if (!$res) {
    rollback();
    echo json_encode(['error' => true, 'error_msg' => 'Erro ao concluir agendamento: ' . mysqli_error($con)]);
    exit;
}

$linhas_afetadas = mysqli_affected_rows($con);

if ($linhas_afetadas == 0) {
    commit(); // Se não deu erro, pode commitar.
    echo json_encode(['error' => false, 'msg' => 'Mentoria já estava marcada como realizada. (Nenhuma alteração feita)']);
    exit;
}

commit();

echo json_encode(['error' => false, 'msg' => 'Mentoria marcada como realizada com sucesso!']);