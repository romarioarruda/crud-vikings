<?php
require_once 'vendor/autoload.php';
require_once 'src/config/config.php';

$funcionarios = new FuncionariosController;

//Endpoints que pegam dados
Flight::route('GET /funcionarios', array($funcionarios, 'getAll'));
Flight::route('GET /funcionario/@id', array($funcionarios, 'getOne'));

//Endpoints que deletam dados
Flight::route('DELETE /funcionario/@id', array($funcionarios, 'deleteDadosFuncionario'));

//Endpoints que atualizam dados
Flight::route('POST /funcionario/@id', array($funcionarios, 'updateDadosFuncionario'));

//Endpoints que inserem dados
Flight::route('POST /novo-funcionario', array($funcionarios, 'novoFuncionario'));
Flight::route('POST /funcionario/upload/@id', array($funcionarios, 'uploadImagem'));



//Mapeando o diretório da pasta Views
Flight::set('flight.views.path', './src/views');

//Página sem rota definida
Flight::map('notFound', function(){
    Flight::render('404');
});


//Requests de views
Flight::route('GET /', function(){
    Flight::render('funcionarios');
});

Flight::route('GET /funcionario/editar/@id', function(){
    Flight::render('funcionarioEditar');
});


Flight::start();
