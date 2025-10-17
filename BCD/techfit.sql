CREATE DATABASE TechFit;
USE TechFit;

CREATE TABLE Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    senha_usuario_hash VARCHAR(255) NOT NULL,
    nome_usuario VARCHAR(255) NOT NULL,
    telefone_usuario VARCHAR(14) NOT NULL,
    cpf_usuario VARCHAR(14) UNIQUE NOT NULL,
    tipo_usuario TINYINT NOT NULL,
    endereco_usuario varchar(255)
);

CREATE TABLE Unidades (
    id_unidade INT AUTO_INCREMENT PRIMARY KEY,
    estado_unidade VARCHAR(100) NOT NULL,
    cidade_unidade VARCHAR(100) NOT NULL,
    bairro_unidade VARCHAR(100) NOT NULL,
    rua_unidade VARCHAR(100) NOT NULL,
    numero_unidade INT NOT NULL
);

CREATE TABLE Planos (
    id_plano INT AUTO_INCREMENT PRIMARY KEY,
    preco_plano DECIMAL(5,2) NOT NULL,
    descricao_plano VARCHAR(500)
);

CREATE TABLE Cursos (
    id_curso INT AUTO_INCREMENT PRIMARY KEY,
    nome_curso VARCHAR(255) NOT NULL,
    tipo_curso VARCHAR(100) NOT NULL,
    descricao_curso VARCHAR(500),
    preco_curso DECIMAL(5,2) NOT NULL
);

CREATE TABLE Turmas (
    id_turma INT AUTO_INCREMENT PRIMARY KEY,
    id_curso INT NOT NULL,
    responsavel_turma int not null,
    nome_turma VARCHAR(255) NOT NULL,
    data_inicio DATETIME NOT NULL,
    data_fim DATETIME NOT NULL,
    horario_turma VARCHAR(100),
    FOREIGN KEY (id_curso) REFERENCES Cursos (id_curso),
    foreign key(responsavel_turma) references Usuarios (id_usuario)
);

CREATE TABLE Usuario_Turma (
    id_turma INT NOT NULL,
    id_usuario INT NOT NULL,
    data_matricula DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_turma, id_usuario),
    FOREIGN KEY (id_turma) REFERENCES Turmas (id_turma),
    FOREIGN KEY (id_usuario) REFERENCES Usuarios (id_usuario)
);

create table Usuario_Plano(
	id_plano int not null,
    id_usuario int not null,
    data_inicio_plano datetime not null,
    data_fim_plano datetime,
    primary key(id_plano,id_usuario),
    foreign key(id_plano) references Planos (id_plano),
    foreign key(id_usuario) references Usuarios (id_usuario)
);

create table Pagamentos(
	id_pagamento int not null auto_increment,
    id_usuario int not null,
    id_plano int not null,
    tipo_pagamento tinyint,
    valor_pagamento decimal(5,2) not null,
    data_pagamento datetime not null default(current_timestamp()),
    primary key(id_pagamento),
    foreign key(id_usuario) references Usuarios (id_usuario),
    foreign key (id_plano) references Planos (id_plano)
);

CREATE TABLE Presencas (
    id_presenca INT AUTO_INCREMENT PRIMARY KEY,
    id_turma INT NOT NULL,
    id_usuario INT NOT NULL,
    data_aula DATE NOT NULL,
    presente BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (id_turma) REFERENCES Turmas(id_turma),
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
);

CREATE TABLE Logs (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    acao VARCHAR(255) NOT NULL,
    data_acao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
);

INSERT INTO Cursos (id_curso, nome_curso, tipo_curso, descricao_curso, preco_curso)
VALUES
(1, 'Musculação', 'forca', 'Treinos completos para ganho de massa e força com acompanhamento profissional especializado.', 99.00),
(2, 'Yoga', 'mente-corpo', 'Equilíbrio, alongamento e bem-estar físico e mental com professores especializados.', 149.00),
(3, 'Pilates', 'mente-corpo', 'Fortaleça seu corpo e melhore sua postura com aulas modernas de Pilates.', 149.00),
(4, 'CrossFit', 'forca', 'Treinos intensos de alta performance para resistência e condicionamento físico.', 199.00),
(5, 'Spinning', 'cardio', 'Aulas dinâmicas de bike indoor com muita energia e queima calórica intensa.', 149.00),
(6, 'Zumba', 'cardio', 'Dance, divirta-se e entre em forma com coreografias animadas e intensas.', 99.00),
(7, 'Muay Thai', 'lutas', 'Defesa pessoal e condicionamento físico com artes marciais de alto impacto.', 199.00),
(8, 'Natação', 'cardio', 'Aulas para todas as idades, desenvolvendo resistência e saúde cardiovascular.', 149.00),
(9, 'Treinamento Funcional', 'forca', 'Movimentos naturais para melhorar força, coordenação e qualidade de vida.', 149.00);

