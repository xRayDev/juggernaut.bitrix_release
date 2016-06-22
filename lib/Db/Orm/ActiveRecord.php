<?php

namespace Jugger\Db\Orm;

use Bitrix\Main\Application;
use Jugger\Helper\Event;
use Jugger\Db\SqlExpressionEx;

/**
 * Реалиазация паттерна ActiveRecord.
 * Облегчает работу с базой
 */
abstract class ActiveRecord
{
    /*
     * События записи
     */
    const EVENT_BEFORE_SAVE     = "AR_EVENT_BEFORE_SAVE";
    const EVENT_BEFORE_INSERT   = "AR_EVENT_BEFORE_INSERT";
    const EVENT_BEFORE_UPDATE   = "AR_EVENT_BEFORE_UPDATE";
    const EVENT_AFTER_SAVE      = "AR_EVENT_AFTER_SAVE";
    const EVENT_AFTER_INSERT    = "AR_EVENT_AFTER_INSERT";
    const EVENT_AFTER_UPDATE    = "AR_EVENT_AFTER_UPDATE";
    /**
     * Флаг, определяющий новая ли запись
     * @var boolean
     */
    protected $_isNewRecord = false;
    /**
     * Поля таблицы
     * @var array
     */
    protected $_fields;
    /**
     * Старые поля таблицы.
     * Нужны при операции UPDATE
     * @var array
     */
    protected $_oldFields;
    /**
     * Создание либо инициализация записи
     * @param array $params если NULL - то создается новая запись, если ARRAY или ActiveRecord- то инициализируется существующая запись
     * @throws \InvalidArgumentException
     */
    public function __construct($params = null) {
        if (empty($params)) {
            $this->_isNewRecord = true;
            $this->_fields = [];
        }
        elseif (is_array($params)) {
            $this->_fields = $params;
        }
        elseif ($params instanceof ActiveRecord) {
            $this->_isNewRecord = $params->_isNewRecord;
            $this->_oldFields = $params->_oldFields;
            $this->_fields = $params->_fields;
        }
        else {
            throw new \InvalidArgumentException("Param = ".json_encode($params).", Class = ".  get_called_class());
        }
    }
    /**
     * Геттер
     * @throws \Exception если свойство не найдено, выплевывает исключение
     */
    public function __get($name) {
        $method = 'get'.$name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        elseif (property_exists($this, $name)) {
            return $this->$name;
        }
        elseif (array_key_exists($name, $this->_fields)) {
            $value = $this->_fields[$name];
            if (is_numeric($value)) {
                return ctype_digit($value."") ? (int) $value : (float) $value;
            }
            else {
                return $value;
            }
        }
        else {
            throw new \Exception("Не найдено свойство `{$name}`");
        }
    }
    /**
     * Сеттер
     * @throws \Exception если свойство не найдено, выплевывает исключение
     */
    public function __set($name, $value) {
        $method = 'set'.$name;
        if (method_exists($this, $method)) {
            $this->$method($value);
        }
        elseif (property_exists($this, $name)) {
            $this->$name = $value;
        }
        elseif ($this->_isNewRecord || array_key_exists($name, $this->_fields)) {
            if (!$this->_isNewRecord && empty($this->_oldFields)) {
                $this->_oldFields = $this->_fields;
            }
            $this->_fields[$name] = $value;
        }
        else {
            throw new \Exception("not found property `{$name}`");
        }
    }
    /**
     * Возвращает родительский класс DataMapper
     */
    abstract protected static function getDataManagerClass();
    /**
     * Создает экземпляр родительского класса
     * @return \ReflectionClass
     */
    protected static function getDataManager() {
        $class = static::getDataManagerClass();
        return new \ReflectionClass($class);
    }
    /**
     * Возвращает первичный ключ.
     * @return mixed если ключ составной - массив, иначе - имя ключа
     */
    public static function getPrimaryKey() {
        return 'ID';
    }
    /**
     * Возвращает имя таблицы
     * @return string
     */
    public static function getTableName() {
        return static::getDataManager()->getMethod("getTableName")->invoke(null);
    }
    /**
     * Сохранение записи.
     * Если запись новая - insert
     * Если запись старая - update
     * @return boolean
     */
    public function save() {
        if (!$this->onBeforeSave()) {
            return false;
        }
        $result = false;
        if ($this->_isNewRecord) {
            if ($this->onBeforeInsert()) {
                $result = $this->insert();
            }
            if ($result) {
                $this->onAfterInsert();
            }
        }
        else {
            if ($this->onBeforeUpdate()) {
                $result = $this->update();
            }
            if ($result) {
                $this->onAfterUpdate();
            }
        }
        if ($result) {
            $this->onAfterSave();
        }
        return $result;
    }
    /**
     * Выполняет запрос на создание
     * @return boolean
     */
    protected function insert() {
        $args = [];
        $values = [];
        $prepares = [];
        //
        $args[] = self::getTableName();
        foreach ($this->_fields as $column => $value) {
            if (is_null($value)) {
                continue;
            }
            $prepares[] = "?";
            $args[] = $column;
            $values[] = $value;
        }
        $args = array_merge($args, $values);
        $columns = implode(",",array_map(function($row){ return $row."#"; }, $prepares));
        $prepares = implode(",", $prepares);
        $sql = "INSERT INTO ?#({$columns}) VALUES({$prepares})";
        $sql = (new SqlExpressionEx($sql, $args))->compile();
        Application::getConnection()->queryExecute($sql);
        $key = self::getPrimaryKey();
        if (!is_array($key)) {
            $this->_fields[$key] = Application::getConnection()->getInsertedId();
        }
        return true;
    }
    /**
     * Выполняет запрос на изменение
     * @return boolean
     */
    protected function update() {
        if (empty($this->_oldFields)) {
            return true;
        }
        $key = static::getPrimaryKey();
        if (is_array($key)) {
            return $this->updateCompositeKey($key);
        }
        else {
            return $this->updateSingleKey($key);
        }
    }
    /**
     * Выполняет запрос UPDATE для составного ключа
     * @param array $key
     * @return boolean
     */
    protected function updateCompositeKey(array $key) {
        $args = [];
        $args[] = self::getTableName();
        $set = [];
        foreach ($this->_fields as $column => $value) {
            if (is_null($value)) {
                continue;
            }
            $set[] = "?# = ?";
            $args[] = $column;
            $args[] = $value;
        }
        $set = implode(",", $set);
        //
        $keyPrepare = [];
        foreach ($key as $column) {
            $value = $this->_oldFields[$column];
            if (is_null($value)) {
                continue;
            }
            $keyPrepare[] = "?# = ?";
            $args[] = $column;
            $args[] = $value;
        }
        $keyPrepare = implode(" AND ", $keyPrepare);
        $sql = "UPDATE ?# SET {$set} WHERE {$keyPrepare}";
        $sql = (new SqlExpressionEx($sql, $args))->compile();
        Application::getConnection()->queryExecute($sql);
        return true;
    }
    /**
     * Выполняет запрос UPDATE для простого ключа
     * @param string $key
     * @return boolean
     */
    protected function updateSingleKey($key) {
        $args = [];
        $args[] = self::getTableName();
        $set = [];
        foreach ($this->_fields as $column => $value) {
            if ($column === $key || is_null($value)) {
                continue;
            }
            $set[] = "?# = ?";
            $args[] = $column;
            $args[] = $value;
        }
        $set = implode(",", $set);
        $args[] = $key;
        $args[] = $this->_oldFields[$key];
        $sql = "UPDATE ?# SET {$set} WHERE ?# = ?i";
        $sql = (new SqlExpressionEx($sql, $args))->compile();
        Application::getConnection()->queryExecute($sql);
        return true;
    }
    /**
     * Удаление записи
     * @return boolean
     */
    public function delete() {
        $key = static::getPrimaryKey();
        $value = $this->_fields[$key];
        if ($value) {
            $sql = "DELETE FROM ?# WHERE ?# = ?i";
            $sql = new SqlExpressionEx($sql, [
                self::getTableName(),
                $key,
                (int) $value
            ]);
            Application::getConnection()->queryExecute($sql->compile());
            return true;
        }
        return false;
    }
    /**
     * Поиск по первичному ключу
     * @param string $primaryValue
     * @return ActiveRecord
     */
    public static function getByPrimary($primaryValue) {
        $primaryKey = static::getPrimaryKey();
        return static::getRow([
            "filter" => [$primaryKey => $primaryValue]
        ]);
    }
    /**
     * Поиск одной записи по параметрам
     * @param array $params параметры запроса
     * @return mixed если ничего не найдено - NULL, иначе - ActiveRecord
     */
    public static function getRow(array $params) {
        $params['limit'] = 1;
        $class = get_called_class();
        $row = static::getList($params)->fetch();
        return $row ? new $class($row) : null;
    }
    /**
     * Поиск одно записи по конкретному полю
     * @param string $field имя столбца
     * @param mixed $value значение
     * @param array $params другие параметры
     * @return mixed если ничего не найдено - NULL, иначе - ActiveRecord
     */
    public static function getRowByField($field, $value, array $params = []) {
        $params['limit'] = 1;
        $class = get_called_class();
        $row = static::getListByField($field, $value, $params)->fetch();
        return $row ? new $class($row) : null;
    }
    /**
     * Поиск всех записей по параметрам
     * @param array $params параметры запроса
     * @return \Bitrix\Main\DB\Result
     */
    public static function getList(array $params = []) {
        return static::getDataManager()->getMethod("getList")->invoke(null, $params);
    }
    /**
     * Поиск всех записей по конкретному полю
     * @param string $field имя столбца
     * @param mixed $value значение
     * @param array $params другие параметры
     * @return \Bitrix\Main\DB\Result
     */
    public static function getListByField($field, $value, array $params = []) {
        if (!isset($params['filter'])) {
            $params['filter'] = [];
        }
        $params['filter'][$field] = $value;
        return static::getList($params);
    }
    /**
     * До сохранения (insert & update)
     * @return boolean
     */
    public function onBeforeSave() {
        $event = Event::trigger(self::EVENT_BEFORE_SAVE, $this, "juggernaut");
        return Event::isSuccess($event);
    }
    /**
     * После сохранения (insert & update)
     * @return boolean
     */
    public function onAfterSave() {
        $event = Event::trigger(self::EVENT_AFTER_SAVE, $this, "juggernaut");
        return Event::isSuccess($event);
    }
    /**
     * До добавления (insert)
     * @return boolean
     */
    public function onBeforeInsert() {
        $event = Event::trigger(self::EVENT_BEFORE_INSERT, $this, "juggernaut");
        return Event::isSuccess($event);
    }
    /**
     * После добавления (insert)
     * @return boolean
     */
    public function onAfterInsert() {
        $event = Event::trigger(self::EVENT_AFTER_INSERT, $this, "juggernaut");
        return Event::isSuccess($event);
    }
    /**
     * До обновления (update)
     * @return boolean
     */
    public function onBeforeUpdate() {
        $event = Event::trigger(self::EVENT_BEFORE_UPDATE, $this, "juggernaut");
        return Event::isSuccess($event);
    }
    /**
     * После обнавленя (update)
     * @return boolean
     */
    public function onAfterUpdate() {
        $event = Event::trigger(self::EVENT_AFTER_UPDATE, $this, "juggernaut");
        return Event::isSuccess($event);
    }
}