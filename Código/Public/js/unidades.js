const unidades = [
    {
        id: 1,
        cidade: 'Limeira',
        estado: 'SP',
        bairro: 'Centro',
        rua: 'Av. Campinas',
        numero: 1500,
        imagem: '../../Public/Imagens/limeira.jpg'
    },
    {
        id: 2,
        cidade: 'Campinas',
        estado: 'SP',
        bairro: 'Centro',
        rua: 'R. Barão de Jaguara',
        numero: 900,
        imagem: '../../Public/Imagens/campinas.jpg'
    },
    {
        id: 3,
        cidade: 'Piracicaba',
        estado: 'SP',
        bairro: 'Centro',
        rua: 'Av. Independência',
        numero: 2100,
        imagem: '../../Public/Imagens/piracicaba.jpg'
    },
    {
        id: 4,
        cidade: 'Sorocaba',
        estado: 'SP',
        bairro: 'Centro',
        rua: 'R. XV de Novembro',
        numero: 450,
        imagem: '../../Public/Imagens/sorocaba.jpg'
    },
    {
        id: 5,
        cidade: 'Ribeirão Preto',
        estado: 'SP',
        bairro: 'Jardim Paulista',
        rua: 'Av. Pres. Vargas',
        numero: 1800,
        imagem: '../../Public/Imagens/ribeirao.jpg'
    },
    {
        id: 6,
        cidade: 'Araraquara',
        estado: 'SP',
        bairro: 'Centro',
        rua: 'Av. Bento de Abreu',
        numero: 300,
        imagem: '../../Public/Imagens/araraquara.jpg'
    },
    {
        id: 7,
        cidade: 'São Carlos',
        estado: 'SP',
        bairro: 'Centro',
        rua: 'R. Episcopal',
        numero: 1200,
        imagem: '../../Public/Imagens/saocarlos.jpg'
    },
    {
        id: 8,
        cidade: 'Jundiaí',
        estado: 'SP',
        bairro: 'Anhangabaú',
        rua: 'Av. Jundiaí',
        numero: 500,
        imagem: '../../Public/Imagens/jundiai.jpg'
    },
    {
        id: 9,
        cidade: 'Bauru',
        estado: 'SP',
        bairro: 'Centro',
        rua: 'Av. Getúlio Vargas',
        numero: 100,
        imagem: '../../Public/Imagens/bauru.jpg'
    },
    {
        id: 10,
        cidade: 'São José do Rio Preto',
        estado: 'SP',
        bairro: 'Boa Vista',
        rua: 'R. Bernardino',
        numero: 950,
        imagem: '../../Public/Imagens/riopreto.jpg'
    },
    {
        id: 11,
        cidade: 'Marília',
        estado: 'SP',
        bairro: 'Fragata',
        rua: 'Av. das Esmeraldas',
        numero: 400,
        imagem: '../../Public/Imagens/marilia.jpg'
    },
    {
        id: 12,
        cidade: 'Presidente Prudente',
        estado: 'SP',
        bairro: 'Vila Nova',
        rua: 'Av. Manoel Goulart',
        numero: 1300,
        imagem: '../../Public/Imagens/presidente.jpg'
    },
    {
        id: 13,
        cidade: 'Americana',
        estado: 'SP',
        bairro: 'Centro',
        rua: 'R. Fernando Camargo',
        numero: 800,
        imagem: '../../Public/Imagens/americana.jpg'
    },
    {
        id: 14,
        cidade: 'Indaiatuba',
        estado: 'SP',
        bairro: 'Cidade Nova',
        rua: 'Av. Pres. Kennedy',
        numero: 700,
        imagem: '../../Public/Imagens/indaiatuba.jpg'
    },
    {
        id: 15,
        cidade: 'Barueri',
        estado: 'SP',
        bairro: 'Alphaville',
        rua: 'Av. Arnaldo Rodrigues',
        numero: 250,
        imagem: '../../Public/Imagens/barueri.jpg'
    }
];

let unidadeSelecionada = null;

function criarItemLista(unidade) {
    const item = document.createElement('div');
    item.className = 'unidade-item';
    item.dataset.id = unidade.id;
    
    item.innerHTML = `
        <div class="unidade-item-cidade">${unidade.cidade}</div>
        <div class="unidade-item-endereco">
            <i class="fas fa-map-marker-alt"></i>
            <span>${unidade.rua}, ${unidade.numero}</span>
        </div>
    `;
    
    item.addEventListener('click', () => selecionarUnidade(unidade));
    
    return item;
}

function selecionarUnidade(unidade) {
    unidadeSelecionada = unidade;
    
    document.querySelectorAll('.unidade-item').forEach(item => {
        item.classList.remove('ativo');
        if (parseInt(item.dataset.id) === unidade.id) {
            item.classList.add('ativo');
        }
    });
    
    mostrarDetalhes(unidade);
}

function mostrarDetalhes(unidade) {
    const container = document.getElementById('detalhes-unidade');
    if (!container) return;
    
    container.classList.remove('vazio');
    
    const enderecoCompleto = `${unidade.rua}, ${unidade.numero}, ${unidade.bairro}, ${unidade.cidade} - ${unidade.estado}`;
    
    container.innerHTML = `
        <div class="detalhes-conteudo">
            <img src="${unidade.imagem}" alt="TechFit ${unidade.cidade}" class="unidade-imagem" onerror="this.style.display='none';">
            <h1 class="unidade-titulo">${unidade.cidade}</h1>
            <p class="unidade-subtitulo">${unidade.estado}</p>
            
            <div class="unidade-info-grid">
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="info-item-content">
                        <h4>Endereço</h4>
                        <p>${unidade.rua}, ${unidade.numero}</p>
                        <p style="font-size: 0.9rem; color: #666; margin-top: 5px;">${unidade.bairro}</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div class="info-item-content">
                        <h4>Horário</h4>
                        <p>Seg-Sex: 6h às 23h</p>
                        <p style="font-size: 0.9rem; color: #666; margin-top: 5px;">Sáb-Dom: 8h às 20h</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div class="info-item-content">
                        <h4>Telefone</h4>
                        <p>(19) 99949-5895</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <i class="fas fa-dumbbell"></i>
                    <div class="info-item-content">
                        <h4>Estrutura</h4>
                        <p>Academia completa</p>
                    </div>
                </div>
            </div>
            
            <div class="unidade-descricao">
                <h3>Sobre a Unidade</h3>
                <p>Nossa unidade em ${unidade.cidade} oferece uma estrutura completa com equipamentos modernos, área de musculação, aulas coletivas e estacionamento. Venha conhecer e transforme sua vida com a TechFit!</p>
                <a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(enderecoCompleto)}" target="_blank" class="btn-maps">
                    <i class="fas fa-directions"></i> Ver no Google Maps
                </a>
            </div>
        </div>
    `;
}

function inicializar() {
    const listaContainer = document.getElementById('lista-unidades');
    const detalhesContainer = document.getElementById('detalhes-unidade');
    
    if (!listaContainer || !detalhesContainer) return;
    
    unidades.forEach(unidade => {
        const item = criarItemLista(unidade);
        listaContainer.appendChild(item);
    });
    
    if (unidades.length > 0) {
        selecionarUnidade(unidades[0]);
    }
}

document.addEventListener('DOMContentLoaded', inicializar);
