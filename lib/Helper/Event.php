<?php

namespace Jugger\Helper;

use Bitrix\Main\EventManager;
use Bitrix\Main\EventResult;

/**
 * Класс для работы с событиями
 */
class Event
{
    /**
     * Добавление события
     * @param stirng $eventName имя события
     * @param callback $eventHandler обработчик
     * @param string $moduleName имя модуля
     * @return integer ключ (индекс) обработчика события
     */
    public static function on($eventName, $eventHandler, $moduleName = 'main') {
        return EventManager::getInstance()->addEventHandler($moduleName, $eventName, $eventHandler);
    }
    /**
     * Удаление события
     * @param string $eventName имя события
     * @param integer $eventHandlerKey ключ обработчика
     * @param string $moduleName имя модуля
     * @return boolean результат операции
     */
    public static function off($eventName, $eventHandlerKey = 0, $moduleName = 'main') {
        return EventManager::getInstance()->removeEventHandler($moduleName, $eventName, $eventHandlerKey);
    }
    /**
     * Вызов события
     * @param string $eventName имя события
     * @param object $sender объект который отправил события
     * @param string $moduleName 
     * @return \Bitrix\Main\Event объект события
     */
    public static function trigger($eventName, $sender = null, $moduleName = 'main') {
        $eventObject = new \Bitrix\Main\Event($moduleName, $eventName);
        return self::triggerObject($eventObject, $sender);
    }
    /**
     * Вызов события с помощью объекта события
     * @param \Bitrix\Main\Event $eventObject
     * @param object $sender объект который отправил события
     * @return \Bitrix\Main\Event объект события
     */
    public static function triggerObject(\Bitrix\Main\Event $eventObject, $sender = null) {
        $eventObject->send($sender);
        return $eventObject;
    }

    public static function isSuccess(\Bitrix\Main\Event $event) {
        foreach ($event->getResults() as $result) {
            if ($result->getType() === EventResult::ERROR) {
                return false;
            }
        }
        return true;
    }
}