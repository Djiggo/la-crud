<?php

namespace LA\CRUD;

/**
 * Interface InterfaceWidget
 * @package LA\CRUD
 */
interface InterfaceWidget
{
    /**
     * Рендерит поле на странице редактрирования
     * @param $field_name string Название поля
     * @param $field_value mixed Значение поля
     * @param $obj mixed Редактируемый объект
     * @param $widget_options array Дополнительные параметры
     * @return string
     */
    public static function fieldWidget($field_name, $field_value, $obj, $widget_options);


    /**
     * Обрабатывает результат формы перед сохранением
     * @param $field_value mixed
     * @return mixed
     */
    public static function saveWidget($field_value, $obj);


    /**
     * Выводит значение на странице списка моделей
     * @param $field_value mixed
     * @return mixed
     */
    public static function listWidget($field_value);
}