INSERT INTO Unidades (id_unidade, estado_unidade, cidade_unidade, bairro_unidade, rua_unidade, numero_unidade)
VALUES
(1, 'SP', 'Limeira', 'Centro', 'Av. Campinas', 1500),
(2, 'SP', 'Campinas', 'Centro', 'R. Barão de Jaguara', 900),
(3, 'SP', 'Piracicaba', 'Centro', 'Av. Independência', 2100),
(4, 'SP', 'Sorocaba', 'Centro', 'R. XV de Novembro', 450),
(5, 'SP', 'Ribeirão Preto', 'Jardim Paulista', 'Av. Pres. Vargas', 1800),
(6, 'SP', 'Araraquara', 'Centro', 'Av. Bento de Abreu', 300),
(7, 'SP', 'São Carlos', 'Centro', 'R. Episcopal', 1200),
(8, 'SP', 'Jundiaí', 'Anhangabaú', 'Av. Jundiaí', 500),
(9, 'SP', 'Bauru', 'Centro', 'Av. Getúlio Vargas', 100),
(10, 'SP', 'São José do Rio Preto', 'Boa Vista', 'R. Bernardino', 950),
(11, 'SP', 'Marília', 'Fragata', 'Av. das Esmeraldas', 400),
(12, 'SP', 'Presidente Prudente', 'Vila Nova', 'Av. Manoel Goulart', 1300),
(13, 'SP', 'Americana', 'Centro', 'R. Fernando Camargo', 800),
(14, 'SP', 'Indaiatuba', 'Cidade Nova', 'Av. Pres. Kennedy', 700),
(15, 'SP', 'Barueri', 'Alphaville', 'Av. Arnaldo Rodrigues', 250);

INSERT INTO Planos (preco_plano, descricao_plano)
VALUES
(99.00, '1 unidade, Musculação, App TechFit, sem aulas coletivas, sem Personal Trainer'),
(149.00, 'Todas unidades, Musculação, 4 aulas coletivas/mês, App TechFit, sem Personal Trainer'),
(199.00, 'Todas unidades, Todos os cursos, Aulas ilimitadas, App TechFit Pro, 2 sessões personal/mês');

INSERT INTO Usuarios (nome_usuario, senha_usuario_hash, telefone_usuario, cpf_usuario, tipo_usuario, endereco_usuario)
VALUES
('ADMIN', 'hash123', '(19)11111-1111', '111.111.111-11', 0, 'Rua João Gomes de Pinho, 83, Limeira'),
('Gabriel Ramos', 'hash234', '(19)22222-2222', '222.222.222-22', 1, 'Chácara, 150, Limeira'),
('Daniel Charlo', 'hash345', '(19)33333-3333', '333.333.333-33', 2, 'Luz do Sol, 200, Campinas'),
('Gabriel Xavier', 'hash456', '(19)44444-4444', '444.444.444-44', 3, 'Rodinha, 50, Piracicaba'),
('Eduardo Rocha', 'hash567', '(19)55555-5555', '555.555.555-55', 3, 'Rave, 80, Sorocaba'),
('Artur Maia', 'hash678', '(19)66666-6666', '666.666.666-66', 3, 'Paulínia, 120, Ribeirão Preto'),
('Douglas Júnior', 'hash789', '(19)77777-7777', '777.777.777-77', 3, 'Condomínio, 25, Araraquara'),
('Murilo Coletta', 'hash890', '(19)88888-8888', '888.888.888-88', 3, 'Cordeiro, 300, São Carlos');

INSERT INTO Turmas (id_curso, responsavel_turma, nome_turma, data_inicio, data_fim, horario_turma)
VALUES
(1, 2, 'Musculação Manhã', '2025-10-20', '2026-01-20', '07:00 - 08:00'),
(1, 2, 'Musculação Noite', '2025-10-20', '2026-01-20', '19:00 - 20:00'),
(2, 3, 'Yoga Tarde', '2025-10-21', '2026-01-21', '17:00 - 18:15'),
(3, 3, 'Pilates Noite', '2025-10-22', '2026-01-22', '19:00 - 19:55'),
(4, 2, 'CrossFit Intermediário', '2025-10-20', '2026-01-20', '06:00 - 07:00'),
(5, 2, 'Spinning Intensivo', '2025-10-21', '2026-01-21', '18:00 - 18:45'),
(6, 3, 'Zumba Iniciante', '2025-10-23', '2026-01-23', '17:30 - 18:20'),
(7, 2, 'Muay Thai Avançado', '2025-10-24', '2026-01-24', '19:00 - 20:10'),
(8, 3, 'Natação Adulto', '2025-10-20', '2026-01-20', '08:00 - 08:45'),
(9, 2, 'Treinamento Funcional', '2025-10-21', '2026-01-21', '07:30 - 08:25');

INSERT INTO Usuario_Turma (id_turma, id_usuario)
VALUES
(1, 4), (2, 5), (3, 6), (4, 7),
(5, 4), (6, 5), (7, 6), (8, 7),
(9, 4);

INSERT INTO Usuario_Plano (id_plano, id_usuario, data_inicio_plano, data_fim_plano)
VALUES
(1, 4, '2025-10-01', '2026-01-01'),
(2, 5, '2025-10-01', '2026-01-01'),
(3, 6, '2025-10-01', '2026-01-01'),
(2, 7, '2025-10-01', '2026-01-01');

INSERT INTO Pagamentos (id_usuario, id_plano, tipo_pagamento, valor_pagamento)
VALUES
(4, 1, 1, 99.00),
(5, 2, 2, 149.00),
(6, 3, 1, 199.00),
(7, 2, 2, 149.00);

INSERT INTO Presencas (id_turma, id_usuario, data_aula, presente)
VALUES
(1, 4, '2025-10-21', TRUE),
(2, 5, '2025-10-21', TRUE),
(3, 6, '2025-10-22', TRUE),
(4, 7, '2025-10-22', TRUE),
(5, 4, '2025-10-23', FALSE),
(6, 5, '2025-10-23', TRUE),
(7, 6, '2025-10-24', TRUE),
(8, 7, '2025-10-24', TRUE),
(9, 4, '2025-10-25', TRUE);

