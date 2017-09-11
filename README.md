# php-binbank-client
Класс PHP клиента онлайн-оплаты через интернет-эквайринг "БИНБАНК" для интернет-магазинов  
[www.binbank.ru](https://www.binbank.ru/corporate-clients/e-commerce/internet-acquiring/)
И запрос на платёж и обратный запрос от банка подписываются специальной безопасной подписью.
  
  
## Требования
Версия PHP >= 7.0,  
и CSS в этом примере для Bootstrap 3, но вы можете легко заменить стили на свои

## Установка
Включить PHP файл класса [Payment__binbank.class.php](/Payment__binbank.class.php) в свой проект:
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
$this->setCallbackUrl('https://АДРЕС ОБРАТНОГО ВЫЗОВА НА ВАШЕМ САЙТЕ - CALLBACK');
$this->setReturnUrl('https://АДРЕС ПЕРЕНАПРАВЛЕНИЯ НА ВАШЕМ САЙТЕ "Вернуться в магазин" ПОСЛЕ ОПЛАТЫ');
$this->setApiUrl('https://АДРЕС API');
$this->setTimezone('Europe/Moscow');
```
Указать путь к [логотипу Бинбанка](/binbank_lil_logo.png) для отображения на кнопке  
Подключить стиль SCSS [button_style.scss](/button_style.scss),  


## Использование
### Кнопка "Оплатить"
```
$Payment = new Payment__binbank();

//Чтобы включить отладку, раскомментируйте:
//$Payment->setDebugOn();

//Вывести кнопку оплаты, например:
$Payment
  ->setOrderId( 'тестовый платёж #1501813130' ) //строка -- уникальный идентификатор платежа
  ->setAmountSum( 91 ) //сумма в рублях, без копеек
  ->setDescripton( 'Тестовое описание' ) //необязательно
  ->drawButton();
```
В результате должна получиться кнопка для оплаты с указанными параметрами:

![Screenshot](/screenshot.png)
  
И форма оплаты по нажатию на кнопку:

![Screenshot2](/screenshot2.png)
  
Выполненные платежи вы увидите в своём личном кабинете мерчанта.

### Обработка обратного запроса (callback) от БИНБАНКа
После совершения платежа (успешного или нет), банк отправляет вам информацию о транзакции POST-запросом на адрес, указанный при [установке](#Установка) в 
$this->setCallbackUrl('https:// АДРЕС ОБРАТНОГО ВЫЗОВА НА ВАШЕМ САЙТЕ - CALLBACK');
  
Пропишите по этому адресу обработчик:
```
$Payment = new Payment__binbank();  
$Payment->processCallback();
```
Затем в методе processCallback() в файле [Payment__binbank.class.php](/Payment__binbank.class.php) добавьте нужную вам обработку обратного запроса.  
  
Например, для сохранения информации о транзакциях, создайте MySQL таблицу из файла [mysql - sys_payment.sql](/mysql%20-%20sys_payment.sql) и поставьте на выполнение к вашей БД SQL запрос, содержащийся в методе processCallback

## Поддержка
* [Документация API](/OWS-MdmPayWebAPI1.2-110417-1700-64.pdf) (раздел WEB HTML), свежую версию ищите на [сайте БИНБАНК](https://www.binbank.ru/corporate-clients/e-commerce/internet-acquiring/#b1v4)  
* По этому скрипту -- [пишите на GitHub](https://github.com/bridgemedia/php-binbank-client/issues)  
* Поддержка БИНБАНК -- [www.binbank.ru/contacts](https://www.binbank.ru/contacts/)  
  
  
  
EOF
