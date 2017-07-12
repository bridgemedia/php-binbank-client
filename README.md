# php-binbank-client
Класс PHP для онлайн-оплаты через "БИНБАНК" ( www.binbank.ru )

## Требования
PHP >= 7.0.0

## Установка
Включить файл класса
```
include '';
```

## Использование
### Кнопка "Оплатить"
```
$Payment = new Payment__binbank();

$Payment
  ->setOrderId( 'тестовый платёж #'.$test_num ) //строка - номер платежа
  ->setAmountSum( 100 ) //сумма в рублях
  ->setDescripton( 'Тестовое описание' ) //необязательно
  ->drawButton();
```
### Обработать обратный запрос от банка
```
$Payment->processCallback( $_POST );
```

