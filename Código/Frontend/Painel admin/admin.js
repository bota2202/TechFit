// Storage helpers
const STORAGE_KEYS = {
    unidades: 'techfit_unidades',
    cursos: 'techfit_cursos',
    planos: 'techfit_planos',
    usuarios: 'techfit_usuarios',
    logs: 'techfit_logs',
    presencas: 'techfit_presencas'
};

// Defaults embutidos para garantir seed confiável
const DEFAULT_UNIDADES = [
    { id: 1, cidade: "Limeira", bairro: "Centro", endereco: "Av. Campinas, 1500 - Centro", telefone: "(19) 3451-1000", horario: "24h", vagas: 50, matriz: true, imagem: "../Imagens/limeira.jpg" },
    { id: 2, cidade: "Campinas", bairro: "Centro", endereco: "R. Barão de Jaguara, 900 - Centro", telefone: "(19) 3234-5600", horario: "24h", vagas: 80, matriz: false, imagem: "../Imagens/campinas.jpg" },
    { id: 3, cidade: "Piracicaba", bairro: "Centro", endereco: "Av. Independência, 2100 - Centro", telefone: "(19) 3422-7800", horario: "24h", vagas: 60, matriz: false, imagem: "../Imagens/piracicaba.jpg" }
];

const DEFAULT_CURSOS = [
    { id: 1, nome: "Musculação", categoria: "forca", descricao: "Treinos completos para ganho de massa e força.", imagem: "../Imagens/musculacao.jpg", intensidade: "Alta", duracao: "60 min", nivel: "Todos", instrutor: "Carlos Silva" },
    { id: 2, nome: "Yoga", categoria: "mente-corpo", descricao: "Equilíbrio e bem-estar físico e mental.", imagem: "../Imagens/yoga.jpg", intensidade: "Baixa", duracao: "75 min", nivel: "Todos", instrutor: "Ana Costa" },
    { id: 3, nome: "CrossFit", categoria: "forca", descricao: "Alta performance para resistência.", imagem: "../Imagens/crossfit.jpg", intensidade: "Alta", duracao: "60 min", nivel: "Intermediário", instrutor: "Ricardo Santos" }
];

function readFromStorage(key, fallback) {
    try {
        const raw = localStorage.getItem(key);
        if (!raw) return fallback;
        const data = JSON.parse(raw);
        return Array.isArray(fallback) && !Array.isArray(data) ? fallback : (data ?? fallback);
    } catch (_) {
        return fallback;
    }
}

function writeToStorage(key, value) {
    localStorage.setItem(key, JSON.stringify(value));
}

// Auditoria
function addLog(entity, action, id, summary) {
    const logs = readFromStorage(STORAGE_KEYS.logs, []);
    logs.unshift({
        at: new Date().toISOString(),
        entity,
        action,
        id,
        summary
    });
    writeToStorage(STORAGE_KEYS.logs, logs.slice(0, 500)); // mantém últimos 500
    renderTabelaLogs();
}

// Toasts (feedback visual)
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const id = `t_${Date.now()}`;
    const bg = type === 'danger' ? 'bg-danger' : type === 'warning' ? 'bg-warning text-dark' : 'bg-success';
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white ${bg} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.id = id;
    toast.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    container.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 2500 });
    bsToast.show();
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}

// Bootstrap helpers
function getModal(id) {
    const el = document.getElementById(id);
    return new bootstrap.Modal(el);
}

// Unidades
function loadUnidades() {
    return readFromStorage(STORAGE_KEYS.unidades, []);
}

function saveUnidades(list) {
    writeToStorage(STORAGE_KEYS.unidades, list);
    renderTabelaUnidades();
}

