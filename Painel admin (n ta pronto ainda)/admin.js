const filiais = [
  "Limeira (Matriz)","Campinas","Piracicaba","Sorocaba","Ribeirão Preto",
  "Araraquara","São Carlos","Jundiaí","Bauru","São José do Rio Preto",
  "Marília","Presidente Prudente","Americana","Indaiatuba","Barueri"
];

const cursos = [
  "Musculação","Yoga","Pilates","CrossFit","Spinning",
  "Zumba","Muay Thai","Natação","Treinamento Funcional"
];

let usuarios = [];
let alunosPorCurso = cursos.reduce((acc, curso) => ({ ...acc, [curso]: [] }), {});

function atualizarFiliais() {
  const listaFiliais = document.getElementById('lista-filiais');
  listaFiliais.innerHTML = '';
  filiais.forEach(f => {
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between align-items-center';
    li.textContent = f;
    listaFiliais.appendChild(li);
  });
}

function atualizarTabelaUsuarios() {
  const tabela = document.getElementById('tabela-usuarios-geral');
  tabela.innerHTML = `
    <div class="mb-3 d-flex">
      <input type="text" id="pesquisaUsuarios" class="form-control me-2" placeholder="Pesquisar por nome">
      <select id="filialFiltro" class="form-select">
        <option value="">Todas filiais</option>
        ${filiais.map(f => `<option value="${f}">${f}</option>`).join('')}
      </select>
      <button class="btn btn-success ms-2" id="btnAddUsuario">Adicionar</button>
    </div>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th><th>Nome</th><th>Email</th><th>Filial</th><th>Ações</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  `;
  const tbody = tabela.querySelector('tbody');
  let lista = [...usuarios];
  const filtroNome = tabela.querySelector('#pesquisaUsuarios').value.toLowerCase();
  const filtroFilial = tabela.querySelector('#filialFiltro').value;
  if(filtroNome) lista = lista.filter(u => u.nome.toLowerCase().includes(filtroNome));
  if(filtroFilial) lista = lista.filter(u => u.filial === filtroFilial);
  lista.forEach((u, i) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${i+1}</td>
      <td>${u.nome}</td>
      <td>${u.email}</td>
      <td>${u.filial}</td>
      <td>
        <button class="btn btn-sm btn-primary btnEditar" data-index="${i}">Editar</button>
        <button class="btn btn-sm btn-danger btnExcluir" data-index="${i}">Excluir</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
  tabela.querySelector('#pesquisaUsuarios').addEventListener('input', atualizarTabelaUsuarios);
  tabela.querySelector('#filialFiltro').addEventListener('change', atualizarTabelaUsuarios);
  tabela.querySelector('#btnAddUsuario').addEventListener('click', () => abrirModalUsuario());
  tbody.querySelectorAll('.btnEditar').forEach(btn => btn.addEventListener('click', e => abrirModalUsuario(e.target.dataset.index)));
  tbody.querySelectorAll('.btnExcluir').forEach(btn => btn.addEventListener('click', e => { usuarios.splice(e.target.dataset.index,1); atualizarTabelaUsuarios(); }));
}

function abrirModalUsuario(index = null) {
  const modal = new bootstrap.Modal(document.getElementById('modalUsuarios'));
  const tbody = document.getElementById('tabelaUsuariosFilial').querySelector('tbody');
  tbody.innerHTML = '';
  usuarios.forEach((u,i) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${i+1}</td>
      <td><input type="text" class="form-control nomeUsuario" value="${u.nome}"></td>
      <td><input type="email" class="form-control emailUsuario" value="${u.email}"></td>
      <td>
        <select class="form-select filialUsuario">
          ${filiais.map(f => `<option value="${f}" ${f===u.filial?'selected':''}>${f}</option>`).join('')}
        </select>
      </td>
      <td>
        <button class="btn btn-success btnSalvar" data-index="${i}">Salvar</button>
        <button class="btn btn-danger btnExcluir" data-index="${i}">Excluir</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
  tbody.querySelectorAll('.btnSalvar').forEach(btn => btn.addEventListener('click', e => {
    const i = e.target.dataset.index;
    const tr = tbody.children[i];
    usuarios[i].nome = tr.querySelector('.nomeUsuario').value;
    usuarios[i].email = tr.querySelector('.emailUsuario').value;
    usuarios[i].filial = tr.querySelector('.filialUsuario').value;
    atualizarTabelaUsuarios();
  }));
  tbody.querySelectorAll('.btnExcluir').forEach(btn => btn.addEventListener('click', e => {
    usuarios.splice(e.target.dataset.index,1);
    atualizarTabelaUsuarios();
    abrirModalUsuario();
  }));
  modal.show();
}

function atualizarCursos() {
  const lista = document.getElementById('lista-cursos');
  lista.innerHTML = '';
  cursos.forEach(c => {
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between align-items-center';
    li.innerHTML = `${c} <button class="btn btn-sm btn-success btnAddAluno" data-curso="${c}">Adicionar Aluno</button>`;
    lista.appendChild(li);
  });
  lista.querySelectorAll('.btnAddAluno').forEach(btn => {
    btn.addEventListener('click', e => {
      const curso = e.target.dataset.curso;
      const nome = prompt(`Digite o nome do aluno para o curso ${curso}:`);
      if(nome) {
        const usuario = usuarios.find(u => u.nome===nome);
        if(usuario && !alunosPorCurso[curso].includes(usuario.nome)) alunosPorCurso[curso].push(usuario.nome);
        alert('Aluno adicionado!');
      }
    });
  });
}

function init() {
  atualizarFiliais();
  atualizarTabelaUsuarios();
  atualizarCursos();
}

document.addEventListener('DOMContentLoaded', init);
