<?php

namespace LA\CRUD;

/**
 * Interface InterfaceWidget
 * @package LA\CRUD
 */
interface InterfaceAdditionalWidget
{
    /**
     * Рендерит поле на странице редактрирования
     * @param $obj mixed Редактируемый объект
     * @param null $settings
     * @return string
     */
    public static function fieldWidget($obj);


    /**
     * Обрабатывает результат формы перед сохранением
     * @param $obj
     * @return mixed
     * @internal param mixed $field_value
     */
    public static function saveWidget($obj);

}
