const ADMIN_STORAGE_KEY_UNIDADES = 'techfit_unidades';
let unidades = JSON.parse(localStorage.getItem(ADMIN_STORAGE_KEY_UNIDADES) || 'null');
if (!Array.isArray(unidades) || unidades.length === 0) {
const unidadesDefault = [
    {
        id: 1,
        cidade: "Limeira",
        bairro: "Centro",
        endereco: "Av. Campinas, 1500 - Centro",
        telefone: "(19) 3451-1000",
        horario: "24h",
        vagas: 50,
        matriz: true,
        imagem: "../Imagens/limeira.jpg"
    },
    {
        id: 2,
        cidade: "Campinas",
        bairro: "Centro",
        endereco: "R. Barão de Jaguara, 900 - Centro",
        telefone: "(19) 3234-5600",
        horario: "24h",
        vagas: 80,
        matriz: false,
        imagem: "../Imagens/campinas.jpg"
    },
    {
        id: 3,
        cidade: "Piracicaba",
        bairro: "Centro",
        endereco: "Av. Independência, 2100 - Centro",
        telefone: "(19) 3422-7800",
        horario: "24h",
        vagas: 60,
        matriz: false,
        imagem: "../Imagens/piracicaba.jpg"
    },
    {
        id: 4,
        cidade: "Sorocaba",
        bairro: "Centro",
        endereco: "R. XV de Novembro, 450 - Centro",
        telefone: "(15) 3225-8900",
        horario: "24h",
        vagas: 70,
        matriz: false,
        imagem: "../Imagens/sorocaba.jpg"
    },
    {
        id: 5,
        cidade: "Ribeirão Preto",
        bairro: "Jardim Paulista",
        endereco: "Av. Pres. Vargas, 1800 - Jardim Paulista",
        telefone: "(16) 3624-1200",
        horario: "24h",
        vagas: 90,
        matriz: false,
        imagem: "../Imagens/ribeirao.jpg"
    },
    {
        id: 6,
        cidade: "Araraquara",
        bairro: "Centro",
        endereco: "Av. Bento de Abreu, 300 - Centro",
        telefone: "(16) 3321-4500",
        horario: "24h",
        vagas: 55,
        matriz: false,
        imagem: "../Imagens/araraquara.jpg"
    },
    {
        id: 7,
        cidade: "São Carlos",
        bairro: "Centro",
        endereco: "R. Episcopal, 1200 - Centro",
        telefone: "(16) 3378-9900",
        horario: "24h",
        vagas: 65,
        matriz: false,
        imagem: "../Imagens/saocarlos.jpg"
    },
    {
        id: 8,
        cidade: "Jundiaí",
        bairro: "Anhangabaú",
        endereco: "Av. Jundiaí, 500 - Anhangabaú",
        telefone: "(11) 4521-3300",
        horario: "24h",
        vagas: 75,
        matriz: false,
        imagem: "../Imagens/jundiai.jpg"
    },
    {
        id: 9,
        cidade: "Bauru",
        bairro: "Centro",
        endereco: "Av. Getúlio Vargas, 100 - Centro",
        telefone: "(14) 3234-5600",
        horario: "24h",
        vagas: 60,
        matriz: false,
        imagem: "../Imagens/bauru.jpg"
    },
    {
        id: 10,
        cidade: "São José do Rio Preto",
        bairro: "Boa Vista",
        endereco: "R. Bernardino, 950 - Boa Vista",
        telefone: "(17) 3321-7800",
        horario: "24h",
        vagas: 85,
        matriz: false,
        imagem: "../Imagens/riopreto.jpg"
    },
    {
        id: 11,
        cidade: "Marília",
        bairro: "Fragata",
        endereco: "Av. das Esmeraldas, 400 - Fragata",
        telefone: "(14) 3456-7800",
        horario: "24h",
        vagas: 45,
        matriz: false,
        imagem: "../Imagens/marilia.jpg"
    },
    {
        id: 12,
        cidade: "Presidente Prudente",
        bairro: "Vila Nova",
        endereco: "Av. Manoel Goulart, 1300 - Vila Nova",
        telefone: "(18) 3221-4500",
        horario: "24h",
        vagas: 55,
        matriz: false,
        imagem: "../Imagens/presidente.jpg"
    },
    {
        id: 13,
        cidade: "Americana",
        bairro: "Centro",
        endereco: "R. Fernando Camargo, 800 - Centro",
        telefone: "(19) 3678-9900",
        horario: "24h",
        vagas: 40,
        matriz: false,
        imagem: "../Imagens/americana.jpg"
    },
    {
        id: 14,
        cidade: "Indaiatuba",
        bairro: "Cidade Nova",
        endereco: "Av. Pres. Kennedy, 700 - Cidade Nova",
        telefone: "(19) 3875-1200",
        horario: "24h",
        vagas: 65,
        matriz: false,
        imagem: "../Imagens/indaiatuba.jpg"
    },
    {
        id: 15,
        cidade: "Barueri",
        bairro: "Alphaville",
        endereco: "Av. Arnaldo Rodrigues, 250 - Alphaville",
        telefone: "(11) 4201-7800",
        horario: "24h",
        vagas: 100,
        matriz: false,
        imagem: "../Imagens/barueri.jpg"
    }
];
unidades = unidadesDefault;
try { window.unidadesDefault = unidadesDefault; } catch (_) {}
}

