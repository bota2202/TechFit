const API_BASE = '../DB/';

let mensagemDiv = null;

function mostrarMensagem(mensagem, tipo) {
    if (!mensagemDiv) {
        mensagemDiv = document.createElement('div');
        mensagemDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; padding: 15px 20px; border-radius: 8px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);';
        document.body.appendChild(mensagemDiv);
    }
    
    mensagemDiv.textContent = mensagem;
    mensagemDiv.style.display = 'block';
    mensagemDiv.style.background = tipo === 'erro' ? '#e74c3c' : '#27ae60';
    mensagemDiv.style.color = 'white';
    
    setTimeout(() => {
        mensagemDiv.style.display = 'none';
    }, 5000);
}

async function carregarDashboard() {
    try {
        const [usuarios, planos, cursos, turmas, unidades, pagamentos] = await Promise.all([
            fetch(API_BASE + 'api_usuarios.php').then(r => r.json()),
            fetch(API_BASE + 'api_planos.php').then(r => r.json()),
            fetch(API_BASE + 'api_cursos.php').then(r => r.json()),
            fetch(API_BASE + 'api_turmas.php').then(r => r.json()),
            fetch(API_BASE + 'api_unidades.php').then(r => r.json()),
            fetch(API_BASE + 'api_pagamentos.php').then(r => r.json())
        ]);

        document.getElementById('total-usuarios').textContent = usuarios.usuarios?.length || 0;
        document.getElementById('total-planos').textContent = planos.planos?.length || 0;
        document.getElementById('total-cursos').textContent = cursos.cursos?.length || 0;
        document.getElementById('total-turmas').textContent = turmas.turmas?.length || 0;
        document.getElementById('total-unidades').textContent = unidades.unidades?.length || 0;
        
        const mesAtual = new Date().getMonth();
        const pagamentosMes = pagamentos.pagamentos?.filter(p => {
            const dataPag = new Date(p.data_pagamento);
            return dataPag.getMonth() === mesAtual;
        }).length || 0;
        document.getElementById('total-pagamentos').textContent = pagamentosMes;
    } catch (error) {
        mostrarMensagem(`Erro ao carregar dashboard: ${error.message}`, 'erro');
    }
}

