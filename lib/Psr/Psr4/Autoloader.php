<?php

namespace Jugger\Psr\Psr4;

/**
 * Автозагрузчик.
 * Реализует PSR-4 с доработками для Битрикс.
 * По умолчанию (если не указана директория файла) ищет классы в поддиректориях 'local\modules' и 'bitrix\modules'
 * 
 * src: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
 */
class Autoloader
{
    /**
     * Список сопоставлений пространств имен и базовых директорий
     * @var array
     */
    protected static $prefixes = array();

    /**
     * Ручное добавление маршрутизации по namespace.
     * Нужно для переопределения логики по умолчанию.
     *
     * @param string $prefix префикс пространства имен (наприме, "Foo" или "Foo\Bar")
     * @param string $baseDir директория где содержаться дочерние классы
     * @param bool $prepend если TRUE - то добавляется в начало очереди списка директорий
     * @return void
     */
    public static function addNamespace($prefix, $baseDir, $prepend = false)
    {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . '/';
        if (isset(self::$prefixes[$prefix]) === false) {
            self::$prefixes[$prefix] = [];
        }
        if ($prepend) {
            array_unshift(self::$prefixes[$prefix], $baseDir);
        }
        else {
            array_push(self::$prefixes[$prefix], $baseDir);
        }
    }
    
    /**
     * Загрузка класса
     * @param string $class имя класса
     * @return mixed имя файла если он существует, иначе - FALSE
     */
    public static function loadClass($class)
    {
        $prefix = $class;
        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);
            $relativeClass = substr($class, $pos + 1);
            $mappedFile = self::loadMappedFile($prefix, $relativeClass);
            if ($mappedFile) {
                return $mappedFile;
            }
            $prefix = rtrim($prefix, '\\');
        }
        return false;
    }

    /**
     * Загрузка файла класса
     *
     * @param string $prefix префикс пространства имен
     * @param string $relativeClass запрашиваемый класс 
     * @return mixed имя файла если он существует, иначе - FALSE
     */
    protected static function loadMappedFile($prefix, $relativeClass)
    {
        $baseDirList = [];
        /**
         * Если не задана директория для запрашиваемого префикса,
         * то по умолчанию поиск производиться в папках 'local\modules' и 'bitrix\modules'
         */
        if (isset(self::$prefixes[$prefix]) === false) {
            $baseDirList = [
                self::getDocumentRoot()."/local/modules/{$prefix}/lib/",
                self::getDocumentRoot()."/bitrix/modules/{$prefix}/lib/",
            ];
        }
        else {
            $baseDirList = self::$prefixes[$prefix];
        }
        foreach ($baseDirList as $baseDir) {
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (self::requireFile($file)) {
                return $file;
            }
        }
        return false;
    }

    protected static function requireFile($file)
    {
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
    
    protected static function getDocumentRoot()
    {
        return rtrim($_SERVER["DOCUMENT_ROOT"], "/\\");
    }
}