<?php

session_start();
require_once __DIR__ . "/../Model/config.php";
require_once __DIR__ . "/../Model/Auth.php";
include_once __DIR__ . "/../Model/Conexao.php";
include_once __DIR__ . "/../Model/TurmaDAO.php";
include_once __DIR__ . "/../Model/helpers.php";

class TurmaController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new TurmaDAO();
    }

    public function cadastrar()
    {
        Auth::requireAdmin();
        
        $idCurso = intval($_POST['id_curso'] ?? 0);
        $responsavel = intval($_POST['responsavel'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $dataInicio = $_POST['data_inicio'] ?? '';
        $dataFim = $_POST['data_fim'] ?? '';
        $dataFimTurma = $_POST['data_fim_turma'] ?? '';
        $diasSemana = $_POST['dias_semana'] ?? [];
        $duracao = intval($_POST['duracao'] ?? 60);

        if (!$idCurso || !$responsavel || !$nome || !$dataInicio || !$dataFim || !$dataFimTurma || empty($diasSemana)) {
            $_SESSION['erro'] = 'Preencha todos os campos obrigatórios!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        try {
            $horario = $this->gerarHorarioTexto($diasSemana, $dataInicio);
            $capacidadeMaxima = intval($_POST['capacidade_maxima'] ?? 20);
            
            $dataInicioObj = new DateTime($dataInicio);
            $dataFimTurmaObj = new DateTime($dataFimTurma);
            $diasSemanaInt = array_map('intval', $diasSemana);
            
            $dataPrimeiraAula = null;
            $dataUltimaAula = null;
            $aulasCriadas = 0;
            $dataAtual = clone $dataInicioObj;
            $dataAtual->setTime(0, 0, 0);
            
            $horarioInicio = new DateTime($dataInicio);
            $horaInicio = $horarioInicio->format('H:i');
            
            while ($dataAtual <= $dataFimTurmaObj) {
                $diaSemana = (int)$dataAtual->format('w');
                
                if (in_array($diaSemana, $diasSemanaInt)) {
                    $dataAula = clone $dataAtual;
                    list($hora, $minuto) = explode(':', $horaInicio);
                    $dataAula->setTime($hora, $minuto, 0);
                    
                    $dataFimAula = clone $dataAula;
                    $dataFimAula->modify('+' . $duracao . ' minutes');
                    
                    $conflito = $this->dao->verificarConflitoAula(
                        $dataAula->format('Y-m-d H:i:s'),
                        $dataFimAula->format('Y-m-d H:i:s')
                    );
                    
                    if (empty($conflito)) {
                        if ($dataPrimeiraAula === null) {
                            $dataPrimeiraAula = clone $dataAula;
                        }
                        $dataUltimaAula = clone $dataFimAula;
                    }
                }
                
                $dataAtual->modify('+1 day');
            }
            
            if ($dataPrimeiraAula === null) {
                $_SESSION['erro'] = 'Não foi possível criar nenhuma aula. Todos os horários estão ocupados ou nenhum dia válido foi selecionado.';
                header('Location: ' . getViewUrl('dashboard_admin.php'));
                exit;
            }
            
            $turma = new Turma(
                null,
                $idCurso,
                $responsavel,
                $nome,
                $dataPrimeiraAula->format('Y-m-d H:i:s'),
                $dataUltimaAula->format('Y-m-d H:i:s'),
                $horario,
                $capacidadeMaxima
            );
            $idTurma = $this->dao->cadastrar($turma);
            
            $dataAtual = clone $dataInicioObj;
            $dataAtual->setTime(0, 0, 0);
            
            while ($dataAtual <= $dataFimTurmaObj) {
                $diaSemana = (int)$dataAtual->format('w');
                
                if (in_array($diaSemana, $diasSemanaInt)) {
                    $dataAula = clone $dataAtual;
                    list($hora, $minuto) = explode(':', $horaInicio);
                    $dataAula->setTime($hora, $minuto, 0);
                    
                    $dataFimAula = clone $dataAula;
                    $dataFimAula->modify('+' . $duracao . ' minutes');
                    
                    $conflito = $this->dao->verificarConflitoAula(
                        $dataAula->format('Y-m-d H:i:s'),
                        $dataFimAula->format('Y-m-d H:i:s')
                    );
                    
                    if (empty($conflito)) {
                        $this->dao->cadastrarAula(
                            $idTurma,
                            $dataAula->format('Y-m-d H:i:s'),
                            $dataFimAula->format('Y-m-d H:i:s')
                        );
                        $aulasCriadas++;
                    }
                }
                
                $dataAtual->modify('+1 day');
            }
            
            if ($aulasCriadas > 0) {
                $_SESSION['sucesso'] = "Turma criada com sucesso! {$aulasCriadas} aula(s) agendada(s).";
            } else {
                $_SESSION['erro'] = 'Turma criada, mas nenhuma aula pôde ser agendada devido a conflitos de horário.';
            }
            
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar turma: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao cadastrar turma. Tente novamente.';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (Exception $e) {
            error_log("Erro geral ao cadastrar turma: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao processar. Tente novamente.';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
    }
    
    private function gerarHorarioTexto($diasSemana, $horaInicio)
    {
        $nomesDias = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        $dias = [];
        foreach ($diasSemana as $dia) {
            $dias[] = $nomesDias[$dia] ?? '';
        }
        $hora = date('H:i', strtotime($horaInicio));
        return implode(', ', $dias) . ' às ' . $hora;
    }

    public function listar()
    {
        return $this->dao->readAll();
    }

    public function buscar($id)
    {
        return $this->dao->readById($id);
    }

    public function listarPorCurso($idCurso)
    {
        return $this->dao->readByCurso($idCurso);
    }

    public function atualizar()
    {
        Auth::requireAdmin();
        
        $id = intval($_POST['id'] ?? 0);
        $idCurso = intval($_POST['id_curso'] ?? 0);
        $responsavel = intval($_POST['responsavel'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $dataInicio = $_POST['data_inicio'] ?? '';
        $dataFim = $_POST['data_fim'] ?? '';
        $horario = trim($_POST['horario'] ?? '');
        $capacidadeMaxima = intval($_POST['capacidade_maxima'] ?? 20);

        if (!$id || !$idCurso || !$responsavel || !$nome || !$dataInicio || !$dataFim) {
            $_SESSION['erro'] = 'Preencha todos os campos obrigatórios!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        try {
            $turma = new Turma($id, $idCurso, $responsavel, $nome, $dataInicio, $dataFim, $horario, $capacidadeMaxima);
            $this->dao->update($turma);
            $_SESSION['sucesso'] = 'Turma atualizada com sucesso!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar turma: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao atualizar turma. Tente novamente.';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
    }

    public function deletar()
    {
        Auth::requireAdmin();
        
        $id = intval($_POST['id'] ?? 0);

        if (!$id) {
            $_SESSION['erro'] = 'ID inválido!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }

        try {
            $this->dao->delete($id);
            $_SESSION['sucesso'] = 'Turma deletada com sucesso!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao deletar turma: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao deletar turma. Tente novamente.';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
    }
}