async function carregarUsuarios() {
    try {
        const response = await fetch(API_BASE + 'api_usuarios.php');
        const data = await response.json();
        
        const tbody = document.getElementById('usuarios-table-body');
        tbody.innerHTML = '';
        
        if (data.usuarios) {
            data.usuarios.forEach(usuario => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${usuario.id_usuario}</td>
                    <td>${usuario.nome_usuario}</td>
                    <td>${usuario.email_usuario}</td>
                    <td>${usuario.cpf_usuario}</td>
                    <td>${usuario.tipo_usuario == 1 ? 'Admin' : 'Usuário'}</td>
                    <td>
                        <button class="btn-action btn-edit" onclick="editarUsuario(${usuario.id_usuario})">Editar</button>
                        <button class="btn-action btn-delete" onclick="deletarUsuario(${usuario.id_usuario})">Deletar</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (error) {
        mostrarMensagem(`Erro ao carregar usuários: ${error.message}`, 'erro');
    }
}

async function carregarPlanos() {
    try {
        const response = await fetch(API_BASE + 'api_planos.php');
        const data = await response.json();
        
        const tbody = document.getElementById('planos-table-body');
        tbody.innerHTML = '';
        
        if (data.planos) {
            data.planos.forEach(plano => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${plano.id_plano}</td>
                    <td>R$ ${parseFloat(plano.preco_plano).toFixed(2)}</td>
                    <td>${plano.descricao_plano}</td>
                    <td>
                        <button class="btn-action btn-edit" onclick="editarPlano(${plano.id_plano})">Editar</button>
                        <button class="btn-action btn-delete" onclick="deletarPlano(${plano.id_plano})">Deletar</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (error) {
        mostrarMensagem(`Erro ao carregar planos: ${error.message}`, 'erro');
    }
}

async function carregarCursos() {
    try {
        const response = await fetch(API_BASE + 'api_cursos.php');
        const data = await response.json();
        
        const tbody = document.getElementById('cursos-table-body');
        tbody.innerHTML = '';
        
        if (data.cursos) {
            data.cursos.forEach(curso => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${curso.id_curso}</td>
                    <td>${curso.nome_curso}</td>
                    <td>${curso.tipo_curso}</td>
                    <td>R$ ${parseFloat(curso.preco_curso).toFixed(2)}</td>
                    <td>
                        <button class="btn-action btn-edit" onclick="editarCurso(${curso.id_curso})">Editar</button>
                        <button class="btn-action btn-delete" onclick="deletarCurso(${curso.id_curso})">Deletar</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (error) {
        mostrarMensagem(`Erro ao carregar cursos: ${error.message}`, 'erro');
    }
}

async function carregarTurmas() {
    try {
        const response = await fetch(API_BASE + 'api_turmas.php');
        const data = await response.json();
        
        const tbody = document.getElementById('turmas-table-body');
        tbody.innerHTML = '';
        
        if (data.turmas) {
            data.turmas.forEach(turma => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${turma.id_turma}</td>
                    <td>${turma.nome_turma}</td>
                    <td>${turma.nome_curso}</td>
                    <td>${turma.responsavel_nome || 'N/A'}</td>
                    <td>${turma.horario_turma || 'N/A'}</td>
                    <td>
                        <button class="btn-action btn-edit" onclick="editarTurma(${turma.id_turma})">Editar</button>
                        <button class="btn-action btn-delete" onclick="deletarTurma(${turma.id_turma})">Deletar</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (error) {
        mostrarMensagem(`Erro ao carregar turmas: ${error.message}`, 'erro');
    }
}

async function carregarUnidades() {
    try {
        const response = await fetch(API_BASE + 'api_unidades.php');
        const data = await response.json();
        
        const tbody = document.getElementById('unidades-table-body');
        tbody.innerHTML = '';
        
        if (data.unidades) {
            data.unidades.forEach(unidade => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${unidade.id_unidade}</td>
                    <td>${unidade.cidade_unidade}</td>
                    <td>${unidade.estado_unidade}</td>
                    <td>${unidade.bairro_unidade}</td>
                    <td>${unidade.rua_unidade}, ${unidade.numero_unidade}</td>
                    <td>
                        <button class="btn-action btn-edit" onclick="editarUnidade(${unidade.id_unidade})">Editar</button>
                        <button class="btn-action btn-delete" onclick="deletarUnidade(${unidade.id_unidade})">Deletar</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (error) {
        mostrarMensagem(`Erro ao carregar unidades: ${error.message}`, 'erro');
    }
}

async function carregarPagamentos() {
    try {
        const response = await fetch(API_BASE + 'api_pagamentos.php');
        const data = await response.json();
        
        const tbody = document.getElementById('pagamentos-table-body');
        tbody.innerHTML = '';
        
        if (data.pagamentos) {
            data.pagamentos.forEach(pagamento => {
                const dataPag = new Date(pagamento.data_pagamento);
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${pagamento.id_pagamento}</td>
                    <td>${pagamento.nome_usuario}</td>
                    <td>${pagamento.descricao_plano}</td>
                    <td>R$ ${parseFloat(pagamento.valor_pagamento).toFixed(2)}</td>
                    <td>${dataPag.toLocaleDateString('pt-BR')}</td>
                    <td>
                        <button class="btn-action btn-delete" onclick="deletarPagamento(${pagamento.id_pagamento})">Deletar</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (error) {
        mostrarMensagem(`Erro ao carregar pagamentos: ${error.message}`, 'erro');
    }
}

async function carregarPresencas() {
    try {
        const response = await fetch(API_BASE + 'api_presencas.php');
        const data = await response.json();
        
        const tbody = document.getElementById('presencas-table-body');
        tbody.innerHTML = '';
        
        if (data.presencas) {
            data.presencas.forEach(presenca => {
                const dataAula = new Date(presenca.data_aula);
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${presenca.id_presenca}</td>
                    <td>${presenca.nome_usuario}</td>
                    <td>${presenca.nome_turma}</td>
                    <td>${dataAula.toLocaleDateString('pt-BR')}</td>
                    <td>${presenca.presente ? 'Sim' : 'Não'}</td>
                    <td>
                        <button class="btn-action btn-edit" onclick="editarPresenca(${presenca.id_presenca})">Editar</button>
                        <button class="btn-action btn-delete" onclick="deletarPresenca(${presenca.id_presenca})">Deletar</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (error) {
        mostrarMensagem(`Erro ao carregar presenças: ${error.message}`, 'erro');
    }
}

async function deletarUsuario(id) {
    if (!confirm('Tem certeza que deseja deletar este usuário?')) return;
    
    try {
        const response = await fetch(`${API_BASE}api_usuarios.php?id=${id}`, { method: 'DELETE' });
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error(`Erro ao processar resposta: ${text.substring(0, 200)}`);
        }
        
        if (data.success) {
            mostrarMensagem('Usuário deletado com sucesso!', 'sucesso');
            carregarUsuarios();
            carregarDashboard();
        } else {
            mostrarMensagem(`Erro ao deletar usuário: ${data.message || 'Erro desconhecido'}`, 'erro');
        }
    } catch (error) {
        mostrarMensagem(`Erro ao deletar usuário: ${error.message}`, 'erro');
    }
}

async function deletarPlano(id) {
    if (!confirm('Tem certeza que deseja deletar este plano?')) return;
    
    try {
        const response = await fetch(`${API_BASE}api_planos.php?id=${id}`, { method: 'DELETE' });
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error(`Erro ao processar resposta: ${text.substring(0, 200)}`);
        }
        
        if (data.success) {
            mostrarMensagem('Plano deletado com sucesso!', 'sucesso');
            carregarPlanos();
            carregarDashboard();
        } else {
            mostrarMensagem(`Erro ao deletar plano: ${data.message || 'Erro desconhecido'}`, 'erro');
        }
    } catch (error) {
        mostrarMensagem(`Erro ao deletar plano: ${error.message}`, 'erro');
    }
}

async function deletarCurso(id) {
    if (!confirm('Tem certeza que deseja deletar este curso?')) return;
    
    try {
        const response = await fetch(`${API_BASE}api_cursos.php?id=${id}`, { method: 'DELETE' });
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error(`Erro ao processar resposta: ${text.substring(0, 200)}`);
        }
        
        if (data.success) {
            mostrarMensagem('Curso deletado com sucesso!', 'sucesso');
            carregarCursos();
            carregarDashboard();
        } else {
            mostrarMensagem(`Erro ao deletar curso: ${data.message || 'Erro desconhecido'}`, 'erro');
        }
    } catch (error) {
        mostrarMensagem(`Erro ao deletar curso: ${error.message}`, 'erro');
    }
}

async function deletarTurma(id) {
    if (!confirm('Tem certeza que deseja deletar esta turma?')) return;
    
    try {
        const response = await fetch(`${API_BASE}api_turmas.php?id=${id}`, { method: 'DELETE' });
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error(`Erro ao processar resposta: ${text.substring(0, 200)}`);
        }
        
        if (data.success) {
            mostrarMensagem('Turma deletada com sucesso!', 'sucesso');
            carregarTurmas();
            carregarDashboard();
        } else {
            mostrarMensagem(`Erro ao deletar turma: ${data.message || 'Erro desconhecido'}`, 'erro');
        }
    } catch (error) {
        mostrarMensagem(`Erro ao deletar turma: ${error.message}`, 'erro');
    }
}

async function deletarUnidade(id) {
    if (!confirm('Tem certeza que deseja deletar esta unidade?')) return;
    
    try {
        const response = await fetch(`${API_BASE}api_unidades.php?id=${id}`, { method: 'DELETE' });
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error(`Erro ao processar resposta: ${text.substring(0, 200)}`);
        }
        
        if (data.success) {
            mostrarMensagem('Unidade deletada com sucesso!', 'sucesso');
            carregarUnidades();
            carregarDashboard();
        } else {
            mostrarMensagem(`Erro ao deletar unidade: ${data.message || 'Erro desconhecido'}`, 'erro');
        }
    } catch (error) {
        mostrarMensagem(`Erro ao deletar unidade: ${error.message}`, 'erro');
    }
}

async function deletarPagamento(id) {
    if (!confirm('Tem certeza que deseja deletar este pagamento?')) return;
    
    try {
        const response = await fetch(`${API_BASE}api_pagamentos.php?id=${id}`, { method: 'DELETE' });
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error(`Erro ao processar resposta: ${text.substring(0, 200)}`);
        }
        
        if (data.success) {
            mostrarMensagem('Pagamento deletado com sucesso!', 'sucesso');
            carregarPagamentos();
            carregarDashboard();
        } else {
            mostrarMensagem(`Erro ao deletar pagamento: ${data.message || 'Erro desconhecido'}`, 'erro');
        }
    } catch (error) {
        mostrarMensagem(`Erro ao deletar pagamento: ${error.message}`, 'erro');
    }
}

async function deletarPresenca(id) {
    if (!confirm('Tem certeza que deseja deletar esta presença?')) return;
    
    try {
        const response = await fetch(`${API_BASE}api_presencas.php?id=${id}`, { method: 'DELETE' });
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error(`Erro ao processar resposta: ${text.substring(0, 200)}`);
        }
        
        if (data.success) {
            mostrarMensagem('Presença deletada com sucesso!', 'sucesso');
            carregarPresencas();
        } else {
            mostrarMensagem(`Erro ao deletar presença: ${data.message || 'Erro desconhecido'}`, 'erro');
        }
    } catch (error) {
        mostrarMensagem(`Erro ao deletar presença: ${error.message}`, 'erro');
    }
}

function abrirModalUsuario() {
    mostrarMensagem('Funcionalidade de criar/editar usuário será implementada', 'erro');
}

function abrirModalPlano() {
    mostrarMensagem('Funcionalidade de criar/editar plano será implementada', 'erro');
}

function abrirModalCurso() {
    mostrarMensagem('Funcionalidade de criar/editar curso será implementada', 'erro');
}

function abrirModalTurma() {
    mostrarMensagem('Funcionalidade de criar/editar turma será implementada', 'erro');
}

function abrirModalUnidade() {
    mostrarMensagem('Funcionalidade de criar/editar unidade será implementada', 'erro');
}

function abrirModalPagamento() {
    mostrarMensagem('Funcionalidade de criar pagamento será implementada', 'erro');
}

function abrirModalPresenca() {
    mostrarMensagem('Funcionalidade de criar presença será implementada', 'erro');
}

function editarUsuario(id) {
    mostrarMensagem(`Editar usuário ${id} - funcionalidade será implementada`, 'erro');
}

function editarPlano(id) {
    mostrarMensagem(`Editar plano ${id} - funcionalidade será implementada`, 'erro');
}

function editarCurso(id) {
    mostrarMensagem(`Editar curso ${id} - funcionalidade será implementada`, 'erro');
}

function editarTurma(id) {
    mostrarMensagem(`Editar turma ${id} - funcionalidade será implementada`, 'erro');
}

function editarUnidade(id) {
    mostrarMensagem(`Editar unidade ${id} - funcionalidade será implementada`, 'erro');
}

function editarPresenca(id) {
    mostrarMensagem(`Editar presença ${id} - funcionalidade será implementada`, 'erro');
}

document.addEventListener('DOMContentLoaded', () => {
    carregarDashboard();
    
    document.getElementById('usuarios-tab').addEventListener('shown.bs.tab', carregarUsuarios);
    document.getElementById('planos-tab').addEventListener('shown.bs.tab', carregarPlanos);
    document.getElementById('cursos-tab').addEventListener('shown.bs.tab', carregarCursos);
    document.getElementById('turmas-tab').addEventListener('shown.bs.tab', carregarTurmas);
    document.getElementById('unidades-tab').addEventListener('shown.bs.tab', carregarUnidades);
    document.getElementById('pagamentos-tab').addEventListener('shown.bs.tab', carregarPagamentos);
    document.getElementById('presencas-tab').addEventListener('shown.bs.tab', carregarPresencas);
});
