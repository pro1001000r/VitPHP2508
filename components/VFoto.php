<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VFoto
 *
 * @author MSI
 */
class VFoto
{

    //Функция загрузки файла фото на сервер****************************************************************************
    public static function getImage($table, $id)
    {

        $sql = "SELECT * FROM fotos WHERE (tablename='" . $table . "' AND tableid=" . $id . ")";
        $listFoto = Db::getSQL($sql);
        if (count($listFoto) > 0) {
            $foto = $listFoto;
        } else {
            $foto = [];
        };

        return $foto;
    }

    //Функция загрузки файла фото на сервер****************************************************************************
    public static function setImage($tableName, $id, $fileFoto = '', $isOldFoto = false, $i = 0)
    {

        if (empty($fileFoto)) {
            $fileFoto = $_FILES["fotopath"];
        }
        //debug($fileFoto);

        //Проверка на безопасность
        $blacklist = array(".php", ".phtml", ".php3", ".php4", ".html", ".htm");
        foreach ($blacklist as $item) {
            if (preg_match("/$item\$/i", $fileFoto['name'])) {
                return '';
            }
        };

        //Определяем тип фото
        $typeImg = $fileFoto["type"];

        switch ($typeImg) {
            case "image/jpg":
                $typeFile = "jpg";
                break;
            case "image/jpeg":
                $typeFile = "jpeg";
                break;
            case "image/png":
                $typeFile = "png";
                break;
            case "image/bmp":
                $typeFile = "bmp";
                break;
            default:
                return '';
                break;
        }
        //Удаляем старое фото
        if ($isOldFoto) {
            self::deleteImage($tableName, $id);
        }

        //Генерируем новое имя файла фото
        $nameF = VFunc::ImgNameGenereteDate($tableName . "_" . $id);
        $nameImg = "/upload/images/" . $nameF . '_' . $i . '.' . $typeFile;
        $nameImgFull = "/upload/images/" . $nameF . '_' . $i . 'Full.' . $typeFile;

        $pathFoto = ROOT . $nameImg;
        $pathFullFoto = ROOT . $nameImgFull;

        //Сохраняем оригинал
        move_uploaded_file($fileFoto["tmp_name"], $pathFullFoto);

        //Редактируем файл и сохраняем его
        VFoto::editImageSize($pathFullFoto, $pathFoto);

        $arr[] = $nameImg;
        $arr[] = $nameImgFull;
        return $arr;
    }

    // // генерирует новое имя для фото
    // public static function ImgNameGenereteDate($nameTableId = '') {
    //     $nameDate = date('Y-m-d h:m:s:v');
    //     $nameDate = str_replace(" ", "", $nameDate);
    //     $nameDate = str_replace("-", "", $nameDate);
    //     if (!empty($nameTableId)) {
    //         $nameTableId = '_' . $nameTableId;
    //     }
    //     $nameImg = "img" . $nameTableId . "_" . str_replace(":", "", $nameDate);

    //     $pathFoto = $nameImg;

    //     return $nameImg;
    // }

    //2025 Удаляет запись из fotos и фото привязанные к ней
    public static function deleteImageById($id)
    {

        $tableName = 'fotos';

        $item = Db::getById($tableName, $id);
        if (isset($item)) {
            // * Удаляем сами фото                
            $foto = $item['foto'];
            if (!empty($foto)) {
                $foto = ROOT . $foto;
                if (file_exists($foto)) {
                    unlink($foto);
                }
            }
            $foto64 = $item['foto64'];
            if (!empty($foto64)) {
                $foto64 = ROOT . $foto64;
                if (file_exists($foto64)) {
                    unlink($foto64);
                }
            }
            // * Удаляем запись 
            Db::delete($tableName, $id);
        }
    }

    // нужен рефакторинг!!! Удаляет фото привязанные к данному элементу
    public static function deleteImage($tableName, $id)
    {
        $sql = "SELECT id FROM fotos WHERE (tableid = " . $id . " AND tablename = '" . $tableName . "')";
        $listid = Db::getSQL($sql);
        foreach ($listid as $value) {
            self::deleteImageById($value['id']);
        }
    }

    public static function editImageSize($pathFotoIn, $pathFotoOut = '')
    {
        if (empty($pathFotoOut)) {
            $pathFotoOut = $pathFotoIn;
        };
        $image = new thumbs($pathFotoIn);
        $width = $image->width;
        $height = $image->height;
        //        if ($width > $height){
        //           $image->resizeCanvas($width, $width*1.5, array(255, 255, 255));
        //           $image->rotateRight();
        //        } 

        $image->thumb(640, 480);
        $image->save($pathFotoOut);
    }

    public static function editImageRotateRight($pathFotoIn, $pathFotoOut = '')
    {
        if (empty($pathFotoOut)) {
            $pathFotoOut = $pathFotoIn;
        };
        //Db::log($pathFotoOut);
        $image = new thumbs($pathFotoIn);
        $width = $image->width;
        $height = $image->height;
        $heightNew = $width * 1.333333333;
        $image->resizeCanvas($width, (int)$heightNew, array(255, 255, 255));
        $image->rotateRight();
        $image->thumb(640, 480);
        $image->save($pathFotoOut);
    }

    public static function editImageRotateLeft($pathFotoIn, $pathFotoOut = '')
    {
        if (empty($pathFotoOut)) {
            $pathFotoOut = $pathFotoIn;
        };
        //Db::log($pathFotoOut);
        $image = new thumbs($pathFotoIn);
        $width = $image->width;
        $height = $image->height;
        $heightNew = $width * 1.333333333;
        $image->resizeCanvas($width, (int)$heightNew, array(255, 255, 255));
        $image->rotateLeft();
        $image->thumb(640, 480);
        $image->save($pathFotoOut);
    }
}
