<?php

namespace Mynd\Core\Model\Abstraction;

class Pgsql_Select extends \Mynd\Core\Model\Select
{
    public function execute()
    {
        if ( (!empty($this->order)) && (is_array($this->order)) ) {
            foreach ($this->order as $key => $value) {
                $this->order[$key] = preg_replace ('#\+([0-9a-zA-Z\-\_]+)#', '$1 ASC', $value);
                $this->order[$key] = preg_replace ('#\-([0-9a-zA-Z\-\_]+)#', '$1 DESC', $value);
            }
        }

        $query = "SELECT ";
        if ($this->distinct) {
            $query .= 'DISTINCT ';
        }

        $query .= "*  FROM " . $this->table;
        $query .= $this->buildWhere();

        if (! empty($this->group)) {
            $query .= " GROUP BY " . implode(',', $this->group);
        }
        if (! empty($this->order)) {
            $query .= " ORDER BY " . implode(',', $this->order);
        }

        if (! empty($this->limitLength)) {
            $query .= " LIMIT " . $this->limitLength . " OFFSET " . $this->limitOffset;
        }

        $s = $this->db->prepare($query);
        $s->execute($this->params);

        $this->results = array();

        while ($row = $s->fetchObject($this->class)) {
            $this->results[] = $row;
        }
        $this->key = 0;
    }
}