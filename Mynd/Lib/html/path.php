<?php

function rootPath()
{
    return str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
}
function wwwPath()
{
    return rootPath().'www';
}
function _img($path)
{
    return wwwPath().'/images/'.$path;
}

function _cssPath($path)
{
    return wwwPath().'/css/'.$path;
}

function _css($path)
{
    return '<link rel="stylesheet" href="'._cssPath($path).'" media="all"/>';
}

function _file($path)
{
    return wwwPath().'/files/'.$path;
}

function _jsPath($path)
{
    return wwwPath().'/js/'.$path;
}

function _js($path)
{
    return '<script src="'._jsPath($path).'"></script>';
}