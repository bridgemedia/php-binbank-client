# php-binbank-client
Класс PHP для онлайн-оплаты через интернет-эквайринг "БИНБАНК"  
[www.binbank.ru](https://www.binbank.ru/corporate-clients/e-commerce/internet-acquiring/)


## Требования
Версия PHP >= 7.0,  
и CSS в этом примере для Bootstrap 3, но вы можете заменить стили на свои.

## Установка
Включить файл класса [Payment__binbank.class.php](/Payment__binbank.class.php) в свой проект
```
require_once 'Payment__binbank.class.php';
```
Задать настройки мерчанта в конструкторе класса.  
Рабочие настройки мерчанта выдаст ваш менеджер, а тестовые включены в файл.
```
$this->setToken('ТОКЕН');
$this->setKey('КЛЮЧ ШИФРОВАНИЯ');
$this->setCurrency('RUB');
$this->setMerchantName('НАЗВАНИЕ МАГАЗИНА');
$this->setCallbackUrl('https://АДРЕС ОБРАТНОГО ВЫЗОВА - CALLBACK');
$this->setReturnUrl('https://АДРЕС ПЕРЕНАПРАВЛЕНИЯ ПОСЛЕ ОПЛАТЫ');
$this->setApiUrl('https://АДРЕС API');
$this->setTimezone('Europe/Moscow');
```
Подключить стиль SCSS [button_style.scss](/button_style.scss),  
указать путь к [логотипу Бинбанка](/binbank_lil_logo.png) для отображения кнопки

## Использование
### Кнопка "Оплатить"
```
$Payment = new Payment__binbank();

//Чтобы включить отладку:
//$Payment->setDebugOn();

$Payment
  ->setOrderId( 'тестовый платёж' ) //строка - идентификатор платежа
  ->setAmountSum( 91 ) //сумма в рублях, без копеек
  ->setDescripton( 'Тестовое описание' ) //необязательно
  ->drawButton();
```
В результате должна получиться кнопка для оплаты с указанными параметрами:

![Screenshot](/screenshot.png)

### Обработать обратный запрос от банка
Пропишите обработчик:
```
$Payment->processCallback();
```
по адресу на вашем сайте, заданному в $this->setCallbackUrl('https : //АДРЕС ОБРАТНОГО ВЫЗОВА - CALLBACK');

## Поддержка
По этому PHP скрипту -- [прямо на GitHub](https://github.com/bridgemedia/php-binbank-client/issues)  
Поддержка БИНБАНК -- [www.binbank.ru/contacts](https://www.binbank.ru/contacts/)
