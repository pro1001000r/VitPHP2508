<?php

// Debug отладчик
function debug($param)
{
    echo '<pre>';
    print_r($param);
    echo '</pre>';
}

class VFunc
{

    // Перенаправляет к списку /Таблица/List/
    public static function show($tableName)
    {
        $tableNameUp = ucfirst($tableName);
        header("Location: /" . $tableNameUp . "/List/");
    }

    // Debug отладчик
    public static function debug($param)
    {

        echo '<br>';
        echo time() . ' Начало тест---------------------------';
        echo '<br>';
        echo '<pre>';
        print_r($param);
        echo '</pre>';
        echo time() . ' Окончание тест---------------------------';
        echo '<br>';
        return true;
    }

    // Генериратор новое имя для фото
    public static function ImgNameGenereteDate($nameTableId = '')
    {
        $nameDate = date('Y-m-d h:m:s:v');
        $nameDate = str_replace(" ", "", $nameDate);
        $nameDate = str_replace("-", "", $nameDate);
        if (!empty($nameTableId)) {
            $nameTableId = '_' . $nameTableId;
        }
        $nameImg = "img" . $nameTableId . "_" . str_replace(":", "", $nameDate);

        $pathFoto = $nameImg;
        return $nameImg;
    }

    // генерирует новое имя для jpg
    public static function NewImgPathName($id = '')
    {
        $nameDate = date('Y-m-d h:m:s');
        $nameDate = str_replace(" ", "", $nameDate);
        $nameDate = str_replace("-", "", $nameDate);
        if (!empty($id)) {
            $id = '_' . $id;
        }
        $nameImg = "img" . $id . "_" . str_replace(":", "", $nameDate);

        $pathFoto = "/upload/images/" . $nameImg . ".jpg";

        return $pathFoto;
    }

    // Форматирование даты чтобы отображалась в HTML
    public static function formatDate($strdate = '')
    {
        if ($strdate <> '') {
            $d = new DateTime($strdate);
            //debug($d);
            return $d->format('Y-m-d\TH:i');
        }

        return $strdate;
    }

    // строка ДатаВремяПоРусски
    // возвращает массив
    public static function formatDateRus($strdate = '')
    {
        if (empty($strdate)) {
            return '';
        };

        $month_list = array(
            1 => 'января',
            2 => 'февраля',
            3 => 'марта',
            4 => 'апреля',
            5 => 'мая',
            6 => 'июня',
            7 => 'июля',
            8 => 'августа',
            9 => 'сентября',
            10 => 'октября',
            11 => 'ноября',
            12 => 'декабря'
        );

        $d = date_parse($strdate);
        //date('d', ) . ' ' . $month_list[date('n')] . ' ' . date('Y')
        if (($d['hour'] != 0) || ($d['minute'] != 0)) {
            if ($d['hour'] < 10) {
                $d['hour'] = '0' . $d['hour'];
            };
            if ($d['minute'] < 10) {
                $d['minute'] = '0' . $d['minute'];
            };
            $str = $d['day'] . ' '
                . $month_list[$d['month']] . ' '
                . $d['year'] . 'г. '
                . $d['hour'] . ':'
                . $d['minute'];
        } else {
            $str = $d['day'] . ' '
                . $month_list[$d['month']] . ' '
                . $d['year'] . 'г.';
        }
        return $str;
    }

    // Время сейчас
    public static function vTimeNow()
    {
        date_default_timezone_set('Europe/Moscow');
        //$d = date('m/d/Y h:i:s a', time());
        //return self::formatDate($d);
        return date("Y-m-d H:i:s");
    }

    // Вычисляет Время между датами
    // возвращает строку
    public static function vTimeBetween($d2, $d1 = '')
    {
        date_default_timezone_set('Asia/Krasnoyarsk');
        $date1 = new DateTime($d1);
        $date2 = new DateTime($d2);
        $interval = date_diff($date1, $date2);
        //debug($interval);
        $y = '';
        $m = '';
        $d = '';
        $h = '';
        $min = '';
        if ($interval->y > 0) {
            if ($interval->y > 4)
                $y .= $interval->y . ' лет';
            else if ($interval->y == 1)
                $y .= $interval->y . ' год';
            else
                $y .= $interval->y . ' года';
            $y .= ', ';
        }

        if ($interval->m > 0) {
            if ($interval->m > 4)
                $m .= $interval->m . ' месяцев';
            else if ($interval->m > 1)
                $m .= $interval->m . ' месяца';
            else
                $m .= $interval->m . ' месяц';
            $m .= ', ';
        }

        if ($interval->d > 0) {
            if ($interval->d > 4)
                $d .= $interval->d . ' дней';
            else if ($interval->d > 1)
                $d .= $interval->d . ' дня';
            else
                $d .= $interval->d . ' день';
            $d .= ', ';
        }

        if ($interval->h == 0 && $interval->i == 0) {
            $str = $y . $m . $d;
        } else {
            $h = $interval->h . ' ч. ';
            $min = $interval->i . ' мин.';
            $str = $y . $m . $d . $h . $min;
        }

        return $str;
    }

