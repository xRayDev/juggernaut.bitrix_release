<?php

namespace Jugger\Db\Orm\Ib;

use Jugger\Db\Orm\ActiveRecord;

/**
 * Инфоблок
 * @property int $ID
 * @property \Bitrix\Main\Type\DateTime $TIMESTAMP_X
 * @property string $IBLOCK_TYPE_ID
 * @property string $CODE
 * @property string $NAME
 * @property string $ACTIVE
 * @property int $SORT
 * @property string $LIST_PAGE_URL
 * @property string $DETAIL_PAGE_URL
 * @property string $SECTION_PAGE_URL
 * @property string $CANONICAL_PAGE_URL
 * @property int $PICTURE
 * @property string $DESCRIPTION
 * @property string $DESCRIPTION_TYPE
 * @property string $XML_ID
 * @property string $TMP_ID
 * @property string $INDEX_ELEMENT
 * @property string $INDEX_SECTION
 * @property string $WORKFLOW
 * @property string $BIZPROC
 * @property string $SECTION_CHOOSER
 * @property string $LIST_MODE
 * @property string $RIGHTS_MODE
 * @property string $SECTION_PROPERTY
 * @property string $PROPERTY_INDEX
 * @property int $VERSION
 * @property string $LAST_CONV_ELEMENT
 * @property string $SOCNET_GROUP_ID
 * @property string $EDIT_FILE_BEFORE
 * @property string $EDIT_FILE_AFTER
 */
class Iblock extends ActiveRecord
{
    /**
     * Элементы инфоблока
     * @param array $params
     * @return \Bitrix\Main\DB\Result
     */
    public function getElements(array $params = []) {
        return IblockElement::getListByField('=IBLOCK_ID', $this->ID, $params);
    }
    /**
     * Разделы инфоблока
     * @param array $params
     * @return \Bitrix\Main\DB\Result
     */
    public function getSections(array $params = []) {
        return IblockSection::getListByField('=IBLOCK_ID', $this->ID, $params);
    }
    /**
     * Корневые разделы инфоблока
     * @param array $params
     * @return \Bitrix\Main\DB\Result
     */
    public function getSectionsTopLevel(array $params = []) {
        if (!isset($params['filter'])) {
            $params['filter'] = [];
        }
        $params['filter'] = array_merge(
            [
                '=IBLOCK_ID' => $this->ID,
                '=IBLOCK_SECTION_ID' => null
            ],
            $params['filter']
        );
        return IblockSection::getList($params);
    }
    /**
     * Свойства инфоблока
     * @param array $params
     * @return \Bitrix\Main\DB\Result
     */
    public function getProperties(array $params = []) {
        return IblockProperty::getListByField('=IBLOCK_ID', $this->ID, $params);
    }
    
    protected static function getDataManagerClass() {
        return \Bitrix\Iblock\IblockTable::class;
    }
}