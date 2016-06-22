<?php

namespace Jugger\Context\UrlManager;

use Jugger\Db\Orm\Ib;
use Jugger\Db\Orm\Ib\IblockSection;
use Jugger\Db\Orm\Ib\IblockElement;

/**
 * Класс для работы с URL для инфоблоков
 */
abstract class Iblock
{
    /**
     * Возвращает URL создания элемента
     * @param integer $iblockId ID инфоблока
     * @param boolean $bxPublic флаг, определяющий вывод из публичной части
     * @return string
     */
    public static function getElementCreateUrl($iblockId, $bxPublic = true) {
        $iblockType = Ib\Iblock::getByPrimary($iblockId)->IBLOCK_TYPE_ID;
        $url = "/bitrix/admin/iblock_element_edit.php?IBLOCK_ID={$iblockId}&type={$iblockType}&ID=0&lang=ru";
        if ($bxPublic) {
            $url .= "&bxpublic=Y";
        }
        return $url;
    }
    /**
     * Возвращает URL создания раздела
     * @param integer $iblockId ID инфоблока
     * @param boolean $bxPublic флаг, определяющий вывод из публичной части
     * @return string
     */
    public static function getSectionCreateUrl($iblockId, $bxPublic = true) {
        $iblockType = Ib\Iblock::getByPrimary($iblockId)->IBLOCK_TYPE_ID;
        $url = "/bitrix/admin/iblock_section_edit.php?IBLOCK_ID={$iblockId}&type={$iblockType}&ID=0&lang=ru";
        if ($bxPublic) {
            $url .= "&bxpublic=Y";
        }
        return $url;
    }
    /**
     * Возвращает URL удаления элемента
     * @param IblockElement $model объет элемента, для которого формируется URL
     * @return string
     */
    public static function getElementDeleteUrl(IblockElement $model) {
        return self::generateDeleteUrl("iblock_element_admin", $model);
    }
    /**
     * Возвращает URL редактирования элемента
     * @param IblockElement $model объет элемента, для которого формируется URL
     * @param boolean $bxPublic флаг, определяющий вывод из публичной части
     * @return string
     */
    public static function getElementUpdateUrl(IblockElement $model, $bxPublic = true) {
        return self::generateUpdateUrl("iblock_element_edit", $model, $bxPublic);
    }
    /**
     * Возвращает URL редактирования элемента
     * @param IblockSection $model объет раздела, для которого формируется URL
     * @param boolean $bxPublic флаг, определяющий вывод из публичной части
     * @return string
     */
    public static function getSectionUpdateUrl(IblockSection $model, $bxPublic = true) {
        return self::generateUpdateUrl("iblock_section_edit", $model, $bxPublic);
    }
    /**
     * Возвращает URL удаления раздела
     * @param IblockSection $model объет раздела, для которого формируется URL
     * @return string
     */
    public static function getSectionDeleteUrl(IblockSection $model) {
        return self::generateDeleteUrl("iblock_section_admin", $model);
    }
    /**
     * Генерация URL для редактирования
     * @param string $scriptName имя скрипта к которому проводится запрос
     * @param mixed $model объект элемента или раздела для которого формируется запрос
     * @param boolean $bxPublic флаг, определяющий вывод из публичной части
     * @return string
     */
    protected static function generateUpdateUrl($scriptName, $model, $bxPublic = true) {
        $elementId = $model->ID;
        $iblockId = $model->IBLOCK_ID;
        $iblockType = $model->getIblock()->IBLOCK_TYPE_ID;
        $url = "/bitrix/admin/{$scriptName}.php?IBLOCK_ID={$iblockId}&type={$iblockType}&ID={$elementId}&lang=ru";
        if ($bxPublic) {
            $url .= "&bxpublic=Y";
        }
        return $url;
    }
    /**
     * Генерация URL для удаления
     * @param string $scriptName имя скрипта к которому проводится запрос
     * @param mixed $model объект элемента или раздела для которого формируется запрос
     * @return string
     */
    protected static function generateDeleteUrl($scriptName, $model) {
        $elementId = $model->ID;
        $iblockId = $model->IBLOCK_ID;
        $iblockType = $model->getIblock()->IBLOCK_TYPE_ID;
        $url = "/bitrix/admin/{$scriptName}.php?IBLOCK_ID={$iblockId}&type={$iblockType}&lang=ru&action=delete&ID={$elementId}";
        return $url;
    }
}