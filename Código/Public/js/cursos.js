let cursos = [];
const imagensMap = {
    'Musculação': '../Imagens/musculacao.jpg',
    'Yoga': '../Imagens/yoga.jpg',
    'Pilates': '../Imagens/pilates.jpg',
    'CrossFit': '../Imagens/crossfit.jpg',
    'Spinning': '../Imagens/spinning.jpg',
    'Zumba': '../Imagens/zumba.jpg',
    'Muay Thai': '../Imagens/muaythai.jpg',
    'Natação': '../Imagens/natacao.jpg',
    'Treinamento Funcional': '../Imagens/funcional.jpg'
};

let filtroAtivo = 'todos';

async function carregarCursos() {
    try {
        const response = await fetch('../DB/api_cursos.php');
        const data = await response.json();
        
        if (!data.success || !data.cursos) {
            console.error('Erro ao carregar cursos');
            return;
        }
        
        cursos = data.cursos;
        renderizarCursos();
    } catch (error) {
        console.error('Erro ao carregar cursos:', error);
    }
}

function criarCardCurso(curso) {
    const card = document.createElement('div');
    card.className = 'curso-card';
    card.dataset.tipo = curso.tipo_curso;
    
    const tipoLabel = {
        'forca': 'Força',
        'cardio': 'Cardio',
        'mente-corpo': 'Mente-Corpo',
        'lutas': 'Lutas'
    };
    
    const imagem = imagensMap[curso.nome_curso] || '../Imagens/fundo.jpg';
    
    card.innerHTML = `
        <img src="${imagem}" alt="${curso.nome_curso}" class="curso-imagem" onerror="this.style.display='none';">
        <div class="curso-content">
            <span class="curso-tipo ${curso.tipo_curso}">${tipoLabel[curso.tipo_curso] || curso.tipo_curso}</span>
            <h3 class="curso-nome">${curso.nome_curso}</h3>
            <p class="curso-descricao">${curso.descricao_curso || ''}</p>
            <div class="curso-footer">
                <div class="curso-preco">
                    R$ <span>${parseFloat(curso.preco_curso).toFixed(2).replace('.', ',')}</span>
                </div>
                <button class="btn-ver-turmas" data-curso-id="${curso.id_curso}" data-curso-nome="${curso.nome_curso}">Ver turmas</button>
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

async function abrirModalTurmas(cursoId, cursoNome) {
    const modal = document.getElementById('modal-turmas');
    const modalTitulo = document.getElementById('modal-titulo');
    const modalConteudo = document.getElementById('modal-conteudo');
    
    if (!modal || !modalTitulo || !modalConteudo) return;
    
    modalTitulo.textContent = `Turmas - ${cursoNome}`;
    modalConteudo.innerHTML = '<p>Carregando turmas...</p>';
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    try {
        const response = await fetch(`../DB/api_turmas.php?curso_id=${cursoId}`);
        const data = await response.json();
        
        if (data.success && data.turmas && data.turmas.length > 0) {
            modalConteudo.innerHTML = data.turmas.map(turma => {
                const dataInicio = new Date(turma.data_inicio);
                const dataFim = new Date(turma.data_fim);
                return `
                    <div style="padding: 15px; border-bottom: 1px solid #ddd; margin-bottom: 10px;">
                        <h4>${turma.nome_turma}</h4>
                        <p><strong>Horário:</strong> ${turma.horario_turma || 'Não definido'}</p>
                        <p><strong>Início:</strong> ${dataInicio.toLocaleDateString('pt-BR')}</p>
                        <p><strong>Fim:</strong> ${dataFim.toLocaleDateString('pt-BR')}</p>
                        ${turma.responsavel_nome ? `<p><strong>Responsável:</strong> ${turma.responsavel_nome}</p>` : ''}
                    </div>
                `;
            }).join('');
        } else {
            modalConteudo.innerHTML = `
                <div class="modal-mensagem">
                    <i class="fas fa-info-circle"></i>
                    <p>Nenhuma turma disponível para este curso no momento.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Erro ao carregar turmas:', error);
        modalConteudo.innerHTML = `
            <div class="modal-mensagem">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Erro ao carregar turmas. Tente novamente mais tarde.</p>
            </div>
        `;
    }
}

function fecharModalTurmas() {
    const modal = document.getElementById('modal-turmas');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function inicializar() {
    carregarCursos();
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

