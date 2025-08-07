<?php
class Db1c
{

    //Записываем структуру в таблицу сайта
    public static function  setTable1CToSite($table, $data)
    {
        foreach ($data as $value) {
            self::setRecord1CToSite($table, $value);
        }
        return true;
    }

    public static function setRecord1CToSite($table, $value)
    {

        if (isset($value['code1c']) && !empty($value['code1c'])) {


            $vp = [];
            $id = self::findCode1C($table, $value['code1c']); //Ищем по коду1С

            if ($id) {      //Если запись найдена по коду 1С
                foreach ($value as $key => $value2) { //перебираем всю полученную структуру
                    $pos = strpos($key, '_id'); //Находим Позицию ссылочного ключа

                    if ($pos === false) {
                        if ($key == 'parentcode1c') {
                            $idAsCode1C = self::findCode1C($table, $value2);
                            //Сделать проверку существования записи таблицы
                            if ($idAsCode1C) {
                                $vp['parent_id'] = $idAsCode1C;
                            };
                        } else {
                            $vp[$key] = $value2; //обычная переменная!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                        }
                    } else {                   //Ссылочная 
                        $tabName = explode("_", $key);
                        $idAsCode1C = self::findCode1C($tabName[0], $value2);
                        //Db::log($key . ' --Изм--> ' .  $tabName[0] . ' ----> ' . $value2 . ' ==== ' . $idAsCode1C);
                        if ($idAsCode1C) {
                            $vp[$key] = $idAsCode1C;
                        };
                    }
                }
                //Обновляем запись
                //Db::log($vp);
                Db::update($table, $id, $vp, true);

                if (isset($value['vjson'])) {
                    Db::updateVJSON($table, $id, $value['vjson']);
                };
            } else { // Новая Запись
                //Здесь Говорит о том что запись пришла из сайта и ей присваиваем ТОЛЬКО код из 1С!!!
                if (isset($value['id']) && !empty($value['id'])) {
                    self::setCode1C($table, $value['id'], $value['code1c']);
                } else { //Если нет ничего - это НОВАЯ Запись из 1С и тогда создаём её

                    foreach ($value as $key => $value2) { //перебираем всю полученную структуру
                        $pos = strpos($key, '_id'); //Находим Позицию ссылочного ключа

                        if ($pos === false) {
                            if ($key == 'parentcode1c') {
                                $idAsCode1C = self::findCode1C($table, $value2);
                                //Сделать проверку существования записи таблицы
                                if ($idAsCode1C) {
                                    $vp['parent_id'] = $idAsCode1C;
                                };
                            } else {
                                //Db::log($table . ' **** ' . $key . ' --Созд--> ' . $value2 . ' обычная переменная!');
                                $vp[$key] = $value2; //обычная переменная!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                            }
                        } else {                   //Ссылочная 
                            $tabName = explode("_", $key);
                            $idAsCode1C = self::findCode1C($tabName[0], $value2);

                            //Db::log($table . ' **** ' . $key . ' --Созд--> ' .  $tabName[0] . ' ----> ' . $value2 . ' ==== ' . $idAsCode1C);

                            //Сделать проверку существования записи таблицы
                            if ($idAsCode1C) {
                                $vp[$key] = $idAsCode1C;
                            };
                        }
                    }
                    //Новая запись
                    //Db::log($vp);
                    $id = Db::create($table, $vp, true);

                    if (isset($value['vjson'])) {
                        Db::updateVJSON($table, $id, $value['vjson']);
                    };
                };
            };
        } else {
            //приход без code1С - это отчет
            foreach ($value as $key => $value2) { //перебираем всю полученную структуру
                $pos = strpos($key, '_id'); //Находим Позицию ссылочного ключа

                if ($pos === false) {
                    if ($key == 'parentcode1c') {
                        $idAsCode1C = self::findCode1C($table, $value2);
                        //Сделать проверку существования записи таблицы
                        if ($idAsCode1C) {
                            $vp['parent_id'] = $idAsCode1C;
                        };
                    } else {
                        $vp[$key] = $value2; //обычная переменная!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    }
                } else {                   //Ссылочная 
                    $tabName = explode("_", $key);
                    $idAsCode1C = self::findCode1C($tabName[0], $value2);
                    //Сделать проверку существования записи таблицы
                    if ($idAsCode1C) {
                        $vp[$key] = $idAsCode1C;
                    };
                }
            }
            //Новая запись
            $id = Db::create($table, $vp);
        };

        return true;
    }

    //Находим код1с записи
    public static function findCode1C($tableName, $code1c)
    {
        $sql = "SELECT id FROM " . $tableName . ' WHERE (code1c = "' .  $code1c . '")';
        //Db::log($sql);
        $list = Db::getSQL($sql);
        if (empty($list)) {
            return false;
        }
        return $list[0]['id'];
    }

    //Устанавливаем код1с записи
    public static function setCode1C($table, $id, $code1c)
    {
        $vr['code1c'] = $code1c;
        Db::update($table, $id, $vr, true);
        return true;
    }

    //берем по коду 1с записи
    public static function getCode1C($tableName, $id)
    {
        $sql = "SELECT code1C FROM " . $tableName . " WHERE (id = " .  $id . ")";
        //self::log($sql);
        $list = Db::getSQL($sql);
        if (empty($list)) {
            return false;
        }
        return $list[0]['code1C'];
    }
}
