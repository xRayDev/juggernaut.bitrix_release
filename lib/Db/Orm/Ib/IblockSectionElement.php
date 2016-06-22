<?php

namespace Jugger\Db\Orm\Ib;

use Jugger\Db\Orm\ActiveRecord;

/**
 * Соответствие разделов и элементов инфоблоков
 */
class IblockSectionElement extends ActiveRecord
{
    protected static function getDataManagerClass() {
        return \Bitrix\Iblock\SectionElementTable::class;
    }
    
    public static function getPrimaryKey() {
        return ["IBLOCK_SECTION_ID","IBLOCK_ELEMENT_ID","ADDITIONAL_PROPERTY_ID"];
    }
}