<?php

namespace Jugger\Context;

/**
 * Класс для работы с сессией.
 * Обертка на объект $_SESSION
 * Для удобства работы, все методы, которые не возвращают значение, возвращают текущий объект,
 * поэтому возможны следующие конструкции:
 *  Session::getInstance()
 *      ->set("p1", "v1")
 *      ->set("p2", "v2")
 *      ->set("p3", ["v3", "v4"])
 *      ->append("p3", "v5")
 *      ->commit();
 */
class Session
{
    /**
     * Флаг определяющий начало сессии
     * @var boolean
     */
    protected $isStarted = false;
    /**
     * Объект синглтона
     * @var Session
     */
    protected static $instance;
    /**
     * Получить текущий экземпляр
     * @return \Jugger\Context\Session
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function __construct() {
        $this->start();
    }
    /**
     * Начало сессии.
     * По факту можно явно не вызывать этот метод,
     * т.к. методы 'get', 'set', 'delete', 'exist' автоматически запускают сессию
     * @return \Jugger\Context\Session
     */
    public function start() {
        if (!$this->isStarted) {
            session_start();
            $this->isStarted = true;
        }
        return $this;
    }
    /**
     * Сброс сессии
     * @return \Jugger\Context\Session
     */
    public function abort() {
        if ($this->isStarted) {
            session_abort();
        }
        return $this;
    }
    /**
     * Возвращает значение элемента
     * @param string $name имя
     * @return mixed если запрошенный элемент не существует, то NULL, иначе - значение
     */
    public function get($name) {
        $this->start();
        return $this->exist($name) ? $_SESSION[$name] : null;
    }
    /**
     * Устанавливает значение элемента
     * @param string $name имя элемента
     * @param mixed $value значение
     * @return \Jugger\Context\Session
     */
    public function set($name, $value) {
        $this->start();
        $_SESSION[$name] = $value;
        return $this;
    }
    /**
     * Проверяет, существует ли элемент
     * @param string $name имя элемента
     * @return boolean
     */
    public function exist($name) {
        $this->start();
        return isset($_SESSION[$name]);
    }
    /**
     * Удаление элемента
     * @param string $name имя элемента
     * @return \Jugger\Context\Session
     */
    public function delete($name) {
        $this->start();
        unset($_SESSION[$name]);
        return $this;
    }
    /**
     * Удаление нескольких элементов
     * @param array $names массив имен удаляемых элементов
     * @return \Jugger\Context\Session
     */
    public function deleteMany(array $names) {
        foreach ($names as $name) {
            $this->delete($name);
        }
        return $this;
    }
    /**
     * Добавляет значение к элементу сессии.
     * Если элемент сессии не массив, то он к нему приводится
     * Если элемент сессии массив, то новое значение просто добавляется в конец
     * Если элемент не существует, то создается новый элемент с новым значением
     * @param string $name имя элемента
     * @param mixed $value значение
     * @return \Jugger\Context\Session
     */
    public function append($name, $value) {
        $x = $this->get($name);
        if (is_array($x)) {
            $x[] = $value;
            $this->set($name, $x);
        }
        elseif ($x) {
            $this->set($name, [$x, $value]);
        }
        else {
            $this->set($name, [$value]);
        }
        return $this;
    }
}