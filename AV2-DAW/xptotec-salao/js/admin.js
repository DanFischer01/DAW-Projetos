document.addEventListener("DOMContentLoaded", () => {
    verificarAcessoAdmin();

    document.getElementById("form-login-admin").addEventListener("submit", fazerLoginAdmin);
    document.getElementById("form-servico").addEventListener("submit", cadastrarServico);
    document.getElementById("form-funcionario").addEventListener("submit", cadastrarFuncionario);
});

function verificarAcessoAdmin() {
    const admin = localStorage.getItem("admin");
    if (admin) {
        document.getElementById("tela-login-admin").classList.add("hidden");
        document.getElementById("tela-dashboard-admin").classList.remove("hidden");
        
        // Carrega todas as listagens iniciais ao logar
        carregarAvaliacoes();
        carregarListaServicos();
        carregarAgendamentosAdmin(); // CORREÇÃO: Faltava chamar a listagem de agendamentos aqui!
    } else {
        document.getElementById("tela-login-admin").classList.remove("hidden");
        document.getElementById("tela-dashboard-admin").classList.add("hidden");
    }
}

function fazerLoginAdmin(e) {
    e.preventDefault();
    const dados = {
        email: document.getElementById("admin-email").value,
        senha: document.getElementById("admin-senha").value
    };

    fetch("api/admin.php?acao=login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(dados)
    })
    .then(res => res.json())
    .then(data => {
        if (data.erro) {
            const msg = document.getElementById("msg-login-admin");
            msg.innerText = data.erro;
            msg.className = "mensagem erro";
            msg.classList.remove("hidden");
        } else {
            localStorage.setItem("admin", JSON.stringify(data.admin));
            verificarAcessoAdmin();
        }
    })
    .catch(() => alert("Erro ao conectar com o servidor administrativo."));
}

function deslogarAdmin() {
    localStorage.removeItem("admin");
    window.location.reload();
}

function mostrarAba(abaId) {
    // CORREÇÃO: Esconde todas as abas, incluindo a de agendamentos
    document.getElementById("aba-servicos").classList.add("hidden");
    document.getElementById("aba-funcionarios").classList.add("hidden");
    document.getElementById("aba-avaliacoes").classList.add("hidden");
    document.getElementById("aba-agendamentos").classList.add("hidden"); 
    
    document.getElementById(abaId).classList.remove("hidden");

    // CORREÇÃO: Dispara a função correta de acordo com a aba clicada
    if (abaId === 'aba-avaliacoes') carregarAvaliacoes();
    if (abaId === 'aba-servicos') carregarListaServicos();
    if (abaId === 'aba-agendamentos') carregarAgendamentosAdmin(); 
}

function exibirMensagem(texto, tipo) {
    const msgDiv = document.getElementById("msg-admin");
    msgDiv.innerText = texto;
    msgDiv.className = tipo === 'erro' ? "mensagem erro" : "mensagem sucesso";
    msgDiv.classList.remove("hidden");
    setTimeout(() => { msgDiv.classList.add("hidden"); }, 4000);
}

function cadastrarServico(e) {
    e.preventDefault();
    const dados = {
        nome: document.getElementById("serv-nome").value,
        descricao: document.getElementById("serv-descricao").value,
        categoria: document.getElementById("serv-categoria").value,
        duracao_minutos: document.getElementById("serv-duracao").value,
        preco: document.getElementById("serv-preco").value
    };

    fetch("api/admin.php?acao=cadastrar_servico", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(dados)
    })
    .then(res => res.json())
    .then(data => {
        if (data.erro) exibirMensagem(data.erro, 'erro');
        else {
            exibirMensagem(data.sucesso, 'sucesso');
            document.getElementById("form-servico").reset();
            carregarListaServicos(); 
        }
    });
}

function cadastrarFuncionario(e) {
    e.preventDefault();
    const dados = {
        nome: document.getElementById("func-nome").value,
        especialidade: document.getElementById("func-especialidade").value
    };

    fetch("api/admin.php?acao=cadastrar_funcionario", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(dados)
    })
    .then(res => res.json())
    .then(data => {
        if (data.erro) exibirMensagem(data.erro, 'erro');
        else {
            exibirMensagem(data.sucesso, 'sucesso');
            document.getElementById("form-funcionario").reset();
        }
    });
}

