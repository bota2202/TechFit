const planos = [
    {
        id: 1,
        nome: 'Básico',
        preco: 99.00,
        descricao: 'Acesso à 1 unidade, Musculação, App TechFit, sem aulas coletivas',
        beneficios: [
            'Acesso à 1 unidade',
            'Acesso à Musculação',
            'App TechFit',
        ]
    },
    {
        id: 2,
        nome: 'Intermediário',
        preco: 149.00,
        descricao: 'Acesso à todas unidades, Até 3 cursos, Aulas coletivas, App TechFit',
        beneficios: [
            'Acesso à todas as unidades',
            'Até 3 cursos',
            'Aulas coletivas limitadas',
            'App TechFit Standard'
        ],
        destaque: true
    },
    {
        id: 3,
        nome: 'Premium',
        preco: 199.00,
        descricao: 'Acesso à todas unidades, Todos os cursos a vontade, Aulas coletivas, App TechFit pro',
        beneficios: [
            'Acesso à todas as unidades',
            'Todos os cursos à vontade',
            'Aulas coletivas ilimitadas',
            'App TechFit Pro'
        ]
    }
];

function criarCardPlano(plano) {
    const card = document.createElement('div');
    card.className = `plano-card ${plano.destaque ? 'destaque' : ''}`;
    
    card.innerHTML = `
        <h2 class="plano-nome">${plano.nome}</h2>
        <div class="plano-preco">
            <div class="plano-preco-valor">
                R$ <span>${plano.preco.toFixed(2).replace('.', ',')}</span>
            </div>
            <div class="plano-preco-periodo">por mês</div>
        </div>
        <div class="plano-beneficios">
            <h3>Benefícios:</h3>
            ${plano.beneficios.map(beneficio => `
                <div class="beneficio-item">
                    <i class="fas fa-check-circle"></i>
                    <span>${beneficio}</span>
                </div>
            `).join('')}
        </div>
        <button class="btn-assinar">Assinar Agora</button>
    `;
    
    return card;
}

function renderizarPlanos() {
    const grid = document.getElementById('planos-grid');
    if (!grid) return;
    
    grid.innerHTML = '';
    
    planos.forEach(plano => {
        const card = criarCardPlano(plano);
        grid.appendChild(card);
    });
}

document.addEventListener('DOMContentLoaded', renderizarPlanos);