const gradientBackgrounds = [
    'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
    'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
    'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
    'linear-gradient(135deg, #fa709a 0%, #fee140 100%)'
];

function generateUnidadeImage(unidade, index) {
    const gradient = gradientBackgrounds[index % gradientBackgrounds.length];
    
    return `
        <div class="unidade-imagem">
            <img src="${unidade.imagem}" alt="TechFit ${unidade.cidade}" 
                 onerror="this.style.display='none'; this.parentNode.innerHTML = fallbackImage('${unidade.cidade}', ${index})">
            ${unidade.matriz ? '<div class="unidade-badge">MATRIZ</div>' : ''}
        </div>
    `;
}

function fallbackImage(cidade, index) {
    const gradient = gradientBackgrounds[index % gradientBackgrounds.length];
    const icons = ['fas fa-dumbbell', 'fas fa-running', 'fas fa-weight-hanging', 'fas fa-fire', 'fas fa-heartbeat'];
    const icon = icons[index % icons.length];
    
    return `
        <div class="unidade-imagem-fallback" style="background: ${gradient}">
            <i class="${icon}"></i>
            <div class="unidade-cidade">${cidade}</div>
        </div>
    `;
}

function renderUnidades(unidadesToRender = unidades) {
    const grid = document.getElementById('unidadesGrid');
    grid.innerHTML = '';

    if (unidadesToRender.length === 0) {
        grid.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x mb-3" style="color: var(--cinza);"></i>
                <h4 style="color: var(--prata);">Nenhuma unidade encontrada</h4>
                <p style="color: var(--cinza);">Tente buscar por outra cidade ou bairro</p>
            </div>
        `;
        return;
    }

    unidadesToRender.forEach((unidade, index) => {
        const card = document.createElement('div');
        card.className = 'col-lg-6 col-xl-4';
        card.innerHTML = `
            <div class="unidade-card">
                ${generateUnidadeImage(unidade, index)}
                <div class="unidade-content">
                    <h3>${unidade.cidade}</h3>
                    <p class="unidade-bairro">${unidade.bairro}</p>
                    <p class="unidade-endereco">${unidade.endereco}</p>
                    
                    <div class="unidade-info">
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>${unidade.horario}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span>${unidade.telefone}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-car"></i>
                            <span>${unidade.vagas} vagas</span>
                        </div>
                    </div>
                    
                    <div class="unidade-actions">
                        <a href="https://maps.google.com/?q=${encodeURIComponent(unidade.endereco + ', ' + unidade.cidade)}" 
                           target="_blank" class="btn-unidade btn-visitar">
                            <i class="fas fa-directions"></i> Como Chegar
                        </a>
                        <button class="btn-unidade btn-detalhes" onclick="showUnidadeDetails(${unidade.id})">
                            <i class="fas fa-info-circle"></i> Detalhes
                        </button>
                    </div>
                </div>
            </div>
        `;
        grid.appendChild(card);
    });
}

function showUnidadeDetails(unidadeId) {
    const unidade = unidades.find(u => u.id === unidadeId);
    if (!unidade) return;

    const modalHtml = `
        <div class="modal fade" id="unidadeModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${unidade.cidade} - ${unidade.bairro}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Informações</h6>
                                <p><strong>Endereço:</strong> ${unidade.endereco}</p>
                                <p><strong>Telefone:</strong> ${unidade.telefone}</p>
                                <p><strong>Horário:</strong> ${unidade.horario}</p>
                                <p><strong>Vagas:</strong> ${unidade.vagas} estacionamento</p>
                                ${unidade.matriz ? '<p><strong>Status:</strong> Unidade Matriz</p>' : ''}
                            </div>
                            <div class="col-md-6">
                                <h6>Estrutura</h6>
                                <ul>
                                    <li>Área de musculação completa</li>
                                    <li>Studio para aulas coletivas</li>
                                    <li>Área de cardio</li>
                                    <li>Vestiários completos</li>
                                    <li>Estacionamento gratuito</li>
                                    <li>Wi-Fi liberado</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <a href="https://maps.google.com/?q=${encodeURIComponent(unidade.endereco + ', ' + unidade.cidade)}" 
                           target="_blank" class="btn btn-green">
                            <i class="fas fa-map-marker-alt"></i> Ver no Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;

    const existingModal = document.getElementById('unidadeModal');
    if (existingModal) {
        existingModal.remove();
    }

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    const modal = new bootstrap.Modal(document.getElementById('unidadeModal'));
    modal.show();
}

function setupSearch() {
    const searchInput = document.getElementById('searchUnidades');
    
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        
        if (searchTerm === '') {
            renderUnidades();
            return;
        }
        
        const filteredUnidades = unidades.filter(unidade => 
            unidade.cidade.toLowerCase().includes(searchTerm) ||
            unidade.bairro.toLowerCase().includes(searchTerm) ||
            unidade.endereco.toLowerCase().includes(searchTerm)
        );
        
        renderUnidades(filteredUnidades);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    renderUnidades();
    setupSearch();
    
    const unitCountElement = document.querySelector('.header-stats .stat:first-child h3');
    if (unitCountElement) {
        unitCountElement.textContent = unidades.length;
    }
});