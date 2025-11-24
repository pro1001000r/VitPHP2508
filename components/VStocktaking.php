<?php

class VStocktaking
{

    //Создание записи в инвентаризации ******************************************************************************
    public static function CreateUpdate($param)
    {
        if (!empty($param['id'])) {

            // Если есть id

            $sqlArray[] =  "SELECT * FROM  stocktaking 
                    WHERE (stocktaking.id = " . $param['id'] . ")";
            $nomeninv = Db::getSQLPackage($sqlArray);
        } else {

            //здесь ищем запись

            $sqlArray[] =  "SELECT * FROM  stocktaking 
                    WHERE (stocktaking.products_id = " . $param['productsId'] . ")
                    AND (stocktaking.productsColor_id = " . $param['productsColorId'] . ")
                    AND (stocktaking.productsSize_id = " . $param['productsSizeId'] . ")

                    AND (stocktaking.users_id = " . $param['usersId'] . ")
                    AND (stocktaking.storage_id = " . $param['storageId'] . ")
                    AND (stocktaking.place_id = " . $param['placeId'] . ")";
            $nomeninv = Db::getSQLPackage($sqlArray);
        }

        if ($nomeninv != []) {
            $invred  = $nomeninv[0];

            //     $vp['users_id'] =  $vTab['userid'];
            $vp['date'] = VFunc::vTimeNow();
            $vp['count'] =   $invred['count'] + $param['count'];
            //     $vp['count'] =   $vTab['count'];
            //     $vp['comment'] =  "Изменено";

            Db::update('stocktaking', $invred['id'], $vp);

            $str = 'Изменено в инвентаризации';
        } else {

            //Если нет то новая

            $vp['date'] = VFunc::vTimeNow();

            $vp['users_id'] =  $param['usersId'];
            $vp['storage_id'] =  $param['storageId'];
            $vp['place_id'] =  $param['placeId'];
            $vp['products_id'] =  $param['productsId'];
            $vp['productsColor_id'] =  $param['productsColorId'];
            $vp['productsSize_id'] =  $param['productsSizeId'];
            $vp['count'] =  $param['count'];
            //$vp['comment'] =  "Первые пробные записи";

            Db::create('stocktaking', $vp);

            $str = 'Добавлено в инвентаризацию';
        }

        return $str;
    }

    //получение записей в инвентаризации ******************************************************************************
    public static function GetStocktaking($param)
    {

        $sqllj = "SELECT 
        S.id as id, 
        S.date as date, 
        S.count as count,
        S.barcode as barcode,

        S.products_id as products_id, 
        P.article as article, 
        P.price as price, 
        P.name as productsname, 
        P.code1c as code1c, 
        
        P.compositions_id as compositions_id, 
        Comp.name as compositionsname, 

        S.productsColor_id as productsColor_id, 
        PColor.name as colorname, 

        S.productsSize_id as productsSize_id, 
        PSize.name as sizename, 

        S.users_id as users_id, 
        U.name as usersname, 
       
        S.place_id as place_id, 
        Pl.name as placename, 

        S.storage_id as storage_id,
        St.name as storagename 
        
        FROM stocktaking S 
        LEFT JOIN products P ON S.products_id = P.id
        LEFT JOIN productsColor PColor ON S.productsColor_id = PColor.id
        LEFT JOIN productsSize PSize ON S.productsSize_id = PSize.id
        LEFT JOIN users U ON S.users_id = U.id
        LEFT JOIN place Pl ON S.place_id = Pl.id
        LEFT JOIN storage St ON S.storage_id = St.id
        LEFT JOIN compositions Comp ON P.compositions_id = Comp.id
        ORDER BY date DESC";

        $sqlArray[] = "CREATE TEMPORARY TABLE vittemp " . $sqllj;

        //Db::log($sqllj);

        if (!empty($param)) {
            //временная таблица
            //$sqlArray[] = "CREATE TEMPORARY TABLE vittemp " . $sqllj;


            $tableName = $param['tableName'];
            $tableId = $param['tableId'];
            $sqlArray[] =  "SELECT * FROM vittemp WHERE ( " . $tableName . " = " . $tableId . " )";
        }
        $findlist1 = Db::getSQLPackage($sqlArray);
        
        if (count($findlist1) > 0) {
            $findlist = $findlist1;
        } else {
            $findlist = [];
        };

        return $findlist;
    }
}
