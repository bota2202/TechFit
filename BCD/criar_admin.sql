-- Script para criar o usuário administrador inicial
-- IMPORTANTE: Execute este script após criar o banco de dados

USE TechFit;

-- Criar usuário admin (id = 1)
-- Senha padrão: admin123 (você deve alterar após o primeiro login)
INSERT INTO Usuarios (
    id_usuario,
    email_usuario,
    senha_usuario_hash,
    nome_usuario,
    telefone_usuario,
    cpf_usuario,
    tipo_usuario,
    cidade_usario,
    estado_usuario,
    bairro_usuario,
    rua_usuario
) VALUES (
    1,
    'admin@techfit.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- senha: admin123
    'Administrador TechFit',
    '(19) 99999-9999',
    '00000000000',
    1, -- tipo_usuario = 1 significa admin
    'Limeira',
    'SP',
    'Centro',
    'Av. Admin'
);

-- Verificar se foi criado corretamente
SELECT id_usuario, email_usuario, nome_usuario, tipo_usuario 
FROM Usuarios 
WHERE id_usuario = 1;

