document.getElementById('estado-sign').addEventListener("change", function() {
    const first = this.querySelector("option[value='']");
    if (first) first.remove();
});

document.getElementById("estado-sign").addEventListener("change", async function() {
    const uf = this.value;
    const cidadesSelect = document.getElementById("cidade-sign");

    cidadesSelect.disabled = false;
    cidadesSelect.innerHTML = "<option value=''>Carregando...</option>";

    if (!uf) return;

    try {
        const resposta = await fetch(
            `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`
        );
        const dados = await resposta.json();

        cidadesSelect.innerHTML = "<option value=''>Selecione uma cidade</option>";

        dados.forEach(c => {
            const option = document.createElement("option");
            option.value = c.nome;
            option.textContent = c.nome;
            cidadesSelect.appendChild(option);
        });
    } catch (erro) {
        cidadesSelect.innerHTML = "<option>Erro ao carregar cidades</option>";
        console.error('Erro ao buscar cidades:', erro);
    }
});

document.getElementById('cidade-sign').addEventListener("change", function() {
    const first = this.querySelector("option[value='']");
    if (first) first.remove();
});

document.getElementById('cpf-sign').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    }
});

document.getElementById('telefone-sign').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length <= 11) {
        value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
        value = value.replace(/(\d)(\d{4})$/, '$1-$2');
        e.target.value = value;
    }
});

document.querySelector('.form-sign').addEventListener('submit', function(e) {
    const senha = document.getElementById('senha-sign').value;
    const confirmar = document.getElementById('confirmar-sign').value;

    if (senha !== confirmar) {
        e.preventDefault();
        alert('As senhas não coincidem!');
        document.getElementById('confirmar-sign').focus();
        return false;
    }

    if (senha.length < 6) {
        e.preventDefault();
        alert('A senha deve ter pelo menos 6 caracteres!');
        document.getElementById('senha-sign').focus();
        return false;
    }
});