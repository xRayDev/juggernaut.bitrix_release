<?php

namespace Jugger\Ui;

use Jugger\Helper\Main;
use Jugger\Ui\Hermitage\Icon;
use Jugger\Context\UrlManager\Iblock;
use Jugger\Db\Orm\Ib\IblockSection;
use Jugger\Db\Orm\Ib\IblockElement;

/**
 * Класс для работы с интерфейсом "Эрмитаж"
 */
class Hermitage
{
    // остальные параметры:  $icon, $src $alt = null, $menu = null, $type = null, $inParamsMenu = null, $inMenu = null, $default = false, $image = null, $separator = null
    public static function addButton(\CBitrixComponent $component, $url, $title, $params = []) {
        $params['URL'] = $url;
        $params['TITLE'] = $title;
        $component->addIncludeAreaIcon($params);
    }
    /**
     * Добавляет кнопку редактирования инфоблока
     * @param \CBitrixComponent $template
     * @param IblockElement $element
     */
    public static function addButtonEditIblockElement(\CBitrixComponentTemplate & $template, IblockElement $element, $title = "Редактировать") {
        $template->AddEditAction(
            $element->ID,
            Iblock::getElementUpdateUrl($element),
            $title
        );
    }
    /**
     * Добавляет кнопку удаление элемента инфоблока
     * @param \CBitrixComponent $template
     * @param IblockElement $element
     */
    public static function addButtonDeleteIblockElement(\CBitrixComponentTemplate & $template, IblockElement $element, $title = "Удалить") {
        $template->AddDeleteAction(
            $element->ID,
            Iblock::getElementDeleteUrl($element),
            $title,
            [
                "CONFIRM" => "Вы уверены что хотите удалить запись?"
            ]
        );
    }
    /**
     * Добавляет кнопку редактирования инфоблока
     * @param \CBitrixComponent $template
     * @param IblockElement $element
     */
    public static function addButtonEditIblockSection(\CBitrixComponentTemplate & $template, IblockSection $element, $title = "Редактировать") {
        $template->AddEditAction(
            $element->ID,
            Iblock::getSectionUpdateUrl($element),
            $title
        );
    }
    /**
     * Добавляет кнопку удаление элемента инфоблока
     * @param \CBitrixComponent $template
     * @param IblockElement $element
     */
    public static function addButtonDeleteIblockSection(\CBitrixComponentTemplate & $template, IblockSection $element, $title = "Удалить") {
        $template->AddDeleteAction(
            $element->ID,
            Iblock::getSectionDeleteUrl($element),
            $title,
            [
                "CONFIRM" => "Вы уверены что хотите удалить запись?"
            ]
        );
    }
    /**
     * Добавляет кнопку в верхнюю панель в публичной части
     * @param string $href ссылка на кнопке
     * @param string $text текст кнопки
     * @param array $params необязательный параметры:
     *  ALT - текст всплывающей подсказки на кнопке
     *  MAIN_SORT - индекс сортировки для группы кнопок, для стандартных групп иконок данный параметр имеет следующие значения:
     *      100 - группа иконок модуля управления статикой
     *      200 - группа иконок модуля документооборота
     *      300 - группа иконок модуля информационных блоков
     *  SORT - индекс сортировки внутри группы кнопок
     *  TYPE - (BIG/SMALL) размер иконки. (По умолчанию "SMALL".)
     *  HINT - Массив с ключами:
     *      TITLE - Заголовок всплывающей подсказки;
     *      TEXT - Текст всплывающей подсказки.
     *  ICON - CSS иконки.
     */
    public static function addPanelButton($href, $text, array $params = []) {
        $params['HREF'] = $href;
        $params['TEXT'] = $text;
        Main::app()->AddPanelButton($params);
    }
}