<?php

namespace Jugger\Db\Orm\Ib;

use Jugger\Db\Orm\ActiveRecord;

/**
 * Раздел инфоблока
 */
class IblockSection extends ActiveRecord
{
    protected static function getDataManagerClass() {
        return \Bitrix\Iblock\SectionTable::class;
    }
    
    protected $_path;
    protected $_iblock;
    
    /**
     * Возвращает цепочку разделов от текущего, до корневого (в порядке убывания)
     * @return array
     */
    public function getPath() {
        if (!$this->_path) {
            $this->_path = [ $this ];
            $item = $this;
            while ($id_section = $item->IBLOCK_SECTION_ID) {
                array_unshift($this->_path, self::getByPrimary($id_section));
            }
        }
        return $this->_path;
    }
    /**
     * Родительский инфоблок
     * @return Iblock
     */
    public function getIblock() {
        if (!$this->_iblock) {
            $this->_iblock = Iblock::getByPrimary($this->IBLOCK_ID);
        }
        return $this->_iblock;
    }
    /**
     * Элементы инфоблока данного раздела
     * @return Result
     */
    public function getElements() {
        return IblockElement::getListByField('IBLOCK_SECTION_ID', $this->ID);
    }
    /**
     * Дочерние разделы
     * @param integer $level
     * @return array
     */
    public function getChilds($level = 1) {
        $ret = self::getListByField("=IBLOCK_SECTION_ID", $this->ID)->fetchAll();
        if ($level !== 1) {
            $nowLevel = 1;
            foreach ($ret as $row) {
                $row = new self($row);
                $ret = array_merge($ret, $row->getChilds());
                $nowLevel++;
                if ($nowLevel == $level) {
                    break;
                }
            }
        }
        return $ret;
    }
}