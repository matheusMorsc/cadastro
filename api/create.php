<?php
header(header: 'Content-Type: application/json; charset=utf-8');

require_once dirname(path: __DIR__) . '/funcoes_db.php';
require_once dirname(path: __DIR__) . '/config.php';

// Conecta ao banco
$con = bd_Conexao(database_server: DB_HOST, database_username: DB_USER, database_password: DB_PASS, database_name: DB_NAME);

// Pega os dados do POST
$nome     = trim(string: $_POST['nome'] ?? '');
$email    = trim(string: $_POST['email'] ?? '');
$telefone = trim(string: $_POST['telefone'] ?? '');

// ATUALIZAÇÃO 1: Inicializa variáveis para os dois arquivos
$foto_perfil = ''; 
$nome_documento = '';
$erros = [];

// Validação simples dos dados de texto
if (mb_strlen(string: $nome) < 2) $erros[] = 'Nome deve ter ao menos 2 caracteres.';
if (!filter_var(value: $email, filter: FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';

// Define o diretório de upload
$upload_dir = dirname(path: __DIR__) . '/uploads/'; 


// --- Processamento do Upload da FOTO DE PERFIL ---
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['foto'];
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        $erros[] = 'Tipo de arquivo para Foto não permitido.';
    } else {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nome_unico = uniqid('foto_') . '_' . time() . '.' . $ext;
        $caminho_final = $upload_dir . $nome_unico;
        
        if (move_uploaded_file($file['tmp_name'], $caminho_final)) {
            $foto_perfil = $nome_unico; 
        } else {
            $erros[] = 'Erro ao mover o arquivo de foto. Verifique as permissões da pasta uploads.';
        }
    }
} elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
    $erros[] = 'Erro no upload da foto (Código: ' . $_FILES['foto']['error'] . ').';
}

// --- Processamento do Upload do DOCUMENTO/ARQUIVO ---
if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['documento'];
    
    // Tipos comuns para documentos/arquivos
    $allowed_types = [
        'application/pdf', 
        'application/zip', 
        'application/msword', // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
        'application/x-rar-compressed' // .rar
    ];
    
    if (!in_array($file['type'], $allowed_types)) {
        $erros[] = 'Tipo de documento não permitido. Apenas PDF, DOC, DOCX, ZIP ou RAR.';
    } else {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nome_doc_unico = 'doc_' . uniqid() . '_' . time() . '.' . $ext;
        $caminho_final_doc = $upload_dir . $nome_doc_unico;
        
        if (move_uploaded_file($file['tmp_name'], $caminho_final_doc)) {
            $nome_documento = $nome_doc_unico; 
        } else {
            $erros[] = 'Erro ao mover o arquivo de documento. Verifique as permissões da pasta uploads.';
        }
    }
} elseif (isset($_FILES['documento']) && $_FILES['documento']['error'] !== UPLOAD_ERR_NO_FILE) {
    $erros[] = 'Erro no upload do documento (Código: ' . $_FILES['documento']['error'] . ').';
}


if ($erros) {
    echo json_encode(value: ['error' => true, 'error_msg' => implode(separator: ' ', array: $erros)]);
    exit;
}
// Escape para evitar SQL injection
$nome_esc           = mysqli_real_escape_string(mysql: $con, string: $nome);
$email_esc          = mysqli_real_escape_string(mysql: $con, string: $email);
$telefone_esc       = mysqli_real_escape_string(mysql: $con, string: $telefone);
$foto_perfil_esc    = mysqli_real_escape_string(mysql: $con, string: $foto_perfil); 
$nome_documento_esc = mysqli_real_escape_string(mysql: $con, string: $nome_documento); // NOVO ESCAPE

try {
    abrir();

    $sql = "INSERT INTO pessoas (nome, email, telefone, foto_perfil, nome_documento)
            VALUES ('{$nome_esc}','{$email_esc}','{$telefone_esc}','{$foto_perfil_esc}','{$nome_documento_esc}')";

    $res = bd_query(str_sql: $sql, str_conexao: $con);

    if ($res === false) {
        $errno = mysqli_errno(mysql: $con);
        rollback();
        if ($errno === 1062) {
            echo json_encode(value: ['error' => true, 'error_msg' => 'E-mail já cadastrado.']);
        } else {
            echo json_encode(value: ['error' => true, 'error_msg' => 'Erro ao inserir registro. SQL Error: ' . mysqli_error($con)]);
        }
        exit;
    }

    $novoId = mysqli_insert_id(mysql: $con);
    commit();

    echo json_encode(value: [
        'error' => false,
        'msg' => 'Registro salvo com sucesso.',
        'data'  => ['id' => $novoId, 'rows' => bd_affect_rows()]
    ]);

} catch (Throwable $e) {
    rollback();
    echo json_encode(value: [
        'error' => true, 
        'error_msg' => 'Falha na transação. Detalhe: ' . $e->getMessage()
    ]);
}