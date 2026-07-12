// js/painel.js

document.addEventListener("DOMContentLoaded", () => {
    const clienteStorage = localStorage.getItem("cliente");
    if (!clienteStorage) {
        window.location.href = "index.html";
        return;
    }
    const cliente = JSON.parse(clienteStorage);
    
    carregarAgendamentos(cliente.id);

    document.getElementById("form-avaliacao").addEventListener("submit", enviarAvaliacao);
});

function carregarAgendamentos(clienteId) {
    const divLista = document.getElementById("lista-agendamentos");

    fetch(`api/painel_cliente.php?acao=listar&cliente_id=${clienteId}`)
        .then(res => res.json())
        .then(data => {
            divLista.innerHTML = "";
            if (data.length === 0) {
                divLista.innerHTML = "<p>Você ainda não possui agendamentos.</p>";
                return;
            }

            data.forEach(ag => {
                const stringDataHora = `${ag.data_reserva}T${ag.hora_reserva}`;
                const dataFormatada = new Date(stringDataHora).toLocaleString('pt-BR', {
                    dateStyle: 'short',
                    timeStyle: 'short'
                });
                
                let botoes = "";
                
                // SE O AGENDAMENTO ESTIVER CONFIRMADO: Mostra Cancelar E TAMBÉM Avaliar (para facilitar seus testes)
                if (ag.status === 'confirmado' || ag.status === 'agendado') {
                    botoes = `
                        <button onclick="cancelarAgendamento(${ag.id})" style="background:var(--cor-erro); margin-top:10px;">Cancelar Reserva</button>
                        <button onclick="abrirAvaliacao(${ag.id})" style="background:var(--cor-sucesso); margin-top:10px; margin-left:5px;">Avaliar Serviço</button>
                    `;
                } else if (ag.status === 'concluido') {
                    // SE JÁ ESTIVER CONCLUÍDO: Mostra apenas a opção de avaliar
                    botoes = `<button onclick="abrirAvaliacao(${ag.id})" style="background:var(--cor-sucesso); margin-top:10px;">Avaliar Serviço</button>`;
                }

                const card = document.createElement("div");
                card.className = "card-agendamento";
                card.innerHTML = `
                    <h4>${ag.servico_nome} com ${ag.profissional_nome}</h4>
                    <p><strong>Data/Hora:</strong> ${dataFormatada}</p>
                    <p><strong>Status:</strong> ${ag.status.toUpperCase()}</p>
                    ${botoes}
                `;
                divLista.appendChild(card);
            });
        })
        .catch(err => {
            console.error("Erro no carregamento:", err);
            divLista.innerHTML = "<p>Erro ao processar as informações do painel.</p>";
        });
}

function cancelarAgendamento(agendamentoId) {
    if (!confirm("Tem certeza que deseja cancelar? Você receberá 50% do valor pago como crédito em sua conta.")) return;

    const msgDiv = document.getElementById("msg-painel");

    fetch("api/painel_cliente.php?acao=cancelar", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ agendamento_id: agendamentoId })
    })
    .then(res => res.json())
    .then(data => {
        msgDiv.className = data.erro ? "mensagem erro" : "mensagem sucesso";
        msgDiv.innerText = data.erro || data.sucesso;
        
        const cliente = JSON.parse(localStorage.getItem("cliente"));
        carregarAgendamentos(cliente.id);
    });
}

function abrirAvaliacao(agendamentoId) {
    document.getElementById("area-avaliacao").classList.remove("hidden");
    document.getElementById("aval-agendamento-id").value = agendamentoId;
    window.scrollTo(0, document.body.scrollHeight);
}

function fecharAvaliacao() {
    document.getElementById("area-avaliacao").classList.add("hidden");
    document.getElementById("form-avaliacao").reset();
}

function enviarAvaliacao(e) {
    e.preventDefault();
    const msgDiv = document.getElementById("msg-painel");
    
    const dados = {
        agendamento_id: document.getElementById("aval-agendamento-id").value,
        nota: document.getElementById("aval-nota").value,
        comentario: document.getElementById("aval-comentario").value
    };

    fetch("api/painel_cliente.php?acao=avaliar", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(dados)
    })
    .then(res => res.json())
    .then(data => {
        msgDiv.className = data.erro ? "mensagem erro" : "mensagem sucesso";
        msgDiv.innerText = data.erro || data.sucesso;
        fecharAvaliacao();
        
        const cliente = JSON.parse(localStorage.getItem("cliente"));
        carregarAgendamentos(cliente.id);
    });
}