<?php

namespace Jugger\Db\Orm\Ib;

use Jugger\Db\Orm\ActiveRecord;

/**
 * Свойство инфоблока
 */
class IblockProperty extends ActiveRecord
{
    const TYPE_STRING =     'S';
    const TYPE_NUMBER =     'N';
    const TYPE_LIST =       'L';
    const TYPE_FILE =       'F';
    const TYPE_SECTION =    'G';
    const TYPE_ELEMENT =    'E';
    
    protected $_values;
    
    protected static function getDataManagerClass() {
        return \Bitrix\Iblock\PropertyTable::class;
    }
    /**
     * Список не списковых свойства
     * @return array
     */
    public static function getNotListTypes() {
        return [
            self::TYPE_FILE,
            self::TYPE_STRING,
            self::TYPE_NUMBER
        ];
    }
    /**
     * Получить доступные значения свойств
     * @return array для списковых свойств - значения элементов, для остальных - NULL
     */
    public function getValues() {
        if (!$this->_values) {
            switch ($this->_fields['PROPERTY_TYPE']) {
                case 'L':
                    $this->_values = $this->getValuesEnum();
                    break;
                case 'E':
                    $this->_values = $this->getValuesElements();
                    break;
                case 'G':
                    $this->_values = $this->getValuesSections();
                    break;
                default:
                    return null;
            }
        }
        return $this->_values;
    }
    /**
     * Список значений для списка
     * @return array
     */
    protected function getValuesEnum() {
        return IblockPropertyEnum::getListByField("=PROPERTY_ID", $this->ID)->fetchAll();
    }
    /**
     * Список связных элементов
     * @return array
     */
    protected function getValuesElements() {
        return IblockElement::getListByField("=IBLOCK_ID", $this->_fields['LINK_IBLOCK_ID'])->fetchAll();
    }
    /**
     * Список связных разделов
     * @return array
     */
    protected function getValuesSections() {
        return IblockSection::getListByField("=IBLOCK_ID", $this->_fields['LINK_IBLOCK_ID'])->fetchAll();
    }
}