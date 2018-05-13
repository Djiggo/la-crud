<?php
/**
 * Created by PhpStorm.
 * User: lev
 * Date: 25.03.15
 * Time: 12:57
 */

namespace LA\CRUD;


class CrudController
{


    public function listAction($model_class_name)
    {

        $model_class_name = str_replace(".", "\\", $model_class_name);

        \Auto\Helper::assert($model_class_name);

        if (array_key_exists('delete', $_POST) && is_array($_POST['delete'])) {
            foreach ($_POST['delete'] as $obj_id) {
                $obj_obj = \LA\CRUD\Helpers::createAndLoadObject($model_class_name, $obj_id);
                \Auto\Helper::assert($obj_obj);
                $obj_obj->remove();
            }
        }

        $layout_content = \Core\View::renderLocaltemplate("Views/list.tpl.php", array('model_class_name' => $model_class_name));
        echo \Core\View::render('/templates/admin_layout.tpl.php', array('layout_content' => $layout_content));

    }


    public function deleteAction($model_class_name, $obj_id)
    {

        $model_class_name = str_replace(".", "\\", $model_class_name);

        \Auto\Helper::assert($model_class_name);

        $obj_obj = \LA\CRUD\Helpers::createAndLoadObject($model_class_name, $obj_id);
        \Auto\Helper::assert($obj_obj);
        $obj_obj->remove();

        \Auto\Helper::redirect(Helpers::getListUrl($model_class_name));

    }

    public function editAction($model_class_name, $obj_id)
    {

        $model_class_name = str_replace(".", "\\", $model_class_name);

        \Auto\Helper::assert($model_class_name);
        \Auto\Helper::assert($obj_id);


        \Auto\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, \Auto\Model\InterfaceLoad::class);


        $layout_content = \Core\View::renderLocaltemplate("Views/edit.tpl.php", array('model_class_name' => $model_class_name, 'obj_id' => $obj_id));
        echo \Core\View::render('/templates/admin_layout.tpl.php', array('layout_content' => $layout_content));


    }


    public function addAction($model_class_name)
    {

        $model_class_name = str_replace(".", "\\", $model_class_name);

        \Auto\Helper::assert($model_class_name);


        $layout_content = \Core\View::renderLocaltemplate("Views/add.tpl.php", array('model_class_name' => $model_class_name));
        echo \Core\View::render('/templates/admin_layout.tpl.php', array('layout_content' => $layout_content));


    }

    public function createAction($model_class_name)
    {

        $model_class_name = str_replace(".", "\\", $model_class_name);

        \Auto\Helper::assert($model_class_name);

        \Auto\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, \Auto\Model\InterfaceSave::class);

        $obj = new $model_class_name;


        $reflect = new \ReflectionClass($model_class_name);

        foreach ($reflect->getProperties() as $prop_obj) {
            if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
                $prop_name = $prop_obj->getName();

                if (array_key_exists($prop_name, $_POST)) {
                    if (($_POST[$prop_name] == '') && \LA\CRUD\Helpers::isRequiredField($model_class_name, $prop_obj->getName())) {
                        throw new \Exception('поле ' . $prop_obj->getName() . ' обязательно для заполнения');
                    }

                    $prop_value = $_POST[$prop_name];

                    $prop_value = \LA\CRUD\Widgets::widgetPreSaveAction($prop_value, $prop_name, $obj);

                    $prop_obj->setAccessible(true);
                    $prop_obj->setValue($obj, $prop_value);
                }
            }
        }

        // Сохраняем, чтобы у объекта появился ID и сбросился кеш
        $obj->save();

        if (property_exists($model_class_name, 'crud_additional_widgets')) {

            \Auto\Helper::assert(is_array($model_class_name::$crud_additional_widgets));

            foreach ($model_class_name::$crud_additional_widgets as $widget_title => $widget_class_name) {
                if (is_callable($widget_class_name . "::saveWidget")) {
                    call_user_func_array($widget_class_name . "::saveWidget", array($obj));
                }
            }
        }

        $redirect_url = Helpers::getEditUrl($model_class_name, $obj->getId());

        if (array_key_exists('destination', $_POST)) {
            $redirect_url = $_POST['destination'];
        }

        \Auto\Helper::redirect($redirect_url);
    }

    public function saveAction($model_class_name, $obj_id)
    {

        $model_class_name = str_replace(".", "\\", $model_class_name);

        \Auto\Helper::assert($model_class_name);
        \Auto\Helper::assert($obj_id);

        \Auto\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, \Auto\Model\InterfaceLoad::class);
        \Auto\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, \Auto\Model\InterfaceSave::class);

        $obj = Helpers::createAndLoadObject($model_class_name, $obj_id);


        $reflect = new \ReflectionClass($model_class_name);

        foreach ($reflect->getProperties() as $prop_obj) {
            if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
                $prop_name = $prop_obj->getName();

                if (array_key_exists($prop_name, $_POST)) {
                    if (($_POST[$prop_name] == '') && Helpers::isRequiredField($model_class_name, $prop_obj->getName())) {
                        throw new \Exception('поле ' . $prop_obj->getName() . ' обязательно для заполнения');
                    }

                    $prop_value = $_POST[$prop_name];

                    $prop_value = Widgets::widgetPreSaveAction($prop_value, $prop_name, $obj);
                    $prop_obj->setAccessible(true);
                    $prop_obj->setValue($obj, $prop_value);
                }
            }
        }

        $obj->save();


        if (property_exists($model_class_name, 'crud_additional_widgets')) {

            \Auto\Helper::assert(is_array($model_class_name::$crud_additional_widgets));

            foreach ($model_class_name::$crud_additional_widgets as $widget_title => $widget_class_name) {
                if (is_callable($widget_class_name . "::saveWidget")) {
                    call_user_func_array($widget_class_name . "::saveWidget", array($obj));
                }
            }
        }


        $redirect_url = Helpers::getEditUrl($model_class_name, $obj_id);

        if (array_key_exists('destination', $_POST)) {
            $redirect_url = $_POST['destination'];
        }

        \Auto\Helper::redirect($redirect_url);
    }
}