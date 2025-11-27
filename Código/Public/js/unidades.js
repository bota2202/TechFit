let unidades = [];
let unidadeSelecionada = null;

const imagensMap = {
    'Limeira': '../Imagens/limeira.jpg',
    'Campinas': '../Imagens/campinas.jpg',
    'Piracicaba': '../Imagens/piracicaba.jpg',
    'Sorocaba': '../Imagens/sorocaba.jpg',
    'Ribeirão Preto': '../Imagens/ribeirao.jpg',
    'Araraquara': '../Imagens/araraquara.jpg',
    'São Carlos': '../Imagens/saocarlos.jpg',
    'Jundiaí': '../Imagens/jundiai.jpg',
    'Bauru': '../Imagens/bauru.jpg',
    'São José do Rio Preto': '../Imagens/riopreto.jpg',
    'Marília': '../Imagens/marilia.jpg',
    'Presidente Prudente': '../Imagens/presidente.jpg',
    'Americana': '../Imagens/americana.jpg',
    'Indaiatuba': '../Imagens/indaiatuba.jpg',
    'Barueri': '../Imagens/barueri.jpg'
};

async function carregarUnidades() {
    try {
        const response = await fetch('../DB/api_unidades.php');
        const data = await response.json();
        
        if (!data.success || !data.unidades) {
            console.error('Erro ao carregar unidades');
            return;
        }
        
        unidades = data.unidades;
        inicializar();
    } catch (error) {
        console.error('Erro ao carregar unidades:', error);
    }
}

function criarItemLista(unidade) {
    const item = document.createElement('div');
    item.className = 'unidade-item';
    item.dataset.id = unidade.id_unidade;
    
    item.innerHTML = `
        <div class="unidade-item-cidade">${unidade.cidade_unidade}</div>
        <div class="unidade-item-endereco">
            <i class="fas fa-map-marker-alt"></i>
            <span>${unidade.rua_unidade}, ${unidade.numero_unidade}</span>
        </div>
    `;
    
    item.addEventListener('click', () => selecionarUnidade(unidade));
    
    return item;
}

function selecionarUnidade(unidade) {
    unidadeSelecionada = unidade;
    
    document.querySelectorAll('.unidade-item').forEach(item => {
        item.classList.remove('ativo');
        if (parseInt(item.dataset.id) === unidade.id_unidade) {
            item.classList.add('ativo');
        }
    });
    
    mostrarDetalhes(unidade);
}

function mostrarDetalhes(unidade) {
    const container = document.getElementById('detalhes-unidade');
    if (!container) return;
    
    container.classList.remove('vazio');
    
    const enderecoCompleto = `${unidade.rua_unidade}, ${unidade.numero_unidade}, ${unidade.bairro_unidade}, ${unidade.cidade_unidade} - ${unidade.estado_unidade}`;
    const imagem = imagensMap[unidade.cidade_unidade] || '../Imagens/fundo.jpg';
    
    container.innerHTML = `
        <div class="detalhes-conteudo">
            <img src="${imagem}" alt="TechFit ${unidade.cidade_unidade}" class="unidade-imagem" onerror="this.style.display='none';">
            <h1 class="unidade-titulo">${unidade.cidade_unidade}</h1>
            <p class="unidade-subtitulo">${unidade.estado_unidade}</p>
            
            <div class="unidade-info-grid">
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="info-item-content">
                        <h4>Endereço</h4>
                        <p>${unidade.rua_unidade}, ${unidade.numero_unidade}</p>
                        <p style="font-size: 0.9rem; color: #666; margin-top: 5px;">${unidade.bairro_unidade}</p>
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
                <p>Nossa unidade em ${unidade.cidade_unidade} oferece uma estrutura completa com equipamentos modernos, área de musculação, aulas coletivas e estacionamento. Venha conhecer e transforme sua vida com a TechFit!</p>
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
    
    listaContainer.innerHTML = '';
    
    unidades.forEach(unidade => {
        const item = criarItemLista(unidade);
        listaContainer.appendChild(item);
    });
    
    if (unidades.length > 0) {
        selecionarUnidade(unidades[0]);
    }
}

document.addEventListener('DOMContentLoaded', carregarUnidades);