    //Дни от и до указанного времени
    public static function vDayAgo($d2)
    {
        date_default_timezone_set('Asia/Krasnoyarsk');
        $date1 = date_format(new DateTime(), 'z');
        $date2 = date_format(new DateTime($d2), 'z');
        $d = '';
        switch ($date1 - $date2) {
            case -2: {
                    $d = 'Послезавтра';
                };
                break;
            case -1: {
                    $d = 'Завтра';
                };
                break;
            case 0: {
                    $d = 'Сегодня';
                };
                break;

            case 1: {
                    $d = 'Вчера';
                };
                break;
            case 2: {
                    $d = 'Позавчера';
                };
                break;
        };

        return $d;
    }

    //день недели
    public static function vWeek($d2)
    {
        date_default_timezone_set('Asia/Krasnoyarsk');
        $date2 = new DateTime($d2);
        $interval = date_format($date2, 'N');
        $str = '';
        switch ($interval) {
            case 1: {
                    $str = 'Понедельник';
                };
                break;
            case 2: {
                    $str = 'Вторник';
                };
                break;
            case 3: {
                    $str = 'Среда';
                };
                break;
            case 4: {
                    $str = 'Четверг';
                };
                break;
            case 5: {
                    $str = 'Пятница';
                };
                break;
            case 6: {
                    $str = 'Суббота';
                };
                break;
            case 7: {
                    $str = 'Воскресенье';
                };
                break;
        };
        return $str;
    }

    // проверяет количество символов в наименовании
    public static function checkName($name)
    {
        if (strlen($name) >= 3) {
            return true;
        }
        return false;
    }

    /**
     * Проверяет имя: не меньше, чем 2 символа
     * @param string $name <p>Имя</p>
     * @return boolean <p>Результат выполнения метода</p>
     */
    public static function checkName2($name)
    {
        if (strlen($name) >= 2) {
            return true;
        }
        return false;
    }

    /**
     * Проверяет телефон: не меньше, чем 10 символов
     * @param string $phone <p>Телефон</p>
     * @return boolean <p>Результат выполнения метода</p>
     */
    public static function checkPhone($phone)
    {
        if (strlen($phone) >= 10) {
            return true;
        }
        return false;
    }

    /**
     * Проверяет имя: не меньше, чем 6 символов
     * @param string $password <p>Пароль</p>
     * @return boolean <p>Результат выполнения метода</p>
     */
    public static function checkPassword($password)
    {
        if (strlen($password) >= 6) {
            return true;
        }
        return false;
    }


    // получаем все IP текущего соединения
    public static function get_ip_list()
    {
        $list = array();
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $list = array_merge($list, $ip);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $list = array_merge($list, $ip);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $list[] = $_SERVER['REMOTE_ADDR'];
        }

        $list = array_unique($list);
        return implode(',', $list);
    }

    // получаем браузер текущего соединения
    public static function useragent()
    {
        $vbrowser = 'Браузер не определен';
        if (isset($_SERVER['HTTP_SEC_CH_UA'])) {
            $vbrowser = $_SERVER['HTTP_SEC_CH_UA'];
            $vbrowser = str_replace("\"", "", $vbrowser);
            $vbrowser = str_replace(" ", "", $vbrowser);
        }
        return $vbrowser;
    }

    /** Добавить Get к URL
     */
    // public static function setGet($param = '')
    // {
    //     //$url = $_SERVER['REQUEST_URI'];

    //     $url = $_SERVER['REQUEST_URI'];
    //     //$arr = parse_url($url, PHP_URL_QUERY);
    //     $arr = $_SERVER['argv'];
    //     foreach ($arr as $key => $value) {
    //         //Db::log($key . '------' . $value);
    //     }
    //     //VDb2::log($arr);
    //     //Db::log($url);
    //     return true;
    // }
}
