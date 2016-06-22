<?php

namespace Jugger\Context;

use Jugger\Exception\ParamException;

/**
 * Класс для работы с маршрутизацией.
 * На данные момент работает только с компонентами
 */
class UrlManager
{
    /**
     * Базовый URL от которого строятся URL по псевдонимам
     * @var array 
     */
    protected static $baseUrl = [];
    /**
     * Параметры запроса, полученные после разбора URL
     * @var array
     */
    protected static $params = [];
    /**
     * Маршруты, задающие соотвествие URL к шаблонам псевдонимов.
     * @var array
     */
    protected static $aliases = [];
    /**
     * Добавление маршрута
     * @param string $alias имя маршрута
     * @param string $pattern шаблон
     * @param string $group группа маршрута
     */
    public static function addAlias($alias, $pattern, $group = "main") {
        if (is_null(self::$aliases[$group])) {
            self::$aliases[$group] = [];
        }
        self::$aliases[$group][$alias] = $pattern;
    }
    /**
     * Добавление параметра маршрута
     * @param string $param имя параметра
     * @param string $value значение
     * @param string $group группа маршрута
     */
    public static function addParam($param, $value, $group = "main") {
        if (is_null(self::$params[$group])) {
            self::$params[$group] = [];
        }
        self::$params[$group][$param] = $value;
    }
    /**
     * Добавление параметров маршрута
     * @param array $params параметры маршрута
     * @param string $group группа маршрута
     */
    public static function addParams(array $params, $group = "main") {
        self::$params[$group] = $params;
    }
    /**
     * Возвращает список параметров
     * @param string $group группа маршрута
     * @return array
     */
    public static function getParams($group = 'main') {
        return self::$params[$group];
    }
    /**
     * Генерация URL
     * @param string    $alias  имя маршрута
     * @param array     $params параметры подстановки 
     * @param string    $group  группа маршрута
     * @return string   сформированный URL. Если какой то параметр не задан,
     * то в возвращаемом значении будет его маска указанная в шаблоне
     * @throws ParamException
     */
    public static function build($alias, array $params = [], $group = "main") {
        $pattern = self::$aliases[$group][$alias];
        if (empty($pattern)) {
            throw new ParamException("Маршрут '{$alias}' не найден");
        }
        if (is_null(self::$baseUrl[$group])) {
            throw new ParamException("Базовый URL для модуля '{$group}' не найден");
        }
        //
        $re = "/#([A-Za-z_]+)#/";
        $url = self::$baseUrl[$group] . $pattern;
        preg_match_all($re, $url, $vars);
        foreach ($vars[1] as $var) {
            $value = isset($params[$var]) ? $params[$var] : self::$params[$group][$var];
            $url = str_replace("#".$var."#", $value, $url);
        }
        return $url;
    }
    /**
     * Установка базовоного URL
     * @param string $baseUrl базовый URL (подставляется перед маршрутами)
     * @param string $group   группа маршрутов
     */
    public static function setBaseUrl($baseUrl, $group = "main") {
        self::$baseUrl[$group] = $baseUrl;
    }
    /**
     * Разборка запроса
     * @param string $group группа маршрутов
     * @return string имя маршрута, шаблон которого подошел под вызванный URL
     */
    public static function parseRequest($group = 'main') {
        return \CComponentEngine::parseComponentPath(self::$baseUrl[$group], self::$aliases[$group], self::$params[$group]);
    }
}