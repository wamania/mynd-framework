<?php

namespace Mynd\Core\Model\Paginator;

abstract class PaginatorBase
{
    protected $count;

    protected $page;

    protected $perPage;

    public function __construct($page, $perPage)
    {
        $this->page = $page;
        $this->perPage = $perPage;
    }

    /**
     * Setter du nombre total de résultats
     * @param Int $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * Nombre total de résultats sans pagination
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * Nombre de page
     * @return number
     */
    public function pageCount()
    {
        return ceil($this->count/$this->perPage);
    }

    /**
     * Setter de la page actuelle
     * @param int $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * Page actuelle
     */
    public function page()
    {
        return $this->page;
    }
}