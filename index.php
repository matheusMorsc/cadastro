<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Cadastro de Pessoas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: Arial, sans-serif; margin: 24px; }
    .card { max-width: 900px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
    h1,h2 { margin-top: 0; }
    label { display: block; margin-top: 10px; font-weight: 600; }
    input:not([type="file"]) { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; }
    .grid-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; }
    .grid-2 { display: grid; grid-template-columns: repeat(2,1fr); gap: 10px; }
    .actions { margin-top: 12px; display: flex; gap: 8px; }
    button { padding: 10px 16px; border: 0; border-radius: 6px; cursor: pointer; }
    button.primary { background: #1a73e8; color: #fff; }
    table { width: 100%; margin-top: 20px; border-collapse: collapse; }
    th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
    th { background: #f7f7f7; }
    .muted { color: #888; text-align: center; }
    .foto { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
  </style>
</head>
<body>

<div class="card">
  <h1>Cadastro de Pessoas</h1>

  <form id="formPessoa" enctype="multipart/form-data">
    <div class="grid-3">
      <div><label>Nome *</label><input name="nome" required></div>
      <div><label>E-mail *</label><input name="email" type="email" required></div>
      <div><label>Telefone</label><input name="telefone"></div>
    </div>

    <div class="grid-2">
      <div><label>Foto (JPG, PNG, GIF)</label><input name="foto" type="file" accept="image/*"></div>
      <div><label>Documento (PDF, DOC, ZIP)</label><input name="documento" type="file" accept=".pdf,.doc,.docx,.zip,.rar"></div>
    </div>

    <div class="actions">
      <button type="submit" class="primary" id="btnSalvar">Salvar</button>
      <button type="reset">Limpar</button>
    </div>
  </form>
</div>

<div class="card">
  <h2>Registros</h2>
  <table>
    <thead><tr><th>#</th><th>Foto</th><th>Nome</th><th>E-mail</th><th>Telefone</th><th>Documento</th><th>Criado em</th></tr></thead>
    <tbody id="listaPessoas"><tr><td colspan="7" class="muted">Carregando...</td></tr></tbody>
  </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){
  const $form = $('#formPessoa'), $tbody = $('#listaPessoas'), $btn = $('#btnSalvar');
  const esc = t => t ? String(t).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s])) : '';

  function carregar(){
    $tbody.html('<tr><td colspan="7" class="muted">Carregando...</td></tr>');
    $.getJSON('api/list.php', res => {
      if(res.error) return $tbody.html(`<tr><td colspan="7" class="muted">${esc(res.error_msg)}</td></tr>`);
      if(!res.data.length) return $tbody.html('<tr><td colspan="7" class="muted">Nenhum registro encontrado.</td></tr>');
      $tbody.html(res.data.map(r => `
        <tr>
          <td>${r.id}</td>
          <td>${r.foto_perfil ? `<img src="uploads/${esc(r.foto_perfil)}" class="foto">` : '-'}</td>
          <td>${esc(r.nome)}</td>
          <td>${esc(r.email)}</td>
          <td>${esc(r.telefone||'')}</td>
          <td>${r.nome_documento ? `<a href="uploads/${esc(r.nome_documento)}" target="_blank">Ver</a>` : '-'}</td>
          <td>${esc(r.created_at||'')}</td>
        </tr>`).join(''));
    }).fail(()=> $tbody.html('<tr><td colspan="7" class="muted">Erro ao carregar.</td></tr>'));
  }

  $form.on('submit', e => {
    e.preventDefault();
    if(!e.target.checkValidity()) return e.target.reportValidity();
    $btn.prop('disabled', true).text('Salvando...');
    const data = new FormData(e.target);
    $.ajax({
      method: 'POST',
      datatype: 'json',
      url: 'api/create.php',
      processData: false,
      contentType: false,
      data: data,
      success: function (res) {
        res = JSON.parse(res);
        if(res.error) Swal.fire({icon:'error', title:res.error_msg});
        else { Swal.fire({icon:'success', title:res.msg}); $form[0].reset(); carregar(); }
      },
      error: ()=> Swal.fire({icon:'error', title:'Erro de comunicação'}),
      complete: ()=> $btn.prop('disabled', false).text('Salvar')
    });
  });

  carregar();
});
</script>
</body>
</html>
