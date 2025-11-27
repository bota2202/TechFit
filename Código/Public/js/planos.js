async function carregarPlanos() {
    try {
        const response = await fetch('../DB/api_planos.php');
        const data = await response.json();
        
        if (!data.success || !data.planos) {
            console.error('Erro ao carregar planos');
            return;
        }
        
        const planos = data.planos;
        const nomes = ['Básico', 'Intermediário', 'Premium'];
        
        function criarCardPlano(plano, index) {
            const card = document.createElement('div');
            const nome = nomes[index] || `Plano ${plano.id_plano}`;
            const destaque = index === 1; // Intermediário é destaque
            
            card.className = `plano-card ${destaque ? 'destaque' : ''}`;
            
            // Extrair benefícios da descrição
            const beneficios = plano.descricao_plano.split(',').map(b => b.trim());
            
            card.innerHTML = `
                <h2 class="plano-nome">${nome}</h2>
                <div class="plano-preco">
                    <div class="plano-preco-valor">
                        R$ <span>${parseFloat(plano.preco_plano).toFixed(2).replace('.', ',')}</span>
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
                <button class="btn-assinar">Assinar Agora</button>
            `;
            
            return card;
        }
        
        function renderizarPlanos() {
            const grid = document.getElementById('planos-grid');
            if (!grid) return;
            
            grid.innerHTML = '';
            
            planos.forEach((plano, index) => {
                const card = criarCardPlano(plano, index);
                grid.appendChild(card);
            });
        }
        
        renderizarPlanos();
    } catch (error) {
        console.error('Erro ao carregar planos:', error);
    }
}

document.addEventListener('DOMContentLoaded', carregarPlanos);

