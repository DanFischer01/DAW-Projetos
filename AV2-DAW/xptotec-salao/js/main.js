// js/main.js

document.addEventListener("DOMContentLoaded", () => {
    verificarSessao();
    carregarServicosHome();
});

// Verifica se o usuário está logado pelo LocalStorage
function verificarSessao() {
    const cliente = localStorage.getItem("cliente");
    
    if (cliente) {
        // Esconde o botão de Entrar/Cadastrar
        const btnLogin = document.getElementById("btn-login");
        if (btnLogin) btnLogin.classList.add("hidden");

        // Mostra os links da área logada (Minha Conta e Sair)
        const linkPainel = document.getElementById("link-painel");
        const btnSair = document.getElementById("btn-sair");
        
        if (linkPainel) linkPainel.classList.remove("hidden");
        if (btnSair) btnSair.classList.remove("hidden");
    }
}

// Função para deslogar o usuário
function deslogar() {
    localStorage.removeItem("cliente");
    window.location.reload();
}

// AJAX: Carrega serviços vindos do banco de dados na Home
function carregarServicosHome() {
    const gridSalao = document.getElementById("lista-salaon");
    const gridEstetica = document.getElementById("lista-estetica");

    // Se as divs não existirem na página, cancela a execução
    if (!gridSalao || !gridEstetica) return;

    fetch("api/servicos.php")
    .then(async res => {
        // Lemos como texto primeiro para interceptar erros em HTML ou do PHP
        const text = await res.text();
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error("ALERTA CRÍTICO: O PHP não retornou um JSON. Ele retornou isto:", text);
            throw new Error("Erro de resposta do servidor");
        }
    })
    .then(servicos => {
        if (servicos.erro) {
            gridSalao.innerHTML = `<p style='color: var(--cor-erro);'>${servicos.erro}</p>`;
            gridEstetica.innerHTML = `<p style='color: var(--cor-erro);'>${servicos.erro}</p>`;
            return;
        }

        if (servicos.length === 0) {
            gridSalao.innerHTML = "<p style='color: var(--cor-texto-claro);'>Nenhum serviço cadastrado.</p>";
            gridEstetica.innerHTML = "<p style='color: var(--cor-texto-claro);'>Nenhum serviço cadastrado.</p>";
            return;
        }

        // Limpa as mensagens de "Carregando..."
        gridSalao.innerHTML = "";
        gridEstetica.innerHTML = "";

        // Monta os serviços na tela
        servicos.forEach(s => {
            const item = document.createElement("div");
            item.className = "item-servico";
            item.innerHTML = `
                <div class="item-servico-info">
                    <h4>${s.nome}</h4>
                    <p>${s.duracao_minutos} min • ${s.descricao || ''}</p>
                </div>
                <div class="item-servico-preco">R$ ${parseFloat(s.preco).toFixed(2)}</div>
            `;

            // CORREÇÃO AQUI: Procurando pelo nome exato no banco de dados!
            if (s.categoria === 'Salão de Beleza') {
                gridSalao.appendChild(item);
            } else if (s.categoria === 'Centro de Estética') {
                gridEstetica.appendChild(item);
            } else {
                // Se cair uma categoria fantasma, manda pro salão.
                gridSalao.appendChild(item);
            }
        });
    })
    .catch(erro => {
        console.error("Erro na Requisição JS:", erro);
        gridSalao.innerHTML = "<p style='color: var(--cor-erro);'>Erro de conexão com a API.</p>";
        gridEstetica.innerHTML = "<p style='color: var(--cor-erro);'>Erro de conexão com a API.</p>";
    });
}