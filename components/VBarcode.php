<?php

class VBarcode
{

    //Проверка свободного штрихкода******************************************************************************
    public static function Control($barcode)
    {
        $sqlArray =  "SELECT * FROM barcode WHERE ( name = '" . $barcode . "')";

        $findItem = Db::getSQLPackage($sqlArray);

        $findid['free'] = false;

        $findid['products'] = [];

        if (count($findItem) > 0) {
            $findid['products'] = $findItem;
        } else {
            $findid['free'] = true;
        };

        return $findid;
    }

    //Запись Нового штрихкода******************************************************************************
    public static function CreateUpdate($param)
    {
        if (isset($param['barcode']) && isset($param['products_id'])) {
            $barcode =  $param['barcode'];
            $products_id = $param['products_id'];
            $productsColor_id = null;
            $productsSize_id = null;

            if (isset($param['productsColor_id']) && $param['productsColor_id'] <> 0) {
                $productsColor_id =  $param['productsColor_id'];
            };
            if (isset($param['productsSize_id']) && $param['productsSize_id'] <> 0) {
                $productsSize_id =  $param['productsSize_id'];
            };

            $vp = [];
            $vp['name'] = $barcode;
            $vp['products_id'] = $products_id;
            $vp['productsColor_id'] = $productsColor_id;
            $vp['productsSize_id'] = $productsSize_id;

            $fi = self::Find($barcode);

            if (!$fi['errorScan']) {
                if ($fi['products_id'] <> $products_id || $fi['productsColor_id'] <> $productsColor_id || $fi['productsSize_id'] <> $productsSize_id) {
                    $findid = $fi['id'];
                    Db::update('barcode', $findid, $vp); //Если есть обновляем
                }
                //Db::log('обновлено'.$findid);
            } else {
                Db::create('barcode', $vp); //Если нет то создаЁм
                //Db::log('Добавлено'.$barcode);
            };
        };
    }

    //Поиск по любой части******************************************************************************
    public static function FindProducts($searchQuery)
    {
        //Db::log('Штрихкод: ' . $barcode);
        $words = preg_split('/\s+/', trim($searchQuery));

       

        $sqlArray =  "SELECT 
        
                      B.id as id, 
                      B.name as name, 

                      B.products_id as products_id,
                      P.name as productsname, 
        
                      B.productsColor_id as productsColor_id, 
                      PColor.name as colorname, 
        
                      B.productsSize_id as productsSize_id, 
                      PSize.name as sizename

                      FROM barcode B
                      LEFT JOIN products P ON B.products_id = P.id
                      LEFT JOIN productsColor PColor ON B.productsColor_id = PColor.id
                      LEFT JOIN productsSize PSize ON B.productsSize_id = PSize.id
                      WHERE ( B.name = " . $words . " )";

        $findItems = Db::getSQLPackage($sqlArray);

        

        return $findItems;
    }

    //Поиск по штрихкоду******************************************************************************
    public static function Find($barcode)
    {
        //Db::log('Штрихкод: ' . $barcode);

        $barcode = strval($barcode);

        $sqlArray =  "SELECT 
        
                      B.id as id, 
                      B.name as name, 

                      B.products_id as products_id,
                      P.name as productsname, 
        
                      B.productsColor_id as productsColor_id, 
                      PColor.name as colorname, 
        
                      B.productsSize_id as productsSize_id, 
                      PSize.name as sizename

                      FROM barcode B
                      LEFT JOIN products P ON B.products_id = P.id
                      LEFT JOIN productsColor PColor ON B.productsColor_id = PColor.id
                      LEFT JOIN productsSize PSize ON B.productsSize_id = PSize.id
                      WHERE ( B.name = " . $barcode . " )";

        $findItem = Db::getSQLPackage($sqlArray);

        $findid = [];

        if (count($findItem) > 0) {
            $findid = $findItem[0];
            $findid['errorScan'] = false;
        } else {
            $findid['errorScan'] = true;
        };

        return $findid;
    }
    //Обновление штрихкодов****************************************************************************
    public static function UpdateBarcode()
    {

        $sqlbarcode = "SELECT DISTINCT
            
                S.products_id as products_id, 
                S.barcode as barcode, 
                P.article as article, 
                P.name as productsname, 
        
                S.productsColor_id as productsColor_id, 
                PColor.name as colorname, 
        
                S.productsSize_id as productsSize_id, 
                PSize.name as sizename
                
                FROM stocktaking S
                LEFT JOIN products P ON S.products_id = P.id
                LEFT JOIN productsColor PColor ON S.productsColor_id = PColor.id
                LEFT JOIN productsSize PSize ON S.productsSize_id = PSize.id 

                ORDER BY productsname, colorname, sizename";

        //Выбрали все штрихкоды из инвентаризации
        $findBarcode = Db::getSQLPackage($sqlbarcode);
        if (count($findBarcode) > 0) {
            foreach ($findBarcode as $value) {
                $vp = [];
                $vp['name'] = $value['barcode'];
                $vp['products_id'] = $value['products_id'];
                $vp['productsColor_id'] = $value['productsColor_id'];
                $vp['productsSize_id'] = $value['productsSize_id'];

                $findItem = self::Find($value['barcode']);

                if ($findItem) {
                    $findid = $findItem['id'];
                    Db::update('barcode', $findid, $vp); //Если есть обновляем
                    //Db::log('обновлено'.$value['barcode']);
                } else {
                    Db::create('barcode', $vp); //Если нет то создаЁм
                    //Db::log('Добавлено'.$value['barcode']);
                };
            }
            $str = $vp;
        } else {
            $str = "Нет ничего";
        };

        return $str;
    }

    //все штрихкоды продукта****************************************************************************
    public static function ListBarcodeProducts($products_id)
    {

        $sqlbarcode = "
SELECT
    DISTINCT B.products_id as products_id,
    B.id as id,
    B.name as barcode,
    P.article as article,
    P.name as productsname,
    B.productsColor_id as productsColor_id,
    PColor.name as colorname,
    B.productsSize_id as productsSize_id,
    PSize.name as sizename
FROM
    barcode B
    LEFT JOIN products P ON B.products_id = P.id
    LEFT JOIN productsColor PColor ON B.productsColor_id = PColor.id
    LEFT JOIN productsSize PSize ON B.productsSize_id = PSize.id
WHERE
    (products_id = " . $products_id . ")
ORDER BY
    productsname,
    colorname,
    sizename
    ";

        //Выбрали все штрихкоды из товара
        $findBarcode = Db::getSQLPackage($sqlbarcode);
        if (count($findBarcode) > 0) {
            $findlist = $findBarcode;
        } else {
            $findlist = [];
        };

        return $findlist;
    }

    //картинка штрихкода******************************************************************************
}
