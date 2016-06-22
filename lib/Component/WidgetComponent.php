<?php

namespace Jugger\Component;

/**
 * Компонент - виджет.
 * Не реализует никакой сложной логики. Главна задача - это преобразовывать входящие данные в форматированный выход.
 */
abstract class WidgetComponent extends BaseComponent
{
    /**
     * Флаг определяющий кеширование компонента
     * @var boolean
     */
    protected $isCachingTemplate = false;
    /**
     * Время кеширования
     * @var int
     */
    protected $cacheTime = 36000000;
    /**
     * Исполнение виджета.
     * По умолчанию отображение представления
     */
    protected function run() {
        $this->render();
    }
    /**
     * @see \CBitrixComponent
     */
    public function executeComponent() {
        $this->init();
        if ($this->onBefore()) {
            if (!$this->isCachingTemplate || $this->startResultCache($this->cacheTime)) {
                $this->initResult();
                $this->run();
                $this->endResultCache();
            }
            $this->onAfter();
        }
    }
    /**
     * Инициализация входных параметров
     */
    protected function init() {
        $this->initProperties();
    }
    /**
     * Инициализация объекта $arResut
     */
    protected function initResult() {
        // pass
    }
    /**
     * Событие перед исполнением виджета
     * @return boolean FALSE - если действие нельзя выполнять
     */
    protected function onBefore() {
        return true;
    }
    /**
     * Событие после исполнения виджета
     */
    protected function onAfter() {
        // pass
    }
}