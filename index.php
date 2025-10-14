<?php ?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Cadastro de Pessoas (PHP + MySQLi + jQuery AJAX)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 24px; }
    .card { max-width: 900px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 12px; } 
    h1 { margin-top: 0; }
    label { display: block; margin: 10px 0 6px; font-weight: 600; }
    input:not([type="file"]) { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; }
    .row-text { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
    .row-file { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    
    .actions { margin-top: 14px; display: flex; gap: 10px; align-items: center; }
    button { padding: 10px 16px; border: 0; border-radius: 8px; cursor: pointer; }
    button.primary { background: #1a73e8; color: #fff; }
    
    table { width: 100%; margin-top: 24px; border-collapse: collapse; }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
    th { background-color: #f7f7f7; }
    .muted { text-align: center; color: #888; }

    .foto-perfil { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
  </style>
</head>
<body>

<div class="card">
  <h1>Cadastro de Pessoas</h1>

  <form id="formPessoa" autocomplete="off" novalidate enctype="multipart/form-data">
    <div class="row-text">
        <div>
            <label for="nome">Nome *</label>
            <input id="nome" name="nome" type="text" required autocomplete="off">
        </div>
        <div>
            <label for="email">E-mail *</label>
            <input id="email" name="email" type="email" required autocomplete="off">
        </div>
        <div>
            <label for="telefone">Telefone</label>
            <input id="telefone" name="telefone" type="tel" autocomplete="off">
        </div>
    </div>
    
    <div class="row-file">
        <div>
            <label for="foto">Foto de Perfil (JPEG, PNG, GIF)</label>
            <input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/gif">
        </div>
        
        <div>
            <label for="documento">Documento/Arquivo (PDF, DOC, ZIP)</label>
            <input id="documento" name="documento" type="file" accept=".pdf,.doc,.docx,.zip">
        </div>
    </div>

    <div class="actions">
      <button id="btnSalvar" type="submit" class="primary">Salvar</button>
      <button type="reset">Limpar</button>
    </div>
  </form>
</div>

<div class="card">
  <h2>Registros Salvos</h2>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Foto</th> 
        <th>Nome</th>
        <th>E-mail</th>
        <th>Telefone</th>
        <th>Documento</th>
        <th>Criado em</th>
      </tr>
    </thead>
    <tbody id="listaPessoas">
      <tr><td colspan="7" class="muted">Carregando...</td></tr> 
    </tbody>
  </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  $(function() {
    const $form = $('#formPessoa');
    const $tbody = $('#listaPessoas');
    const $btn = $('#btnSalvar');

    function esc(text) {
      if (text === null || typeof text === 'undefined') return '';
      return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }
    
    function carregar() {
      $tbody.html('<tr><td colspan="7" class="muted">Carregando...</td></tr>'); 
      
      $.ajax({
        url: 'api/list.php',
        method: 'GET',
        dataType: 'json'
      })
      .done(res => {
          if (res.error) {
            $tbody.html(`<tr><td colspan="7" class="muted">Erro: ${esc(res.error_msg||'')}</td></tr>`);
            return;
          }
          
          let html = '';
          const rows = res.data;

          if (rows.length === 0) {
             html = '<tr><td colspan="7" class="muted">Nenhum registro encontrado.</td></tr>';
          } else {
             rows.forEach(r => {
                // Lógica para exibir a imagem
                const fotoHtml = r.foto_perfil 
                               ? `<img src="uploads/${esc(r.foto_perfil)}" alt="Foto" class="foto-perfil">` 
                               : '<span class="muted">-</span>'; 
                
                // Lógica para exibir o link do documento
                const docHtml = r.nome_documento
                               ? `<a href="uploads/${esc(r.nome_documento)}" target="_blank">Ver Arquivo</a>`
                               : '<span class="muted">-</span>';

                html += `<tr>
                          <td>${esc(r.id)}</td>
                          <td>${fotoHtml}</td> 
                          <td>${esc(r.nome)}</td>
                          <td>${esc(r.email)}</td>
                          <td>${esc(r.telefone||'')}</td>
                          <td>${docHtml}</td> <td>${esc(r.created_at)}</td>
                        </tr>`;
              });
          }

          $tbody.html(html);
      })
      .fail(() => {
        $tbody.html('<tr><td colspan="7" class="muted">Erro ao carregar registros</td></tr>');
      });
    }

    $form.on('submit', function(e) {
      e.preventDefault();
      if (!this.checkValidity()) { this.reportValidity(); return; }

      $btn.prop('disabled', true).text('Salvando...');
      
      const formData = new FormData(this); 

      $.ajax({
        url: 'api/create.php',
        method: 'POST',
        data: formData, 
        dataType: 'json',
        processData: false, 
        contentType: false  
      })
      .done(res => {
        if (!res.error) {
          Swal.fire({ icon:'success', title: res.msg || 'Registro salvo com sucesso.' });
          $form[0].reset();
          carregar();
        } else {
          Swal.fire({ icon:'error', title:'Erro ao salvar', text: res.error_msg });
        }
      })
      .fail((jqXHR, textStatus, errorThrown) => {
        console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
        Swal.fire({ 
            icon:'error', 
            title:'Erro de comunicação', 
            text: 'Não foi possível se comunicar com o servidor.' 
        });
      })
      .always(() => {
        $btn.prop('disabled', false).text('Salvar');
      });
    });
    carregar();
  });
</script>
</body>
</html>