function renderTabelaUnidades() {
    const tbody = document.querySelector('#tabelaUnidades tbody');
    const unidades = loadUnidades();
    tbody.innerHTML = '';
    unidades.forEach(u => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${u.id}</td>
            <td>${u.cidade}</td>
            <td>${u.bairro}</td>
            <td>${u.endereco}</td>
            <td>${u.telefone}</td>
            <td>${u.horario || ''}</td>
            <td>${u.vagas ?? ''}</td>
            <td>${u.matriz ? 'Sim' : 'Não'}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-primary me-2" data-action="edit" data-id="${u.id}"><i class="fa-solid fa-pen"></i></button>
                <button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="${u.id}"><i class="fa-solid fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function openUnidadeModal(unidade) {
    const form = document.getElementById('formUnidade');
    form.reset();
    form.id.value = unidade?.id || '';
    form.cidade.value = unidade?.cidade || '';
    form.bairro.value = unidade?.bairro || '';
    form.endereco.value = unidade?.endereco || '';
    form.telefone.value = unidade?.telefone || '';
    form.horario.value = unidade?.horario || '24h';
    form.vagas.value = unidade?.vagas ?? 50;
    form.imagem.value = unidade?.imagem || '';
    form.matriz.checked = !!unidade?.matriz;
    getModal('modalUnidade').show();
}

function handleSalvarUnidade() {
    const form = document.getElementById('formUnidade');
    const unidades = loadUnidades();

    const payload = {
        id: form.id.value ? Number(form.id.value) : (unidades.reduce((m, x) => Math.max(m, x.id || 0), 0) + 1),
        cidade: form.cidade.value.trim(),
        bairro: form.bairro.value.trim(),
        endereco: form.endereco.value.trim(),
        telefone: form.telefone.value.trim(),
        horario: form.horario.value.trim(),
        vagas: Number(form.vagas.value || 0),
        imagem: form.imagem.value.trim(),
        matriz: form.matriz.checked
    };

    const exists = unidades.findIndex(u => u.id === payload.id);
    if (exists >= 0) {
        unidades[exists] = payload;
        addLog('Unidade', 'update', payload.id, `${payload.cidade} - ${payload.bairro}`);
        showToast('Unidade atualizada');
    } else {
        unidades.push(payload);
        addLog('Unidade', 'create', payload.id, `${payload.cidade} - ${payload.bairro}`);
        showToast('Unidade criada');
    }
    saveUnidades(unidades);
    getModal('modalUnidade').hide();
}

function handleTabelaUnidadesClick(e) {
    const btn = e.target.closest('button[data-action]');
    if (!btn) return;
    const action = btn.getAttribute('data-action');
    const id = Number(btn.getAttribute('data-id'));
    const unidades = loadUnidades();
    const idx = unidades.findIndex(u => u.id === id);
    if (idx < 0) return;
    if (action === 'edit') {
        openUnidadeModal(unidades[idx]);
    } else if (action === 'delete') {
        if (confirm('Deseja remover esta unidade?')) {
            const removed = unidades.splice(idx, 1)[0];
            saveUnidades(unidades);
            addLog('Unidade', 'delete', removed.id, `${removed.cidade} - ${removed.bairro}`);
            showToast('Unidade removida', 'warning');
        }
    }
}

// Cursos
function loadCursos() {
    return readFromStorage(STORAGE_KEYS.cursos, []);
}

function saveCursos(list) {
    writeToStorage(STORAGE_KEYS.cursos, list);
    renderTabelaCursos();
}

function renderTabelaCursos() {
    const tbody = document.querySelector('#tabelaCursos tbody');
    const cursos = loadCursos();
    tbody.innerHTML = '';
    cursos.forEach(c => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${c.id}</td>
            <td>${c.nome}</td>
            <td>${c.categoria}</td>
            <td>${c.intensidade || ''}</td>
            <td>${c.duracao || ''}</td>
            <td>${c.nivel || ''}</td>
            <td>${c.instrutor || ''}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-primary me-2" data-c-action="edit" data-id="${c.id}"><i class="fa-solid fa-pen"></i></button>
                <button class="btn btn-sm btn-outline-danger" data-c-action="delete" data-id="${c.id}"><i class="fa-solid fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function openCursoModal(curso) {
    const form = document.getElementById('formCurso');
    form.reset();
    form.id.value = curso?.id || '';
    form.nome.value = curso?.nome || '';
    form.categoria.value = curso?.categoria || 'forca';
    form.instrutor.value = curso?.instrutor || '';
    form.intensidade.value = curso?.intensidade || '';
    form.duracao.value = curso?.duracao || '';
    form.nivel.value = curso?.nivel || '';
    form.descricao.value = curso?.descricao || '';
    form.imagem.value = curso?.imagem || '';
    getModal('modalCurso').show();
}

function handleSalvarCurso() {
    const form = document.getElementById('formCurso');
    const cursos = loadCursos();
    const payload = {
        id: form.id.value ? Number(form.id.value) : (cursos.reduce((m, x) => Math.max(m, x.id || 0), 0) + 1),
        nome: form.nome.value.trim(),
        categoria: form.categoria.value,
        instrutor: form.instrutor.value.trim(),
        intensidade: form.intensidade.value.trim(),
        duracao: form.duracao.value.trim(),
        nivel: form.nivel.value.trim(),
        descricao: form.descricao.value.trim(),
        imagem: form.imagem.value.trim()
    };
    const exists = cursos.findIndex(c => c.id === payload.id);
    if (exists >= 0) {
        cursos[exists] = payload;
        addLog('Curso', 'update', payload.id, payload.nome);
        showToast('Curso atualizado');
    } else {
        cursos.push(payload);
        addLog('Curso', 'create', payload.id, payload.nome);
        showToast('Curso criado');
    }
    saveCursos(cursos);
    getModal('modalCurso').hide();
}

function handleTabelaCursosClick(e) {
    const btn = e.target.closest('button[data-c-action]');
    if (!btn) return;
    const action = btn.getAttribute('data-c-action');
    const id = Number(btn.getAttribute('data-id'));
    const cursos = loadCursos();
    const idx = cursos.findIndex(c => c.id === id);
    if (idx < 0) return;
    if (action === 'edit') {
        openCursoModal(cursos[idx]);
    } else if (action === 'delete') {
        if (confirm('Deseja remover este curso?')) {
            const removed = cursos.splice(idx, 1)[0];
            saveCursos(cursos);
            addLog('Curso', 'delete', removed.id, removed.nome);
            showToast('Curso removido', 'warning');
        }
    }
}

// Planos
function loadPlanos() { return readFromStorage(STORAGE_KEYS.planos, []); }
function savePlanos(list) { writeToStorage(STORAGE_KEYS.planos, list); renderTabelaPlanos(); }
function renderTabelaPlanos() {
    const tbody = document.querySelector('#tabelaPlanos tbody');
    if (!tbody) return;
    const planos = loadPlanos();
    tbody.innerHTML = '';
    planos.forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${p.id}</td>
            <td>${p.nome}</td>
            <td>R$ ${Number(p.preco).toFixed(2)}</td>
            <td>${p.periodicidade}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-primary me-2" data-p-action="edit" data-id="${p.id}"><i class="fa-solid fa-pen"></i></button>
                <button class="btn btn-sm btn-outline-danger" data-p-action="delete" data-id="${p.id}"><i class="fa-solid fa-trash"></i></button>
            </td>`;
        tbody.appendChild(tr);
    });
}
function openPlanoModal(plano) {
    const form = document.getElementById('formPlano');
    form.reset();
    form.id.value = plano?.id || '';
    form.nome.value = plano?.nome || '';
    form.preco.value = plano?.preco ?? '';
    form.periodicidade.value = plano?.periodicidade || 'Mensal';
    form.descricao.value = plano?.descricao || '';
    getModal('modalPlano').show();
}
function handleSalvarPlano() {
    const form = document.getElementById('formPlano');
    const planos = loadPlanos();
    const payload = {
        id: form.id.value ? Number(form.id.value) : (planos.reduce((m, x) => Math.max(m, x.id || 0), 0) + 1),
        nome: form.nome.value.trim(),
        preco: Number(form.preco.value || 0),
        periodicidade: form.periodicidade.value,
        descricao: form.descricao.value.trim()
    };
    const exists = planos.findIndex(p => p.id === payload.id);
    if (exists >= 0) { planos[exists] = payload; addLog('Plano', 'update', payload.id, payload.nome); }
    else { planos.push(payload); addLog('Plano', 'create', payload.id, payload.nome); }
    savePlanos(planos);
    getModal('modalPlano').hide();
    populatePlanoSelect();
    showToast('Plano salvo');
}
function handleTabelaPlanosClick(e) {
    const btn = e.target.closest('button[data-p-action]');
    if (!btn) return;
    const action = btn.getAttribute('data-p-action');
    const id = Number(btn.getAttribute('data-id'));
    const planos = loadPlanos();
    const idx = planos.findIndex(p => p.id === id);
    if (idx < 0) return;
    if (action === 'edit') openPlanoModal(planos[idx]);
    if (action === 'delete' && confirm('Deseja remover este plano?')) {
        const removed = planos.splice(idx, 1)[0];
        savePlanos(planos);
        addLog('Plano', 'delete', removed.id, removed.nome);
        populatePlanoSelect();
        showToast('Plano removido', 'warning');
    }
}

// Usuários
function loadUsuarios() { return readFromStorage(STORAGE_KEYS.usuarios, []); }
function saveUsuarios(list) { writeToStorage(STORAGE_KEYS.usuarios, list); renderTabelaUsuarios(); }
function renderTabelaUsuarios() {
    const tbody = document.querySelector('#tabelaUsuarios tbody');
    if (!tbody) return;
    const usuarios = loadUsuarios();
    tbody.innerHTML = '';
    usuarios.forEach(u => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${u.id}</td>
            <td>${u.nome}</td>
            <td>${u.email}</td>
            <td>${u.planoNome || '-'}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-primary me-2" data-u-action="edit" data-id="${u.id}"><i class="fa-solid fa-pen"></i></button>
                <button class="btn btn-sm btn-outline-danger" data-u-action="delete" data-id="${u.id}"><i class="fa-solid fa-trash"></i></button>
            </td>`;
        tbody.appendChild(tr);
    });
}
function openUsuarioModal(usuario) {
    const form = document.getElementById('formUsuario');
    form.reset();
    populatePlanoSelect();
    form.id.value = usuario?.id || '';
    form.nome.value = usuario?.nome || '';
    form.email.value = usuario?.email || '';
    form.plano.value = usuario?.planoId || '';
    getModal('modalUsuario').show();
}
function populatePlanoSelect() {
    const select = document.getElementById('selectPlanoUsuario');
    if (!select) return;
    const planos = loadPlanos();
    select.innerHTML = '<option value="">Sem plano</option>' + planos.map(p => `<option value="${p.id}">${p.nome} - R$ ${Number(p.preco).toFixed(2)} (${p.periodicidade})</option>`).join('');
}
function handleSalvarUsuario() {
    const form = document.getElementById('formUsuario');
    const usuarios = loadUsuarios();
    const planos = loadPlanos();
    const planoId = form.plano.value ? Number(form.plano.value) : null;
    const plano = planos.find(p => p.id === planoId);
    const payload = {
        id: form.id.value ? Number(form.id.value) : (usuarios.reduce((m, x) => Math.max(m, x.id || 0), 0) + 1),
        nome: form.nome.value.trim(),
        email: form.email.value.trim(),
        planoId: plano?.id || null,
        planoNome: plano?.nome || null
    };
    const exists = usuarios.findIndex(u => u.id === payload.id);
    if (exists >= 0) { usuarios[exists] = payload; addLog('Usuario', 'update', payload.id, payload.nome); }
    else { usuarios.push(payload); addLog('Usuario', 'create', payload.id, payload.nome); }
    saveUsuarios(usuarios);
    getModal('modalUsuario').hide();
    showToast('Usuário salvo');
}
function handleTabelaUsuariosClick(e) {
    const btn = e.target.closest('button[data-u-action]');
    if (!btn) return;
    const action = btn.getAttribute('data-u-action');
    const id = Number(btn.getAttribute('data-id'));
    const usuarios = loadUsuarios();
    const idx = usuarios.findIndex(u => u.id === id);
    if (idx < 0) return;
    if (action === 'edit') openUsuarioModal(usuarios[idx]);
    if (action === 'delete' && confirm('Deseja remover este usuário?')) {
        const removed = usuarios.splice(idx, 1)[0];
        saveUsuarios(usuarios);
        addLog('Usuario', 'delete', removed.id, removed.nome);
        showToast('Usuário removido', 'warning');
    }
}

