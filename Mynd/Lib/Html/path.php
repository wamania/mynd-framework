<?php

function _rootPath()
{
    return str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
}
function _wwwPath()
{
    return _rootPath().'www';
}
function _img($path)
{
    return _wwwPath().'/images/'.$path;
}

function _cssPath($path)
{
    return _wwwPath().'/css/'.$path;
}

function _css($path)
{
    return '<link rel="stylesheet" href="'._cssPath($path).'" media="all"/>';
}

function _file($path)
{
    return _wwwPath().'/files/'.$path;
}

function _jsPath($path)
{
    return _wwwPath().'/js/'.$path;
}

function _js($path)
{
    return '<script src="'._jsPath($path).'"></script>';
}