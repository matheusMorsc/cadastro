<?php 
header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/funcoes_db.php';
require_once dirname(__DIR__) . '/config.php';

$con = bd_Conexao(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$con) {
    echo json_encode(['error' => true, 'error_msg' => 'Erro de conexão com o banco de dados.']);
    exit;
}

$sql = "
    SELECT 
        a.*,                
        a.status,          
        p.nome AS pessoa_nome, 
        p.email AS pessoa_email
    FROM 
        agendamentos a
    INNER JOIN 
        pessoas p ON a.pessoa_id = p.id
    ORDER BY 
        a.data_consulta DESC, a.hora_inicio DESC
";

$dados = bd_sql_to_array_assoc($con, $sql);

mysqli_close($con); 

if (is_array($dados)) {
    foreach ($dados as &$agendamento) {
        
        $agendamento['data_formatada'] = date('d/m/Y', strtotime($agendamento['data_consulta']));
        $agendamento['horario_formatado'] = date('H:i', strtotime($agendamento['hora_inicio'])) . ' - ' . 
                                            date('H:i', strtotime($agendamento['hora_fim']));
    }
    echo json_encode(['error' => false, 'data' => $dados]);
} else {
    echo json_encode(['error' => true, 'error_msg' => 'Erro ao executar a consulta. Verifique o log do servidor.']);
}