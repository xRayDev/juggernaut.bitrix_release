<?php

namespace Jugger\Component;

use Bitrix\Main\Context;

/**
 * Базовый функционал для всех компонентов
 */
abstract class BaseComponent extends \CBitrixComponent
{
    /**
     * Объект запроса
     * @var \Bitrix\Main\Request
     */
    protected $request;
    /**
     * Проверка на AJAX'ость запроса
     * @return boolean
     */
    protected function isAjaxRequest() {
        return $this->request->isAjaxRequest();
    }
    /**
     * Вывод представления
     * @param string $view имя представления
     */
    protected function render($view = '') {
        if ($this->onBeforeRender($view)) {
            $this->includeComponentTemplate($view);
        }
        else {
            $this->onFailRender();
        }
    }
    /**
     * Вывод представления без шаблона (без шапки)
     * @global \CMain $APPLICATION
     * @param string $view имя представления
     */
    protected function renderPartial($view = '') {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        $this->render($view);
    }
    /**
     * Обработка события до вывода страницы на экран
     * @param string $view имя представления
     * @return boolean
     */
    protected function onBeforeRender($view) {
        return true;
    }
    /**
     * Информация о невозможности вывести шаблон (onBeforeRender вернул FALSE)
     */
    protected function onFailRender() {
        echo "не удалось вывести данные: onBeforeRender === false";
    }
    /**
     * Инициализация свойств класса из входных параметров
     */
    protected function initProperties() {
        $this->request = Context::getCurrent()->getRequest();
        foreach ($this->arParams as $name => $value) {
            if ($name === "CACHE_TIME") {
                $this->cacheTime = (int) $value;
            }
            elseif (property_exists($this, $name)) {
                $this->$name = is_numeric($value) ? +$value : $value;
            }
        }
    }
}