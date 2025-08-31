<?php

header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type, Authorization');




class Obmen
{
    //Работа с 1С
    public function action1C()
    {

        //Авторизация
        //Security::loginSite();

        //Получаем из Запроса
        $strinput = file_get_contents('php://input');

        //Декодируем
        $sVit = json_decode($strinput, true);
        $str = 'false';

        //Обмен Кодами 1С и сайта 123
        if (isset($sVit['codes'])) {
            if ($sVit['codes'] == 'siteto1c') {
                $table = $sVit['table'];
                $code1c = $sVit['code1c'];
                $sql = "SELECT id FROM " . $table . ' WHERE (code1c = "' .  $code1c . '")';
                $arr = Db::getSQL($sql);
                $str = json_encode($arr[0]['id'], JSON_UNESCAPED_UNICODE);
                echo $str;
                return true;
            };
            if ($sVit['codes'] == '1ctosite') {

                $table = $sVit['table'];
                $vr['code1c'] = $sVit['code1c'];
                $id = $sVit['id'];
                Db::update($table, $id, $vr, true);
                echo $str;
                return true;
            };
        };

        //Общий обмен с 1с
        if (isset($sVit['fromsiteto1c'])) {
            $table = $sVit['fromsiteto1c'];
            $arr = Db::getObmen($table);
            $str = json_encode($arr, JSON_UNESCAPED_UNICODE);
            echo $str;
            return true;
        };

        if (isset($sVit['from1ctosite'])) {
            if (isset($sVit['data'])) {
                $table = $sVit['from1ctosite'];
                $data = $sVit['data'];
                Db1c::setTable1CToSite($table, $data);
                echo $str;
                return true;
            };

            //JGFCYJ
            if (isset($sVit['deleteall'])) {
                if ($sVit['deleteall'] == 'JGFCYJ') {
                    $table = $sVit['from1ctosite'];
                    Db::deleteall($table);
                    echo $str;
                    return true;
                };
            };
        };

        //2025 SELECT Получение элемента таблицы по айди SELECT
        if (isset($sVit['GetTableById'])) {
            if (!empty($sVit['GetTableById'])) {

                $vTab = $sVit['GetTableById'];

                $sqlArray[] =  "SELECT * FROM " . $vTab['tableName']  . " WHERE (id = " . $vTab['tableId']  . ")";

                $findItem = Db::getSQLPackage($sqlArray);

                if (count($findItem) > 0) {
                    $findlist = $findItem[0];
                } else {
                    $findlist = null;
                };
            } else {
                $findlist = null;
            }
            $str = json_encode($findlist, JSON_UNESCAPED_UNICODE);
            echo $str;
            return true;
        };

        //2025 SELECT Инвентаризации более
        if (isset($sVit['GetStocktakingMoreId'])) {
            if (!empty($sVit['GetStocktakingMoreId'])) {

                $vTab = $sVit['GetStocktakingMoreId'];

                $sqlArray[] =  "SELECT * FROM stocktaking WHERE (id >= " . $vTab . ")";

                $findItem = Db::getSQLPackage($sqlArray);

                if (count($findItem) > 0) {
                    $findlist = $findItem;
                } else {
                    $findlist = [];
                };
            } else {
                $findlist = [];
            }
            $str = json_encode($findlist, JSON_UNESCAPED_UNICODE);
            echo $str;
            return true;
        };

        //Пользователи
        if (isset($sVit['users'])) {
            if ($sVit['users'] == 'siteto1c') {
                $arr = Db::getObmen('users');
                $str = json_encode($arr, JSON_UNESCAPED_UNICODE);
                echo $str;
                return true;
            };
            if ($sVit['users'] == '1ctosite') {
                if (isset($sVit['data'])) {
                    //VDb::deleteAll('nomen');
                    $data = $sVit['data'];
                    Db1c::setTable1CToSite('users', $data);
                    echo $str;
                    return true;
                };
            };
        };

        //Задачи
        if (isset($sVit['task'])) {
            if ($sVit['task'] == 'siteto1c') {
                $arr = Db::getObmen('task');
                $str = json_encode($arr, JSON_UNESCAPED_UNICODE);
                echo $str;
                return true;
            };
            if ($sVit['task'] == '1ctosite') {
                if (isset($sVit['data'])) {
                    $data = $sVit['data'];
                    Db1c::setTable1CToSite('task', $data);
                    echo $str;
                    return true;
                };
            };
        };

        //Места строительстра и работ
        if (isset($sVit['place'])) {
            if ($sVit['place'] == 'siteto1c') {
                $arr = Db::getObmen('place');
                $str = json_encode($arr, JSON_UNESCAPED_UNICODE);
                echo $str;
                return true;
            };
            if ($sVit['place'] == '1ctosite') {
                if (isset($sVit['data'])) {
                    $data = $sVit['data'];
                    Db1c::setTable1CToSite('place', $data);
                    echo $str;
                    return true;
                };
            };
        };

        //табель
        if (isset($sVit['timesheet'])) {
            if ($sVit['timesheet'] == 'siteto1c') {
                $arr = Db::getObmen('timesheet');
                $str = json_encode($arr, JSON_UNESCAPED_UNICODE);
                echo $str;
                return true;
            };
            if ($sVit['timesheet'] == '1ctosite') {
                if (isset($sVit['data'])) {
                    $data = $sVit['data'];
                    Db1c::setTable1CToSite('timesheet', $data);
                    echo $str;
                    return true;
                };
            };
        };

        //табель
        if (isset($sVit['timesheetT'])) {
            if ($sVit['timesheetT'] == 'siteto1c') {
                $arr = Db::getObmen('timesheetT');
                $str = json_encode($arr, JSON_UNESCAPED_UNICODE);
                echo $str;
                return true;
            };
            if ($sVit['timesheetT'] == '1ctosite') {
                if (isset($sVit['data'])) {
                    $data = $sVit['data'];
                    Db1c::setTable1CToSite('timesheetT', $data);
                    echo $str;
                    return true;
                };
            };
        };
        //Товар
        if (isset($sVit['products'])) {
            if ($sVit['products'] == 'siteto1c') {
                $arr = Db::getObmen('products');
                $str = json_encode($arr, JSON_UNESCAPED_UNICODE);
                echo $str;
                return true;
            };
            if ($sVit['products'] == '1ctosite') {
                if (isset($sVit['deleteall'])) {
                    Db::deleteAll('products');
                    echo 'номенклатура очищена';
                    return true;
                };
                if (isset($sVit['data'])) {
                    //VDb::deleteAll('nomen');
                    $data = $sVit['data'];
                    Db1c::setTable1CToSite('products', $data);
                    echo $str;
                    return true;
                };
            };
        };

        //Свободный Запрос
        if (isset($sVit['freeSQL'])) {

            //Проверка на доступ
            $auth = false;
            $paramsPath = ROOT . '/config/db_params.php';
            $params = include($paramsPath);

            if (isset($sVit['pass'])) {
                $pass = $sVit['pass'];
                if ($pass == $params['password']) {
                    $auth = true;
                };
            };

            if (!$auth) {
                echo "false";
                return true;
            };


            //сам запрос
            $sql = $sVit['freeSQL'];

            if (empty($sql)) {
                echo "false";
                return true;
            };

            // echo $sql;
            // return false;

            $otvet = true;
            if (isset($sVit['$otvet'])) {
                $otvet = $sVit['$otvet'];
            };

            if ($otvet) {
                $findlist = Db::getSQLPackage($sql, $otvet);

                $str = json_encode($findlist, JSON_UNESCAPED_UNICODE);

                echo $str;
                return true;
            } else {
                Db::getSQLPackage($sql, $otvet);
                echo "it worked";
                return true;
            };
        };

        //Получаем имена таблиц из базы
        if (isset($sVit['getTables'])) {
            $findlist = Db::getTables();
            $str = json_encode($findlist, JSON_UNESCAPED_UNICODE);
            echo $str;
            return true;
        };

        //Получаем имена полей из таблицы
        if (isset($sVit['getColumns'])) {
            $tableName = $sVit['getColumns'];
            $findlist = Db::getColumns($tableName);
            $str = json_encode($findlist, JSON_UNESCAPED_UNICODE);
            echo $str;
            return true;
        };

        if (isset($sVit['getFoto'])) {
            $tableName = $sVit['getFoto'];

            $table = $tableName['table'];
            $id = $tableName['id'];

            $sql = "SELECT * FROM fotos WHERE (tablename='" . $table . "' AND tableid=" . $id . ")";
            $findlist = Db::getSQLPackage($sql);

            $str = json_encode($findlist, JSON_UNESCAPED_UNICODE);

            echo $str;
            return true;
        }
        //Если не сработало
        echo $str;
        return true;
    }

