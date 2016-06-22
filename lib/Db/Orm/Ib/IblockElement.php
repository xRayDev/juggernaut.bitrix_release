<?php

namespace Jugger\Db\Orm\Ib;

use Jugger\Db\Orm\Ib\IblockSectionElement;
use Jugger\Db\Orm\ActiveRecord;

/**
 * Элемент инфоблока
 */
class IblockElement extends ActiveRecord
{
    protected $_iblock;
    protected $_section;
    protected $_properties;
    protected $_isLoadAllProperties = false;

    protected static function getDataManagerClass() {
        return \Bitrix\Iblock\ElementTable::class;
    }
    /**
     * Перед сохранением элемента,
     * синхранизация со всеми подразделами указанного раздела
     * @return boolean
     */
    public function onBeforeSave() {
        if (parent::onBeforeSave()) {
            $path = $this->getSection();
            if ($path) {
                $path = $path->getPath();
            }
            else {
                return true;
            }
            if (count($path) === 1) {
                return true;
            }
            foreach ($path as $section) {
                $sectionElement = IblockSectionElement::getRow([
                    "filter" => [
                        "=IBLOCK_ELEMENT_ID" => $this->_fields['ID'],
                        "=IBLOCK_SECTION_ID" => $section->ID
                    ]
                ]);
                $sectionElement = new IblockSectionElement($sectionElement);
                $sectionElement->IBLOCK_ELEMENT_ID = $this->_fields['ID'];
                $sectionElement->IBLOCK_SECTION_ID = $section->ID;
                $sectionElement->save();
            }
            return true;
        }
        return false;
    }
    /**
     * Родительский инфоблок
     * @return Iblock
     */
    public function getIblock() {
        if (!$this->_iblock) {
            $this->_iblock = Iblock::getByPrimary($this->_fields['IBLOCK_ID']);
        }
        return $this->_iblock;
    }
    /**
     * Родительский раздел
     * @return IblockSection
     */
    public function getSection() {
        if (!$this->_section) {
            $this->_section = IblockSection::getByPrimary($this->_fields['IBLOCK_SECTION_ID']);
        }
        return $this->_section;
    }
    /**
     * Возвращает конкретное значение свойства
     * @param integer $id_property ID свойства
     * @return \Jugger\Db\Orm\Ib\IblockElementProperty
     */
    public function getProperty($id_property) {
        if (!$this->_properties[$id_property]) {
            $this->_properties[$id_property] = $this->loadProperty($id_property);
        }
        return $this->_properties[$id_property];
    }
    /**
     * Возвращает все свойства элемента
     * @return \Jugger\Db\Orm\Ib\IblockElementProperty[]
     */
    public function getProperties() {
        if (!$this->_isLoadAllProperties) {
            $this->_properties = [];
            $result = IblockElementProperty::getListByField('=IBLOCK_ELEMENT_ID', $this->ID);
            while ($row = $result->fetch()) {
                $id_property = (int) $row['IBLOCK_PROPERTY_ID'];
                $this->addProperty($id_property, new IblockElementProperty($row));
            }
            $this->_isLoadAllProperties = true;
        }
        return $this->_properties;
    }
    /**
     * Добавляет значение свйоства для элемента
     * @param integer $propertyId ID свойства
     * @param string $value значение
     * @return type
     */
    public function createProperty($propertyId, $value) {
        $prop = new IblockElementProperty();
        $prop->IBLOCK_ELEMENT_ID = $this->ID;
        $prop->IBLOCK_PROPERTY_ID = $propertyId;
        $prop->VALUE = $value;
        return $prop->save();
    }
    /**
     * Добавление запрошенного свойства в локальный кеш
     * @param integer $id_property ID свойства
     * @param \Jugger\Db\Orm\Ib\IblockElementProperty $object добавляемое свойство
     */
    protected function addProperty($id_property, IblockElementProperty $object) {
        $prop = $this->_properties[$id_property];
        if ($prop) {
            if (!is_array($prop)) {
                $prop = [ $prop ];
            }
            $prop[] = $object;
        }
        else {
            $prop = $object;
        }
        $this->_properties[$id_property] = $prop;
    }
    /**
     * Загрузка свойства из базы
     * @param integer $id_property ID свойства
     * @return \Jugger\Db\Orm\Ib\IblockElementProperty
     */
    protected function loadProperty($id_property) {
        $rows = IblockElementProperty::getList([
            "filter" => [
                "=IBLOCK_PROPERTY_ID" => $id_property,
                "=IBLOCK_ELEMENT_ID" => $this->ID
            ]
        ]);
        if ($rows->getSelectedRowsCount() === 0) {
            $prop = null;
        }
        elseif ($rows->getSelectedRowsCount() === 1) {
            $prop = new IblockElementProperty($rows->fetch());
        }
        else {
            $prop = [];
            while ($row = $rows->fetch()) {
                $prop[] = new IblockElementProperty($row);
            }
        }
        return $prop;
    }
}