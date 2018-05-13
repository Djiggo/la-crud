<?php

namespace LA\CRUD;

/**
 * Класс, реализующий этот интерфейс, используется для фильтрации вывода на странице списка моделей.
 * Например, можно скрыть от редактора чужие новости.
 * Interface InterfaceFilter
 * @package LA\CRUD
 */
interface InterfaceListFilter
{
    /**
     * Метод должен вернуть массив,  вида ["user = ?" => 1]
     * @return array
     */
    public static function listFilter();

}
