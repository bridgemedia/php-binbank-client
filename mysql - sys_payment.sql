-- Версия сервера: 5.7.14-8
-- Версия PHP: 7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Структура таблицы `sys_payment`
--

CREATE TABLE `sys_payment` (
  `payment:id` int(11) NOT NULL,
  `payment:order_id` varchar(255) NOT NULL COMMENT 'номер заказа, общий с банком',
  `payment:creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment:amount` int(11) NOT NULL COMMENT 'мы не хотим использовать копейки, поэтому INT',
  `payment:callback_status` varchar(16) DEFAULT NULL COMMENT 'разрешим NULL, чтобы логировать ошибки',
  `payment:card` varchar(255) DEFAULT NULL,
  `payment:transaction` varchar(255) DEFAULT NULL,
  `payment:callback` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы таблицы `sys_payment`
--
ALTER TABLE `sys_payment`
  ADD PRIMARY KEY (`payment:id`);

--
-- AUTO_INCREMENT для таблицы `sys_payment`
--
ALTER TABLE `sys_payment`
  MODIFY `payment:id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
