// Dados dos cursos
const cursos = [
    {
        id: 1,
        nome: "Musculação",
        categoria: "forca",
        descricao: "Treinos completos para ganho de massa e força com acompanhamento profissional especializado.",
        imagem: "../Imagens/musculacao.jpg",
        intensidade: "Alta",
        duracao: "60 min",
        nivel: "Todos",
        instrutor: "Carlos Silva"
    },
    {
        id: 2,
        nome: "Yoga",
        categoria: "mente-corpo",
        descricao: "Equilíbrio, alongamento e bem-estar físico e mental com professores especializados.",
        imagem: "../Imagens/yoga.jpg",
        intensidade: "Baixa",
        duracao: "75 min",
        nivel: "Todos",
        instrutor: "Ana Costa"
    },
    {
        id: 3,
        nome: "Pilates",
        categoria: "mente-corpo",
        descricao: "Fortaleça seu corpo e melhore sua postura com aulas modernas de Pilates.",
        imagem: "../Imagens/pilates.jpg",
        intensidade: "Média",
        duracao: "55 min",
        nivel: "Todos",
        instrutor: "Mariana Lima"
    },
    {
        id: 4,
        nome: "CrossFit",
        categoria: "forca",
        descricao: "Treinos intensos de alta performance para resistência e condicionamento físico.",
        imagem: "../Imagens/crossfit.jpg",
        intensidade: "Alta",
        duracao: "60 min",
        nivel: "Intermediário",
        instrutor: "Ricardo Santos"
    },
    {
        id: 5,
        nome: "Spinning",
        categoria: "cardio",
        descricao: "Aulas dinâmicas de bike indoor com muita energia e queima calórica intensa.",
        imagem: "../Imagens/spinning.jpg",
        intensidade: "Alta",
        duracao: "45 min",
        nivel: "Todos",
        instrutor: "Paula Oliveira"
    },
    {
        id: 6,
        nome: "Zumba",
        categoria: "cardio",
        descricao: "Dance, divirta-se e entre em forma com coreografias animadas e intensas.",
        imagem: "../Imagens/zumba.jpg",
        intensidade: "Média",
        duracao: "50 min",
        nivel: "Iniciante",
        instrutor: "Fernanda Rocha"
    },
    {
        id: 7,
        nome: "Muay Thai",
        categoria: "lutas",
        descricao: "Defesa pessoal e condicionamento físico com artes marciais de alto impacto.",
        imagem: "../Imagens/muaythai.jpg",
        intensidade: "Alta",
        duracao: "70 min",
        nivel: "Todos",
        instrutor: "Thiago Alves"
    },
    {
        id: 8,
        nome: "Natação",
        categoria: "cardio",
        descricao: "Aulas para todas as idades, desenvolvendo resistência e saúde cardiovascular.",
        imagem: "../Imagens/natacao.jpg",
        intensidade: "Média",
        duracao: "45 min",
        nivel: "Todos",
        instrutor: "Roberto Ferreira"
    },
    {
        id: 9,
        nome: "Treinamento Funcional",
        categoria: "forca",
        descricao: "Movimentos naturais para melhorar força, coordenação e qualidade de vida.",
        imagem: "../Imagens/funcional.jpg",
        intensidade: "Alta",
        duracao: "55 min",
        nivel: "Intermediário",
        instrutor: "Juliana Mendes"
    }
];

// Categorias e cores
const categorias = {
    'forca': { nome: 'Força', cor: '#e74c3c' },
    'cardio': { nome: 'Cardio', cor: '#3498db' },
    'lutas': { nome: 'Lutas', cor: '#e67e22' },
    'mente-corpo': { nome: 'Mente & Corpo', cor: '#9b59b6' }
};

// Gradientes de fallback
const gradientBackgrounds = [
    'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
    'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
    'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
    'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
    'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)'
];

// Função para gerar imagem do curso com fallback
function generateCursoImage(curso, index) {
    const gradient = gradientBackgrounds[index % gradientBackgrounds.length];
    const categoria = categorias[curso.categoria];
    
    return `
        <div class="curso-imagem">
            <img src="${curso.imagem}" alt="${curso.nome}" 
                 onerror="this.style.display='none'; this.parentNode.innerHTML = fallbackCursoImage('${curso.nome}', ${index})">
            <div class="curso-categoria" style="background: ${categoria.cor}">
                ${categoria.nome}
            </div>
        </div>
    `;
}

