// Dados simulados vindos do banco para os filtros dinâmicos
const servicos = [
    { id: 1, nome: "Corte de Cabelo", especialidade: "Cabelo" },
    { id: 2, nome: "Manicure", especialidade: "Unhas" },
    { id: 3, nome: "Limpeza de Pele", especialidade: "Estética" }
];

const profissionais = [
    { id: 1, nome: "Ana Silva", especialidade: "Cabelo" },
    { id: 2, nome: "Beatriz Souza", especialidade: "Unhas" },
    { id: 3, nome: "Carla Costa", especialidade: "Estética" },
    { id: 4, nome: "Daniela Lima", especialidade: "Cabelo" }
];

function filtrarOpcoes() {
    const espSelecionada = document.getElementById('especialidade').value;
    const selectServico = document.getElementById('servico');
    const selectProfissional = document.getElementById('profissional');

    // Limpar opções anteriores
    selectServico.innerHTML = '<option value="">Selecione...</option>';
    selectProfissional.innerHTML = '<option value="">Qualquer profissional disponível no momento</option>';

    if (!espSelecionada) return;

    // Filtrar e adicionar serviços
    servicos.filter(s => s.especialidade === espSelecionada).forEach(s => {
        let opt = document.createElement('option');
        opt.value = s.id;
        opt.innerHTML = s.nome;
        selectServico.appendChild(opt);
    });

    // Filtrar e adicionar profissionais
    profissionais.filter(p => p.especialidade === espSelecionada).forEach(p => {
        let opt = document.createElement('option');
        opt.value = p.id;
        opt.innerHTML = p.nome;
        selectProfissional.appendChild(opt);
    });
}

function validarPagamento() {
    const cartao = document.getElementById('cartao').value.replace(/\s/g, '');
    const cvv = document.getElementById('cvv').value;

    if (cartao.length < 16 || isNaN(cartao)) {
        alert("Número de cartão de crédito inválido.");
        return false;
    }
    if (cvv.length < 3 || isNaN(cvv)) {
        alert("CVV inválido.");
        return false;
    }
    
    // Simulação de gateway de pagamento aprovado
    alert("Pagamento processado com sucesso via cartão de crédito!");
    return true;
}