class Disciplina {
    public $codigo;
    public $nome;
    public $creditos;
    public $horarios1;
    public $horarios2;
    public $pre_requisitos;
    public $capacidade;
    public $alunos_inscritos = array();
    public $professor;
    public $local;

    function __construct($codigo, $nome, $creditos, $horarios1, $horarios2, $pre_requisitos, $capacidade, $professor, $local) {
        $this->codigo = $codigo;
        $this->nome = $nome;
        $this->creditos = $creditos;
        $this->horarios1 = $horarios1;
        $this->horarios2 = $horarios2;
        $this->pre_requisitos = $pre_requisitos;
        $this->capacidade = $capacidade;
        $this->professor = $professor;
        $this->local = $local;
    }

    function mostrarDetalhes() {
        echo "Disciplina: " . $this->nome . "\n";
        echo "Código: " . $this->codigo . "\n";
        echo "Horários 1: " . implode(", ", $this->horarios1) . "\n";
        echo "Horários 2: " . implode(", ", $this->horarios2) . "\n";
        echo "Professor: " . $this->professor . "\n";
        echo "Local: " . $this->local . "\n";
        echo "\n";
    }

    function temPreRequisitos($aluno) {
        foreach ($this->pre_requisitos as $pre_req) {
            if (!in_array($pre_req, $aluno->disciplinas_cursadas)) {
                return false;
            }
        }
        return true;
    }

    function temConflitoHorario($aluno) {
        foreach (array_merge($this->horarios1, $this->horarios2) as $horario) {
            if (in_array($horario, $aluno->disciplinas_cursando)) {
                return true;
            }
        }
        return false;
    }

    function inscreverAluno($aluno) {
        if (count($this->alunos_inscritos) < $this->capacidade && !$this->temConflitoHorario($aluno) && $this->temPreRequisitos($aluno)) {
            array_push($this->alunos_inscritos, $aluno);
            array_push($aluno->disciplinas_cursadas, $this);
            return true;
        }
        return false;
    }
}

class Aluno {
    public $nome;
    public $disciplinas_cursadas = array();
    public $disciplinas_cursando = array();

    function __construct($nome) {
        $this->nome = $nome;
    }
}

class SistemaInscricoes {
    public $disciplinas_disponiveis = array();

    function disciplinasDisponiveis($aluno) {
        $disponiveis = array();
        foreach ($this->disciplinas_disponiveis as $disciplina) {
            if ($disciplina->temPreRequisitos($aluno) && !$disciplina->temConflitoHorario($aluno)) {
                array_push($disponiveis, $disciplina);
            }
        }
        return $disponiveis;
    }

    function inscrever($aluno, $codigo_disciplina) {
        $disciplina = null;
        foreach ($this->disciplinas_disponiveis as $disc) {
            if ($disc->codigo === $codigo_disciplina) {
                $disciplina = $disc;
                break;
            }
        }

        if ($disciplina) {
            if ($disciplina->inscreverAluno($aluno)) {
                echo "Aluno " . $aluno->nome . " selecinou a disciplina " . $disciplina->nome . ".\n";
            } else {
                echo "Não foi possível inscrever o aluno " . $aluno->nome . " na disciplina " . $disciplina->nome . ".\n";
            }
        } else {
            echo "Disciplina não encontrada.\n";
        }
    }
}

// Exemplo de uso:
$sistema = new SistemaInscricoes();

// Criação de disciplinas
$disciplina1 = new Disciplina("001", "Química", 4, ["Segunda 7h-16h"], ["Quarta 10h-12h"], [], 30, "Prof. Lucas", "Sala 2");
$disciplina2 = new Disciplina("002", "Matemática", 4, ["Segunda 7h-16h"], ["Terça 10h-12h"], [], 30, "Prof. Toni", "Lab 101");
$disciplina3 = new Disciplina("003", "Física", 4, ["Quarta 18h-20h"], ["Sábado 13h-16h"], [], 30, "Prof. Cleber", "Sala 135");
$disciplina4 = new Disciplina("004", "Desenvolvimento de sistema", 4, ["Segunda 14h-16h"] , ["Quarta 10h-12h"], [], 30, "Prof. Douglas", "Sala 1");
$disciplina5 = new Disciplina("005", "Meio ambiente", 4, ["terça 7h-12h"],  ["sexta 18h-22h"], [], 30, "Prof. Joabe", "Sala 5");
$disciplina6 = new Disciplina("006", "Cultura inglesa", 4, ["Quinta 11h-13h"],  ["Quarta 10h-12h"], [], 30, "Prof. Silva", "Lab 104");

// Adicionar disciplinas ao sistema de inscrição
array_push($sistema->disciplinas_disponiveis, $disciplina1, $disciplina2, $disciplina3, $disciplina4, $disciplina5, $disciplina6);

// Criação de um aluno
$aluno1 = new Aluno("Luan");

// Mostrar detalhes de uma disciplina
$disciplina1->mostrarDetalhes();

// Mostrar disciplinas disponíveis para inscrição
$disponiveisParaAluno1 = $sistema->disciplinasDisponiveis($aluno1);
echo "Disciplinas disponíveis para o aluno " . $aluno1->nome . ": ";
foreach ($disponiveisParaAluno1 as $disciplina) {
    echo $disciplina->nome . ", ";
}
echo "\n";

// Realizar inscrição
$sistema->inscrever($aluno1, "001");  

// Mostrar disciplinas cursadas pelo aluno após inscrição
echo "Disciplinas cursadas pelo aluno " . $aluno1->nome . ": ";
foreach ($aluno1->disciplinas_cursadas as $disciplina) {
    echo $disciplina->nome . ", ";
}
echo "\n";