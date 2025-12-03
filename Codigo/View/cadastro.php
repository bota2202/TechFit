<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Cadastro</title>
    <link rel="icon" type="image/svg+xml" href="../Public/favicon.svg">
    <link rel="alternate icon" href="../Public/favicon.svg">
    <link rel="stylesheet" href="../Public/css/cadastro.css">
</head>
<body>

<div class="form-container">
    <div class="form-header">
        <h1>TECHFIT</h1>
        <p>Cadastro de Membro</p>
    </div>

    <form action="../../index.php?action=store" method="post" class="form-sign" novalidate>

        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill" id="progress-fill"></div>
            </div>
        </div>

        <main id="sign-fields">

            <section id="dadospessoais" class="form-step active">
                <h2>Dados Pessoais</h2>

                <div class="form-group">
                    <label for="nome-sign">Nome Completo</label>
                    <input type="text" name="nome" id="nome-sign" class="campo-sign" required>
                    <div class="error-message" id="error-nome"></div>
                </div>

                <div class="form-group">
                    <label for="email-sign">Email</label>
                    <input type="email" name="email" id="email-sign" class="campo-sign" required>
                    <div class="error-message" id="error-email"></div>
                </div>

                <div class="form-group">
                    <label for="cpf-sign">CPF</label>
                    <input type="text" name="cpf" id="cpf-sign" class="campo-sign" required>
                    <div class="error-message" id="error-cpf"></div>
                </div>

                <div class="form-group">
                    <label for="telefone-sign">Telefone</label>
                    <input type="text" name="telefone" id="telefone-sign" class="campo-sign" required>
                    <div class="error-message" id="error-telefone"></div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn-next" data-next="2">Próximo</button>
                </div>
            </section>

            <section id="endereco" class="form-step">
                <h2>Endereço</h2>

                <div class="form-group">
                    <label for="estado-sign">Estado</label>
                    <select name="estado" id="estado-sign" class="campo-sign" required>
                        <option value="">Selecione</option>
                        <option value="AC">Acre (AC)</option>
                        <option value="AL">Alagoas (AL)</option>
                        <option value="AP">Amapá (AP)</option>
                        <option value="AM">Amazonas (AM)</option>
                        <option value="BA">Bahia (BA)</option>
                        <option value="CE">Ceará (CE)</option>
                        <option value="DF">Distrito Federal (DF)</option>
                        <option value="ES">Espírito Santo (ES)</option>
                        <option value="GO">Goiás (GO)</option>
                        <option value="MA">Maranhão (MA)</option>
                        <option value="MT">Mato Grosso (MT)</option>
                        <option value="MS">Mato Grosso do Sul (MS)</option>
                        <option value="MG">Minas Gerais (MG)</option>
                        <option value="PA">Pará (PA)</option>
                        <option value="PB">Paraíba (PB)</option>
                        <option value="PR">Paraná (PR)</option>
                        <option value="PE">Pernambuco (PE)</option>
                        <option value="PI">Piauí (PI)</option>
                        <option value="RJ">Rio de Janeiro (RJ)</option>
                        <option value="RN">Rio Grande do Norte (RN)</option>
                        <option value="RS">Rio Grande do Sul (RS)</option>
                        <option value="RO">Rondônia (RO)</option>
                        <option value="RR">Roraima (RR)</option>
                        <option value="SC">Santa Catarina (SC)</option>
                        <option value="SP">São Paulo (SP)</option>
                        <option value="SE">Sergipe (SE)</option>
                        <option value="TO">Tocantins (TO)</option>
                    </select>
                    <div class="error-message" id="error-estado"></div>
                </div>

                <div class="form-group">
                    <label for="cidade-sign">Cidade</label>
                    <select name="cidade" id="cidade-sign" class="campo-sign" required disabled>
                        <option value="">Selecione primeiro um estado</option>
                    </select>
                    <div class="error-message" id="error-cidade"></div>
                </div>

                <div class="form-group">
                    <label for="bairro-sign">Bairro</label>
                    <input type="text" name="bairro" id="bairro-sign" class="campo-sign" required>
                    <div class="error-message" id="error-bairro"></div>
                </div>

                <div class="form-group">
                    <label for="rua-sign">Rua/Endereço</label>
                    <input type="text" name="rua" id="rua-sign" class="campo-sign" required>
                    <div class="error-message" id="error-rua"></div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn-prev" data-prev="1">Voltar</button>
                    <button type="button" class="btn-next" data-next="3">Próximo</button>
                </div>
            </section>

            <section id="senha" class="form-step">
                <h2>Segurança</h2>

                <div class="form-group">
                    <label for="senha-sign">Senha</label>
                    <input type="password" name="senha" id="senha-sign" class="campo-sign" required>
                    <div class="error-message" id="error-senha"></div>
                </div>

                <div class="form-group">
                    <label for="confirmar-sign">Confirmar senha</label>
                    <input type="password" name="confirmar" id="confirmar-sign" class="campo-sign" required>
                    <div class="error-message" id="error-confirmar"></div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn-prev" data-prev="2">Voltar</button>
                    <button type="submit">Cadastrar</button>
                </div>
            </section>

        </main>

        <?php
        if (isset($_SESSION['erro'])) {
            echo '<div class="alert alert-danger" id="alert-erro" style="margin-top: 20px; padding: 12px; background: #f8d7da; color: #721c24; border-radius: 8px;">' . htmlspecialchars($_SESSION['erro']) . '</div>';
            unset($_SESSION['erro']);
        }
        if (isset($_SESSION['sucesso'])) {
            echo '<div class="alert alert-success" id="alert-sucesso" style="margin-top: 20px; padding: 12px; background: #d4edda; color: #155724; border-radius: 8px;">' . htmlspecialchars($_SESSION['sucesso']) . '</div>';
            unset($_SESSION['sucesso']);
        }
        ?>
        <div class="links">
            <a href="telalogin.php">Já tem uma conta?</a>
            <a href="inicial.php">Voltar à tela inicial</a>
        </div>

    </form>
</div>

<script src="../Public/js/cadastro.js"></script>
<script>
    // Faz as mensagens de sucesso/erro desaparecerem após 7 segundos
    document.addEventListener('DOMContentLoaded', function() {
        const alertErro = document.getElementById('alert-erro');
        const alertSucesso = document.getElementById('alert-sucesso');
        
        if (alertErro) {
            setTimeout(function() {
                alertErro.style.transition = 'opacity 0.5s ease';
                alertErro.style.opacity = '0';
                setTimeout(function() {
                    alertErro.remove();
                }, 500);
            }, 7000);
        }
        
        if (alertSucesso) {
            setTimeout(function() {
                alertSucesso.style.transition = 'opacity 0.5s ease';
                alertSucesso.style.opacity = '0';
                setTimeout(function() {
                    alertSucesso.remove();
                }, 500);
            }, 7000);
        }
    });
</script>
</body>
</html>

