<?php
class Funcionarios extends Model {
    protected static $tableName = 'funcionarios';
    protected static $columns = [
        'nome',
        'email',
        'last_updated',
    ];


    public static function getAllJoin() {
        $objects = [];
        $sql = "SELECT func.id_registro, func.nome, func.email, contato.telefone, func.last_updated 
        FROM funcionarios as func INNER JOIN telefone as contato 
        ON func.id_registro = contato.id_funcionario";

        $result = Database::getResultFromQuery($sql);

        if (!$result) return $objects;

        $class = get_called_class();
        while($row = $result->fetch_assoc()) {
            array_push($objects, new $class($row));
        }

        return $objects;
    }
}
