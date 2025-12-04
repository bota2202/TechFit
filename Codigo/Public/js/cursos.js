let cursos = [];
const imagensCursos = {
    'Musculação': '../Public/Imagens/musculacao.jpg',
    'Yoga': '../Public/Imagens/yoga.jpg',
    'Pilates': '../Public/Imagens/pilates.jpg',
    'CrossFit': '../Public/Imagens/crossfit.jpg',
    'Spinning': '../Public/Imagens/spinning.jpg',
    'Zumba': '../Public/Imagens/zumba.jpg',
    'Muay Thai': '../Public/Imagens/muaythai.jpg',
    'Natação': '../Public/Imagens/natacao.jpg',
    'Treinamento Funcional': '../Public/Imagens/funcional.jpg'
};

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
    
    const imagem = imagensCursos[curso.nome] || '../Public/Imagens/musculacao.jpg';
    
    card.innerHTML = `
        <img src="${imagem}" alt="${curso.nome}" class="curso-imagem" onerror="this.style.display='none';">
        <div class="curso-content">
            <span class="curso-tipo ${curso.tipo}">${tipoLabel[curso.tipo] || curso.tipo}</span>
            <h3 class="curso-nome">${curso.nome}</h3>
            <p class="curso-descricao">${curso.descricao || ''}</p>
            <div class="curso-footer">
                <div class="curso-preco">
                    R$ <span>${parseFloat(curso.preco).toFixed(2).replace('.', ',')}</span>
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
    
    grid.innerHTML = '<div class="loading">Carregando cursos...</div>';
    
    const url = window.location.pathname.includes('/Codigo/View/') 
        ? '../../index.php?action=listar-cursos' 
        : 'index.php?action=listar-cursos';
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta do servidor: ' + response.status);
            }
            return response.text();
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.erro) {
                    throw new Error(data.erro);
                }
                cursos = data;
                grid.innerHTML = '';
                
                if (cursos.length === 0) {
                    grid.innerHTML = '<p class="text-center">Nenhum curso disponível no momento.</p>';
                    return;
                }
                
                const cursosFiltrados = filtroAtivo === 'todos' 
                    ? cursos 
                    : cursos.filter(curso => curso.tipo === filtroAtivo);
                
                cursosFiltrados.forEach(curso => {
                    const card = criarCardCurso(curso);
                    grid.appendChild(card);
                });
            } catch (e) {
                console.error('Erro ao parsear JSON:', e, 'Resposta:', text);
                grid.innerHTML = '<p class="text-center text-danger">Erro ao carregar cursos. Tente novamente.</p>';
            }
        })
        .catch(error => {
            console.error('Erro ao carregar cursos:', error);
            grid.innerHTML = '<p class="text-center text-danger">Erro ao carregar cursos. Tente novamente.</p>';
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
    modalConteudo.innerHTML = '<div class="loading">Carregando turmas...</div>';
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    const url = window.location.pathname.includes('/Codigo/View/') 
        ? `../../index.php?action=listar-turmas-curso&id_curso=${cursoId}` 
        : `index.php?action=listar-turmas-curso&id_curso=${cursoId}`;
    
    fetch(url)
        .then(response => response.text())
        .then(text => {
            try {
                const turmas = JSON.parse(text);
                if (turmas.erro) {
                    throw new Error(turmas.erro);
                }
                if (!Array.isArray(turmas)) {
                    throw new Error('Resposta inválida: não é um array');
                }
                
                if (turmas.length === 0) {
                    modalConteudo.innerHTML = `
                        <div class="modal-mensagem">
                            <i class="fas fa-info-circle"></i>
                            <p>Nenhuma turma disponível para este curso no momento.</p>
                        </div>
                    `;
                    return;
                }
                
                const temPlanoAtivo = turmas.length > 0 && turmas[0].tem_plano_ativo;
                
                if (!temPlanoAtivo) {
                    modalConteudo.innerHTML = `
                        <div class="modal-mensagem" style="background: #fff3cd; border-color: #ffc107;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p><strong>Você precisa ter um plano ativo para se matricular em cursos!</strong></p>
                            <p style="margin-top: 10px;">Acesse a página de <a href="planos.php" style="color: #11998e; font-weight: bold;">Planos</a> para contratar um plano.</p>
                        </div>
                    `;
                    return;
                }
                
                let html = '<div class="turmas-lista">';
                turmas.forEach(turma => {
                    let dataInicio, dataFim;
                    try {
                        if (turma.data_inicio && turma.data_inicio !== '0000-00-00' && turma.data_inicio !== '0000-00-00 00:00:00') {
                            dataInicio = new Date(turma.data_inicio);
                        } else {
                            dataInicio = new Date();
                        }
                        if (turma.data_fim && turma.data_fim !== '0000-00-00' && turma.data_fim !== '0000-00-00 00:00:00') {
                            dataFim = new Date(turma.data_fim);
                        } else {
                            dataFim = new Date();
                            dataFim.setMonth(dataFim.getMonth() + 1);
                        }
                    } catch (e) {
                        dataInicio = new Date();
                        dataFim = new Date();
                        dataFim.setMonth(dataFim.getMonth() + 1);
                    }
                    const disponivel = turma.disponivel && !turma.ja_matriculado;
                    
                    html += `
                        <div class="turma-item ${!disponivel ? 'indisponivel' : ''}">
                            <div class="turma-info">
                                <h4>${turma.nome}</h4>
                                <p><i class="fas fa-clock"></i> ${turma.horario || 'Horário a definir'}</p>
                                <p><i class="fas fa-calendar"></i> ${dataInicio.toLocaleDateString('pt-BR')} - ${dataFim.toLocaleDateString('pt-BR')}</p>
                                <p><i class="fas fa-users"></i> ${turma.ocupacao}/${turma.capacidade} alunos</p>
                            </div>
                            <div class="turma-acoes">
                                ${turma.ja_matriculado ? 
                                    '<span class="badge badge-success">Você está matriculado</span>' :
                                    (turma.disponivel ? 
                                        `<button class="btn-matricular" data-turma-id="${turma.id}" data-turma-nome="${turma.nome}">
                                            <i class="fas fa-user-plus"></i> Matricular-se
                                        </button>` :
                                        '<span class="badge badge-danger">Turma lotada</span>'
                                    )
                                }
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                modalConteudo.innerHTML = html;
            } catch (e) {
                console.error('Erro ao processar turmas:', e);
                modalConteudo.innerHTML = `
                    <div class="modal-mensagem">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Erro ao carregar turmas. Tente novamente.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Erro ao carregar turmas:', error);
            modalConteudo.innerHTML = `
                <div class="modal-mensagem">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Erro ao carregar turmas. Tente novamente.</p>
                </div>
            `;
        });
}

function matricularEmTurma(idTurma) {
    const currentPath = window.location.pathname;
    let actionUrl = 'index.php?action=matricular';
    
    if (currentPath.includes('/Codigo/View/')) {
        actionUrl = '../../index.php?action=matricular';
    } else if (currentPath.includes('/Codigo/')) {
        actionUrl = '../index.php?action=matricular';
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = actionUrl;
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'id_turma';
    input.value = idTurma;
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}

function fecharModalTurmas() {
    const modal = document.getElementById('modal-turmas');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Delegação de eventos para elementos dinâmicos
document.addEventListener('click', function(e) {
    // Botão ver turmas
    if (e.target.closest('.btn-ver-turmas')) {
        const btn = e.target.closest('.btn-ver-turmas');
        const cursoId = btn.getAttribute('data-curso-id');
        const cursoNome = btn.getAttribute('data-curso-nome');
        if (cursoId && cursoNome) {
            abrirModalTurmas(cursoId, cursoNome);
        }
    }
    
    // Botão matricular
    if (e.target.closest('.btn-matricular')) {
        const btn = e.target.closest('.btn-matricular');
        e.preventDefault();
        e.stopPropagation();
        
        const turmaId = btn.getAttribute('data-turma-id');
        const turmaNome = btn.getAttribute('data-turma-nome');
        
        if (!turmaId) return;
        
        if (typeof window.mostrarConfirmacao === 'function') {
            window.mostrarConfirmacao(
                'Confirmar Matrícula',
                `Deseja realmente se matricular na turma "${turmaNome}"?`,
                function() {
                    matricularEmTurma(turmaId);
                }
            );
        } else {
            if (confirm(`Deseja realmente se matricular na turma "${turmaNome}"?`)) {
                matricularEmTurma(turmaId);
            }
        }
    }
    
    // Fechar modal
    if (e.target.classList.contains('modal-fechar') || (e.target.closest('.modal-overlay') && e.target.classList.contains('modal-overlay'))) {
        fecharModalTurmas();
    }
});

function inicializar() {
    renderizarCursos();
    inicializarFiltros();
    
    const modal = document.getElementById('modal-turmas');
    const btnFechar = document.querySelector('.modal-fechar');
    
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
