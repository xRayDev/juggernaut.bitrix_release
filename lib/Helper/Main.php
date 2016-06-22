<?php

namespace Jugger\Helper;

use Bitrix\Main\Application;

/**
 * Класс облегчающий работу с глобальными меременными
 */
class Main
{
    /**
     * @return \CMain
     */
    public static function app() {
        return $GLOBALS['APPLICATION'];
    }
    /**
     * @return \CUser
     */
    public static function user() {
        return $GLOBALS['USER'];
    }
    /**
     * @return \Bitrix\Main\DB\Connection
     */
    public static function db() {
        return Application::getConnection();
        //return $GLOBALS['DB'];
    }
    /**
     * Добавить свойство к странице
     * @param string $propertyId ID свойства
     * @param string $value значение свойства
     */
    public static function addPageProperty($propertyId, $value) {
        if (empty($value)) {
            return;
        }
        $oldValue = self::app()->GetPageProperty($propertyId, null);
        if ($oldValue) {
            $value = $oldValue ." ". $value;
        }
        self::app()->SetPageProperty($propertyId, $value);
    }
    /**
     * Подключение компонента
     * @param string $name имя компонента
     * @param array $params параметры компонента
     * @param string $template имя шаблона
     * @return mixed данные компонента
     */
    public static function includeComponent($name, array $params = [], $template = '.default') {
        $parent = $params['parent'] ? $params['parent'] : null;
        $functionParams = $params['functionParams'] ? $params['functionParams'] : [];
        unset(
            $params['functionParams'], 
            $params['parent']
        );
        //
        return self::app()->IncludeComponent($name, $template, $params, $parent, $functionParams);
    }
}