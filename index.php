<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Cadastro de Pessoas e Agendamentos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 24px; 
        }
        .card { 
            max-width: 900px; 
            margin: auto; 
            padding: 20px; 
            border: 1px solid #ddd; 
            border-radius: 10px; 
            margin-bottom: 20px; 
        }
        h1, h2 { 
            margin-top: 0; 
        }
        label { 
            display: block; 
            margin-top: 10px; 
            font-weight: 600; 
        }
        input:not([type="file"]), select { 
            width: 100%; 
            padding: 8px; 
            border: 1px solid #ccc; 
            border-radius: 6px; 
        }
        .grid-3 { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 10px; 
        }
        .grid-2 { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 10px; 
        }
        .actions { 
            margin-top: 12px; 
            display: flex; 
            gap: 8px; 
        }
        button { 
            padding: 10px 16px; 
            border: 0; 
            border-radius: 6px; 
            cursor: pointer; 
        }
        button.primary { 
            background: #1a73e8; 
            color: #fff; 
        }
        button.secondary { 
            background: #34a853; 
            color: #fff; 
        }
        table { 
            width: 100%; 
            margin-top: 20px; 
            border-collapse: collapse; 
        }
        th, td { 
            padding: 10px; 
            border-bottom: 1px solid #eee; 
            text-align: left; 
        }
        th { 
            background: #f7f7f7; 
        }
        .muted { 
            color: #888; 
            text-align: center; 
        }
        .foto { 
            width: 50px; 
            height: 50px; 
            border-radius: 50%; 
            object-fit: cover; 
        }
        small { 
            font-size: 0.8em; 
            color: #666; 
        }
        .section-divider { 
            border-top: 2px solid #eee; 
            margin: 30px 0; 
        }

        .accordion { 
            margin-bottom: 10px; 
        }
        .accordion-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 15px; 
            background: #f8f9fa; 
            border: 1px solid #dee2e6; 
            border-radius: 6px; 
            cursor: pointer; 
            transition: all 0.3s ease;
        }
        .accordion-header:hover { 
            background: #e9ecef; 
        }
        .accordion-header h2 { 
            margin: 0; 
            font-size: 1.2em; 
            color: #495057;
        }
        .accordion-icon { 
            transition: transform 0.3s ease; 
            font-weight: bold;
            color: #6c757d;
        }
        .accordion-icon.expanded { 
            transform: rotate(180deg); 
        }
        .accordion-content { 
            max-height: 0; 
            overflow: hidden; 
            transition: max-height 0.3s ease, padding 0.3s ease;
            background: white;
        }
        .accordion-content.expanded { 
            max-height: 2000px; 
            padding: 20px 0;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            background: #6c757d;
            color: white;
            border-radius: 12px;
            font-size: 0.8em;
            margin-left: 8px;
        }
        
        .status-realizada {
            color: #34a853; 
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="card">
    <h1>Cadastro de Pessoas</h1>

    <form id="formPessoa" enctype="multipart/form-data">
        <div class="grid-3">
            <div>
                <label>Nome *</label>
                <input name="nome" required>
            </div>
            <div>
                <label>E-mail *</label>
                <input name="email" type="email" required>
            </div>
            <div>
                <label>Telefone</label>
                <input name="telefone">
            </div>
        </div>

        <div class="grid-2">
            <div>
                <label>Foto</label>
                <input name="foto" type="file" accept="image/*">
            </div>
            <div>
                <label>Documento</label>
                <input name="documento" type="file" accept=".pdf,.doc,.docx,.zip,.rar">
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="primary" id="btnSalvar">Salvar</button>
            <button type="reset">Limpar</button>
        </div>
    </form>
</div>

<div class="accordion">
    <div class="accordion-header" id="accordionPessoasHeader">
        <h2>Registros de Pessoas <span class="badge" id="contadorPessoas">0</span></h2>
        <span class="accordion-icon">▼</span>
    </div>
    <div class="accordion-content" id="accordionPessoasContent">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Foto</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Documento</th>
                    
                </tr>
            </thead>
            <tbody id="listaPessoas">
                <tr>
                    <td colspan="7" class="muted">Carregando...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>



<div class="section-divider"></div>

<div class="card">
    <h2>Agendar mentoria</h2>

    <form id="formAgendamento">
        <div class="grid-3">
            <div>
                <label>Pessoa *</label>
                <select name="pessoa_id" required id="selectPessoa">
                    <option value="">Selecione uma pessoa</option>
                </select>
            </div>
            <div>
                <label>Título *</label>
                <input name="titulo" required placeholder="Ex: Módulo 2">
            </div>
            <div>
                <label>Data *</label>
                <input name="data_consulta" type="date" required>
            </div>
        </div>

        <div class="grid-3">
            <div>
                <label>Hora Início *</label>
                <input name="hora_inicio" type="time" required>
            </div>
            <div>
                <label>Hora Fim *</label>
                <input name="hora_fim" type="time" required>
            </div>
            <div>
                <label>Descrição</label>
                <input name="descricao" placeholder="Detalhes da consulta">
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="secondary" id="btnAgendar">Agendar mentoria</button>
            <button type="reset">Limpar</button>
        </div>
    </form>
</div>

<div class="accordion">
    <div class="accordion-header" id="accordionAgendamentosHeader">
        <h2>Mentorias Agendadas <span class="badge" id="contadorAgendamentos">0</span></h2>
        <span class="accordion-icon">▼</span>
    </div>
    <div class="accordion-content" id="accordionAgendamentosContent">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pessoa</th>
                    <th>Título</th>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Descrição</th>
                    <th>Agendado em</th>
                    <th>Ação</th> </tr>
            </thead>
            <tbody id="listaAgendamentos">
                <tr>
                    <td colspan="8" class="muted">Carregando...</td> </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h2>Estatísticas de Mentorias Realizadas</h2>
    <div class="grid-3" id="statsContainer">
        <div>
            <label>Sessões Realizadas</label>
            <strong id="statTotalSessoes">-</strong>
        </div>
        <div>
            <label>Duração Total</label>
            <strong id="statDuracaoTotal">-</strong>
        </div>
        <div>
            <label>Tempo Médio por Sessão</label>
            <strong id="statMediaMinutos">-</strong>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){
    const $form = $('#formPessoa'),
          $tbody = $('#listaPessoas'),
          $btnSalvar = $('#btnSalvar'),
          $formAgendamento = $('#formAgendamento'),
          $btnAgendar = $('#btnAgendar'),
          $listaAgendamentos = $('#listaAgendamentos'),
          $selectPessoa = $('#selectPessoa');

    /**
     * @param {string} t 
     * @returns {string} 
     */
    const esc = t => t ? String(t).replace(/[&<>"']/g, s => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    }[s])) : '';

    function initAccordion() {
        $('#accordionPessoasHeader').on('click', function() {
            const $content = $('#accordionPessoasContent');
            const $icon = $(this).find('.accordion-icon');
            
            $content.toggleClass('expanded');
            $icon.toggleClass('expanded');
            
            if ($content.hasClass('expanded') && $tbody.find('tr').length === 1) {
                carregarPessoas();
            }
        });

        // Accordion para Agendamentos
        $('#accordionAgendamentosHeader').on('click', function() {
            const $content = $('#accordionAgendamentosContent');
            const $icon = $(this).find('.accordion-icon');
            
            $content.toggleClass('expanded');
            $icon.toggleClass('expanded');
            
            if ($content.hasClass('expanded') && $('#listaAgendamentos').find('tr').length === 1) {
                carregarAgendamentos();
            }
        });

        $('#accordionPessoasContent').removeClass('expanded');
        $('#accordionAgendamentosContent').removeClass('expanded');
    }

    //Carrega a lista de pessoas cadastradas na tabela.
    function carregarPessoas() {
        $tbody.html('<tr><td colspan="7" class="muted">Carregando...</td></tr>');
        $.getJSON('api/list.php')
            .done(res => {
                if (res.error) {
                    $tbody.html(`<tr><td colspan="7" class="muted">${esc(res.error_msg)}</td></tr>`);
                    $('#contadorPessoas').text('0');
                    return;
                }
                if (!res.data.length) {
                    $tbody.html('<tr><td colspan="7" class="muted">Nenhum registro encontrado.</td></tr>');
                    $('#contadorPessoas').text('0');
                    return;
                }
                
                const linhas = res.data.map(r => `
                    <tr>
                        <td>${r.id}</td>
                        <td>${r.foto_perfil ? `<img src="uploads/${esc(r.foto_perfil)}" class="foto">` : '-'}</td>
                        <td>${esc(r.nome)}</td>
                        <td>${esc(r.email)}</td>
                        <td>${esc(r.telefone || '')}</td>
                        <td>${r.nome_documento ? `<a href="uploads/${esc(r.nome_documento)}" target="_blank">Ver</a>` : '-'}</td>
                        
                    </tr>`).join('');
                $tbody.html(linhas);
                $('#contadorPessoas').text(res.data.length);
            })
            .fail(() => {
                $tbody.html('<tr><td colspan="7" class="muted">Erro ao carregar.</td></tr>');
                $('#contadorPessoas').text('0');
            });
    }

    function carregarPessoasSelect() {
        $.getJSON('api/list.php')
            .done(res => {
                if (!res.error && res.data.length) {
                    const options = res.data.map(p => 
                        `<option value="${p.id}">${esc(p.nome)} - ${esc(p.email)}</option>`
                    ).join('');
                    $selectPessoa.html('<option value="">Selecione uma pessoa</option>' + options);
                } else {
                    $selectPessoa.html('<option value="">Nenhuma pessoa cadastrada</option>');
                }
            })
            .fail(() => $selectPessoa.html('<option value="">Erro ao carregar pessoas</option>'));
    }

    // Carrega as estatísticas das mentorias realizadas.
    function carregarEstatisticas() {
        $('#statTotalSessoes').text('-');
        $('#statDuracaoTotal').text('-');
        $('#statMediaMinutos').text('-');

        $.getJSON('api/stats.php')
            .done(res => {
                if (res.error) {
                    console.error('Erro ao carregar stats:', res.error_msg);
                    return;
                }
                const stats = res.data;
                $('#statTotalSessoes').text(stats.total_sessoes);
                $('#statDuracaoTotal').text(stats.duracao_total_formatada);
                $('#statMediaMinutos').text(stats.media_minutos_formatada);
            })
            .fail(() => {
                console.error('Erro de comunicação ao carregar estatísticas.');
            });
    }
    
    
     //Carrega a lista de agendamentos e atualiza as estatísticas.
    function carregarAgendamentos() {
        $listaAgendamentos.html('<tr><td colspan="8" class="muted">Carregando...</td></tr>'); // COLSPAN AJUSTADO
        
        $.getJSON('api/listar_agendamentos.php')
            .done(res => {
                if (res.error) {
                    $listaAgendamentos.html(`<tr><td colspan="8" class="muted">${esc(res.error_msg)}</td></tr>`);
                    $('#contadorAgendamentos').text('0');
                    return;
                }
                if (!res.data.length) {
                    $listaAgendamentos.html('<tr><td colspan="8" class="muted">Nenhuma mentoria agendada.</td></tr>');
                    $('#contadorAgendamentos').text('0');
                    return;
                }
                
                const linhas = res.data.map(a => {
                    let acaoHtml;
                    
                    // Lógica para Ação/Status da Mentoria
                    if (a.status == 1) { // 1 = Realizada
                        acaoHtml = '<span class="status-realizada">Realizada</span>';
                    } else { // 0 ou NULL = Pendente
                        acaoHtml = `<button class="secondary btn-concluir" data-id="${a.id}">Concluir</button>`;
                    }
                    
                    return `
                        <tr>
                            <td>${a.id}</td>
                            <td>
                                <strong>${esc(a.pessoa_nome)}</strong><br>
                                <small>${esc(a.pessoa_email)}</small>
                            </td>
                            <td>${esc(a.titulo)}</td>
                            <td>${esc(a.data_formatada)}</td>
                            <td>${esc(a.horario_formatado)}</td>
                            <td>${esc(a.descricao || '-')}</td>
                            <td>${esc(a.created_at || '')}</td>
                            <td>${acaoHtml}</td> </tr>
                    `;
                }).join('');
                
                $listaAgendamentos.html(linhas);
                $('#contadorAgendamentos').text(res.data.length);
            })
            .fail(() => {
                $listaAgendamentos.html('<tr><td colspan="8" class="muted">Erro ao carregar mentorias.</td></tr>');
                $('#contadorAgendamentos').text('0');
            })
            .always(() => carregarEstatisticas()); // CHAMA AS ESTATÍSTICAS APÓS LISTAR
    }

    $form.on('submit', e => {
        e.preventDefault();

        if (!e.target.checkValidity()) return e.target.reportValidity();

        $btnSalvar.prop('disabled', true).text('Salvando...');
        const data = new FormData(e.target);

        $.ajax({
            method: 'POST',
            dataType: 'json',
            url: 'api/create.php',
            processData: false, 
            contentType: false, 
            data: data,
            success: res => {
                if (res.error)
                    Swal.fire({icon: 'error', title: res.error_msg});
                else {
                    Swal.fire({icon: 'success', title: res.msg || 'Pessoa salva com sucesso!'});
                    $form[0].reset();
                    carregarPessoas();
                    carregarPessoasSelect(); 
                }
            },
            error: () => Swal.fire({icon: 'error', title: 'Erro de comunicação com o servidor'}),
            complete: () => $btnSalvar.prop('disabled', false).text('Salvar')
        });
    });

    $formAgendamento.on('submit', function(e) {
        e.preventDefault();
        
        if (!this.checkValidity()) return this.reportValidity();

        $btnAgendar.prop('disabled', true).text('Agendando...');
        
        const data = new FormData(this);

        $.ajax({
            method: 'POST',
            dataType: 'json',
            url: 'api/agendar.php',
            processData: false, 
            contentType: false, 
            data: data,
            success: res => {
                if (res.error) {
                    Swal.fire({icon: 'error', title: res.error_msg});
                } else {
                    Swal.fire({icon: 'success', title: res.msg || 'Mentoria agendada com sucesso!'});
                    this.reset();
                    carregarAgendamentos();
                }
            },
            error: () => Swal.fire({icon: 'error', title: 'Erro de comunicação com o servidor'}),
            complete: () => $btnAgendar.prop('disabled', false).text('Agendar mentoria')
        });
    });
    
    $(document).on('click', '.btn-concluir', function() {
        const id = $(this).data('id');
        const $button = $(this);

        Swal.fire({
            title: 'Tem certeza?',
            text: "Marcar esta mentoria como realizada?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#34a853',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, concluir!'
        }).then((result) => {
            if (result.isConfirmed) {
                $button.prop('disabled', true).text('...');
                $.post('api/concluir_agendamento.php', { id: id })
                    .done(res => {
                        if (res.error) {
                            Swal.fire({icon: 'error', title: res.error_msg});
                        } else {
                            Swal.fire({icon: 'success', title: res.msg});
                            carregarAgendamentos(); 
                        }
                    })
                    .fail(() => Swal.fire({icon: 'error', title: 'Erro de comunicação ao concluir'}))
                    .always(() => $button.prop('disabled', false).text('Concluir'));
            }
        });
    });
    // Validação em tempo real para hora fim (deve ser depois da hora de início)
    $('input[name="hora_inicio"]').on('change', function() {
        const horaFim = $('input[name="hora_fim"]');
        if (horaFim.val() && horaFim.val() <= $(this).val()) {
            horaFim.val('');
            Swal.fire({icon: 'warning', title: 'Hora de fim deve ser depois da hora de início'});
        }
    });

    initAccordion();
    carregarPessoasSelect();
    carregarPessoas();
    carregarAgendamentos();

    // Definir data mínima como hoje para o campo de agendamentos
    const today = new Date().toISOString().split('T')[0];
    $('input[name="data_consulta"]').attr('min', today);
});
</script>
</body>
</html>