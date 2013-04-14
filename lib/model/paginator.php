<?php

abstract class MfPaginatorBase
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

class MfPaginator extends MfPaginatorBase implements Iterator
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

/**
 * Paginator pour la class Select
 * @author wamania
 *
 */
class MfPaginatorSelect extends MfPaginator implements Iterator
{
    private $select;

    public function __construct($page, $perPage)
    {
        parent::__construct($page, $perPage);
    }

    /**
     * Setter du tableau de résultats
     * @param Array $datas
     */
    public function setSelect($select)
    {
        $this->select = $select;
    }

    /**
     * Implémentation de la méthode current de l'interface Iterator
     * Si l'attribut $this->results est vide, on exécute la requête
     *
     * @return Object of LiModel L'objet courant dans l'itérateur
     */
    public function current()
    {
        return $this->select->current();
    }

    /**
     * Implémentation de la méthode key de l'interface Iterator
     *
     * @return
     */
    public function key()
    {
        return $this->select->key();
    }

    /**
     * Implémentation de la méthode rewind de l'interface Iterator

     * @return void
     */
    public function rewind()
    {
        $this->select->rewind();
    }

    /**
     * Implémentation de la méthode next de l'interface Iterator
     *
     * @return void
     */
    public function next()
    {
        $this->select->next();
    }

    /**
     * Implémentation de la méthode valid de l'interface Iterator
     * Si l'attribut $this->results est vide, on exécute la requête
     *
     * @return void
     */
    public function valid()
    {
        return $this->select->valid();
    }

    /**
     * Renvoie le tableau de résultat complet
     * @return multitype:
     */
    public function fetchAll()
    {
        return $this->select->fetchAll();
    }

    /**
     * Fonction magique toString
     */
    public function __toString()
    {
        echo $this->select;
    }
}