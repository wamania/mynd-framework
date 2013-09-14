<?php

namespace Mynd\Core\Model\Paginator;

/**
 * Paginator pour la class Select
 * @author wamania
 *
 */
class PaginatorSelect extends Paginator implements \Iterator
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