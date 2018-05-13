<?php

class Router
{

    public static function route()
    {
        LA\Routing::match(self::getListUrl(), [CrudController::class, 'listAction']);
        LA\Routing::match(self::getAddUrl(), [CrudController::class, 'addAction']);
        LA\Routing::match(self::getCreateUrl(), [CrudController::class, 'createAction']);
        LA\Routing::match(self::getEditUrl(), [CrudController::class, 'editAction']);
        LA\Routing::match(self::getDeleteUrl(), [CrudController::class, 'deleteAction']);
        LA\Routing::match(self::getSaveUrl(), [CrudController::class, 'saveAction']);

    }


    public static function getListUrl($class = "(.*)")
    {
        return "/admin/crud/list/" . $class;
    }

    public static function getAddUrl($class = "(.*)")
    {
        return "/admin/crud/add/" . $class;
    }

    public static function getCreateUrl($class = "(.*)")
    {
        return "/admin/crud/create/" . $class;
    }

    public static function getEditUrl($class = "(.*)", $id = "(\d+)")
    {
        return "/admin/crud/edit/" . $class . "/" . $id;
    }

    public static function getDeleteUrl($class = "(.*)", $id = "(\d+)")
    {
        return "/admin/crud/delete/" . $class . "/" . $id;
    }

    public static function getSaveUrl($class = "(.*)", $id = "(\d+)")
    {
        return "/admin/crud/save/" . $class . "/" . $id;
    }

}