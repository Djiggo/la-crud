<?php

namespace LA\CRUD;


class Widgets
{

    public static function renderListFieldWithWidget($field_name, $obj)
    {

        $widget_name = self::getFieldWidgetName($field_name, $obj);
        $field_value = Helpers::getObjectFieldValue($obj, $field_name);

        $model_class_name = get_class($obj);

        /** @var array $crud_container_model */
        if (property_exists($model_class_name, 'crud_container_model') && array_key_exists($field_name, $model_class_name::$crud_container_model)) {
            $title = '';
            if ($field_value > 0) {
                $assoc_obj_class = $model_class_name::$crud_container_model[$field_name];

                $assoc_obj = Helpers::createAndLoadObject($assoc_obj_class, $field_value);
                $title = $assoc_obj->getTitle();
                $url = Helpers::getEditUrl($assoc_obj_class, $field_value);

                return $title . ' <small>(<a href="' . $url . '">' . $field_value . "</a>)</small>";

            }

            return $title . " <small>(" . $field_value . ")</small>";


        }

        $widget_options = self::getWidgetSettings($field_name, $obj);


        if (is_callable($widget_name . "::listWidget")) {

            return call_user_func_array($widget_name . "::listWidget", array($field_value, $widget_options));
        }

        switch ($widget_name) {
            case 'timestamp':
                $field_value = self::listWidgetTimestamp($field_value);
                break;
            case 'date':
                $field_value = self::listWidgetDate($field_value);
                break;
            case 'checkbox':
                $field_value = self::listWidgetCheckbox($field_value);
                break;
            case 'select':
                $field_value = self::listWidgetSelect($field_value, $widget_options);
                break;
            case 'password':
                return "";
                break;
        }
        return $field_value;

    }

    public static function renderFieldWithWidget($field_name, $obj)
    {
        $widget_name = self::getFieldWidgetName($field_name, $obj);

        $field_value = Helpers::getObjectFieldValue($obj, $field_name);

        $model_class_name = get_class($obj);

        if (Helpers::isReadOnlyField($model_class_name, $field_name)) {
            return "<div class='crud_readonly_widget'>" . self::renderListFieldWithWidget($field_name, $obj) . "</div>";
        }

        /** @var array $crud_container_model */
        if (property_exists($model_class_name, 'crud_container_model')) {
            if (array_key_exists($field_name, $model_class_name::$crud_container_model)) {
                return self::widgetContainerModel($field_name, $field_value, $model_class_name::$crud_container_model[$field_name]);
            }
        }
        $widget_options = self::getWidgetSettings($field_name, $obj);


        if (is_callable($widget_name . "::fieldWidget")) {

            return call_user_func_array($widget_name . "::fieldWidget", array($field_name, $field_value, $obj, $widget_options));
        }

        switch ($widget_name) {
            case 'timestamp':
                $o = self::widgetTimestamp($field_name, $field_value);
                break;
            case 'date':
                $o = self::widgetDate($field_name, $field_value);
                break;
            case 'slug':
                \Auto\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, 'Auto\Model\InterfaceTitle');
                $o = self::widgetSlug($field_name, $field_value);
                break;
            case 'html_editor':
                $o = self::widgetHtmlEditor($field_name, $field_value);
                break;
            case 'image':
                $upload_dir = '';
                if (is_array($widget_options) && count($widget_options) > 0) {
                    $upload_dir = $widget_options[0];
                    $save_only_filename = $widget_options[1];
                }
                $o = self::widgetImageUpload($field_name, $field_value, $upload_dir, $save_only_filename);
                break;
            case 'checkbox':
                $o = self::widgetCheckbox($field_name, $field_value);
                break;
            case 'select':
                $o = self::widgetSelect($field_name, $field_value, $widget_options, $model_class_name);
                break;
            case 'password':
                $o = self::widgetPassword($field_name, $field_value);
                break;
            default:
                $rows = 0;
                if (array_key_exists('rows', $widget_options)) {
                    $rows = $widget_options['rows'];
                }
                $o = self::widgetInput($field_name, $field_value, $rows);

        }

