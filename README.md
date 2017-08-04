# php-binbank-client
Класс PHP клиента для онлайн-оплаты через интернет-эквайринг "БИНБАНК" для интернет-магазинов  
[www.binbank.ru](https://www.binbank.ru/corporate-clients/e-commerce/internet-acquiring/)
  
  
## Требования
Версия PHP >= 7.0,  
и CSS в этом примере для Bootstrap 3, но вы можете заменить стили на свои.

## Установка
Включить PHP файл класса [Payment__binbank.class.php](/Payment__binbank.class.php) в свой проект
```
require_once 'Payment__binbank.class.php';
```
Задать настройки мерчанта в конструкторе класса  
(рабочие настройки выдаст ваш менеджер, а тестовые включены в файл):
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
указать путь к [логотипу Бинбанка](/binbank_lil_logo.png) для отображения на кнопке

## Использование
### Кнопка "Оплатить"
```
$Payment = new Payment__binbank();

//Чтобы включить отладку, раскомментируйте:
//$Payment->setDebugOn();

//Вывести кнопку оплаты:
$Payment
  ->setOrderId( 'тестовый платёж' ) //строка - идентификатор платежа
  ->setAmountSum( 91 ) //сумма в рублях, без копеек
  ->setDescripton( 'Тестовое описание' ) //необязательно
  ->drawButton();
```
В результате должна получиться кнопка для оплаты с указанными параметрами:

![Screenshot](/screenshot.png)

### Обработка обратного запроса (callback) от БИНБАНКа
Пропишите обработчик:
```
$Payment->processCallback();
```
по адресу на вашем сайте, заданному при [установке](#Установка) в $this->setCallbackUrl('АДРЕС ОБРАТНОГО ВЫЗОВА');  
в методе processCallback() в файле [Payment__binbank.class.php](/Payment__binbank.class.php) добавьте нужную вам обработку обратного запроса.

## Поддержка
[Документация API](/OWS-MdmPayWebAPI1.2-110417-1700-64.pdf), свежую версию ищите на [сайте БИНБАНК](https://www.binbank.ru/corporate-clients/e-commerce/internet-acquiring/#b1v4)
По этому скрипту -- [пишите на GitHub](https://github.com/bridgemedia/php-binbank-client/issues)  
Поддержка БИНБАНК -- [www.binbank.ru/contacts](https://www.binbank.ru/contacts/)
  
    
      
        
          
EOF
