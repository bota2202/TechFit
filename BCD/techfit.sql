CREATE DATABASE techfit;
USE techfit;

CREATE TABLE Unidade (
    id_unidade INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cidade VARCHAR(100)
);

CREATE TABLE Plano (
    id_plano INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    valor_mensal DECIMAL(10,2) NOT NULL
);

CREATE TABLE Aluno (
    id_aluno INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    id_unidade INT NOT NULL,
    id_plano INT NOT NULL,
    FOREIGN KEY (id_unidade) REFERENCES Unidade(id_unidade),
    FOREIGN KEY (id_plano) REFERENCES Plano(id_plano)
);

CREATE TABLE Professor (
    id_professor INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    id_unidade INT NOT NULL,
    FOREIGN KEY (id_unidade) REFERENCES Unidade(id_unidade)
);

CREATE TABLE Treino (
    id_treino INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50),
    id_aluno INT NOT NULL,
    id_professor INT NOT NULL,
    FOREIGN KEY (id_aluno) REFERENCES Aluno(id_aluno),
    FOREIGN KEY (id_professor) REFERENCES Professor(id_professor)
);

CREATE TABLE Curso (
    id_curso INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    id_professor INT NOT NULL,
    FOREIGN KEY (id_professor) REFERENCES Professor(id_professor)
);

CREATE TABLE Aluno_Curso (
    id_aluno INT NOT NULL,
    id_curso INT NOT NULL,
    PRIMARY KEY (id_aluno, id_curso),
    FOREIGN KEY (id_aluno) REFERENCES Aluno(id_aluno),
    FOREIGN KEY (id_curso) REFERENCES Curso(id_curso)
);
