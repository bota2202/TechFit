let planos = [];
let temPlanoAtivo = false;

function parsearBeneficios(descricao) {
    if (!descricao) return [];
    const beneficios = descricao.split(',').map(b => b.trim());
    return beneficios;
}

function criarCardPlano(plano, temPlanoAtivo) {
    const card = document.createElement('div');
    const preco = parseFloat(plano.preco);
    const nomePlano = preco <= 100 ? 'Básico' : (preco <= 150 ? 'Intermediário' : 'Premium');
    const destaque = nomePlano === 'Intermediário';
    
    card.className = `plano-card ${destaque ? 'destaque' : ''}`;
    
    const beneficios = parsearBeneficios(plano.descricao);
    
    card.innerHTML = `
        <h2 class="plano-nome">${nomePlano}</h2>
        <div class="plano-preco">
            <div class="plano-preco-valor">
                R$ <span>${preco.toFixed(2).replace('.', ',')}</span>
            </div>
            <div class="plano-preco-periodo">por mês</div>
        </div>
        <div class="plano-beneficios">
            <h3>Benefícios:</h3>
            ${beneficios.map(beneficio => `
                <div class="beneficio-item">
                    <i class="fas fa-check-circle"></i>
                    <span>${beneficio}</span>
                </div>
            `).join('')}
        </div>
        ${temPlanoAtivo ? 
            '<button class="btn-assinar" disabled style="opacity: 0.5; cursor: not-allowed;">Você já possui um plano ativo</button>' :
            `<button class="btn-assinar" data-plano-id="${plano.id}">Assinar Agora</button>`
        }
    `;
    
    if (!temPlanoAtivo) {
        const btnAssinar = card.querySelector('.btn-assinar');
        btnAssinar.addEventListener('click', () => {
            mostrarConfirmacao(
                'Confirmar Assinatura',
                `Deseja realmente assinar o plano ${nomePlano} por R$ ${preco.toFixed(2).replace('.', ',')}?`,
                () => assinarPlano(plano.id)
            );
        });
    }
    
    return card;
}

function assinarPlano(idPlano) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../../index.php?action=contratar-plano';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'id_plano';
    input.value = idPlano;
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}

function renderizarPlanos() {
    const grid = document.getElementById('planos-grid');
    if (!grid) return;
    
    grid.innerHTML = '<div class="loading">Carregando planos...</div>';
    
    const url = window.location.pathname.includes('/Codigo/View/') 
        ? '../../index.php?action=listar-planos' 
        : 'index.php?action=listar-planos';
    
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
                planos = data;
                grid.innerHTML = '';
                
                if (planos.length === 0) {
                    grid.innerHTML = '<p class="text-center">Nenhum plano disponível no momento.</p>';
                    return;
                }
                
                planos.forEach(plano => {
                    const card = criarCardPlano(plano, temPlanoAtivo);
                    grid.appendChild(card);
                });
            } catch (e) {
                console.error('Erro ao parsear JSON:', e, 'Resposta:', text);
                throw new Error('Resposta inválida do servidor');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar planos:', error);
            grid.innerHTML = '<p class="text-center text-danger">Erro ao carregar planos. Tente novamente.</p>';
        });
}

document.addEventListener('DOMContentLoaded', () => {
    temPlanoAtivo = document.body.dataset.temPlanoAtivo === '1';
    renderizarPlanos();
});

