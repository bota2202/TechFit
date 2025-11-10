document.getElementById('estado-sign').addEventListener("change",function(){
    const first=this.querySelector("option[value='']");
    if(first)first.remove();
})

document.getElementById("estado-sign").addEventListener("change", async function () {
    const uf = this.value;
    const cidadesSelect = document.getElementById("cidade-sign");

    cidadesSelect.disabled=false;

    cidadesSelect.innerHTML = "<option value=''>Carregando...</option>";

    if (!uf) return;

    try {
        const resposta = await fetch(
            `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`
        );
        const dados = await resposta.json();

        cidadesSelect.innerHTML = "<option value=''>Selecione...</option>";

        dados.forEach(c => {
            const option = document.createElement("option");
            option.value = c.nome;
            option.textContent = c.nome;
            cidadesSelect.appendChild(option);
        });
    } catch (erro) {
        cidadesSelect.innerHTML = "<option>Erro ao carregar</option>";
    }
});

