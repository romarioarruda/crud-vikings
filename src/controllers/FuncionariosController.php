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
                'telefone' => ($valor->telefone) ? unserialize($valor->telefone) : null,
                'last_updated' => $valor->last_updated
            ];
        }
        return Flight::json(array('funcionarios' => $dados));
    }


    public function getOne($idFuncionario) {
        $result =  Funcionarios::getOne(['id_registro' => $idFuncionario]);

        if(!$result) return Flight::json(array('funcionario' => []));

        $telefone = $this->getTelefoneFuncionario($idFuncionario);

        $dados = [
            'id_registro' => $result->id_registro,
            'nome' =>$result->nome,
            'email' => $result->email,
            'telefone' => unserialize($telefone),
            'last_updated' => $result->last_updated
        ];
        return Flight::json(array('funcionario' => $dados));
    }


    public function getTelefoneFuncionario($idFuncionario) {
        $telefone =  Telefone::getOne(['id_funcionario' => $idFuncionario]);

        return $telefone->telefone ?? null;
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

        $this->deleteTelefone($id);
        $this->deleteAvatar($id);
        
        return Flight::json(array('funcionario_deleted' => $id));
    }


    public function validaFuncionario($idFuncionario){
        $temFuncionario = Funcionarios::getOne(['id_registro' => $idFuncionario]);

        if(empty($temFuncionario)){
            throw new AppException('Exception: Funcionário não existe no banco de dados.');
        }
    }

    public function validaImagem($request){
        if(empty($request)) throw new AppException('Exception: Arquivo não pode ser vazio.');

        $formato = $request['avatar']['type'];
        $formatoPermitido = array('image/jpeg', 'image/png');

        if(!in_array($formato, $formatoPermitido)) {
            return Flight::json(array('status' => 'formato_nao_permitido'));
        }
    }


    public function uploadImagem($idFuncionario) {
        $this->validaImagem($_FILES);

        $this->validaFuncionario($idFuncionario);

        $temAvatar = Avatar::getOne(['id_funcionario' => $idFuncionario]);

        if($temAvatar){
            $this->atualizarAvatar($temAvatar->id_registro, $idFuncionario, $_FILES, $temAvatar->url);
        } else {
            $this->salvarAvatar($idFuncionario, $_FILES);
        }

        return Flight::json(array('avatar_updated' => $idFuncionario));
        
    }


    public function atualizarAvatar($id_registro, $idFuncionario, $file, $fileAntigo){
        $name = md5($file['avatar']['name'].time().rand(0,999)).'.jpg';

        unlink(ASSETS.'/funcionario_avatar/'.$fileAntigo);

        $avatar = new Avatar([
            'id_registro' => $id_registro,
            'id_funcionario' => $idFuncionario,
            'url' => $name
        ]);

        move_uploaded_file($file['avatar']['tmp_name'], ASSETS.'/funcionario_avatar/'.$name);

        $avatar->update();
    }


    public function salvarAvatar($idFuncionario, $file){
        $name = md5($file['avatar']['name'].time().rand(0,999)).'.jpg';

        $avatar = new Avatar([
            'id_funcionario' => $idFuncionario,
            'url' => $name
        ]);

        move_uploaded_file($file['avatar']['tmp_name'], ASSETS.'/funcionario_avatar/'.$name);

        $avatar->insert();
    }


    public function salvarTelefone($idFuncionario, $telefone) {
        if(empty($idFuncionario)) throw new AppException('Exception: ID de funcionário não pode ser vazio.');
        if(empty($telefone)) throw new AppException('Exception: Telefone do funcionário não pode ser vazio.');

        $temTelefone = Telefone::getOne(['id_funcionario' => $idFuncionario]);

        if(!empty($temTelefone)) {
            $temTelefone = new Telefone([
                'id_registro' => $temTelefone->id_registro,
                'id_funcionario' => $idFuncionario,
                'telefone' => serialize($telefone),
                'last_updated' => date('Y-m-d H:i:s')
            ]);
    
            $temTelefone->update();
        } else {
            $temTelefone = new Telefone([
                'id_funcionario' => $idFuncionario,
                'telefone' => serialize($telefone),
                'last_updated' => date('Y-m-d H:i:s')
            ]);
    
            $temTelefone->insert();
        }

    }


    public function deleteTelefone($idFuncionario){
        $temTelefone = Telefone::getOne(['id_funcionario' => $idFuncionario]);

        $temTelefone->delete(['id_registro' => $temTelefone->id_registro]);
    }


    public function deleteAvatar($idFuncionario){
        $temImagem = Avatar::getOne(['id_funcionario' => $idFuncionario]);

        if($temImagem) {
            $temImagem->delete(['id_registro' => $temImagem->id_registro]);

            unlink(ASSETS.'/funcionario_avatar/'.$temImagem->url);
        }
    }

}
