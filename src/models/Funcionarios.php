<?php


class Funcionarios extends Model {
    protected static $tableName = 'funcionarios';
    protected static $columns = [
        'nome',
        'email',
        'last_updated',
    ];
}
