<?php

namespace Jugger\Helper;

use Jugger\Helper\Image;

/**
 * Работа с HTML
 */
class Html
{
    /**
     * Возвращает код тэга IMG
     * @param string $src   атрибут 'src'
     * @param string $alt   атрибут 'alt'
     * @param string $title атрибут 'title'. Если не задан, то устанавливается как 'alt'
     * @return string HTML код
     */
    public static function img($src, $alt = "", $title = "") {
        if (empty($title)) {
            $title = $alt;
        }
        return "<img src='{$src}' alt='{$alt}' title='{$title}'>";
    }
    /**
     * Возвращает код тэга для файла
     * @param integer $id_file ID файда
     * @param string $alt   атрибут 'alt'
     * @param string $title атрибут 'title'. Если не задан, то устанавливается как 'alt'
     * @return string HTML код
     */
    public static function imgByFile($id_file, $alt = "", $title = "") {
        return self::img(Image::getSrc($id_file), $alt, $title);
    }
}