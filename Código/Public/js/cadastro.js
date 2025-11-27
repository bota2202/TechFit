let currentStep = 1;
const totalSteps = 3;

function mostrarErro(mensagem) {
    const erroDiv = document.getElementById('mensagem-erro');
    const sucessoDiv = document.getElementById('mensagem-sucesso');
    erroDiv.textContent = mensagem;
    erroDiv.style.display = 'block';
    sucessoDiv.style.display = 'none';
    erroDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function mostrarSucesso(mensagem) {
    const erroDiv = document.getElementById('mensagem-erro');
    const sucessoDiv = document.getElementById('mensagem-sucesso');
    sucessoDiv.textContent = mensagem;
    sucessoDiv.style.display = 'block';
    erroDiv.style.display = 'none';
}

function esconderMensagens() {
    document.getElementById('mensagem-erro').style.display = 'none';
    document.getElementById('mensagem-sucesso').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    initializeForm();
    setupEventListeners();
});

function initializeForm() {
    updateProgressBar();
    updateStepIndicators();
}

function setupEventListeners() {
    document.querySelectorAll('.btn-next').forEach(button => {
        button.addEventListener('click', handleNextStep);
    });
    
    document.querySelectorAll('.btn-prev').forEach(button => {
        button.addEventListener('click', handlePrevStep);
    });
    
    document.getElementById('cpf-sign').addEventListener('input', formatCPF);
    document.getElementById('telefone-sign').addEventListener('input', formatPhone);
    
    document.getElementById('estado-sign').addEventListener('change', handleStateChange);
    
    document.querySelector('.form-sign').addEventListener('submit', validateForm);
}

function handleNextStep(e) {
    const nextStep = parseInt(e.target.getAttribute('data-next'));
    
    if (validateCurrentStep()) {
        changeStep(nextStep);
    }
}

function handlePrevStep(e) {
    const prevStep = parseInt(e.target.getAttribute('data-prev'));
    changeStep(prevStep);
}

function changeStep(step) {
    document.querySelector(`.form-step.active`).classList.remove('active');
    document.querySelector(`#${getStepId(step)}`).classList.add('active');
    currentStep = step;
    updateProgressBar();
    updateStepIndicators();
    esconderMensagens();
}

function getStepId(step) {
    switch(step) {
        case 1: return 'dadospessoais';
        case 2: return 'endereco';
        case 3: return 'senha';
        default: return 'dadospessoais';
    }
}

function updateProgressBar() {
    const progressFill = document.getElementById('progress-fill');
    const progressPercentage = (currentStep / totalSteps) * 100;
    progressFill.style.width = `${progressPercentage}%`;
}

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

function validateCurrentStep() {
    const currentStepElement = document.querySelector('.form-step.active');
    const inputs = currentStepElement.querySelectorAll('input[required], select[required]');
    
    for (let input of inputs) {
        if (!input.value.trim()) {
            mostrarErro(`Por favor, preencha o campo: ${input.previousElementSibling.textContent}`);
            input.focus();
            return false;
        }
    }
    
    if (currentStep === 1) {
        if (!validateEmail(document.getElementById('email-sign').value)) {
            mostrarErro('Por favor, insira um e-mail válido.');
            document.getElementById('email-sign').focus();
            return false;
        }
        
        if (!validateCPF(document.getElementById('cpf-sign').value)) {
            mostrarErro('Por favor, insira um CPF válido.');
            document.getElementById('cpf-sign').focus();
            return false;
        }
    }
    
    return true;
}

async function validateForm(e) {
    e.preventDefault();
    esconderMensagens();
    
    if (!validateCurrentStep()) {
        return false;
    }
    
    const senha = document.getElementById('senha-sign').value;
    const confirmar = document.getElementById('confirmar-sign').value;

    if (senha !== confirmar) {
        mostrarErro('As senhas não coincidem!');
        document.getElementById('confirmar-sign').focus();
        return false;
    }

    if (senha.length < 6) {
        mostrarErro('A senha deve ter pelo menos 6 caracteres!');
        document.getElementById('senha-sign').focus();
        return false;
    }
    
    const formData = new FormData();
    formData.append('nome', document.getElementById('nome-sign').value);
    formData.append('email', document.getElementById('email-sign').value);
    formData.append('cpf', document.getElementById('cpf-sign').value);
    formData.append('telefone', document.getElementById('telefone-sign').value);
    formData.append('senha', senha);
    formData.append('rua', document.getElementById('rua-sign').value);
    formData.append('bairro', document.getElementById('bairro-sign').value);
    formData.append('cidade', document.getElementById('cidade-sign').value);
    formData.append('estado', document.getElementById('estado-sign').value);
    
    try {
        const response = await fetch('cadastro.php', {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status} - ${response.statusText}`);
        }
        
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            throw new Error(`Erro ao processar resposta do servidor. Resposta recebida: ${text.substring(0, 200)}`);
        }
        
        if (data.success) {
            mostrarSucesso('Cadastro realizado com sucesso! Redirecionando...');
            setTimeout(() => {
                window.location.href = '../PageLogin/telalogin.html';
            }, 1500);
        } else {
            mostrarErro(data.message || 'Erro desconhecido ao realizar cadastro');
        }
    } catch (error) {
        mostrarErro(`Erro ao realizar cadastro: ${error.message}`);
    }
    
    return false;
}

function formatCPF(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    }
}

function validateCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    return cpf.length === 11;
}

function formatPhone(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length <= 11) {
        value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
        value = value.replace(/(\d)(\d{4})$/, '$1-$2');
        e.target.value = value;
    }
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

async function handleStateChange() {
    const uf = this.value;
    const cidadesSelect = document.getElementById('cidade-sign');

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
    }
}

document.getElementById('cidade-sign').addEventListener("change", function() {
    const first = this.querySelector("option[value='']");
    if (first && this.value !== '') first.remove();
});