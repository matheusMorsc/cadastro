<?php
header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/funcoes_db.php';
require_once dirname(__DIR__) . '/config.php';

$con = bd_Conexao(DB_HOST, DB_USER, DB_PASS, DB_NAME);

mysqli_report(MYSQLI_REPORT_OFF);

$nome       = trim($_POST['nome'] ?? '');
$email      = trim($_POST['email'] ?? '');
$telefone   = trim($_POST['telefone'] ?? '');
$erros      = [];


if (strlen($nome) < 2) {
    $erros[] = 'Nome deve ter ao menos 2 caracteres.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'E-mail inválido.';
}

$upload_dir     = dirname(__DIR__) . '/uploads/';
$foto_perfil    = '';
$nome_documento = '';

/**
 * Processa o upload de um arquivo.
 * @param string $campo 
 * @param array $tipos 
 * @param string $prefixo 
 * @param string $upload_dir 
 * @param array $erros 
 * @return string 
 */
function processa_upload(string $campo, array $tipos, string $prefixo, string $upload_dir, array &$erros): string {
    
    if (empty($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    $f = $_FILES[$campo];
    
    if ($f['error'] !== UPLOAD_ERR_OK) {
        $erros[] = "Erro no upload de {$campo}.";
        return '';
    }

    if (!in_array($f['type'], $tipos)) {
        $erros[] = "Tipo de arquivo inválido para {$campo}.";
        return '';
    }
    
    $ext        = pathinfo($f['name'], PATHINFO_EXTENSION);
    $nome       = "{$prefixo}_" . uniqid() . '.' . $ext;
    
    if (!move_uploaded_file($f['tmp_name'], $upload_dir . $nome)) {
        $erros[] = "Falha ao mover o arquivo de {$campo}.";
    }

    return $nome;
}


$foto_perfil = processa_upload(
    'foto', 
    ['image/jpeg', 'image/png', 'image/gif'], 
    'foto', 
    $upload_dir, 
    $erros
);


$nome_documento = processa_upload(
    'documento',
    [
        'application/pdf',
        'application/zip',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/x-rar-compressed'
    ],
    'doc',
    $upload_dir,
    $erros
);


if ($erros) {
    echo json_encode(['error' => true, 'error_msg' => implode(' ', $erros)]);
    exit;
}

$nome_esc       = mysqli_real_escape_string($con, $nome);
$email_esc      = mysqli_real_escape_string($con, $email);
$telefone_esc   = mysqli_real_escape_string($con, $telefone);
$foto_esc       = mysqli_real_escape_string($con, $foto_perfil);
$doc_esc        = mysqli_real_escape_string($con, $nome_documento);


abrir();

$sql = "INSERT INTO pessoas (nome, email, telefone, foto_perfil, nome_documento)
        VALUES ('$nome_esc', '$email_esc', '$telefone_esc', '$foto_esc', '$doc_esc')";

$res = mysqli_query($con, $sql);

if (!$res) {
    rollback();
    
    $errno      = mysqli_errno($con);
    $errtext    = mysqli_error($con);

    if ($errno === 1062 || str_contains(haystack: $errtext, needle: 'Duplicate entry')) {
        echo json_encode(value: ['error' => true, 'error_msg' => 'E-mail já cadastrado.']);
    } else {
        echo json_encode(['error' => true, 'error_msg' => 'Erro ao inserir registro. Detalhe: ' . $errtext]);
    }
    exit;
}

commit();

echo json_encode([
    'error' => false, 
    'msg'   => 'Registro salvo com sucesso.', 
    'id'    => mysqli_insert_id($con)
]);