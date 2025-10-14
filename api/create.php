<?php
header(header: 'Content-Type: application/json; charset=utf-8');
require_once dirname(path: __DIR__) . '/funcoes_db.php';
require_once dirname(path: __DIR__) . '/config.php';

$con = bd_Conexao(host: DB_HOST, user: DB_USER, pass: DB_PASS, db: DB_NAME);

$nome     = trim(string: $_POST['nome'] ?? '');
$email    = trim(string: $_POST['email'] ?? '');
$telefone = trim(string: $_POST['telefone'] ?? '');
$erros = [];

// Validação simples
if (strlen(string: $nome) < 2) $erros[] = 'Nome deve ter ao menos 2 caracteres.';
if (!filter_var(value: $email, filter: FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';

$upload_dir = dirname(path: __DIR__) . '/uploads/';
$foto_perfil = '';
$nome_documento = '';
// Função genérica para upload
function processa_upload($campo, $tipos, $prefixo, $upload_dir, &$erros): string {
    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) return '';
    $f = $_FILES[$campo];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        $erros[] = "Erro no upload de {$campo} (Código {$f['error']}).";
        return '';
    }
    if (!in_array(needle: $f['type'], haystack: $tipos)) {
        $erros[] = "Tipo de arquivo inválido para {$campo}.";
        return '';
    }
    $ext = pathinfo(path: $f['name'], flags: PATHINFO_EXTENSION);
    $nome = "{$prefixo}_" . uniqid() . '.' . $ext;
    if (!move_uploaded_file(from: $f['tmp_name'], to: $upload_dir . $nome))
        $erros[] = "Falha ao mover o arquivo de {$campo}.";
    return $nome;
}

// Uploads
$foto_perfil = processa_upload(campo: 'foto', tipos: ['image/jpeg','image/png','image/gif'], prefixo: 'foto', upload_dir: $upload_dir, erros: $erros);
$nome_documento = processa_upload(campo: 'documento', tipos: [
    'application/pdf','application/zip','application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/x-rar-compressed'
], prefixo: 'doc', upload_dir: $upload_dir, erros: $erros);

if ($erros) {
    echo json_encode(value: ['error' => true, 'error_msg' => implode(separator: ' ', array: $erros)]);
    exit;
}
// Escapes
$nome_esc = mysqli_real_escape_string(mysql: $con, string: $nome);
$email_esc = mysqli_real_escape_string(mysql: $con, string: $email);
$telefone_esc = mysqli_real_escape_string(mysql: $con, string: $telefone);
$foto_esc = mysqli_real_escape_string(mysql: $con, string: $foto_perfil);
$doc_esc = mysqli_real_escape_string(mysql: $con, string: $nome_documento);

try {
    abrir();
    $sql = "INSERT INTO pessoas (nome, email, telefone, foto_perfil, nome_documento)
            VALUES ('$nome_esc','$email_esc','$telefone_esc','$foto_esc','$doc_esc')";
    $res = bd_query(sql: $sql, con: $con);

    if ($res === false) {
        rollback();
        $errno = mysqli_errno(mysql: $con);
        $msg = ($errno === 1062) ? 'E-mail já cadastrado.' : 'Erro ao inserir registro: ' . mysqli_error(mysql: $con);
        echo json_encode(value: ['error' => true, 'error_msg' => $msg]);
        exit;
    }
    commit();
    echo json_encode(value: ['error' => false, 'msg' => 'Registro salvo com sucesso.', 'id' => mysqli_insert_id(mysql: $con)]);
} catch (Throwable $e) {
    rollback();
    echo json_encode(value: ['error' => true, 'error_msg' => 'Falha: ' . $e->getMessage()]);
}
