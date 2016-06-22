<?php

namespace Jugger\Helper;

/**
 * Класс для работы с изображениями
 */
class Image
{
    /**
     * Возвращает путь до файла
     * @param integer $id_file ID файла
     * @return mixed если существует файл - возвращает путь, иначе - FALSE
     */
    public static function getSrc($id_file) {
        $arr = (new \CFile())->GetFileArray($id_file);
        return is_array($arr) ? $arr['SRC'] : false;
    }
}