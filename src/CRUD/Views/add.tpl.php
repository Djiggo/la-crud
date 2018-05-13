<?php
/**
 * @var $model_class_name
 */

\Auto\Helper::assert($model_class_name);


$edited_obj = new $model_class_name();

$reflect = new \ReflectionClass($model_class_name);

$props_arr = array();

foreach ($reflect->getProperties() as $prop_obj) {
    if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
        $prop_obj->setAccessible(true);
        $props_arr[] = $prop_obj;
    }
}

$crud_editor_fields_arr = \LA\CRUD\Helpers::getCrudEditorFieldsArrForClass($model_class_name);
if ($crud_editor_fields_arr) {
    foreach ($props_arr as $delta => $property_obj) {
        if (!array_key_exists($property_obj->getName(), $crud_editor_fields_arr)) {
            unset($props_arr[$delta]);
        }
    }
}

if ($edited_obj instanceof \Auto\Model\InterfaceSave) {
    ?>
    <h1>Добавить
        <small>
            <?= \LA\CRUD\Helpers::getTitleForListForModelClassName($model_class_name); ?>
        </small>
    </h1>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"></h3>
        </div>
        <form id="form" method="post" class="form-horizontal"
              action="<?= \LA\CRUD\Helpers::getCreateItemUrl($model_class_name); ?>">
            <div class="box-body">

                <?php
                foreach ($props_arr as $prop_obj) {
                    $editor_title = \Admin\CRUD\Helpers::getTitleForField($model_class_name, $prop_obj->getName());

                    $required = '';

                    if (\LA\CRUD\Helpers::isRequiredField($model_class_name, $prop_obj->getName())) {
                        $required = 'required';
                    }

                    ?>
                    <div class="form-group <?= $required ?>">
                        <label for="<?php echo $prop_obj->getName() ?>"
                               class="col-sm-3 text-right control-label"><?= $editor_title ?></label>

                        <div class="col-sm-9 crud-widget">
                            <?= \LA\CRUD\Widgets::renderFieldWithWidget($prop_obj->getName(), $edited_obj); ?>
                        </div>
                    </div>
                    <?php
                }

                if (property_exists($model_class_name, 'crud_additional_widgets')) {

                    \Auto\Helper::assert(is_array($model_class_name::$crud_additional_widgets));

                    foreach ($model_class_name::$crud_additional_widgets as $widget_title => $widget_class_name) {
                        if (is_numeric($widget_title)) {
                            $widget_title = '';
                        }
                        ?>
                        <div class="form-group">
                            <label class="col-sm-3 text-right control-label"><?= $widget_title ?></label>

                            <div class="col-sm-9">
                                <?= \LA\CRUD\Widgets::renderFieldWithAdditionalWidget($widget_class_name, $edited_obj); ?>
                            </div>
                        </div>

                        <?php
                    }
                }

                ?>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </form>
    </div>
    <?php
}
