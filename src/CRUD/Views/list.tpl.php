<?php
/**
 * @var $model_class_name
 */

\Auto\Helper::assert($model_class_name);


$filter = '';
if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];
}

$order = '';
if (isset($_GET['order'])) {
    $order = $_GET['order'];
}

$objs_ids_arr = \LA\CRUD\Helpers::getObjIdsArrayForModel($model_class_name, $filter, $order);
$total_obj_count = count($objs_ids_arr);

$page = 1;
if (array_key_exists('page', $_GET) && $_GET['page'] > 1) {
    $page = (int)$_GET['page'];
}

$per_page = \Admin\CRUD\Constants::PAGINATE_PER_PAGE;

$objs_ids_arr = array_slice($objs_ids_arr, $page * $per_page - $per_page, $per_page);


$reflect = new \ReflectionClass($model_class_name);
$props_arr = array();

$crud_table_fields_arr = array();

foreach ($reflect->getProperties() as $prop_obj) {
    if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
        $prop_obj->setAccessible(true);
        $props_arr[] = $prop_obj;
    }
}

$filter_props_arr = $props_arr;

if (property_exists($model_class_name, 'crud_table_fields_arr') && (count($model_class_name::$crud_table_fields_arr) > 0)) {
    foreach ($props_arr as $delta => $property_obj) {
        if (!in_array($property_obj->getName(), $model_class_name::$crud_table_fields_arr)) {
            unset($props_arr[$delta]);
        }
    }
}
$add_url = \LA\CRUD\Helpers::getAddUrl($model_class_name);

?>

<h1>
    <?= \LA\CRUD\Helpers::getTitleForListForModelClassName($model_class_name); ?>
    <a class="btn btn-default btn-xs" href="<?= $add_url ?>">Добавить</a>
</h1>
<div class="box">
    <div class="box-header with-border">
        <div class="box-title">
            <div class=" crud-filters">
                <form class="form-inline">
                    <div class="form-group ">
                        <?php
                        $value = '';
                        if (array_key_exists('filter', $_GET) && array_key_exists('search', $_GET['filter'])) {
                            $value = $_GET['filter']['search']['value'];
                        }
                        ?>
                        <input type="text" class="input-sm form-control" value="<?= $value ?>" placeholder="Найти..."
                               name="filter[search][value]">
                    </div>
                    <div class="form-group ">
                        <select class="form-control input-sm" name="filter[search][field]">
                            <option disabled selected>в поле</option>
                            <?php foreach ($filter_props_arr as $prop_obj) {
                                $table_title = \LA\CRUD\Helpers::getTitleForField($model_class_name, $prop_obj->getName());
                                $selected = '';
                                if (isset($_REQUEST['filter']['search'])) {
                                    if ($_REQUEST['filter']['search']['field'] == $prop_obj->name) {
                                        $selected = 'selected="selected"';
                                    }
                                }
                                ?>
                                <option <?= $selected ?> value="<?= $prop_obj->name ?>"><?= $table_title ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group ">
                        <button class="btn btn-default btn-xs">Найти</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="box-body">
        <form method="POST">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-condensed">
                    <tbody>
                    <tr>
                        <th></th>

                        <?php

                        $list_url = \LA\CRUD\Helpers::getListUrl($model_class_name);

                        foreach ($props_arr as $prop_obj) {
                            $table_title = \LA\CRUD\Helpers::getTitleForField($model_class_name, $prop_obj->getName());

                            $get_params = $_GET;
                            $order_direction_i = "";
                            $new_order_direction = "DESC";

                            if (array_key_exists('order', $get_params)) {
                                if ($get_params['order']['field'] == $prop_obj->getName()) {
                                    $order_direction_i = "<i class='glyphicon glyphicon-arrow-down'></i>";
                                    if ($get_params['order']['dir'] == "DESC") {
                                        $order_direction_i = "<i class='glyphicon glyphicon-arrow-up'></i>";
                                        $new_order_direction = "ASC";
                                    }
                                }
                            }

                            $get_params['order']['field'] = $prop_obj->getName();

                            $get_params['order']['dir'] = $new_order_direction;

                            ?>
                            <th>
                                <a href="<?= $list_url . "?" . http_build_query($get_params) ?>"><?= $table_title ?><?= $order_direction_i ?></a>
                            </th>
                            <?php
                        }
                        ?>
                        <th></th>
                    </tr>
                    <?php
                    foreach ($objs_ids_arr as $obj_id) {
                        $obj_obj = \LA\CRUD\Helpers::createAndLoadObject($model_class_name, $obj_id);

                        $show_edit_button = true;

                        ?>
                        <tr>
                            <td>
                                <?php

                                $delete_disabled = false;
                                $model_class_interfaces_arr = class_implements($model_class_name);
                                if (!array_key_exists('Auto\Model\InterfaceRemove', $model_class_interfaces_arr)) {
                                    $delete_disabled = true;
                                }

                                if (!$delete_disabled) {
                                    ?>
                                    <input type="checkbox" name="delete[]" value="<?= $obj_id ?>">
                                    <?php
                                }
                                ?>
                            </td>

                            <?php
                            foreach ($props_arr as $prop_obj) {

                                ?>
                                <td>
                                    <?= \LA\CRUD\Widgets::renderListFieldWithWidget($prop_obj->getName(), $obj_obj); ?>
                                </td>
                                <?php
                            }

                            $edit_url = \LA\CRUD\Helpers::getEditUrl($model_class_name, $obj_id);
                            ?>
                            <td style="text-align: right;">
                                <?php
                                if ($show_edit_button) {
                                    echo '<a class="glyphicon glyphicon-edit" href="' . $edit_url . '"></a> ';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div>
                <button class="btn btn-default btn-xs">Удалить выбранные</button>
            </div>
        </form>
    </div>
    <div class="box-footer">

        <?php Admin\AdminHelper::paginateLinks($total_obj_count, $per_page); ?>
    </div>
</div>
