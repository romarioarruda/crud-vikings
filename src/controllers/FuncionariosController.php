<?php

class FuncionariosController {

    public function getAll(){
        $dados  = [];
        $result =  Funcionarios::getAllJoin();

        if($result) {
            foreach($result as $chave => $valor) {
                $dados[] = [
                    'id_registro' => $valor->id_registro,
                    'nome' =>$valor->nome,
                    'email' => $valor->email,
                    'telefone' => ($valor->telefone) ? unserialize($valor->telefone) : null,
                    'last_updated' => $valor->last_updated
                ];
            }
        }
        
        return Flight::json(array('funcionarios' => $dados));
    }


    public function getOne($idFuncionario) {
        $dados = [];

        if($idFuncionario = filter_var($idFuncionario, FILTER_VALIDATE_INT)) {
            $result =  Funcionarios::getOne(['id_registro' => $idFuncionario]);

            if($result){
                $telefone = $this->getTelefoneFuncionario($idFuncionario);
                $avatar = $this->getAvatar($idFuncionario);

                $dados = [
                    'id_registro' => $result->id_registro,
                    'nome' =>$result->nome,
                    'email' => $result->email,
                    'telefone' => unserialize($telefone),
                    'avatar' => $avatar,
                    'last_updated' => $result->last_updated
                ];
            }
        }
        
        return Flight::json(array('funcionario' => $dados));
    }


    public function getTelefoneFuncionario($idFuncionario) {
        $telefone =  Telefone::getOne(['id_funcionario' => $idFuncionario]);

        return $telefone->telefone ?? null;
    }


    public function getAvatar($idFuncionario) {
        $avatar =  Avatar::getOne(['id_funcionario' => $idFuncionario]);

        return $avatar->url ?? null;
    }


    public function novoFuncionario() {
        $request = Flight::request()->data;
        $status  = 0;

        if($this->validaDadosDoInputDoUsuario($request)){
            $funcionario = new Funcionarios([
                'nome' => addslashes(strip_tags($request->nome)),
                'email' => filter_var($request->email, FILTER_VALIDATE_EMAIL),
                'last_updated' => date('Y-m-d H:i:s')
            ]);
    
            $funcionario->insert();
    
            $this->salvarTelefone($funcionario->id_registro, $request->telefone);

            $status = $funcionario->id_registro;
        }
        

        return Flight::json(array('funcionario_created' => $status));

    }


    public function validaDadosDoInputDoUsuario(...$dados) {
        if(empty($dados[0]->nome)) return false;
        foreach($dados[0]->telefone as $key => $telefone){
            if(empty($dados[0]->telefone[$key]) || strlen($dados[0]->telefone[$key]) <= 5){
                return false;
            }
        }
        if(!filter_var($dados[0]->email, FILTER_VALIDATE_EMAIL)) return false;
        return true;
    }


    public function updateDadosFuncionario($id) {
        $request = Flight::request()->data;
        $status  = 0;

        $this->validaFuncionario($id);

        if($this->validaDadosDoInputDoUsuario($request)){
            $funcionario = new Funcionarios([
                'id_registro' => $id,
                'nome' => addslashes(strip_tags($request->nome)),
                'email' => filter_var($request->email, FILTER_VALIDATE_EMAIL),
                'last_updated' => date('Y-m-d H:i:s')
            ]);

            $funcionario->update();

            $this->salvarTelefone($id, $request->telefone);

            $status = $funcionario->id_registro;
        }

        return Flight::json(array('funcionario_updated' => $status));

    }


    public function deleteDadosFuncionario($id){
        $temFuncionario = $this->validaFuncionario($id, true);

        $temFuncionario->delete(['id_registro' => $id]);

        $this->deleteTelefone($id);
        $this->deleteAvatar($id);
        
        return Flight::json(array('funcionario_deleted' => $id));
    }


    public function validaFuncionario($idFuncionario, $retornaObjeto = false){
        $temFuncionario = Funcionarios::getOne(['id_registro' => $idFuncionario]);

        if(empty($temFuncionario)){
            throw new AppException('Exception: Funcionário não existe no banco de dados.');
        }

        if($retornaObjeto) return $temFuncionario;
    }

    public function validaImagem($request){
        if(empty($request)) throw new AppException('Exception: Arquivo não pode ser vazio.');

        $formato = $request['avatar']['type'];
        $formatoPermitido = array('image/jpeg', 'image/png');

        if(!in_array($formato, $formatoPermitido)) {
            throw new AppException('Formato de imagem não permitido');
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

        return Flight::redirect("/funcionario/editar/{$idFuncionario}");
        
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
        $this->validaIDNaRequisicao($idFuncionario);

        $this->validaTelefone($telefone);

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


    public function validaTelefone($telefone) {
        if(!is_array($telefone) && empty(trim($telefone[0]))) {
            throw new AppException('Telefone não validado.');
        }
    }


    public function validaIDNaRequisicao($id) {
        if(empty($id)) {
            throw new AppException('Exception: ID não pode ser vazio.');
        }
    }


    public function deleteTelefone($idFuncionario){
        $temTelefone = Telefone::getOne(['id_funcionario' => $idFuncionario]);

        if($temTelefone){
            $temTelefone->delete(['id_registro' => $temTelefone->id_registro]);
        }
    }


    public function deleteAvatar($idFuncionario){
        $temImagem = Avatar::getOne(['id_funcionario' => $idFuncionario]);

        if($temImagem) {
            $temImagem->delete(['id_registro' => $temImagem->id_registro]);

            unlink(ASSETS.'/funcionario_avatar/'.$temImagem->url);
        }
    }

}
