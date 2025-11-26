// Variáveis globais
let currentStep = 1;
const totalSteps = 3;

// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    initializeForm();
    setupEventListeners();
});

// Inicializa o formulário
function initializeForm() {
    updateProgressBar();
    updateStepIndicators();
}

// Configura os event listeners
function setupEventListeners() {
    // Botões de navegação
    document.querySelectorAll('.btn-next').forEach(button => {
        button.addEventListener('click', handleNextStep);
    });
    
    document.querySelectorAll('.btn-prev').forEach(button => {
        button.addEventListener('click', handlePrevStep);
    });
    
    // Formatação de CPF e telefone
    document.getElementById('cpf-sign').addEventListener('input', formatCPF);
    document.getElementById('telefone-sign').addEventListener('input', formatPhone);
    
    // Carregamento de cidades
    document.getElementById('estado-sign').addEventListener('change', handleStateChange);
    
    // Validação do formulário
    document.querySelector('.form-sign').addEventListener('submit', validateForm);
}

// Avança para a próxima etapa
function handleNextStep(e) {
    const nextStep = parseInt(e.target.getAttribute('data-next'));
    
    if (validateCurrentStep()) {
        changeStep(nextStep);
    }
}

// Volta para a etapa anterior
function handlePrevStep(e) {
    const prevStep = parseInt(e.target.getAttribute('data-prev'));
    changeStep(prevStep);
}

// Altera a etapa atual
function changeStep(step) {
    // Esconde a etapa atual
    document.querySelector(`.form-step.active`).classList.remove('active');
    
    // Mostra a nova etapa
    document.querySelector(`#${getStepId(step)}`).classList.add('active');
    
    // Atualiza a etapa atual
    currentStep = step;
    
    // Atualiza a barra de progresso e indicadores
    updateProgressBar();
    updateStepIndicators();
}

// Retorna o ID da seção com base na etapa
function getStepId(step) {
    switch(step) {
        case 1: return 'dadospessoais';
        case 2: return 'endereco';
        case 3: return 'senha';
        default: return 'dadospessoais';
    }
}

// Atualiza a barra de progresso
function updateProgressBar() {
    const progressFill = document.getElementById('progress-fill');
    const progressPercentage = (currentStep / totalSteps) * 100;
    progressFill.style.width = `${progressPercentage}%`;
}

// Atualiza os indicadores de etapa
function updateStepIndicators() {
    document.querySelectorAll('.step').forEach(step => {
        const stepNumber = parseInt(step.getAttribute('data-step'));
        
        if (stepNumber <= currentStep) {
            step.classList.add('active');
        } else {
            step.classList.remove('active');
        }
    });
}

// Valida a etapa atual
function validateCurrentStep() {
    const currentStepElement = document.querySelector('.form-step.active');
    const inputs = currentStepElement.querySelectorAll('input[required], select[required]');
    
    for (let input of inputs) {
        if (!input.value.trim()) {
            alert(`Por favor, preencha o campo: ${input.previousElementSibling.textContent}`);
            input.focus();
            return false;
        }
    }
    
    // Validações específicas por etapa
    if (currentStep === 1) {
        if (!validateEmail(document.getElementById('email-sign').value)) {
            alert('Por favor, insira um e-mail válido.');
            document.getElementById('email-sign').focus();
            return false;
        }
        
        if (!validateCPF(document.getElementById('cpf-sign').value)) {
            alert('Por favor, insira um CPF válido.');
            document.getElementById('cpf-sign').focus();
            return false;
        }
    }
    
    return true;
}

// Valida o formulário completo no envio
function validateForm(e) {
    if (!validateCurrentStep()) {
        e.preventDefault();
        return false;
    }
    
    const senha = document.getElementById('senha-sign').value;
    const confirmar = document.getElementById('confirmar-sign').value;

    if (senha !== confirmar) {
        e.preventDefault();
        alert('As senhas não coincidem!');
        document.getElementById('confirmar-sign').focus();
        return false;
    }

    if (senha.length < 6) {
        e.preventDefault();
        alert('A senha deve ter pelo menos 6 caracteres!');
        document.getElementById('senha-sign').focus();
        return false;
    }
    
    return true;
}

// Formatação de CPF
function formatCPF(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    }
}

// Validação de CPF (simplificada)
function validateCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    return cpf.length === 11;
}

// Formatação de telefone
function formatPhone(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length <= 11) {
        value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
        value = value.replace(/(\d)(\d{4})$/, '$1-$2');
        e.target.value = value;
    }
}

// Validação de e-mail
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Carregamento de cidades
async function handleStateChange() {
    const uf = this.value;
    const cidadesSelect = document.getElementById('cidade-sign');

    // Remove a opção inicial se existir
    const first = this.querySelector("option[value='']");
    if (first) first.remove();

    cidadesSelect.disabled = false;
    cidadesSelect.innerHTML = "<option value=''>Carregando...</option>";

    if (!uf) return;

    try {
        const resposta = await fetch(
            `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`
        );
        const dados = await resposta.json();

        cidadesSelect.innerHTML = "<option value=''>Selecione uma cidade</option>";

        dados.forEach(c => {
            const option = document.createElement("option");
            option.value = c.nome;
            option.textContent = c.nome;
            cidadesSelect.appendChild(option);
        });
    } catch (erro) {
        cidadesSelect.innerHTML = "<option>Erro ao carregar cidades</option>";
        console.error('Erro ao buscar cidades:', erro);
    }
}

// Remove a opção inicial quando uma cidade é selecionada
document.getElementById('cidade-sign').addEventListener("change", function() {
    const first = this.querySelector("option[value='']");
    if (first && this.value !== '') first.remove();
});