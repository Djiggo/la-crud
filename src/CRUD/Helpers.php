<?php
/**
 * Created by PhpStorm.
 * User: lev
 * Date: 19.06.15
 * Time: 13:16
 */

namespace LA\CRUD;


class Helpers
{

    /**
     * @param $model_class_name
     * @param array $filter
     * @return array
     * @throws \Exception
     */
    static public function getObjIdsArrayForModel($model_class_name, $filters = array(), $order = array())
    {

        $db_table_name = $model_class_name::DB_TABLE_NAME;

        $db_id = \Auto\Constants::DB_NAME_AUTO;

        if ($model_class_name::DB_ID) {
            $db_id = $model_class_name::DB_ID;
        }

        $db_id_field_name = self::getIdFieldName($model_class_name);

        // selecting ids by params from context
        $query_param_values_arr = array();

        $where = ' 1 = 1 ';

        if ($filters) {
            foreach ($filters as $filter) {
                if (!strlen($filter['field']) || !strlen($filter['value'])) {
                    continue;
                }


                if (isset($model_class_name::$crud_container_model) && array_key_exists($filter['field'], $model_class_name::$crud_container_model)) {

                    $where .= " AND " . $filter['field'] . " = ? ";
                    $query_param_values_arr[] = $filter['value'];
                } else if ($filter['field'] == 'id') {
                    $where .= " AND " . $filter['field'] . " = ? ";
                    $query_param_values_arr[] = $filter['value'];
                } else {
                    $where .= " AND " . $filter['field'] . " LIKE ? ";
                    $query_param_values_arr[] = "%" . $filter['value'] . "%";
                }
            }
        }

        $additional_filters_arr_arr = self::getModelAdditionalFilters($model_class_name);

        if ($additional_filters_arr_arr) {
            foreach ($additional_filters_arr_arr as $filter_arr) {
                if (!$filter_arr) {
                    continue;
                }
                foreach ($filter_arr as $condition => $val)
                    $where .= " AND " . $condition;
                $query_param_values_arr[] = $val;

            }
        }

        $order_by = $db_id_field_name . " DESC";

        if ($order) {
            if (strlen($order['field'])) {
                $order_direction = "DESC";

                if (array_key_exists('dir', $order)) {
                    if ($order['dir'] == "ASC") {
                        $order_direction = "ASC";
                    }
                }

                $order_by = $order['field'] . " " . $order_direction;
            }
        }

        $sql = "SELECT $db_id_field_name FROM " . $db_table_name . ' WHERE ' . $where . ' ORDER BY ' . $order_by;
        $objs_ids_arr = \Core\DB\DBWrapper::getColomn($db_id, $sql, $query_param_values_arr);

        return $objs_ids_arr;

    }


    /**
     * @return string
     */
    public static function getIdFieldName($model_class_name)
    {
        if (defined($model_class_name . '::DB_ID_FIELD_NAME')) {
            return $model_class_name::DB_ID_FIELD_NAME;
        } else {
            return 'id';
        }
    }

    /**
     * @return string
     */
    public static function getTitleForModelClassName($model_class_name)
    {

        if (property_exists($model_class_name, 'crud_model_class_screen_name')) {
            return $model_class_name::$crud_model_class_screen_name;
        } else {
            $path_arr = explode("\\", $model_class_name);
            return end($path_arr);
        }
    }

    /**
     * @return string
     */
    public static function getTitleForListForModelClassName($model_class_name)
    {
        if (property_exists($model_class_name, 'crud_model_class_screen_name_for_list')) {
            return $model_class_name::$crud_model_class_screen_name_for_list;
        } else {
            $path_arr = explode("\\", $model_class_name);
            return end($path_arr);
        }
    }

    public static function getTitleForField($model_class_name, $field_name)
    {
        $title = $field_name;

        if (property_exists($model_class_name, 'crud_field_titles_arr')) {
            $crud_field_titles_arr = $model_class_name::$crud_field_titles_arr;
            if (array_key_exists($field_name, $crud_field_titles_arr)) {
                $title = $crud_field_titles_arr[$field_name];
            }
        }

        return $title;
    }

