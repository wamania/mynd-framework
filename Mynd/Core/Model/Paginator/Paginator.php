<?php

namespace Mynd\Core\Model\Paginator;

class Paginator extends PaginatorBase implements \Iterator
{
    private $datas;

    private $key;

    public function __construct($page, $perPage)
    {
        $this->key = 0;
        parent::__construct($page, $perPage);
    }

    public function setDatas($datas)
    {
        $this->datas = $datas;
    }

    public function reset()
    {
        $this->key = 0;
    }

    /**
     * Implémentation de la méthode current de l'interface Iterator
     * Si l'attribut $this->results est vide, on exécute la requête
     *
     * @return Object of LiModel L'objet courant dans l'itérateur
     */
    public function current()
    {
        if (!isset($this->datas[$this->key])) {
            return null;
        }
        return $this->datas[$this->key];
    }

    /**
     * Implémentation de la méthode key de l'interface Iterator
     *
     * @return
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Implémentation de la méthode rewind de l'interface Iterator

     * @return void
     */
    public function rewind()
    {
        $this->key = 0;
    }

    /**
     * Implémentation de la méthode next de l'interface Iterator
     *
     * @return void
     */
    public function next()
    {
        $this->key++;
    }

    /**
     * Implémentation de la méthode valid de l'interface Iterator
     * Si l'attribut $this->results est vide, on exécute la requête
     *
     * @return void
     */
    public function valid()
    {
        return isset($this->datas[$this->key]);
    }

    /**
     * Renvoie le tableau de résultat complet
     * @return multitype:
     */
    public function fetchAll()
    {
        return $this->datas;
    }

    public function __toString()
    {
        print_r($this->datas);
    }
}