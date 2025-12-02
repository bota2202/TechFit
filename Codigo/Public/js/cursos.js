const cursos = [
    {
        id: 1,
        nome: 'Musculação',
        tipo: 'forca',
        descricao: 'Treinos completos para ganho de massa e força com acompanhamento profissional especializado.',
        preco: 99.00,
        imagem: '../Public/Imagens/musculacao.jpg'
    },
    {
        id: 2,
        nome: 'Yoga',
        tipo: 'mente-corpo',
        descricao: 'Equilíbrio, alongamento e bem-estar físico e mental com professores especializados.',
        preco: 149.00,
        imagem: '../Public/Imagens/yoga.jpg'
    },
    {
        id: 3,
        nome: 'Pilates',
        tipo: 'mente-corpo',
        descricao: 'Fortaleça seu corpo e melhore sua postura com aulas modernas de Pilates.',
        preco: 149.00,
        imagem: '../Public/Imagens/pilates.jpg'
    },
    {
        id: 4,
        nome: 'CrossFit',
        tipo: 'forca',
        descricao: 'Treinos intensos de alta performance para resistência e condicionamento físico.',
        preco: 199.00,
        imagem: '../Public/Imagens/crossfit.jpg'
    },
    {
        id: 5,
        nome: 'Spinning',
        tipo: 'cardio',
        descricao: 'Aulas dinâmicas de bike indoor com muita energia e queima calórica intensa.',
        preco: 149.00,
        imagem: '../Public/Imagens/spinning.jpg'
    },
    {
        id: 6,
        nome: 'Zumba',
        tipo: 'cardio',
        descricao: 'Dance, divirta-se e entre em forma com coreografias animadas e intensas.',
        preco: 99.00,
        imagem: '../Public/Imagens/zumba.jpg'
    },
    {
        id: 7,
        nome: 'Muay Thai',
        tipo: 'lutas',
        descricao: 'Defesa pessoal e condicionamento físico com artes marciais de alto impacto.',
        preco: 199.00,
        imagem: '../Public/Imagens/muaythai.jpg'
    },
    {
        id: 8,
        nome: 'Natação',
        tipo: 'cardio',
        descricao: 'Aulas para todas as idades, desenvolvendo resistência e saúde cardiovascular.',
        preco: 149.00,
        imagem:  '../Public/Imagens/natacao.jpg'
    },
    {
        id: 9,
        nome: 'Treinamento Funcional',
        tipo: 'forca',
        descricao: 'Movimentos naturais para melhorar força, coordenação e qualidade de vida.',
        preco: 149.00,
        imagem: '../Public/Imagens/funcional.jpg'
    }
];

let filtroAtivo = 'todos';

function criarCardCurso(curso) {
    const card = document.createElement('div');
    card.className = 'curso-card';
    card.dataset.tipo = curso.tipo;
    
    const tipoLabel = {
        'forca': 'Força',
        'cardio': 'Cardio',
        'mente-corpo': 'Mente-Corpo',
        'lutas': 'Lutas'
    };
    
    card.innerHTML = `
        <img src="${curso.imagem}" alt="${curso.nome}" class="curso-imagem" onerror="this.style.display='none';">
        <div class="curso-content">
            <span class="curso-tipo ${curso.tipo}">${tipoLabel[curso.tipo]}</span>
            <h3 class="curso-nome">${curso.nome}</h3>
            <p class="curso-descricao">${curso.descricao}</p>
            <div class="curso-footer">
                <div class="curso-preco">
                    R$ <span>${curso.preco.toFixed(2).replace('.', ',')}</span>
                </div>
                <button class="btn-ver-turmas" data-curso-id="${curso.id}" data-curso-nome="${curso.nome}">Ver turmas</button>
            </div>
        </div>
    `;
    
    return card;
}

function renderizarCursos() {
    const grid = document.getElementById('cursos-grid');
    if (!grid) return;
    
    grid.innerHTML = '';
    
    const cursosFiltrados = filtroAtivo === 'todos' 
        ? cursos 
        : cursos.filter(curso => curso.tipo === filtroAtivo);
    
    cursosFiltrados.forEach(curso => {
        const card = criarCardCurso(curso);
        grid.appendChild(card);
    });
    
    document.querySelectorAll('.btn-ver-turmas').forEach(btn => {
        btn.addEventListener('click', () => {
            const cursoId = btn.dataset.cursoId;
            const cursoNome = btn.dataset.cursoNome;
            abrirModalTurmas(cursoId, cursoNome);
        });
    });
}

function inicializarFiltros() {
    const botoes = document.querySelectorAll('.filtro-btn');
    
    botoes.forEach(btn => {
        btn.addEventListener('click', () => {
            botoes.forEach(b => b.classList.remove('ativo'));
            btn.classList.add('ativo');
            filtroAtivo = btn.dataset.tipo;
            renderizarCursos();
        });
    });
}

function abrirModalTurmas(cursoId, cursoNome) {
    const modal = document.getElementById('modal-turmas');
    const modalTitulo = document.getElementById('modal-titulo');
    const modalConteudo = document.getElementById('modal-conteudo');
    
    if (!modal || !modalTitulo || !modalConteudo) return;
    
    modalTitulo.textContent = `Turmas - ${cursoNome}`;
    modalConteudo.innerHTML = `
        <div class="modal-mensagem">
            <i class="fas fa-info-circle"></i>
            <p>As turmas serão exibidas aqui em breve.</p>
            <p style="margin-top: 10px; font-size: 0.9rem; color: #666;">Em desenvolvimento...</p>
        </div>
    `;
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function fecharModalTurmas() {
    const modal = document.getElementById('modal-turmas');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function inicializar() {
    renderizarCursos();
    inicializarFiltros();
    
    const modal = document.getElementById('modal-turmas');
    const btnFechar = document.querySelector('.modal-fechar');
    
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                fecharModalTurmas();
            }
        });
    }
    
    if (btnFechar) {
        btnFechar.addEventListener('click', fecharModalTurmas);
    }
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            fecharModalTurmas();
        }
    });
}

document.addEventListener('DOMContentLoaded', inicializar);

