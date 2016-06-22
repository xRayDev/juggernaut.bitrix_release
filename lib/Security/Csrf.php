<?php

namespace Jugger\Security;

use Bitrix\Main\Context;
use Jugger\Context\Session;

/**
 * Класс для работы с защитой от CSRF атак
 */
class Csrf
{
    /**
     * Секретное слово (желательно изменить)
     * @var string
     */
    public static $secret = "csrfsecretword";
    /**
     * Соль
     * @var string
     */
    public static $salt = "fgjnu24e4t7f97^G&R#";
    /**
     * Создает токен и выводит поле с ним
     */
    public static function printInput() {
        $token = self::createToken();
        echo "<input type='hidden' name='csrf' value='{$token}'>";
    }
    /**
     * Создает токен
     * @return string
     */
    public static function createToken() {
        $token = md5( self::$secret . microtime() );
        $token = md5( self::$salt . $token );
        $_SESSION[self::$secret] = $token;
        return $token;
    }
    /**
     * Проверка токена
     * @param string $token токен для проверки
     * @return boolean TRUE - если токен валидный
     */
    public static function validateToken($token) {
        $trueToken = Session::getInstance()->get(self::$secret);
        self::removeToken();
        if (trim($trueToken) !== "" && $token === $trueToken) {
            return true;
        }
        return false;
    }
    /**
     * Удаление токена из сессии
     */
    public static function removeToken() {
        Session::getInstance()->delete(self::$secret);
    }
    /**
     * Проверка токена напрямую из запроса
     * @return boolean TRUE - если токен валидный
     */
    public static function validateTokenByPost() {
        $token = Context::getCurrent()->getRequest()->getPost("csrf");
        if ($token) {
            return self::validateToken($token);
        }
        self::removeToken();
        return false;
    }
}