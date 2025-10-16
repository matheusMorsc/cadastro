<?php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/funcoes_db.php';
require_once dirname(__DIR__) . '/config.php';

$con = bd_Conexao(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_report(MYSQLI_REPORT_OFF);

/**
 * Converte um total de minutos em formato "Hh Mm".
 * @param int $total_minutos
 * @return string
 */
function formatar_duracao(int $total_minutos): string {
    if ($total_minutos < 0) {
        return '0h 0m';
    }
    $horas = floor($total_minutos / 60);
    $minutos = $total_minutos % 60;
    
    $saida = '';
    if ($horas > 0) {
        $saida .= "{$horas}h ";
    }
    $saida .= "{$minutos}m";
    
    return trim($saida);
}

$sql_stats = "
    SELECT
        COUNT(id) AS total_sessoes,
        SUM(TIME_TO_SEC(TIMEDIFF(hora_fim, hora_inicio)) / 60) AS total_minutos,
        AVG(TIME_TO_SEC(TIMEDIFF(hora_fim, hora_inicio)) / 60) AS media_minutos
    FROM 
        agendamentos
    WHERE
        status = 1; /* <-- CORREÇÃO: Filtra apenas por mentorias REALIZADAS */
";

$res = mysqli_query($con, $sql_stats);

if (!$res) {
    echo json_encode([
        'error' => true, 
        'error_msg' => 'Erro ao buscar estatísticas: ' . mysqli_error($con)
    ]);
    exit;
}

$stats = mysqli_fetch_assoc($res);

$total_sessoes = (int) ($stats['total_sessoes'] ?? 0);
$total_minutos = (int) ($stats['total_minutos'] ?? 0);
$media_minutos = (float) ($stats['media_minutos'] ?? 0);


$response = [
    'error' => false,
    'data' => [
        'total_sessoes' => $total_sessoes,
        'total_minutos' => $total_minutos,
        'duracao_total_formatada' => formatar_duracao($total_minutos),
        'media_minutos' => round($media_minutos, 2),
        'media_minutos_formatada' => number_format($media_minutos, 2, ',', '.') . ' min'
    ]
];

echo json_encode($response);