// Fallback para imagens de cursos
function fallbackCursoImage(nome, index) {
    const gradient = gradientBackgrounds[index % gradientBackgrounds.length];
    const icons = ['fas fa-dumbbell', 'fas fa-spa', 'fas fa-running', 'fas fa-fire', 'fas fa-heartbeat', 'fas fa-music'];
    const icon = icons[index % icons.length];
    
    return `
        <div class="curso-imagem-fallback" style="background: ${gradient}">
            <i class="${icon}"></i>
            <div class="curso-nome-fallback">${nome}</div>
        </div>
    `;
}

// Função para renderizar cursos
function renderCursos(cursosToRender = cursos) {
    const grid = document.getElementById('cursosGrid');
    grid.innerHTML = '';

    if (cursosToRender.length === 0) {
        grid.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x mb-3" style="color: var(--cinza);"></i>
                <h4 style="color: var(--prata);">Nenhum curso encontrado</h4>
                <p style="color: var(--cinza);">Tente selecionar outra categoria</p>
            </div>
        `;
        return;
    }

    cursosToRender.forEach((curso, index) => {
        const card = document.createElement('div');
        card.className = 'col-lg-6 col-xl-4';
        card.innerHTML = `
            <div class="curso-card">
                ${generateCursoImage(curso, index)}
                <div class="curso-content">
                    <h3>${curso.nome}</h3>
                    <p class="curso-descricao">${curso.descricao}</p>
                    
                    <div class="curso-info">
                        <div class="info-item">
                            <i class="fas fa-bolt"></i>
                            <span>${curso.intensidade}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>${curso.duracao}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-chart-line"></i>
                            <span>${curso.nivel}</span>
                        </div>
                    </div>
                    
                    <div class="curso-actions">
                        <button class="btn-curso btn-inscrever" onclick="inscreverCurso(${curso.id})">
                            <i class="fas fa-play-circle"></i> Inscrever-se
                        </button>
                        <button class="btn-curso btn-detalhes" onclick="showCursoDetails(${curso.id})">
                            <i class="fas fa-info-circle"></i> Detalhes
                        </button>
                    </div>
                </div>
            </div>
        `;
        grid.appendChild(card);
    });
}

// Função para mostrar detalhes do curso
function showCursoDetails(cursoId) {
    const curso = cursos.find(c => c.id === cursoId);
    if (!curso) return;

    const categoria = categorias[curso.categoria];
    
    const modalHtml = `
        <div class="modal fade" id="cursoModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${curso.nome}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Informações do Curso</h6>
                                <p><strong>Categoria:</strong> <span style="color: ${categoria.cor}">${categoria.nome}</span></p>
                                <p><strong>Intensidade:</strong> ${curso.intensidade}</p>
                                <p><strong>Duração:</strong> ${curso.duracao}</p>
                                <p><strong>Nível:</strong> ${curso.nivel}</p>
                                <p><strong>Instrutor:</strong> ${curso.instrutor}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Descrição</h6>
                                <p>${curso.descricao}</p>
                                <h6 class="mt-3">Benefícios</h6>
                                <ul>
                                    <li>Melhora do condicionamento físico</li>
                                    <li>Acompanhamento profissional</li>
                                    <li>Ambiente motivador</li>
                                    <li>Resultados mensuráveis</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button class="btn btn-green" onclick="inscreverCurso(${curso.id})">
                            <i class="fas fa-play-circle"></i> Inscrever-se Agora
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    const existingModal = document.getElementById('cursoModal');
    if (existingModal) {
        existingModal.remove();
    }

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    const modal = new bootstrap.Modal(document.getElementById('cursoModal'));
    modal.show();
}

// Função para inscrição no curso
function inscreverCurso(cursoId) {
    const curso = cursos.find(c => c.id === cursoId);
    if (!curso) return;

    alert(`Inscrição no curso ${curso.nome} realizada com sucesso!\n\nEm breve nossa equipe entrará em contato.`);
}

// Filtros de categoria
function setupFilters() {
    const filterButtons = document.querySelectorAll('.filtro-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active de todos os botões
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Adiciona active no botão clicado
            this.classList.add('active');
            
            const categoria = this.getAttribute('data-categoria');
            
            if (categoria === 'todos') {
                renderCursos();
            } else {
                const filteredCursos = cursos.filter(curso => curso.categoria === categoria);
                renderCursos(filteredCursos);
            }
        });
    });
}

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    renderCursos();
    setupFilters();
});