function carregarAvaliacoes() {
    const tbody = document.getElementById("tabela-avaliacoes");
    tbody.innerHTML = "<tr><td colspan='4'>Carregando registros...</td></tr>";

    fetch("api/admin.php?acao=listar_avaliacoes")
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = "";
            if (!data || data.length === 0) {
                tbody.innerHTML = "<tr><td colspan='4'>Nenhuma avaliação encontrada no sistema.</td></tr>";
                return;
            }
            data.forEach(av => {
                const tr = document.createElement("tr");
                const nota = parseInt(av.nota) || 0;
                let estrelas = "⭐".repeat(nota) + "☆".repeat(5 - nota);
                
                tr.innerHTML = `
                    <td><strong>${av.cliente_nome || av.cliente || 'Desconhecido'}</strong></td>
                    <td>${av.servico_nome || av.servico}<br><small>com ${av.profissional_nome || av.profissional}</small></td>
                    <td>${estrelas}</td>
                    <td>${av.comentario || '<em>Sem comentário</em>'}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(() => {
            tbody.innerHTML = "<tr><td colspan='4'>Erro ao processar as avaliações.</td></tr>";
        });
}

function carregarListaServicos() {
    let container = document.getElementById("lista-servicos-admin");
    if (!container) return;

    container.innerHTML = "<p>Carregando serviços...</p>";

    fetch("api/admin.php?acao=listar_servicos_admin")
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error(text);
            }
        })
        .then(data => {
            if (data.erro) {
                container.innerHTML = `<h3 style="color: red;">⚠️ Erro no PHP:</h3><p>${data.erro}</p>`;
                return;
            }

            if (data.length === 0) {
                container.innerHTML = "<h3>Serviços Cadastrados</h3><p>Nenhum serviço ativo encontrado no banco.</p>";
                return;
            }

            let html = `
                <h3 style="margin-bottom: 10px;">Serviços Cadastrados</h3>
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ccc;">
                            <th style="padding: 10px;">Nome</th>
                            <th style="padding: 10px;">Categoria</th>
                            <th style="padding: 10px;">Preço</th>
                            <th style="padding: 10px; text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.forEach(s => {
                html += `
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;">${s.nome}</td>
                        <td style="padding: 10px; text-transform: capitalize;">${s.categoria}</td>
                        <td style="padding: 10px;">R$ ${parseFloat(s.preco).toFixed(2)}</td>
                        <td style="padding: 10px; text-align: center;">
                            <button onclick="excluirServico(${s.id})" style="background: #d9534f; color: white; border: none; padding: 5px 10px; cursor: pointer;">🗑️ Excluir</button>
                        </td>
                    </tr>
                `;
            });

            html += `</tbody></table>`;
            container.innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = `
                <h3 style="color: red;">⚠️ Erro Crítico:</h3>
                <p style="color: red;">O arquivo PHP falhou. Veja a resposta do servidor:</p>
                <textarea style="width: 100%; height: 100px;">${err.message}</textarea>
            `;
        });
}

function excluirServico(id) {
    if (!confirm("Tem certeza que deseja excluir este serviço?")) return;

    fetch("api/admin.php?acao=excluir_servico", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ servico_id: id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.erro) exibirMensagem(data.erro, 'erro');
        else {
            exibirMensagem(data.sucesso, 'sucesso');
            carregarListaServicos(); 
        }
    });
}

function carregarAgendamentosAdmin() {
    const tabelaCorpo = document.getElementById("tabela-agendamentos-corpo");
    if (!tabelaCorpo) return;

    tabelaCorpo.innerHTML = `<tr><td colspan="7" style="text-align:center;">Carregando agendamentos...</td></tr>`;

    fetch("api/admin.php?acao=listar_agendamentos")
        .then(res => res.json())
        .then(data => {
            tabelaCorpo.innerHTML = "";
            
            if (data.length === 0) {
                tabelaCorpo.innerHTML = `<tr><td colspan="7" style="text-align:center;">Nenhum agendamento encontrado.</td></tr>`;
                return;
            }

            data.forEach(ag => {
                const stringDataHora = `${ag.data_reserva}T${ag.hora_reserva}`;
                const dataFormatada = new Date(stringDataHora).toLocaleString('pt-BR', {
                    dateStyle: 'short',
                    timeStyle: 'short'
                });

                let acaoBotao = "";
                if (ag.status !== 'cancelado' && ag.status !== 'concluido') {
                    acaoBotao = `<button onclick="cancelarAgendamentoPorAdmin(${ag.id})" class="btn-cancelar-admin" style="background:var(--cor-erro, #dc3545); color:#fff; border:none; padding:4px 8px; cursor:pointer; border-radius:4px;">Cancelar</button>`;
                } else {
                    acaoBotao = `<span style="color:#777; font-size:0.9em;">Sem ações</span>`;
                }

                let badgeStatus = ag.status.toUpperCase();
                
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td style="padding:10px;"><strong>${ag.id}</strong></td>
                    <td style="padding:10px;">${ag.cliente_nome}</td>
                    <td style="padding:10px;">${ag.servico_nome}</td>
                    <td style="padding:10px;">${ag.profissional_nome}</td>
                    <td style="padding:10px;">${dataFormatada}</td>
                    <td style="padding:10px;"><span class="status-badge status-${ag.status}">${badgeStatus}</span></td>
                    <td style="padding:10px;">${acaoBotao}</td>
                `;
                tabelaCorpo.appendChild(tr);
            });
        })
        .catch(err => {
            console.error("Erro ao carregar agendamentos:", err);
            tabelaCorpo.innerHTML = `<tr><td colspan="7" style="text-align:center; color:red;">Erro ao conectar com a API de agendamentos.</td></tr>`;
        });
}

function cancelarAgendamentoPorAdmin(agendamentoId) {
    if (!confirm(`Deseja realmente cancelar o agendamento #${agendamentoId}? Esta ação não poderá ser desfeita.`)) return;

    fetch("api/admin.php?acao=cancelar_agendamento_admin", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ agendamento_id: agendamentoId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.erro) {
            alert(data.erro);
        } else {
            alert(data.sucesso);
            carregarAgendamentosAdmin();
        }
    })
    .catch(() => alert("Erro ao tentar conectar com o servidor."));
}