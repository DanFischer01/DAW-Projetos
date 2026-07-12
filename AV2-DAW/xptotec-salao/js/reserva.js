// js/reserva.js

document.addEventListener("DOMContentLoaded", () => {
    // 1. Verifica se está logado. Se não, manda pra Home.
    const clienteStorage = localStorage.getItem("cliente");
    if (!clienteStorage) {
        alert("Você precisa estar logado para agendar.");
        window.location.href = "login.html";
        return;
    }
    const cliente = JSON.parse(clienteStorage);

    // CORRIGIDO: Os IDs agora estão idênticos ao reserva.html
    const selectServico = document.getElementById("reserva-servico");
    const selectProfissional = document.getElementById("reserva-profissional");
    const inputData = document.getElementById("reserva-data");
    const selectHorario = document.getElementById("reserva-hora");
    const formReserva = document.getElementById("form-reserva");

    // Impede datas no passado
    const hoje = new Date().toISOString().split("T")[0];
    inputData.setAttribute("min", hoje);

    // 2. Carregar serviços ao abrir a página
    fetch("api/servicos.php")
        .then(res => res.json())
        .then(data => {
            selectServico.innerHTML = '<option value="" disabled selected>Selecione um serviço...</option>';
            data.forEach(s => {
                const opt = document.createElement("option");
                opt.value = s.id;
                opt.textContent = `${s.nome} - R$ ${parseFloat(s.preco).toFixed(2)}`;
                selectServico.appendChild(opt);
            });
        });

    // 3. Ao escolher o serviço, buscar profissionais
    selectServico.addEventListener("change", function() {
        selectProfissional.innerHTML = '<option value="">Buscando profissionais...</option>';
        selectProfissional.disabled = true;
        inputData.disabled = true;
        selectHorario.disabled = true;
        inputData.value = "";
        
        const servicoId = this.value;
        if (!servicoId) return;

        fetch(`api/agendamentos.php?acao=buscar_profissionais&servico_id=${servicoId}`)
            .then(res => res.json())
            .then(profissionais => {
                selectProfissional.innerHTML = '<option value="" disabled selected>Selecione um profissional...</option>';
                profissionais.forEach(p => {
                    const opt = document.createElement("option");
                    opt.value = p.id;
                    const esp = p.especialidade ? ` (${p.especialidade})` : '';
                    opt.textContent = `${p.nome}${esp}`;
                    selectProfissional.appendChild(opt);
                });
                selectProfissional.disabled = false;
            });
    });

    // 4. Habilitar data ao escolher o profissional
    selectProfissional.addEventListener("change", function() {
        if (this.value) {
            inputData.disabled = false;
            inputData.value = ""; // Reseta a data se mudar o profissional
            selectHorario.innerHTML = '<option value="" disabled selected>Selecione a data primeiro...</option>';
            selectHorario.disabled = true;
        }
    });

    // 5. Ao escolher a data, buscar horários livres
    inputData.addEventListener("change", function() {
        const funcionarioId = selectProfissional.value;
        const servicoId = selectServico.value;
        const data = this.value;

        if (!funcionarioId || !data) return;

        selectHorario.innerHTML = '<option value="">Buscando horários livres...</option>';
        selectHorario.disabled = true;

        fetch(`api/agendamentos.php?acao=horarios_disponiveis&funcionario_id=${funcionarioId}&data=${data}&servico_id=${servicoId}`)
            .then(res => res.json())
            .then(horarios => {
                selectHorario.innerHTML = '<option value="" disabled selected>Selecione um horário...</option>';
                if (horarios.length === 0) {
                    selectHorario.innerHTML = '<option value="" disabled>Nenhum horário livre neste dia</option>';
                } else {
                    horarios.forEach(h => {
                        const opt = document.createElement("option");
                        opt.value = h;
                        opt.textContent = h;
                        selectHorario.appendChild(opt);
                    });
                    selectHorario.disabled = false;
                }
            });
    });

    // 6. Submeter Reserva
    formReserva.addEventListener("submit", function(e) {
        e.preventDefault();
        const msgDiv = document.getElementById("mensagem-reserva");
        
        const dados = {
            cliente_id: cliente.id,
            servico_id: selectServico.value,
            funcionario_id: selectProfissional.value,
            data: inputData.value,
            hora: selectHorario.value,
            cartao: document.getElementById("cartao-numero").value
        };

        fetch("api/agendamentos.php?acao=agendar", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(dados)
        })
        .then(res => res.json())
        .then(data => {
            msgDiv.classList.remove("hidden");
            if (data.erro) {
                msgDiv.className = "mensagem erro";
                msgDiv.innerText = data.erro;
            } else {
                msgDiv.className = "mensagem sucesso";
                msgDiv.innerText = data.sucesso;
                formReserva.reset();
                selectProfissional.disabled = true;
                inputData.disabled = true;
                selectHorario.disabled = true;
                
                // Redireciona para o painel do cliente
                setTimeout(() => window.location.href = "painel_cliente.html", 2000);
            }
        })
        .catch(() => {
            msgDiv.classList.remove("hidden");
            msgDiv.className = "mensagem erro";
            msgDiv.innerText = "Erro ao se conectar com o servidor.";
        });
    });
});