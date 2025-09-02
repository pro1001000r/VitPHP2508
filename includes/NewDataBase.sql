SET
    SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
    time_zone = "Europe/Moscow";


-- CREATE DATABASE IF NOT EXISTS `u3083732_vitbase` DEFAULT CHARSET = cp1251;

-- USE `u3083732_vitbase`;

--
-- Структура таблицы `users`
--
CREATE TABLE `users` (
    `id` int NOT NULL,
    `name` varchar(256) NOT NULL,
    `code1c` varchar(9) DEFAULT NULL,
    `login` varchar(256) DEFAULT NULL,
    `password` varchar(256) DEFAULT NULL,
    `status` varchar(5) NOT NULL DEFAULT 'U',
    `active` tinyint (1) NOT NULL DEFAULT '1',
    `telefon` varchar(25) DEFAULT NULL,
    `storage_id` int DEFAULT NULL,
    `place_id` int DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = cp1251;

--
-- Дамп данных таблицы `users`
--
INSERT INTO
    `users` (
        `id`,
        `name`,
        `code1c`,
        `login`,
        `password`,
        `status`,
        `active`,
        `telefon`,
        `storage_id`,
        `place_id`
    )
VALUES
    (
        1,
        'Виталий',
        '000000001',
        'Vit',
        '50629',
        'S',
        1,
        '+79271095380',
        2,
        19
    );

ALTER TABLE
    `users`
ADD
    PRIMARY KEY (`id`);

ALTER TABLE
    `users`
MODIFY
    `id` int NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 2;

COMMIT;