// Logs
function renderTabelaLogs() {
    const container = document.getElementById('logsAccordion');
    if (!container) return;
    const logs = readFromStorage(STORAGE_KEYS.logs, []);
    // Agrupa por dia (YYYY-MM-DD)
    const groups = logs.reduce((acc, l) => {
        const d = new Date(l.at);
        const key = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
        (acc[key] ||= []).push(l);
        return acc;
    }, {});
    const days = Object.keys(groups).sort((a,b) => b.localeCompare(a));
    container.innerHTML = '';
    days.forEach((day, index) => {
        const pretty = day.split('-').reverse().join('/');
        const itemId = `acc-${day}`;
        const collapseId = `col-${day}`;
        const count = groups[day].length;
        const item = document.createElement('div');
        item.className = 'accordion-item log-accordion-item';
        item.innerHTML = `
            <h2 class="accordion-header" id="h-${itemId}">
                <button class="accordion-button ${index===0 ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${index===0 ? 'true' : 'false'}" aria-controls="${collapseId}">
                    ${pretty} <span class="badge bg-secondary ms-2">${count}</span>
                </button>
            </h2>
            <div id="${collapseId}" class="accordion-collapse collapse ${index===0 ? 'show' : ''}" data-bs-parent="#logsAccordion">
                <div class="accordion-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Quando</th>
                                    <th>Entidade</th>
                                    <th>Ação</th>
                                    <th>ID</th>
                                    <th>Resumo</th>
                                </tr>
                            </thead>
                            <tbody id="tb-${itemId}"></tbody>
                        </table>
                    </div>
                </div>
            </div>`;
        container.appendChild(item);
        const tbody = item.querySelector(`#tb-${itemId}`);
        groups[day].forEach(l => {
            const tr = document.createElement('tr');
            const dateStr = new Date(l.at).toLocaleTimeString();
            tr.innerHTML = `<td>${dateStr}</td><td>${l.entity}</td><td>${l.action}</td><td>${l.id}</td><td>${l.summary || ''}</td>`;
            tbody.appendChild(tr);
        });
    });
}

