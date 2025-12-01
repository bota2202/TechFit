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
    
    // Limpa erros quando usuário começa a digitar
    document.querySelectorAll('.campo-sign').forEach(input => {
        input.addEventListener('input', function() {
            clearError(this.id);
        });
    });
    
    // Carregamento de cidades
    document.getElementById('estado-sign').addEventListener('change', handleStateChange);
    
    // Validação do formulário
    document.querySelector('.form-sign').addEventListener('submit', validateForm);
}

// Limpa mensagem de erro de um campo
function clearError(fieldId) {
    const errorElement = document.getElementById(`error-${fieldId.replace('-sign', '')}`);
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }
    
    // Remove classe de erro do input
    const inputElement = document.getElementById(fieldId);
    if (inputElement) {
        inputElement.classList.remove('error');
    }
}

// Mostra mensagem de erro em um campo
function showError(fieldId, message) {
    const errorElement = document.getElementById(`error-${fieldId}`);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
    
    // Adiciona classe de erro no input
    const inputElement = document.getElementById(`${fieldId}-sign`);
    if (inputElement) {
        inputElement.classList.add('error');
        inputElement.focus();
    }
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
    document.querySelector('.form-step.active').classList.remove('active');
    
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
    let isValid = true;
    
    // Limpa todos os erros da etapa atual primeiro
    inputs.forEach(input => {
        clearError(input.id);
    });
    
    // Valida campos obrigatórios
    for (let input of inputs) {
        if (!input.value.trim()) {
            const fieldName = input.previousElementSibling?.textContent || 'Este campo';
            showError(input.id.replace('-sign', ''), `${fieldName} é obrigatório`);
            isValid = false;
        }
    }
    
    // Se já tem erro de campo obrigatório, não valida o restante
    if (!isValid) return false;
    
    // Validações específicas por etapa
    if (currentStep === 1) {
        // Valida email
        const email = document.getElementById('email-sign').value;
        if (email && !validateEmail(email)) {
            showError('email', 'Por favor, insira um e-mail válido.');
            isValid = false;
        }
        
        // Valida CPF
        const cpf = document.getElementById('cpf-sign').value;
        if (cpf && !validateCPF(cpf)) {
            showError('cpf', 'Por favor, insira um CPF válido (11 dígitos).');
            isValid = false;
        }
    }
    
    // Validação para etapa de senha (etapa 3)
    if (currentStep === 3) {
        const senha = document.getElementById('senha-sign').value;
        const confirmar = document.getElementById('confirmar-sign').value;

        if (senha && confirmar && senha !== confirmar) {
            showError('confirmar', 'As senhas não coincidem!');
            isValid = false;
        }

        if (senha && senha.length < 6) {
            showError('senha', 'A senha deve ter pelo menos 6 caracteres!');
            isValid = false;
        }
    }
    
    return isValid;
}

// Valida o formulário completo no envio
function validateForm(e) {
    // Valida todos os passos antes do envio
    let allStepsValid = true;
    
    // Valida cada etapa
    for (let step = 1; step <= totalSteps; step++) {
        // Temporariamente muda para a etapa para validá-la
        const tempCurrentStep = currentStep;
        currentStep = step;
        
        if (!validateCurrentStep()) {
            allStepsValid = false;
            // Volta para a etapa com erro
            changeStep(step);
            break;
        }
        
        currentStep = tempCurrentStep;
    }
    
    if (!allStepsValid) {
        e.preventDefault();
        return false;
    }
    
    // Limpa formatação antes do envio
    cleanFormattedFields();
    
    return true;
}

// Limpa formatação antes do envio
function cleanFormattedFields() {
    // Limpa CPF
    const cpfInput = document.getElementById('cpf-sign');
    if (cpfInput) {
        cpfInput.value = cpfInput.value.replace(/\D/g, '');
    }
    
    // Limpa Telefone
    const phoneInput = document.getElementById('telefone-sign');
    if (phoneInput) {
        phoneInput.value = phoneInput.value.replace(/\D/g, '');
    }
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
    
    if (value.length > 11) {
        value = value.substring(0, 11);
    }
    
    if (value.length === 11) {
        // Celular: (99) 99999-9999
        value = value.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
    } else if (value.length === 10) {
        // Fixo: (99) 9999-9999
        value = value.replace(/^(\d{2})(\d{4})(\d{4})$/, '($1) $2-$3');
    } else if (value.length > 6) {
        value = value.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
    } else if (value.length > 2) {
        value = value.replace(/^(\d{2})(\d+)$/, '($1) $2');
    } else if (value.length > 0) {
        value = `(${value}`;
    }
    
    e.target.value = value;
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
    
    // Limpa erro do estado
    clearError('estado');

    cidadesSelect.disabled = true;
    cidadesSelect.innerHTML = "<option value=''>Carregando...</option>";

    if (!uf) {
        cidadesSelect.disabled = true;
        cidadesSelect.innerHTML = "<option value=''>Selecione primeiro um estado</option>";
        return;
    }

    try {
        const resposta = await fetch(
            `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`
        );
        
        if (!resposta.ok) {
            throw new Error('Erro ao buscar cidades');
        }
        
        const dados = await resposta.json();

        cidadesSelect.innerHTML = "<option value=''>Selecione uma cidade</option>";
        cidadesSelect.disabled = false;

        dados.forEach(c => {
            const option = document.createElement("option");
            option.value = c.nome;
            option.textContent = c.nome;
            cidadesSelect.appendChild(option);
        });
    } catch (erro) {
        cidadesSelect.innerHTML = "<option value=''>Erro ao carregar cidades</option>";
        console.error('Erro ao buscar cidades:', erro);
    }
}