    //Работа с мобильным приложением
    public function actionMobile()
    {

        //header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: * ');

        $strinput = file_get_contents('php://input');

        //Декодируем и задаем начальные условия
        $input = json_decode($strinput, true);

        $output = [];

        //Авторизация
        // Security::loginSite();

        if (isset($input['command'])) {

            $data = $input['data'];

            switch ($input['command']) {

                case 'test': {
                        // что пришло то и вернули!!!!
                        $output = $input;
                    };
                    break;

                //!!!!!!!!!!!!Одна из самых ГЛАВНЫХ функций!!!!!!!!!!!!!
                //2025 CREATE добавление НОВОГО элемента таблицы 
                case 'CreateTableItem': {

                        $table = $data['tableName'];
                        $vp = $data['vp'];

                        $output = Db::create($table, $vp);
                    };
                    break;


                ///2025 UPDATE Редактирование элемента таблицы по айди UPDATE
                case 'UpdateTableById': {

                        $id = $data['tableId'];
                        $table = $data['tableName'];
                        $vp = $data['vp'];

                        Db::update($table, $id, $vp);
                    };
                    break;
                //!!!!!!!!!!!!Одна из самых ГЛАВНЫХ функций!!!!!!!!!!!!!
                ///2025 Удаление элемента таблицы по айди Delete
                case 'DeleteTableById': {

                        $id = $data['tableId'];
                        $table = $data['tableName'];

                        Db::delete($table, $id);
                    };
                    break;

                case 'GetTableById': {

                        $id = $data['tableId'];
                        $table = $data['tableName'];

                        $sql[] =  "SELECT * FROM " . $table  . " WHERE (id = " . $id  . ")";
                        $output = Db::getSQLPackage($sql, true, true);
                    };
                    break;
                case 'GetTable': {

                        $table = $data['tableName'];

                        $sql[] = "SELECT * FROM " . $data['tableName'] . " ORDER BY name";
                        $output = Db::getSQLPackage($sql);
                    };
                    break;

                case 'GetProperty': {

                        $id = $data['tableId'];
                        $table = $data['tableName'];
                        $property = $data['property'];

                        $sql[] =  "SELECT " . $property . " FROM " . $table  . " WHERE (id = " . $id  . ")";
                        $findItem = Db::getSQLPackage($sql, true, true);

                        if (isset($findItem[$property])) {
                            $output = $findItem[$property];
                        } else {
                            $output = '';
                        };
                    };
                    break;

                default: {
                        $output = [];
                    };
                    break;
            }
        };

        // Кодируем обратно в строку и возвращаем!!!!!
        $str = json_encode($output, JSON_UNESCAPED_UNICODE);
        echo $str;
        return true;

        //. Здесь конец модуля *******************************************************************************




        //Авторизация
        if (isset($sVit['Login'])) {
            if (!empty($sVit['mobileLogin'])) {

                $vAuth = $sVit['mobileLogin'];
                if (!empty($vAuth['login']) && !empty($vAuth['password'])) {

                    $sql =  "SELECT * FROM users 
                    WHERE (users.active = 1 
                    AND users.login  = '" . $vAuth['login']  . "' 
                    AND users.password  = '" . $vAuth['password']  . "' )";
                    $findUser = Db::getSQLPackage($sql);

                    if (count($findUser) > 0) {
                        $findlist = $findUser[0];
                    } else {
                        $findlist = null;
                    };
                } else {
                    $findlist = null;
                };
            } else {
                $findlist = null;
            }
            $str = json_encode($findlist, JSON_UNESCAPED_UNICODE);
            echo $str;
            return true;
        };

        // //2025 Получить список таблицы по названию
        // if (isset($sVit['GetTable'])) {
        //     $table = $sVit['GetTable'];
        //     //$sqlNomen =  "SELECT * FROM '" . $table . " ORDER name";

        //     // $sqlArray[] = "CREATE TEMPORARY TABLE vittemp " . $sqlNomen;
        //     $sqlArray[] = "SELECT * FROM " . $table . " ORDER BY name";

        //     $findlist1 = Db::getSQLPackage($sqlArray);

        //     if (count($findlist1) > 0) {
        //         $findlist = $findlist1;
        //     } else {
        //         $findlist = [];
        //     };

        //     $str = json_encode($findlist, JSON_UNESCAPED_UNICODE);
        //     echo $str;
        //     return true;
        // };

        // //2025 Получение свойства таблицы по айди
        // if (isset($sVit['GetProperty'])) {
        //     if (!empty($sVit['GetProperty'])) {

        //         $vTab = $sVit['GetProperty'];

        //         $sqlArray[] =  "SELECT " . $vTab['property']  . " FROM " . $vTab['table']  . " WHERE (id = " . $vTab['id']  . ")";

        //         $findUser = Db::getSQLPackage($sqlArray);

        //         if (count($findUser) > 0) {
        //             $findlist = $findUser[0][$vTab['property']];
        //             //$findlist = "tcnm";
        //         } else {
        //             $findlist = null;
        //         };
        //     } else {
        //         $findlist = null;
        //     }
        //     $str = json_encode($findlist, JSON_UNESCAPED_UNICODE);
        //     echo $str;
        //     return true;
        // };

        // //2025 SELECT Получение элемента таблицы по айди SELECT
        // if (isset($sVit['GetTableById'])) {
        //     if (!empty($sVit['GetTableById'])) {

        //         $vTab = $sVit['GetTableById'];

        //         $sqlArray[] =  "SELECT * FROM " . $vTab['tableName']  . " WHERE (id = " . $vTab['tableId']  . ")";

        //         $findItem = Db::getSQLPackage($sqlArray);

        //         if (count($findItem) > 0) {
        //             $findlist = $findItem[0];
        //         } else {
        //             $findlist = null;
        //         };
        //     } else {
        //         $findlist = null;
        //     }
        //     $str = json_encode($findlist, JSON_UNESCAPED_UNICODE);
        //     echo $str;
        //     return true;
        // };

        // //2025 UPDATE Редактирование элемента таблицы по айди UPDATE
        // if (isset($sVit['UpdateTableById'])) {
        //     if (!empty($sVit['UpdateTableById'])) {
        //         //обновляем

        //         $struct = $sVit['UpdateTableById'];

        //         $id = $struct['tableId'];
        //         $table = $struct['tableName'];
        //         $vp = $struct['vp'];

        //         //Выходим по пустому id
        //         if (empty($id)) {
        //             echo json_encode('', JSON_UNESCAPED_UNICODE);
        //             return true;
        //         }

        //         Db::update($table, $id, $vp);
        //     } else {
        //     }
        //     $str = json_encode('', JSON_UNESCAPED_UNICODE);
        //     echo $str;
        //     return true;
        // };

        // //!!!!!!!!!!!!Одна из самых ГЛАВНЫХ функций!!!!!!!!!!!!!
        // //2025 CREATE добавление НОВОГО элемента таблицы 
        // if (isset($sVit['CreateTableItem'])) {
        //     if (!empty($sVit['CreateTableItem'])) {
        //         //обновляем

        //         $struct = $sVit['CreateTableItem'];


        //         $table = $struct['tableName'];
        //         $vp = $struct['vp'];

        //         Db::create($table, $vp);
        //     } else {
        //     }
        //     $str = json_encode('', JSON_UNESCAPED_UNICODE);
        //     echo $str;
        //     return true;
        // };

        // $str = "Нет данных из BackEnda";
        // echo $str;
        // return true;
    }
}
