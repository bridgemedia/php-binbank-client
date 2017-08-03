# php-binbank-client
Класс PHP для онлайн-оплаты через "БИНБАНК" ( www.binbank.ru )

## Требования
PHP >= 7.0.0

## Установка
Задать настройки мерчанта в конструкторе класса
```
$this->setToken('ТОКЕН');
$this->setKey('КЛЮЧ ШИФРОВАНИЯ');
$this->setCurrency('RUB');
$this->setMerchantName('НАЗВАНИЕ МАГАЗИНА');
$this->setCallbackUrl('https://АДРЕС ОБРАТНОГО ВЫЗОВА (CALLBACK)');
$this->setReturnUrl('https://АДРЕС ПЕРЕНАПРАВЛЕНИЯ ПОСЛЕ ОПЛАТЫ');
$this->setApiUrl('https://АДРЕС API');
$this->setTimezone('Europe/Moscow');
```

Включить файл класса в свой проект
```
require_once 'Payment__binbank.class.php';
```

## Использование
### Кнопка "Оплатить"
```
$Payment = new Payment__binbank();

//Чтобы включить отладку:
//$Payment->setDebugOn();

$Payment
  ->setOrderId( 'тестовый платёж #'.$test_num ) //строка - номер платежа
  ->setAmountSum( 100 ) //сумма в рублях
  ->setDescripton( 'Тестовое описание' ) //необязательно
  ->drawButton();
```
### Обработать обратный запрос от банка
по адресу, заданному в $this->setCallbackUrl('');
```
$Payment->processCallback();
```

