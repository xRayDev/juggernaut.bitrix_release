<?php

namespace Jugger\Db\Orm\Ib;

use Jugger\Db\Orm\ActiveRecord;
use Jugger\Db\Orm\Ib\IblockProperty;

/**
 * Значение свойства элемента инфоблока
 */
class IblockElementProperty extends ActiveRecord
{
    protected $_meta;

    protected static function getDataManagerClass() {
        return \Jugger\Db\Orm\D7\ElementPropertyTable::class;
    }
    /**
     * Мета данные свойства
     * @return IblockProperty
     */
    public function getMeta() {
        if (!$this->_meta) {
            $this->_meta = IblockProperty::getByPrimary($this->IBLOCK_PROPERTY_ID);
        }
        return $this->_meta;
    }
    /**
     * Возвращает значение свойства элемента
     * @return mixed зависит от типа свойства
     * @throws Exception если тип свойства не найден, то выкидывается ошибка.
     * Такое возможно если только руками задать тип свойства в базе или коде
     */
    public function getValue() {
        $type = $this->getMeta()->PROPERTY_TYPE;
        switch ($type) {
            case IblockProperty::TYPE_STRING:
                if ($this->_fields['USER_TYPE'] === 'HTML') {
                    return $this->getValueHtml();
                }
                else {
                    return $this->_fields['VALUE'];
                }
            case IblockProperty::TYPE_NUMBER:
                return $this->getValueNumber();
            case IblockProperty::TYPE_LIST:
                return $this->getValueEnum();
            case IblockProperty::TYPE_FILE:
                return $this->getValueFile();
            case IblockProperty::TYPE_SECTION:
                return $this->getValueSection();
            case IblockProperty::TYPE_ELEMENT:
                return $this->getValueElement();
            default:
                throw new Exception("invalide type of element property {$type}");
        }
    }
    /**
     * Значение как есть
     * @return string
     */
    public function getValueRaw() {
        return $this->_fields['VALUE'];
    }
    /**
     * Значение как HTML код
     * @return string
     */
    public function getValueHtml() {
        $tmp = unserialize($this->_fields['VALUE']);
        return $tmp['TEXT'];
    }
    /**
     * Значение спискового свойства
     * @return IblockPropertyEnum
     */
    public function getValueEnum() {
        return IblockPropertyEnum::getByPrimary($this->_fields['VALUE_ENUM']);
    }
    /**
     * Данные о файле
     * @return array
     */
    public function getValueFile() {
        return \CFile::GetFileArray($this->_fields['VALUE']);
    }
    /**
     * Связный элемент
     * @return IblockElement
     */
    public function getValueElement() {
        return IblockElement::getByPrimary($this->_fields['VALUE']);
    }
    /**
     * Связный раздел
     * @return IblockSection
     */
    public function getValueSection() {
        return IblockSection::getByPrimary($this->_fields['VALUE']);
    }
    /**
     * Числовое значение
     * @return mixed float иди integer
     */
    public function getValueNumber() {
        $val = $this->_fields['VALUE'];
        return ctype_digit($val) ? (int) $val : (float) $val;
    }
}