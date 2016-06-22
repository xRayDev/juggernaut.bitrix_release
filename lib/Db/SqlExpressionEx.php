<?php

namespace Jugger\Db;

/**
 * Нужен, когда неизвестно заранее количество элементов в запросе
 * @see \Bitrix\Main\DB\SqlExpression
 */
class SqlExpressionEx extends \Bitrix\Main\DB\SqlExpression
{
    /**
     * Получает на вход SQL и массив элементов
     * @param string $sql запрос
     * @param array $args массив элементов
     */
    public function __construct($sql, array $args) {
        $this->expression = $sql;
        foreach ($args as $arg) {
            $this->args[] = $arg;
        }
    }
}