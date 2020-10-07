<?php

class FuncionariosController {

    public function getAll(){
        $result =  Funcionarios::getAllJoin();
        $dados  = [];

        if(!$result) return Flight::json(array('funcionarios' => $dados));
        
        foreach($result as $chave => $valor) {
            $dados[] = [
                'id_registro' => $valor->id_registro,
                'nome' =>$valor->nome,
                'email' => $valor->email,
                'telefone' => $valor->telefone,
                'last_updated' => $valor->last_updated
            ];
        }
        return Flight::json(array('funcionarios' => $dados));
    }


    public function novoFuncionario() {
        $request = Flight::request()->data;

        if(empty($request->nome)) return false;
        if(empty($request->email)) return false;
        if(empty($request->telefone)) return false;

        $funcionario = new Funcionarios([
            'nome' => $request->nome,
            'email' => $request->email,
            'last_updated' => date('Y-m-d H:i:s')
        ]);

        $funcionario->insert();

        $this->salvarTelefone($funcionario->id_registro, $request->telefone);

        return Flight::json(array('funcionario_created' => $funcionario->id_registro));

    }


    public function updateDadosFuncionario($id) {
        $request = Flight::request()->data;

        $temFuncionario = Funcionarios::getOne(['id_registro' => $id]);

        if(empty($temFuncionario)) throw new AppException('Exception: Funcionário não existe no banco de dados.');

        $funcionario = new Funcionarios([
            'id_registro' => $id,
            'nome' => $request->nome,
            'email' => $request->email,
            'last_updated' => date('Y-m-d H:i:s')
        ]);

        $funcionario->update();

        $this->salvarTelefone($id, $request->telefone);

        return Flight::json(array('funcionario_updated' => $id));

    }


    public function deleteDadosFuncionario($id){
        $temFuncionario = Funcionarios::getOne(['id_registro' => $id]);

        if(empty($temFuncionario)) throw new AppException('Exception: Funcionário não existe no banco de dados.');

        $temFuncionario->delete(['id_registro' => $id]);
        
        return Flight::json(array('funcionario_deleted' => $id));
    }


    public function salvarTelefone($idFuncionario, $telefone) {
        if(empty($idFuncionario)) throw new AppException('Exception: ID de funcionário não pode ser vazio.');
        if(empty($telefone)) throw new AppException('Exception: Telefone do funcionário não pode ser vazio.');

        $temTelefone = Telefone::getOne(['id_funcionario' => $idFuncionario]);

        if(!empty($temTelefone)) {
            $temTelefone = new Telefone([
                'id_registro' => $temTelefone->id_registro,
                'id_funcionario' => $idFuncionario,
                'telefone' => $telefone,
                'last_updated' => date('Y-m-d H:i:s')
            ]);
    
            $temTelefone->update();
        } else {
            $temTelefone = new Telefone([
                'id_funcionario' => $idFuncionario,
                'telefone' => $telefone,
                'last_updated' => date('Y-m-d H:i:s')
            ]);
    
            $temTelefone->insert();
        }

    }

}