        return $o;

    }

    public static function renderFieldWithAdditionalWidget($widget_class_name, $obj)
    {

        if (is_callable($widget_class_name . "::fieldWidget")) {

            return call_user_func_array($widget_class_name . "::fieldWidget", array($obj));
        }
    }

    public static function getFieldWidgetName($field_name, $obj)
    {
        $crud_editor_fields_arr = Helpers::getCrudEditorFieldsArrForClass(get_class($obj));

        if (!$crud_editor_fields_arr) {
            return '';
        }

        if (!array_key_exists($field_name, $crud_editor_fields_arr)) {
            return '';
        }

        if (!array_key_exists('widget', $crud_editor_fields_arr[$field_name])) {
            return '';
        }

        return $crud_editor_fields_arr[$field_name]['widget'];

    }


    public static function getWidgetSettings($field_name, $obj)
    {
        $crud_editor_fields_arr = Helpers::getCrudEditorFieldsArrForClass(get_class($obj));

        if (!$crud_editor_fields_arr) {
            return array();
        }

        if (!array_key_exists($field_name, $crud_editor_fields_arr)) {
            return array();
        }

        if (!array_key_exists('widget_settings', $crud_editor_fields_arr[$field_name])) {
            return array();
        }

        return $crud_editor_fields_arr[$field_name]['widget_settings'];

    }

    public static function widgetContainerModel($field_name, $cur_obj_id, $model_class_name)
    {
        $objs_ids_arr = Helpers::getObjIdsArrayForModel($model_class_name);

        $options = '';

        if (!Helpers::isRequiredField($model_class_name, $field_name)) {
            $options .= '<option></option>';
        }

        foreach ($objs_ids_arr as $obj_id) {
            $edited_obj = Helpers::createAndLoadObject($model_class_name, $obj_id);
            $selected = '';
            if ($edited_obj->getId() == $cur_obj_id) {
                $selected = "selected";
            }
            $edited_obj_title = $edited_obj->getTitle();
            $options .= '<option ' . $selected . ' value="' . $edited_obj->getId() . '">' . $edited_obj_title . '</option>';
        }

        return '
            <select name="' . $field_name . '" class="form-control crud_container_model_widget" >
                ' . $options . '
            </select>
        <div><a href="' . Helpers::getEditUrl($model_class_name, $cur_obj_id) . '">Открыть</a></div>';
    }

    public static function widgetPreSaveAction($prop_value, $prop_name, $obj)
    {

        $widget_name = Widgets::getFieldWidgetName($prop_name, $obj);

        $widget_options = Widgets::getWidgetSettings($prop_name, $obj);

        if (is_callable($widget_name . "::saveWidget")) {

            return call_user_func_array($widget_name . "::saveWidget", array($prop_value, $obj, $widget_options));
        }


        switch ($widget_name) {
            case 'slug':
                \Auto\Model\Helper::exceptionIfClassNotImplementsInterface(get_class($obj), 'Auto\Model\InterfaceTitle');

                $prop_value = self::widgetSaveSlug($prop_value, $obj);
                break;
            case 'html_editor':
                $limit_img_max_width = true;
                if (array_key_exists('limit_img_max_width', $widget_options)) {
                    $limit_img_max_width = $widget_options['limit_img_max_width'];
                }
                $prop_value = self::widgetHtmlEditorSave($prop_value, $limit_img_max_width);
                break;


        }

        return $prop_value;

    }

    public static function widgetInput($field_name, $field_value, $rows)
    {
        $rows = $rows ? $rows : 1;
        return '<textarea name="' . $field_name . '" class="form-control" rows="' . $rows . '">' . $field_value . '</textarea>';

    }

    /**
     * @param $prop_value
     * @param $obj
     * @return mixed
     */
    public static function widgetSaveSlug($prop_value, $obj)
    {
        /** @var \Auto\Model\InterfaceTitle $obj */

        $obj_class = get_class($obj);


        $table = $obj_class::DB_TABLE_NAME;


        if ($prop_value != $obj->slug) {
            if (strlen($prop_value) > 0) {
                $prop_value = \Auto\Helper::generateSlugForTable($prop_value, $table);
            } else {
                $prop_value = \Auto\Helper::generateSlugForTable($obj->getTitle(), $table);
            }
        } elseif (strlen($prop_value) == 0) {
            $prop_value = \Auto\Helper::generateSlugForTable($obj->getTitle(), $table);
        }

        return $prop_value;
    }

    public static function widgetSlug($field_name, $field_value)
    {

        return '<textarea name="' . $field_name . '" class="form-control" rows="1" placeholder="Если оставить пустым, заполнится автоматически">' . $field_value . '</textarea>';

    }

    public static function widgetHtmlEditor($field_name, $field_value)
    {

        return '<textarea name="' . $field_name . '" class="form-control html_editor" >' . $field_value . '</textarea>';

    }

    /**
     * Форматирует html для отображения на сайте.
     * (Сжимает картинки, удаляет лишнее...)
     * @param string $html
     * @return string
     */
    public static function widgetHtmlEditorSave($html, $limit_img_max_width)
    {
        // Нужно отдавать сжатые картинки из кеша

        $max_img_width = \Auto\Constants::CONTENT_MAX_IMG_WIDTH;
        $tag_matches = [];
        preg_match_all("~<img[^>]+>~i", $html, $tag_matches);

        foreach ($tag_matches[0] as $tag_str) {
            $atrr_matches = [];
            $attrs = [];
            preg_match_all('~(width|height|src)="([^"]*)"~i', $tag_str, $atrr_matches);
            foreach ($atrr_matches[1] as $index => $attr_name) {
                $attrs[$attr_name] = $atrr_matches[2][$index];
            }

            if (strpos($attrs['src'], "cache/img") !== false) {
                continue; // Пропускаем кешированные картинки
            }

            if (strpos($attrs['src'], "http://") !== false) {
                continue; // Пропускаем картинки с полным url
            }

            if (end(explode(".", $attrs['src'])) == "gif") {
                continue; // Пропускаем gif анимации
            }

            if (array_key_exists('width', $attrs) and intval($attrs['width']) > 0) {

                $old_tag_str = $tag_str;

                // Картинки на всю ширину сайта и картинки шире сайта...
                if (($attrs['width'] == "100%" || intval($attrs['width']) > $max_img_width) && $limit_img_max_width) {
                    $new_src = \Core\ImageCache::getThumbUrl($attrs['src'], $max_img_width);
                    $tag_str = preg_replace('~width="([^"]*)"~i', 'width="' . $max_img_width . '"', $tag_str);
                    $tag_str = preg_replace('~height="([^"]*)"~i', '', $tag_str);
                } else if (array_key_exists('height', $attrs)) {
                    $new_src = \Core\ImageCache::getThumbUrl($attrs['src'], intval($attrs['width']), intval($attrs['height']));
                } else {
                    $new_src = \Core\ImageCache::getThumbUrl($attrs['src'], intval($attrs['width']));
                }

                $tag_str = preg_replace('~src="([^"]*)"~i', 'src="' . $new_src . '"', $tag_str);
                $html = str_replace($old_tag_str, $tag_str, $html);
            }

        }


        // Удаляем все font-family

        $html = preg_replace('/font-family: [^\'";]+;?/', '', $html);
        return $html;
    }

    public static function listWidgetTimestamp($field_value)
    {
        if ($field_value) {
            return \Auto\Helper::date("D, d M Y H:i:s", $field_value);
        }
        return '';
    }


    public static function widgetTimestamp($field_name, $field_value)
    {
        if (!$field_value) {
            $field_value = time();
        }
        $date = \Auto\Helper::date("Y-m-d H:i:s", $field_value);

        return '<input type="hidden"  name="' . $field_name . '" value="' . $field_value . '">
        <div class="crud_timestamp_widget input-group">
            <input readonly class="form-control" type="text" data-name="' . $field_name . '" value="' . $date . '">
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
        ';
    }


    public static function listWidgetDate($field_value)
    {
        if ($field_value) {
            return \Auto\Helper::date("d M Y", strtotime($field_value));
        }
        return '';
    }

    public static function widgetDate($field_name, $field_value)
    {
        return '<div class="crud_date_widget input-group">
            <input readonly class="form-control" type="text" name="' . $field_name . '" value="' . $field_value . '">
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
        ';
    }

    public static function listWidgetCheckbox($field_value)
    {
        if ($field_value) {
            return '<i class="glyphicon glyphicon-ok"></i>';
        }
        return '<i class="glyphicon glyphicon-remove"></i>';
    }

    public static function widgetImageUpload($field_name, $field_value, $upload_dir = '', $save_only_filename = '')
    {
        static $widget_on_page = 0;
        $widget_on_page++;

        $img_src = $field_value;

        if ($save_only_filename) {
            $img_src = '/files/' . $upload_dir . '/' . $field_value;
        }

        $str = '<div class="crud_image_widget">
                    <img src="' . $img_src . '" >
                    <input class="filemanager" type="hidden" name="' . $field_name . '" value="' . $field_value . '" id="crud_image_widget_' . $widget_on_page . '"
                    data-uploaddir="' . $upload_dir . '"
                    data-only_filename="' . $save_only_filename . '">
                    <a href="#" class="btn btn-default btn-xs">Изменить</a>
                </div>';

        return $str;
    }

    public static function widgetCheckbox($field_name, $field_value)
    {

        $checked_str = '';

        if ($field_value) {
            $checked_str = ' checked';
        }

        // после будет скрыто и попадет в POST только в том случае, если checkbox будет unchecked
        $hidden_field_for_unchecked_state = '<input type="hidden" name="' . $field_name . '" value="0">';

        $visible_checkbox = '<input type="checkbox" id="' . $field_name . '"
                               name="' . $field_name . '"
                               value="1"
                               ' . $checked_str . '>';

        return $hidden_field_for_unchecked_state . $visible_checkbox;

    }


    public static function listWidgetSelect($field_value, $widget_options)
    {
        if ($field_value) {
            return $widget_options[$field_value];
        }
        return '';
    }

    public static function widgetSelect($field_name, $field_value, $widget_options, $model_class_name)
    {

        $options = '';

        if (!Helpers::isRequiredField($model_class_name, $field_name)) {
            $options .= '<option></option>';
        }

        if (is_array($widget_options)) {
            foreach ($widget_options as $value => $name) {

                $selected = '';
                if ($field_value == $value) {
                    $selected = "selected";
                }
                $options .= '<option ' . $selected . ' value="' . $value . '">' . $name . '</option>';
            }
        }

        return '
        <select name="' . $field_name . '" class="form-control" >
            ' . $options . '
        </select>';


    }

    public static function widgetPassword($field_name, $field_value)
    {
        return '
        <input type="hidden" name="' . $field_name . '" value="' . $field_value . '" data-default="' . $field_value . '">
        <input type="text" class="form-control crud_md5_widget" data-name="' . $field_name . '">
        ';
    }

}
