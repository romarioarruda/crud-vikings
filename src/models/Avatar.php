<?php
class Avatar extends Model {
    protected static $tableName = 'foto_funcionario';
    protected static $columns = [
        'id_funcionario',
        'url',
    ];

}
