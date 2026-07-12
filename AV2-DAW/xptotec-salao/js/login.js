// js/login.js

document.addEventListener("DOMContentLoaded", () => {
    // Se já estiver logado, chuta pra Home
    if (localStorage.getItem("cliente")) {
        window.location.href = "index.html";
    }

    document.getElementById("form-login")?.addEventListener("submit", fazerLogin);
    document.getElementById("form-cadastro")?.addEventListener("submit", fazerCadastro);
});

function trocarAba(tipo) {
    const formLogin = document.getElementById("form-login");
    const formCad = document.getElementById("form-cadastro");
    const titulo = document.getElementById("form-titulo");
    const sub = document.getElementById("form-subtitulo");
    
    document.getElementById("auth-mensagem").classList.add("hidden");

    if (tipo === 'cadastro') {
        formLogin.classList.add("hidden");
        formCad.classList.remove("hidden");
        titulo.innerText = "Crie sua Conta";
        sub.innerText = "Rápido e prático para você agendar.";
    } else {
        formCad.classList.add("hidden");
        formLogin.classList.remove("hidden");
        titulo.innerText = "Bem-vinda de volta";
        sub.innerText = "Faça login para gerenciar seus horários.";
    }
}

function exibirMensagem(texto, tipo) {
    const msgDiv = document.getElementById("auth-mensagem");
    msgDiv.innerText = texto;
    msgDiv.className = tipo === 'erro' ? "mensagem erro" : "mensagem sucesso";
    msgDiv.classList.remove("hidden");
}

function fazerLogin(e) {
    e.preventDefault();
    const dados = {
        email: document.getElementById("login-email").value,
        senha: document.getElementById("login-senha").value
    };

    fetch("api/clientes.php?acao=login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(dados)
    })
    .then(res => res.json())
    .then(data => {
        if (data.erro) {
            exibirMensagem(data.erro, 'erro');
        } else {
            localStorage.setItem("cliente", JSON.stringify(data.cliente));
            window.location.href = "index.html"; // Redireciona de volta pra home
        }
    })
    .catch(() => exibirMensagem("Erro de conexão com o servidor.", "erro"));
}

function fazerCadastro(e) {
    e.preventDefault();
    const dados = {
        nome: document.getElementById("cad-nome").value,
        email: document.getElementById("cad-email").value,
        telefone: document.getElementById("cad-telefone").value,
        senha: document.getElementById("cad-senha").value
    };

    fetch("api/clientes.php?acao=cadastrar", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(dados)
    })
    .then(res => res.json())
    .then(data => {
        if (data.erro) {
            exibirMensagem(data.erro, 'erro');
        } else {
            exibirMensagem(data.sucesso, 'sucesso');
            document.getElementById("form-cadastro").reset();
            setTimeout(() => trocarAba('login'), 2000);
        }
    })
    .catch(() => exibirMensagem("Erro de conexão com o servidor.", "erro"));
}