    public static function createAndLoadObject($model_class_name, $obj_id)
    {

        \Auto\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, 'Auto\Model\InterfaceLoad');

        $obj = new $model_class_name;
        \Auto\Helper::assert($obj->load($obj_id));

        return $obj;
    }

    public static function getAddUrl($model_class_name)
    {
        $model_class_name = str_replace("\\", ".", $model_class_name);
        return '/admin/crud/add/' . urlencode($model_class_name);
    }

    public static function getListUrl($model_class_name)
    {
        $model_class_name = str_replace("\\", ".", $model_class_name);
        return '/admin/crud/list/' . urlencode($model_class_name);
    }

    public static function getEditUrl($model_class_name, $obj_id)
    {
        $model_class_name = str_replace("\\", ".", $model_class_name);
        return '/admin/crud/edit/' . urlencode($model_class_name) . '/' . $obj_id;
    }

    public static function getDeleteUrl($model_class_name, $obj_id)
    {
        $model_class_name = str_replace("\\", ".", $model_class_name);
        return '/admin/crud/delete/' . urlencode($model_class_name) . '/' . $obj_id;
    }

    public static function getSaveUrl($model_class_name, $obj_id)
    {
        $model_class_name = str_replace("\\", ".", $model_class_name);
        return '/admin/crud/save/' . urlencode($model_class_name) . '/' . $obj_id;
    }

    public static function getCreateItemUrl($model_class_name)
    {
        $model_class_name = str_replace("\\", ".", $model_class_name);
        return '/admin/crud/create/' . urlencode($model_class_name);
    }

    static public function getCrudEditorFieldsArrForClass($model_class_name)
    {
        $rc = new \ReflectionClass($model_class_name);

        if ($rc->hasMethod('crud_editorFieldsArr')) {
            return $model_class_name::crud_editorFieldsArr();
        }

        if (property_exists($model_class_name, 'crud_editor_fields_arr')) {
            return $model_class_name::$crud_editor_fields_arr;
        }


        return null;
    }

    public static function getObjectFieldValue($obj, $field_name)
    {
        $obj_class_name = get_class($obj);

        $reflect = new \ReflectionClass($obj_class_name);
        $field_prop_obj = null;

        foreach ($reflect->getProperties() as $prop_obj) {
            if ($prop_obj->getName() == $field_name) {
                $field_prop_obj = $prop_obj;
            }
        }

        \Auto\Helper::assert($field_prop_obj);

        $field_prop_obj->setAccessible(true);
        return $field_prop_obj->getValue($obj);
    }

    static public function isRequiredField($model_class_name, $field_name)
    {
        $required = false;

        $crud_editor_fields_arr = self::getCrudEditorFieldsArrForClass($model_class_name);
        if ($crud_editor_fields_arr) {
            if ((array_key_exists($field_name, $crud_editor_fields_arr)) && (array_key_exists('required', $crud_editor_fields_arr[$field_name]))) {
                $required = $crud_editor_fields_arr[$field_name]['required'];
            }
        }

        return $required;
    }

    static public function isReadOnlyField($model_class_name, $field_name)
    {
        $read_only = false;

        $crud_editor_fields_arr = self::getCrudEditorFieldsArrForClass($model_class_name);
        if ($crud_editor_fields_arr) {
            if ((array_key_exists($field_name, $crud_editor_fields_arr)) && (array_key_exists('read_only', $crud_editor_fields_arr[$field_name]))) {
                $read_only = $crud_editor_fields_arr[$field_name]['read_only'];
            }
        }

        return $read_only;
    }

    static public function getModelAdditionalFilters($model_class_name)
    {

        $all_filters = array();

        if (property_exists($model_class_name, 'crud_list_filters')) {
            foreach ($model_class_name::$crud_list_filters as $filter_class) {
                /** @var \LA\CRUD\InterfaceListFilter $filter_class */
                $all_filters[] = $filter_class::listFilter();
            }
        }

        return $all_filters;
    }
}