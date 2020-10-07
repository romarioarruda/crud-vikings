<?php


class Telefone extends Model {
    protected static $tableName = 'telefone';
    protected static $columns = [
        'id_funcionario',
        'telefone',
        'last_updated',
    ];

}
