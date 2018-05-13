<?php
include('config/config.php');
if ($_SESSION['RF']["verify"] != "RESPONSIVEfilemanager") die('forbiden');
include('include/utils.php');


$storeFolder = $_POST['path'];
$storeFolderThumb = $_POST['path_thumb'];

$path_pos = strpos($storeFolder, $current_path);
$thumb_pos = strpos($_POST['path_thumb'], $thumbs_base_path);
if ($path_pos !== 0
    || $thumb_pos !== 0
    || strpos($storeFolderThumb, '../', strlen($thumbs_base_path)) !== FALSE
    || strpos($storeFolderThumb, './', strlen($thumbs_base_path)) !== FALSE
    || strpos($storeFolder, '../', strlen($current_path)) !== FALSE
    || strpos($storeFolder, './', strlen($current_path)) !== FALSE
)
    die('wrong path');


$path = $storeFolder;
$cycle = true;
$max_cycles = 50;
$i = 0;
while ($cycle && $i < $max_cycles) {
    $i++;
    if ($path == $current_path) $cycle = false;
    if (file_exists($path . "config.php")) {
        require_once($path . "config.php");
        $cycle = false;
    }
    $path = fix_dirname($path) . '/';
}

if (isset($_POST) && count($_POST['files']) == 0) {
    header('HTTP/1.1 405 Bad Request', true, 405);
    exit();
}

$uploaded = 0;
foreach ($_POST['files'] as $file_info) {

    if (strlen($file_info['url']) < 5) {
        continue;
    }

    $targetPath = $storeFolder;
    $targetPathThumb = $storeFolderThumb;
    $file_info['name'] = fix_filename($file_info['name'], $transliteration, $convert_spaces);

    $info = pathinfo($file_info['name']);

    if (file_exists($targetPath . $file_info['name'])) {
        $i = 1;
        $info = pathinfo($file_info['name']);
        while (file_exists($targetPath . $info['filename'] . "_" . $i . "." . $info['extension'])) {
            $i++;
        }
        $file_info['name'] = $info['filename'] . "_" . $i . "." . $info['extension'];
    }

    $targetFile = $targetPath . $file_info['name'];
    $targetFileThumb = $targetPathThumb . $file_info['name'];


    $content = file_get_contents($file_info['url']);

    file_put_contents($targetFile, $content);
    chmod($targetFile, 0755);

    if (in_array(fix_strtolower($info['extension']), $ext_img)) $is_img = true;
    else $is_img = false;

    if ($is_img) {
        $memory_error = false;
        if (!create_img_gd($targetFile, $targetFileThumb, 122, 91)) {
            $memory_error = false;
        } else {
            if (!new_thumbnails_creation($targetPath, $targetFile, $file_info['name'], $current_path, $relative_image_creation, $relative_path_from_current_pos, $relative_image_creation_name_to_prepend, $relative_image_creation_name_to_append, $relative_image_creation_width, $relative_image_creation_height, $relative_image_creation_option, $fixed_image_creation, $fixed_path_from_filemanager, $fixed_image_creation_name_to_prepend, $fixed_image_creation_to_append, $fixed_image_creation_width, $fixed_image_creation_height, $fixed_image_creation_option)) {
                $memory_error = false;
            } else {
                $imginfo = getimagesize($targetFile);
                $srcWidth = $imginfo[0];
                $srcHeight = $imginfo[1];

                if ($image_resizing) {
                    if ($image_resizing_width == 0) {
                        if ($image_resizing_height == 0) {
                            $image_resizing_width = $srcWidth;
                            $image_resizing_height = $srcHeight;
                        } else {
                            $image_resizing_width = $image_resizing_height * $srcWidth / $srcHeight;
                        }
                    } elseif ($image_resizing_height == 0) {
                        $image_resizing_height = $image_resizing_width * $srcHeight / $srcWidth;
                    }
                    $srcWidth = $image_resizing_width;
                    $srcHeight = $image_resizing_height;
                    create_img_gd($targetFile, $targetFile, $image_resizing_width, $image_resizing_height);
                }
                //max resizing limit control
                $resize = false;
                if ($image_max_width != 0 && $srcWidth > $image_max_width) {
                    $resize = true;
                    $srcHeight = $image_max_width * $srcHeight / $srcWidth;
                    $srcWidth = $image_max_width;
                }
                if ($image_max_height != 0 && $srcHeight > $image_max_height) {
                    $resize = true;
                    $srcWidth = $image_max_height * $srcWidth / $srcHeight;
                    $srcHeight = $image_max_height;
                }
                if ($resize)
                    create_img_gd($targetFile, $targetFile, $srcWidth, $srcHeight);
            }
        }
        if ($memory_error) {
            //error
            unlink($targetFile);
            header('HTTP/1.1 406 Not enought Memory', true, 406);
            exit();
        }
    }
    $uploaded++;
}

if ($uploaded > 0) {
    echo "ok";
}
?>