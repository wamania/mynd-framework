<?php

function paginator($paginator, $params, $options=array())
{
    $length = 11;
    $pager = '<ul>';

    if ($paginator->page() == 1) {
        $before = 1;
    } else {
        $before = $paginator->page() - 1;
    }
    $pager .= '<li><a href="'._url(array_merge($params, array('page' => $before))).'">&#9664;</a></li>';

    // pages d'avant
    $first = max(1, ($paginator->page() - floor($length/2)));
    $last = min ($paginator->pageCount(), ($paginator->page() + floor($length/2)));

    for ($i=$first; $i<$paginator->page(); $i++) {
        $pager .= '<li><a href="'._url(array_merge($params, array('page' => $i))).'">'.$i.'</a></li>';
    }

    $pager .= '<li class="active"><a href="#">'.$paginator->page().'</a></li>';

    for ($i=($paginator->page()+1); $i<=$last; $i++) {
        $pager .= '<li><a href="'._url(array_merge($params, array('page' => $i))).'">'.$i.'</a></li>';
    }

    // after
    if ($paginator->page() == $paginator->pageCount()) {
        $after = $paginator->page();
    } else {
        $after = $paginator->page() + 1;
    }

    $pager .= '<li><a href="'._url(array_merge($params, array('page' => $after))).'">&#9654;</a></li>';
    $pager .= '</ul>';

    return $pager;
}