// Presenças (log semanal)
function readPresencas() { return readFromStorage(STORAGE_KEYS.presencas, []); }
function writePresencas(list) { writeToStorage(STORAGE_KEYS.presencas, list); renderPresencas(); }
function getISOWeek(date) {
    const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
    const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1)/7);
    return { year: d.getUTCFullYear(), week: weekNo };
}
function renderPresencas() {
    const acc = document.getElementById('presencasAccordion');
    if (!acc) return;
    const presencas = readPresencas();
    // agrupamento por semana ISO
    const groups = presencas.reduce((acc, p) => {
        const d = new Date(p.in);
        const { year, week } = getISOWeek(d);
        const key = `${year}-W${String(week).padStart(2,'0')}`;
        (acc[key] ||= []).push(p);
        return acc;
    }, {});
    const weeks = Object.keys(groups).sort((a,b) => b.localeCompare(a));
    acc.innerHTML = '';
    const usuarios = loadUsuarios();
    weeks.forEach((wk, i) => {
        const item = document.createElement('div');
        item.className = 'accordion-item log-accordion-item';
        const collapseId = `wk-${wk}`;
        item.innerHTML = `
            <h2 class="accordion-header">
                <button class="accordion-button ${i===0?'':'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${i===0?'true':'false'}">
                    Semana ${wk}
                </button>
            </h2>
            <div id="${collapseId}" class="accordion-collapse collapse ${i===0?'show':''}" data-bs-parent="#presencasAccordion">
                <div class="accordion-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Dia</th>
                                    <th>Usuário</th>
                                    <th>Entrada</th>
                                    <th>Saída</th>
                                </tr>
                            </thead>
                            <tbody id="tb-${collapseId}"></tbody>
                        </table>
                    </div>
                </div>
            </div>`;
        acc.appendChild(item);
        const tbody = item.querySelector(`#tb-${collapseId}`);
        groups[wk]
            .sort((a,b) => new Date(a.in) - new Date(b.in))
            .forEach(p => {
                const user = usuarios.find(u => u.id === p.userId);
                const inD = new Date(p.in);
                const outD = p.out ? new Date(p.out) : null;
                const dia = inD.toLocaleDateString();
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${dia}</td><td>${user ? user.nome : 'Usuário ' + p.userId}</td><td>${inD.toLocaleTimeString()}</td><td>${outD ? outD.toLocaleTimeString() : '-'}</td>`;
                tbody.appendChild(tr);
            });
    });
}
function exportarPresencas() {
    const data = readPresencas();
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `techfit-presencas-${new Date().toISOString().slice(0,19).replace(/[:T]/g,'-')}.json`;
    a.click();
    URL.revokeObjectURL(url);
}
function limparPresencas() {
    if (!confirm('Limpar registros de presenças?')) return;
    writePresencas([]);
    showToast('Presenças limpas', 'warning');
}
function exportarLog() {
    const logs = readFromStorage(STORAGE_KEYS.logs, []);
    const blob = new Blob([JSON.stringify(logs, null, 2)], { type: 'application/json;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `techfit-logs-${new Date().toISOString().slice(0,19).replace(/[:T]/g,'-')}.json`;
    a.click();
    URL.revokeObjectURL(url);
}
function limparLog() {
    if (!confirm('Limpar todos os registros de log?')) return;
    writeToStorage(STORAGE_KEYS.logs, []);
    renderTabelaLogs();
    showToast('Log limpo', 'warning');
}

// Seeds iniciais a partir dos arquivos públicos (se existirem no site)
function seedIfEmpty() {
    if (!localStorage.getItem(STORAGE_KEYS.unidades)) {
        writeToStorage(STORAGE_KEYS.unidades, DEFAULT_UNIDADES);
    }
    if (!localStorage.getItem(STORAGE_KEYS.cursos)) {
        writeToStorage(STORAGE_KEYS.cursos, DEFAULT_CURSOS);
    }
    if (!localStorage.getItem(STORAGE_KEYS.planos)) {
        writeToStorage(STORAGE_KEYS.planos, [
            { id: 1, nome: 'Essencial', preco: 89.90, periodicidade: 'Mensal', descricao: 'Acesso em horário comercial' },
            { id: 2, nome: 'Premium', preco: 129.90, periodicidade: 'Mensal', descricao: 'Acesso 24h + aulas coletivas' }
        ]);
    }
    if (!localStorage.getItem(STORAGE_KEYS.usuarios)) {
        const planos = readFromStorage(STORAGE_KEYS.planos, []);
        const p1 = planos[0];
        const p2 = planos[1] || planos[0];
        writeToStorage(STORAGE_KEYS.usuarios, [
            { id: 1, nome: 'Ana Souza', email: 'ana@techfit.com', planoId: p1?.id || null, planoNome: p1?.nome || null },
            { id: 2, nome: 'Bruno Lima', email: 'bruno@techfit.com', planoId: p2?.id || null, planoNome: p2?.nome || null },
            { id: 3, nome: 'Carla Mota', email: 'carla@techfit.com', planoId: p1?.id || null, planoNome: p1?.nome || null }
        ]);
    }
    if (!localStorage.getItem(STORAGE_KEYS.presencas)) {
        const now = new Date();
        const list = [];
        // gera entradas/saídas de 3 usuários por 7 dias
        for (let d = 0; d < 14; d++) {
            const day = new Date(now.getFullYear(), now.getMonth(), now.getDate() - d);
            [1,2,3].forEach(uid => {
                const inTime = new Date(day.getFullYear(), day.getMonth(), day.getDate(), 6 + (uid%3), 10 + (d%20));
                const outTime = new Date(day.getFullYear(), day.getMonth(), day.getDate(), 7 + (uid%3) + 1, 5 + (d%20));
                list.push({ userId: uid, in: inTime.toISOString(), out: outTime.toISOString() });
            });
        }
        writeToStorage(STORAGE_KEYS.presencas, list);
    }
}

// Clear all
function clearAll() {
    if (confirm('Isso apagará todos os dados do admin (localStorage). Continuar?')) {
        localStorage.removeItem(STORAGE_KEYS.unidades);
        localStorage.removeItem(STORAGE_KEYS.cursos);
        seedIfEmpty();
        renderTabelaUnidades();
        renderTabelaCursos();
    }
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    seedIfEmpty();
    renderTabelaUnidades();
    renderTabelaCursos();
    renderTabelaPlanos();
    renderTabelaUsuarios();
    renderTabelaLogs();
    renderPresencas();

    document.getElementById('btnNovaUnidade').addEventListener('click', () => openUnidadeModal());
    document.getElementById('salvarUnidade').addEventListener('click', handleSalvarUnidade);
    document.querySelector('#tabelaUnidades tbody').addEventListener('click', handleTabelaUnidadesClick);

    document.getElementById('btnNovoCurso').addEventListener('click', () => openCursoModal());
    document.getElementById('salvarCurso').addEventListener('click', handleSalvarCurso);
    document.querySelector('#tabelaCursos tbody').addEventListener('click', handleTabelaCursosClick);

    const btnNovoPlano = document.getElementById('btnNovoPlano');
    if (btnNovoPlano) btnNovoPlano.addEventListener('click', () => openPlanoModal());
    const salvarPlano = document.getElementById('salvarPlano');
    if (salvarPlano) salvarPlano.addEventListener('click', handleSalvarPlano);
    const tabelaPlanos = document.querySelector('#tabelaPlanos tbody');
    if (tabelaPlanos) tabelaPlanos.addEventListener('click', handleTabelaPlanosClick);

    const btnNovoUsuario = document.getElementById('btnNovoUsuario');
    if (btnNovoUsuario) btnNovoUsuario.addEventListener('click', () => openUsuarioModal());
    const salvarUsuario = document.getElementById('salvarUsuario');
    if (salvarUsuario) salvarUsuario.addEventListener('click', handleSalvarUsuario);
    const tabelaUsuarios = document.querySelector('#tabelaUsuarios tbody');
    if (tabelaUsuarios) tabelaUsuarios.addEventListener('click', handleTabelaUsuariosClick);

    const btnExportarLog = document.getElementById('btnExportarLog');
    if (btnExportarLog) btnExportarLog.addEventListener('click', exportarLog);
    const btnLimparLog = document.getElementById('btnLimparLog');
    if (btnLimparLog) btnLimparLog.addEventListener('click', limparLog);

    const btnExportarPresencas = document.getElementById('btnExportarPresencas');
    if (btnExportarPresencas) btnExportarPresencas.addEventListener('click', exportarPresencas);
    const btnLimparPresencas = document.getElementById('btnLimparPresencas');
    if (btnLimparPresencas) btnLimparPresencas.addEventListener('click', limparPresencas);

    // Buscas
    const su = document.getElementById('searchUnidades');
    if (su) su.addEventListener('input', () => {
        const q = su.value.toLowerCase();
        const all = loadUnidades();
        const filtered = all.filter(u => `${u.cidade} ${u.bairro} ${u.endereco} ${u.telefone}`.toLowerCase().includes(q));
        const tbody = document.querySelector('#tabelaUnidades tbody');
        tbody.innerHTML = '';
        filtered.forEach(u => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${u.id}</td><td>${u.cidade}</td><td>${u.bairro}</td><td>${u.endereco}</td>
                <td>${u.telefone}</td><td>${u.horario || ''}</td><td>${u.vagas ?? ''}</td><td>${u.matriz ? 'Sim' : 'Não'}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary me-2" data-action="edit" data-id="${u.id}"><i class="fa-solid fa-pen"></i></button>
                    <button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="${u.id}"><i class="fa-solid fa-trash"></i></button>
                </td>`;
            tbody.appendChild(tr);
        });
    });
    const sc = document.getElementById('searchCursos');
    if (sc) sc.addEventListener('input', () => {
        const q = sc.value.toLowerCase();
        const all = loadCursos();
        const filtered = all.filter(c => `${c.nome} ${c.categoria} ${c.intensidade} ${c.nivel} ${c.instrutor}`.toLowerCase().includes(q));
        const tbody = document.querySelector('#tabelaCursos tbody');
        tbody.innerHTML = '';
        filtered.forEach(c => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${c.id}</td><td>${c.nome}</td><td>${c.categoria}</td><td>${c.intensidade || ''}</td>
                <td>${c.duracao || ''}</td><td>${c.nivel || ''}</td><td>${c.instrutor || ''}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary me-2" data-c-action="edit" data-id="${c.id}"><i class="fa-solid fa-pen"></i></button>
                    <button class="btn btn-sm btn-outline-danger" data-c-action="delete" data-id="${c.id}"><i class="fa-solid fa-trash"></i></button>
                </td>`;
            tbody.appendChild(tr);
        });
    });
    const sp = document.getElementById('searchPlanos');
    if (sp) sp.addEventListener('input', () => {
        const q = sp.value.toLowerCase();
        const all = loadPlanos();
        const filtered = all.filter(p => `${p.nome} ${p.periodicidade}`.toLowerCase().includes(q));
        const tbody = document.querySelector('#tabelaPlanos tbody');
        tbody.innerHTML = '';
        filtered.forEach(p => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${p.id}</td><td>${p.nome}</td><td>R$ ${Number(p.preco).toFixed(2)}</td><td>${p.periodicidade}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary me-2" data-p-action="edit" data-id="${p.id}"><i class="fa-solid fa-pen"></i></button>
                    <button class="btn btn-sm btn-outline-danger" data-p-action="delete" data-id="${p.id}"><i class="fa-solid fa-trash"></i></button>
                </td>`;
            tbody.appendChild(tr);
        });
    });
    const sux = document.getElementById('searchUsuarios');
    if (sux) sux.addEventListener('input', () => {
        const q = sux.value.toLowerCase();
        const all = loadUsuarios();
        const filtered = all.filter(u => `${u.nome} ${u.email} ${u.planoNome || ''}`.toLowerCase().includes(q));
        const tbody = document.querySelector('#tabelaUsuarios tbody');
        tbody.innerHTML = '';
        filtered.forEach(u => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${u.id}</td><td>${u.nome}</td><td>${u.email}</td><td>${u.planoNome || '-'}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary me-2" data-u-action="edit" data-id="${u.id}"><i class="fa-solid fa-pen"></i></button>
                    <button class="btn btn-sm btn-outline-danger" data-u-action="delete" data-id="${u.id}"><i class="fa-solid fa-trash"></i></button>
                </td>`;
            tbody.appendChild(tr);
        });
    });
});

// Popular logs com 5 dias de exemplo (somente se vazio)
(function seedLogsExample() {
    const existing = readFromStorage(STORAGE_KEYS.logs, []);
    if (existing.length > 0) return;
    const now = new Date();
    const demo = [];
    const actions = ['create','update','delete'];
    const entities = ['Unidade','Curso','Plano','Usuario'];
    for (let d = 0; d < 5; d++) {
        const day = new Date(now.getFullYear(), now.getMonth(), now.getDate() - d);
        for (let i = 0; i < 6; i++) {
            const at = new Date(day.getFullYear(), day.getMonth(), day.getDate(), 9 + i, Math.floor(Math.random()*59), Math.floor(Math.random()*59));
            const entity = entities[i % entities.length];
            const action = actions[i % actions.length];
            const id = 100 + d*10 + i;
            const summary = `${entity} demo ${i+1}`;
            demo.push({ at: at.toISOString(), entity, action, id, summary });
        }
    }
    writeToStorage(STORAGE_KEYS.logs, demo);
    renderTabelaLogs();
})();


