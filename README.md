# Crud MVC

Ambiente de desenvolvimento usando `Docker Compose`.
>
Stack:

* PHP 7.4
* Apache 2.4
* Mysql 8.0
* phpMyAdmin

## Instalação

Com o `Docker` instalado na sua máquina

Faça o clone desse repositório e execute `docker-compose up -d`.

```shell
git clone https://github.com/romarioarruda/crud-vikings.git

cd crud-vikings/

docker-compose up -d
```

Seu ambiente está pronto!! Acesse http://localhost:8081 no seu navegador.

## Tabelas do banco de dados
Por padrão, o banco `crud_vikings` foi criado, agora é necessário importar as tabelas dentro do banco.

Na pasta `extras/tabelas` estão as tabelas necessárias pra aplicação.

## Configuração

_**DOCUMENT_ROOT**_

É o diretório que o Apache vai servir, nesse caso é a pasta raiz `crud-vikings`.

## Web Server

O Apache está configurado para usar a porta `8081`. você pode acessar http://localhost:8081.

#### Módulos do Apache

Por padrão, os seguintes módulos estão habilitados:

* rewrite
* headers

## Database

Esse stack vem com as seguintes credenciais:

_**SENHA DO ROOT**_

/br4vu5_root/is

_**BANCO**_

crud_vikings

_**USUÁRIO**_

devuser

_**SENHA DO USUÁRIO**_

devpass

## PHP

A versão do PHP utilizada nessa stack é a `7.4`

#### Extensões do PHP

Por padrão, os seguintes módulos estarão habilitados:

* mysqli
* pdo_sqlite
* pdo_mysql
* mbstring
* zip
* intl
* mcrypt
* curl
* json
* iconv
* xml
* xmlrpc
* gd

## phpMyAdmin

O phpMyAdmin está configurado para usar a porta `8080`, com as credenciais:

http://localhost:8080
username: `root`
password: `/br4vu5_root/is`
