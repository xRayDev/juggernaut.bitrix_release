<?php

namespace Jugger\Db\Orm\Ib;

use Jugger\Db\Orm\ActiveRecord;

/**
 * Значение спискового свойства инфоблока
 */
class IblockPropertyEnum extends ActiveRecord
{
    protected static function getDataManagerClass() {
        return \Bitrix\Iblock\PropertyEnumerationTable::class;
    }
}