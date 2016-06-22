<?php

namespace Jugger\Component;

use Jugger\Context\UrlManager;

/**
 * Компонент - роутер
 * По факту является контроллером,
 * поэтому главной задачи такого вида компонентов является парсинг запроса и соответствующая реакция
 */
abstract class RouteComponent extends BaseComponent
{   
    /**
     * идентификатор для Url менеджера
     * @var string
     */
    public static $urlManagerId;
    /**
     * @see \CBitrixComponent
     */
    public function executeComponent() {
        $this->init();
        $action = $this->getAlias();
        if ($action === false) {
            $this->error404($this->arParams);
        }
        elseif ($this->onBefore($action)) {
            $this->run($action);
            $this->onAfter($action);
        }
    }
    /**
     * Инициализация входных параметров и UrlManager
     */
    protected function init() {
        if (empty(self::$urlManagerId)) {
            self::$urlManagerId = "main";
        }
        $this->initProperties();
        $this->initUrlManager();
    }
    /**
     * Инициализация маршрутов для менеджера.
     * Маршруты компонент получает из параметров,
     * либо их можно вручную прописать в методе 'getAliases' при реализации класса
     */
    protected function initUrlManager() {
        UrlManager::setBaseUrl($this->arParams['baseUrl'], self::$urlManagerId);
        array_walk($this->getAliases(), function($pattern, $alias){
            UrlManager::addAlias($alias, $pattern, self::$urlManagerId);
        });
    }
    /**
     * Список маршрутов
     * @return array
     */
    protected function getAliases() {
        return $this->arParams['aliases'];
    }
    /**
     * Возвращает имя вызванного псевдонима (действия)
     * @return string
     */
    protected function getAlias() {
        return UrlManager::parseRequest(self::$urlManagerId);
    }
    /**
     * Генерация 404 ошибки
     * @global \CMain $APPLICATION
     */
    protected function error404() {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        include $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DIR.'/404.php';
        die();
    }
    /**
     * Событие перед выполнением действия.
     * Можно назначить персональный обработчик для действия определив метод 'onBeforeAction',
     * где вместо 'Action' - имя действия
     * @param stirng $action имя действия, которое будет выполняться при запросе
     * @return boolean FALSE - если действие нельзя выполнять
     */
    protected function onBefore($action) {
        $methodName = "onBefore".$action;
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }
        else {
            return true;
        }
    }
    /**
     * Событие после выполнения действия.
     * Можно назначить персональный обработчик для действия определив метод 'onAfterAction',
     * где вместо 'Action' - имя действия
     * @param stirng $action имя действия, которое будет выполняться при запросе
     */
    protected function onAfter($action) {
        $methodName = "onAfter".$action;
        if (method_exists($this, $methodName)) {
            $this->$methodName();
        }
    }
    /**
     * Выполнение действия вызванного пользователем.
     * По-умолчанию выводит представление с таким же именем как у действия (псевдонима)
     * @param string $action
     */
    protected function run($action) {
        $methodName = "action".$action;
        if (method_exists($this, $methodName)) {
            call_user_method_array($methodName, $this, UrlManager::getParams());
        }
        else {
            $this->render($action);
        }
    }
}