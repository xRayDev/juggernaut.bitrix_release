<?php

namespace Jugger\Helper;

use Bitrix\Main\Context;

/**
 * Класс для работы с пагинацией
 */
class Paginator
{
    // records
    public $limit;
    public $totalCount;
    // pages
    public $pageNow;
    public $pageTotal;
    // request
    public $pageNowRequestName = 'pageNow';
    public $limitRequestName = 'perPage';
    
    public function __construct($totalCount) {
        $this->totalCount = $totalCount;
    }
    
    public static function create($totalCount) {
        return new self($totalCount);
    }

    public function getOffset() {
        return (int) (($this->pageNow - 1) * $this->limit);
    }
    
    public function init() {
        $o = $this->getOffset();
        $l = (int) $this->limit; // 50
        $t = (int) $this->totalCount; // 600
        //
        $y = (int) ($t / $l);
        $y = ($y <= 0) ? 1 : $y;
        $y = ($t % $l > 0 && $t > $l) ? $y + 1 : $y;
        //
        $x = (int) ($o / $l);
        if ($x >= $y) {
            $x = $y - 1;
        }
        else {
            $x = ($x < 0) ? 0 : $x;
            $x = ($x > $y) ? $y : $x;
        }
        //
        $this->pageNow = $x + 1;
        $this->pageTotal = $y;
    }
    /**
     * Преобразует номер старницы и количество показываемых элементов,
     * в свойства 'limit' и 'offset'
     * @param integer $defaultPageNow номер страницы по умолчанию
     * @param integer $defaultPerPage количество записей на странице по умолчанию
     * @return array массив с ключами 'limit' и 'offset'
     */
    public function parseRequest($defaultPageNow, $defaultPerPage) {
        $pageNow = (int) Context::getCurrent()->getRequest()->getQuery($this->pageNowRequestName);
        $perPage = (int) Context::getCurrent()->getRequest()->getQuery($this->limitRequestName);
        //
        $this->pageNow = $pageNow > 0 ? $pageNow : $defaultPageNow;
        $this->limit   = $perPage > 0 ? $perPage : $defaultPerPage;
        //
        $this->init();
        return [
            "limit" => $this->limit,
            "offset" => $this->getOffset()
        ];
    }
}