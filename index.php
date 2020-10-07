<?php
require_once 'vendor/autoload.php';
require_once 'src/config/config.php';

$funcionarios = new FuncionariosController;

//Endpoints que pegam dados
Flight::route('GET /funcionarios', array($funcionarios, 'getAll'));

//Endpoints que deletam dados
Flight::route('DELETE /funcionario/@id', array($funcionarios, 'deleteDadosFuncionario'));

//Endpoints que atualizam dados
Flight::route('POST /funcionario/@id', array($funcionarios, 'updateDadosFuncionario'));

//Endpoints que inserem dados
Flight::route('POST /novo-funcionario', array($funcionarios, 'novoFuncionario'));


//Mapeando rota vazias.
Flight::map('notFound', function(){
    echo '<h1>Rota sem conteúdo</h1>';
});

Flight::start();
