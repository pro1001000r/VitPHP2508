<?php

class Db
{
    /**
     * 2025 Устанавливает соединение с базой данных
     */

    public static function getConnection()
    {
        // Получаем параметры подключения из файла
        //$paramsPath = ROOT . '/config/db_params.php';
        //$params = include($paramsPath);

        // * Якурнов 08 Август 2025 (пятница)
        // * Получаем параметры подключения из файла
        $json_file_content = file_get_contents(ROOT . '/config.json');
        $params = json_decode($json_file_content, true);

        // Устанавливаем соединение
        $dsn = "mysql:host={$params['host']};dbname={$params['dbname']}";
        $db = new PDO($dsn, $params['user'], $params['password']);

        // Задаем кодировку
        $db->exec("set names utf8");

        //date_default_timezone_set('Asia/Krasnoyarsk');

        return $db;
    }

    // Выдает всё по id
    public static function getById($tableName, $id)
    {
        $db = self::getConnection();

        $sql = "SELECT * FROM $tableName WHERE id = :id";

        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $result->execute();

        while ($row = $result->fetch()) {
            // if (empty($column)) {
            //     return $row;
            // }
            return $row;
        }
        return 0;
    }

    public static function getSQL($sql = [], $params = '')
    {
        // подключение к базе
        $db = self::getConnection();

        // подстановка текста запроса в коннект
        if (is_array($sql)) {
            foreach ($sql as $sqlElem) {
                $result = $db->query($sqlElem);
            }
        } else {
            $result = $db->prepare($sql);
        };

        // Устанавливаем параметры
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $result->bindParam($key, $value);
            }
        }
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $result->execute();

        $list = [];
        $i = 0;
        while ($row = $result->fetch()) {
            $list[$i] = $row;
            $i++;
        }
        // if (empty($i)) {
        //     $list = false;
        // }
        return $list;
    }

    //Обновляет данные табличной части из vjsona
    public static function updateVJSON($table, $id, $vjson = '')
    {
        //удаляем все старые записи 
        $sql = 'DELETE FROM ' . $table . "T WHERE " . $table . "_id = " . $id;

        self::getSQL($sql);

        //Создаем новые из джейсона
        if (empty($vjson)) {
            return true;
        }

        $list = json_decode($vjson, true);

        foreach ($list as $vp) {

            if (isset($vp['id'])) {
                unset($vp['id']);
            };

            $vp[$table . "_id"]  = $id;

            self::create($table . "T", $vp);
        }

        return true;
    }


    // $sqlNomen =  "SELECT 'Nomen' as tableName, Nom.id, Nom.name FROM nomen Nom WHERE (Nom.name LIKE '%" . $vFind . "%') OR (Nom.comment LIKE '%" . $vFind . "%')";
    // $sqlCategory = "SELECT 'Category' as tableName, Cat.id, Cat.name FROM category Cat WHERE (Cat.name LIKE '%" . $vFind . "%')";
    // $sql[] = "CREATE TEMPORARY TABLE vittemp " . $sqlNomen . " UNION ALL " . $sqlCategory;
    // $sql[] = "SELECT * FROM vittemp ORDER BY name";
    public static function getSQLPackage($sql = [], $otvet = true, $isitem = false)
    {
        // подключение к базе
        $db = self::getConnection();

        // подстановка текста запроса в коннект
        if (is_array($sql)) {
            foreach ($sql as $sqlElem) {
                $result = $db->query($sqlElem);
                //self::log($sqlElem);
            }
        } else {
            $result = $db->query($sql);
        };

        $result->setFetchMode(PDO::FETCH_ASSOC);

        if ($otvet) {
            $result->execute();

            if ($isitem) {
                return $result->fetch();
            }

            $list = array();
            $i = 0;
            while ($row = $result->fetch()) {
                $list[$i] = $row;
                $i++;
            }

            // if ($isitem) {
            //     if (count($list) > 0) {
            //         return $list[0];
            //     };
            // }

            return $list;
        } else {
            return $result->execute();
        }
    }

    /** Устанавливаем переменные в запрос
     */
    public static function set($value)
    {
        $set = '';
        $operator = ',';
        foreach ($value as $column => $val) {
            if (!$set) {
                $set = ' SET ' . $column . '=' . "'" . $val . "' ";
            } else {
                $set .= $operator . " " . $column . '=' . "'" . $val . "' ";
            }
        }

        return $set;
    }

    /** Ведем Логи
     */
    public static function log($param)
    {
        $value['comment'] = $param;
        $value['date'] = VFunc::vTimeNow();
        self::createbased('logs', $value);
    }

    /** Создаём новую запись в таблицу<br/>
     */
    public static function create($tableName, $value, $from1C = false)
    {
        // Соединение с БД
        $db = self::getConnection();
        // Текст запроса к БД
        $sql = 'INSERT ' . $tableName . self::set($value);
        //self::log($sql);
        //self::log("Создалось; " . $tableName . ' ----> ' . $sql);
        // Получение и возврат результатов. Используется подготовленный запрос
        $result = $db->prepare($sql);
        //debug($sql);
        if ($result->execute()) {
            // Если запрос выполнен успешно, возвращаем id добавленной записи
            $lastId = $db->lastInsertId();

            //Db::log("Создалось; " . $tableName . ' ----> ' . $lastId);
            if (!$from1C) {
                self::setObmen($tableName, $lastId);
            }

            return $lastId;
        }
        // Иначе возвращаем 0
        return 0;
    }

    /** Создаём новую запись в таблицу<br/>
     */
    public static function createbased($tableName, $value)
    {
        $db = self::getConnection();
        $sql = 'INSERT ' . $tableName . self::set($value);
        $result = $db->prepare($sql);
        if ($result->execute()) {
            $lastId = $db->lastInsertId();
            return $lastId;
        }
        return true;
    }

    //обновляет запись в таблице
    public static function update($tableName, $id, $value, $from1C = false)
    {
        //регистрируем для обмена
        if (!$from1C) {
            self::setObmen($tableName, $id);
        }

        // Соединение с БД
        $db = self::getConnection();

        // Текст запроса к БД
        $sql = 'UPDATE ' . $tableName . self::set($value) . "WHERE id = :id";

        // Получение и возврат результатов. Используется подготовленный запрос
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }

    public static function delete($tableName, $id)
    {
        // Соединение с БД
        $db = self::getConnection();

        // Текст запроса к БД
        $sql = 'DELETE FROM ' . $tableName . " WHERE id = :id";

        // Получение и возврат результатов. Используется подготовленный запрос
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        return $result->execute();
    }


    public static function deleteAll($tableName)
    {
        // Соединение с БД
        $db = self::getConnection();

        // Текст запроса к БД

        $sql = 'DELETE FROM ' . $tableName;
        //обнуляем счетчик
        $sql .= "; ALTER TABLE " . $tableName . " AUTO_INCREMENT=1";
        //Db::log($sql);
        // Получение и возврат результатов. Используется подготовленный запрос
        $result = $db->prepare($sql);
        return $result->execute();
    }

    //Удаляем записи с таблицы Obmen
    public static function deleteObmen($tableName)
    {
        // Соединение с БД
        $db = self::getConnection();

        // Текст запроса к БД

        $sql = 'DELETE FROM obmen WHERE tablename = ' . $tableName;

        //Db::log($sql);
        // Получение и возврат результатов. Используется подготовленный запрос
        $result = $db->prepare($sql);
        return $result->execute();
    }


    //Регистрация записи для обмена   
    public static function setObmen($tableName, $id)
    {
        if ($tableName == 'obmen') {
            return true;
        };

        //Регистрируем только определенные таблицы
        if (!(
            ($tableName == 'users')
            || ($tableName == 'products')
            || ($tableName == 'productsColor')
            || ($tableName == 'productsSize')
            || ($tableName == 'cheque')
            || ($tableName == 'storage')
            || ($tableName == 'place')
            || ($tableName == 'compositions')
            || ($tableName == 'stocktaking'))) {
            return true;
        };

        //из обмена берем все зарегистрированные ИЗМЕНЁННЫЕ записи таблицы

        $sql = "SELECT * FROM obmen WHERE (tableid = " . $id . " AND tablename = '" . $tableName . "')";
        $fr = self::getSQL($sql);
        //self::log($sql);
        if ($fr) {
            return true;
        };
        //Если не найдено то Регистрируем;

        $vr['tablename'] = $tableName;
        $vr['tableid'] = $id;

        self::createbased('obmen', $vr);

        return true;
    }

    // Выборка всех записей по таблице Изменения для обмена   
    public static function getObmen($tableName)
    {
        $ids = array();
        $arr = array();
        //Db::getConnectionRB();
        //из обмена берем все зарегистрированные ИЗМЕНЁННЫЕ записи таблицы
        $sql = "SELECT id,tableid FROM obmen WHERE tablename = :tablename";
        $obm = self::getSQL($sql, ['tablename' => $tableName]);

        //$obm = R::find('obmen', 'tablename = ?', [$tableName]);

        if ($obm == []) {
            return $ids;
        }
        //забираем только id

        foreach ($obm as $key => $value) {
            $ids[] = $value['tableid'];
            //удаляем записи из обмена
            self::delete('obmen', $value['id']);
        };

        //R::trashAll($obm);

        //Забираем записи из таблицы по выборке
        $sql = "SELECT * FROM " . $tableName . " WHERE id IN (" . implode(',', $ids) . ")";
        $list = self::getSQL($sql);

        //$list = R::find($tableName, 'id IN (' . R::genSlots($ids) . ')', $ids);
        $i = 0;
        foreach ($list as $value) {
            foreach ($value as $key => $value2) {
                $arr[$i][$key] = $value2;
            };
            $i++;
        };
        return $arr;
    }

    public static function getTables($baseName = '')
    {
        // Соединение с БД
        $db = self::getConnection();
        // Текст запроса к БД
        if (empty($baseName)) {
            $sql = 'SHOW TABLES';
        } else {
            $sql = 'SHOW TABLES FROM ' . $baseName;
        }
        $result = $db->prepare($sql);
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $result->execute();
        $list = array();
        $i = 0;
        while ($row = $result->fetch()) {
            $list[$i] = $row;
            $i++;
        }
        return $list;
    }
    public static function getColumns($tableName)
    {
        $db = self::getConnection();

        $sql = "SHOW COLUMNS FROM $tableName";
        $result = $db->prepare($sql);
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $result->execute();
        $list = array();
        $i = 0;
        while ($row = $result->fetch()) {
            $list[$i] = $row;
            $i++;
        }
        return $list;
    }
}
