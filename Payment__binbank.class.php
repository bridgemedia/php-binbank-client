<?php
/**
 * User: Алексей Бабак, +7 967-066-07-42
 * Date: 10.07.2017
 * Time: 14:09
 *
 * Платежи через "Бинбанк" www.binbank.ru
 * Версия PHP -- от 7.0
 *
 */
class Payment__binbank
{

	private $_token;
	private $_key;
	private $_order_id;
	private $_amount;
	private $_currency;
	private $_descripton;
	private $_customer;
	private $_additional_info;
	private $_callback_url;
	private $_return_url;
	private $_api_url;
	private $_merchant_name;
	private $_timezone;

	private $_debug;


	public function __construct() {

		if (version_compare(PHP_VERSION, '7.0.0', '<')) {
			exit ('Для корректной работы платежей нужна версия PHP не ниже 7.0');
		}

		//Тестовые данные:
		$this->setToken('BA:07:D3:CB:BC:37:82:4C:97:06:93:F3:A5:64:DF:F8');
		$this->setKey('C7B901B4FD0146CC8F6DE9FF3DCDDD5C');
		$this->setApiUrl('https://mdm-webapi-mdmpay-financial-staging.mdmbank.ru/web/v1/payment');
		
		$this->setCurrency('RUB');
		$this->setMerchantName('МОЙ МАГАЗИН');
		$this->setCallbackUrl('https://example.com/payment/callback/binbank/');
		$this->setReturnUrl('https://example.com/payment/return/binbank/');
		$this->setTimezone('Europe/Moscow');

//		$this->setCustomer([
//			'customer_id' => (string)'',
//			'full_name' => [
//				'first_name' => (string)'',
//				'middle_name' => (string)'',
//				'last_name' => (string)'',
//			],
//
//			'address' => [
//				'country_code' => (int)'', //Страна отправителя. Цифровой ISO 3166-1 код
//				'city' => (string)'', //Город
//				'address_line' => (string)'',
//				'postal_code' => (string)'',
//			],
//
//		]);

		return $this;
	}


	/**
	 * @param array $params
	 * @return string
	 *
	 * Сериализация параметров для подписи
	 * Пример удачной сериализации:
	 * token=BA:07:D3:CB:BC:37:82:4C:97:06:93:F3:A5:64:DF:F8order_id=MYORDER42request_date=2016-07-19T15:54:38+03:0 0amount.value=70.00amount.currency=RUB
	 */
	private function serealise_params(array $params ) {

		$query = '';
		foreach ( $params as $key=>&$val ) {
			if ( is_array( $val ) ){
				foreach ( $val as $sub_key=>&$sub_val ) {

					if ( $key.'.'.$sub_key == 'amount.value' ) {
						$sub_val = sprintf("%0.2f", $sub_val);

					} elseif ( $key.'.'.$sub_key == 'status.error_description' ) {
						continue;

					}

					$query .= $key.'.'.$sub_key.'='.$sub_val;
				}
			} else {
				$query .= $key.'='.$val;
			}

		}

		return $query;

	}

	public function drawButton()
	{
		/*
		 * При регистрации продавцу предоставляется пара: токен token и ключ secret.
		 * secret служит для подписи запросов, token для ссылки на ключ.
		 * Формируется строка data из полей запроса (каждый запрос содержит свой набор полей для подписи) в формате "имя_поля1=значение_поля1 имя_поля2=значение_поля2...".
		 * Порядок полей должен соответствовать порядку в таблице из описания запроса.
		 * Если поле подписываемое, но не обязательное, то оно входит в подпись, если оно присутствует в запросе и имеет непустое значение.
		 * С помощью ключа secret и данных data формируется подпись signature по алгоритму HMAC-SHA256 (бинарные данные и ключ для алгоритма берутся из соответствующих строк UTF8)
		 * Поля token и signature со значением полученной подписи в формате HEX (регистр не важен) добавляются в JSON запрос
		 */
		try {
			$dt = new DateTime();
			$dt->setTimeZone( new DateTimeZone( $this->getTimezone() ) );
//			$payment_time = $dt->format( 'Y-m-d\TH:i:s' ).'+03:00'; //' yyyy-MM-ddTHH:mm:sszzz ';
			$payment_time = $dt->format( 'Y-m-d\TH:i:sP' ); //' yyyy-MM-ddTHH:mm:sszzz ';

		} catch ( Exception $exception ) {
			echo '<pre>'.print_r( $exception, true ).'</pre>';
			exit( 'Неверно указана Time Zone || Для совершение платежей необходимо расширение PHP DateTime' );
		}

		$query_params = [];
		$query_params = array_merge( $query_params, [ 'token' => $this->getToken() ] );
		$query_params = array_merge( $query_params, [ 'order_id' => $this->getOrderId() ] );
		$query_params = array_merge( $query_params, [ 'request_date' => $payment_time ] );
		$query_params = array_merge( $query_params, [ 'amount' => $this->getAmount() ] );

		if ( $this->_debug )
			$this->debug_echo('Параметры платежа:<br><pre>'.print_r( $query_params , true ).'</pre>');

		$signature_params = $this->serealise_params( $query_params );
		if ( $this->_debug )
			$this->debug_echo('Сериализация для подписи:<br><pre>'.$signature_params.'</pre>');

		$signature = hash_hmac( 'sha256', $signature_params, $this->getKey() );
		if ( $this->_debug )
			$this->debug_echo( 'Подпись SHA256:<br><pre>'.$signature.'</pre>' );

		$json_array = [
			(array)$query_params,
			'description' => $this->getDescripton(),
//			'additional_info' => $this->getAdditionalInfo(),
			'callback_url' => $this->getCallbackUrl(),
			'return_url' => $this->getReturnUrl(),
			'merchant_name' => $this->getMerchantName(),
			'signature' => $signature,
		];

		//$json_string = json_fix_cyr( json_encode( $json_array , JSON_PRETTY_PRINT  | JSON_FORCE_OBJECT) );
		$json_string = json_fix_cyr( json_encode( $json_array , JSON_FORCE_OBJECT) );

		/*
		 * PHP json_encode принудительно оборачивает в объект ассоциативне массивы и добавляет индекс
		 * описание проблемы: http://php.net/manual/ru/function.json-encode.php
		 * поэтому вырезаем лишний уровень из строки
		 */
		$json_string = str_replace( '{"0":{"token":', '{"token":', $json_string );
		$json_string = str_replace( '"currency":"RUB"}}', '"currency":"RUB"}', $json_string );

		if ( $this->_debug )
			$this->debug_echo('PaymentRequest в  JSON:<br><pre>'.$json_string.'</pre>');

		$PaymentRequest = base64_encode( $json_string );
		if ( $this->_debug )
			$this->debug_echo('PaymentRequest для POST-запроса в base64:<br><pre>'.$PaymentRequest.'</pre>');

		$button_id = str2url( $this->getOrderId() );
		$html_button_string = '
			<form method="post" action="'.$this->getApiUrl().'" target="_bin" accept-charset="UTF-8" rel="noopener" id="payment_form_'.$button_id.'"  enctype="application/x-www-form-urlencoded">
				<input type="hidden" name="Request" value="'.$PaymentRequest.'">
				<button form="payment_form_'.$button_id.'" type="submit" class="btn btn-default btn__payment btn__payment__binbank" id="payment_btn_'.$button_id.'">
					<div class="btn__payment__label" role="label" for="payment_btn_'.$button_id.'">Оплата <price>'.$this->getAmountSum().' '.$this->getCurrency().'</price> через</div>
					<img src="/s/img/binbank/binbank_lil_logo.png">
				</button>
			</form>
		';

		echo $html_button_string;

	}

	/**
	 * Обработать callback (обратный сигнал) от банка
	 */
	public function processCallback( $params ){
		$json_string = file_get_contents('php://input');
		$callback = json_decode( $json_string, true );
		$this->log( json_fix_cyr( json_encode( $callback ) ) );

		/**
		 * для подписи оставляем только подписываемые параметры,
		 * перечисленные в документации TransactionInfo
		 */
		$signature_params = $callback;
			unset( $signature_params['type'] );
			unset( $signature_params['description'] );
			unset( $signature_params['source_card'] );
			unset( $signature_params['destination_card'] );
			unset( $signature_params['customer'] );
			unset( $signature_params['additional_info'] );
			unset( $signature_params['addendum'] );
			unset( $signature_params['trans_date'] );
			unset( $signature_params['posting_date'] );
			unset( $signature_params['original_transaction_id'] );
			unset( $signature_params['callback_url'] );
			unset( $signature_params['return_url'] );
			unset( $signature_params['request_card_token'] );
			unset( $signature_params['recurring'] );
			unset( $signature_params['signature'] );

		$signature_params = $this->serealise_params( $signature_params );
		$this->log( $signature_params );

		$signature = hash_hmac( 'sha256', $signature_params, $this->getKey() );
		$this->log( $signature );

		//Верна ли подпись
		$signature_is_correct = ( strcasecmp( $signature, $callback['signature'] ) === 0 );

		//Успешна ли транзакция
		$transaction_is_success = ( $callback['status']['type'] == 'success' );

		//В зависимости от статуса и свойства транзакции присваем ей вес в своей системе и обрабатываем дальше

	}


	/**
	 * логируем коллбеки от банка в файл
	 * @param string $string
	 */
	private function log(string $string ){
		if( !file_put_contents( '/home/bridgemedia/sites/bridgemedia.ru/sys/modules/payment/callback_log.txt', print_r( $string, true )."\n\n", FILE_APPEND ) ){
			$this->debug_echo( 'Предупреждение: не удалось записать файл журнала оплаты' );
		}
	}

	/**
	 * Включить отладку
	 */
	public function setDebugOn() : Payment__binbank
	{
		$this->_debug = TRUE;
		return $this;
	}

	private function debug_echo( $string) {
		echo '
			<div style="background-color: #0b0b0b; color: #00a3cc; padding: 10px; max-width: 100%; margin-bottom: 20px;">
				<small style="color:slategray">debug:</small><br>
				'.print_r( $string, true ).'
			</div>
		';
	}

	// ------------- ниже только геттеры и сеттеры --------------
	/**
	 * @return mixed
	 */
	private function getToken() : string
	{
		return $this->_token;
	}

	/**
	 * @param string $token
	 */
	private function setToken( string $token)
	{
		$this->_token = $token;
	}

	/**
	 * @return mixed
	 */
	private function getOrderId()
	{
		return $this->_order_id;
	}


	/**
	 * @param int $sum
	 * @return Payment__binbank
	 *
	 * Сумма в основных ед. валюты с возможным разделителем '.' для копеек.
	 * Три и более десятичных знака приведут к ошибке.
	 *
	 * Мы не хотим использовать копейки, поэтому -- int
	 *
	 */
	public function setAmountSum(int $amount_sum ) : Payment__binbank {
		$amount = [
			'value' => round( abs( $amount_sum ), 2, PHP_ROUND_HALF_EVEN ),
			'currency' => $this->getCurrency()
		];
		$this->setAmount( $amount );

		return $this;

	}

	public function setOrderId( $order_id )  : Payment__binbank {
		$this->_order_id = trim( strip_tags( $order_id ) );
		return $this;
	}

	/**
	 * @return mixed
	 */
	private function getAmount()
	{
		return $this->_amount;
	}

	/**
	 * @return mixed
	 */
	private function getAmountSum()
	{
		$amount = $this->getAmount();
		return $amount['value'];
	}

	/**
	 * @param mixed $amount
	 */
	private function setAmount( array $amount)
	{
		$this->_amount = $amount;
	}

	/**
	 * @return mixed
	 */
	private function getCurrency()
	{
		return $this->_currency;
	}

	/**
	 * @param mixed $currency
	 */
	private function setCurrency($currency)
	{
		$this->_currency = $currency;
	}

	/**
	 * @return mixed
	 */
	private function getDescripton()
	{
		return $this->_descripton;
	}

	/**
	 * @param string $descripton
	 * @return Payment__binbank
	 */
	public function setDescripton(string  $descripton) : Payment__binbank {
		$this->_descripton = $descripton . '';
		return $this;
	}


	/**
	 * @param array $customer
	 * @return Payment__binbank
	 *
	 *
	 */
	public function setCustomer(array $customer) : Payment__binbank {
		$customer['customer_id'] = (int)$customer['customer_id'];
		$customer['full_name']['first_name'] = strip_tags( $customer['full_name']['first_name'] );
		$customer['full_name']['middle_name'] = strip_tags( $customer['full_name']['middle_name'] );
		$customer['full_name']['last_name'] = strip_tags( $customer['full_name']['last_name'] );

		$this->_customer = $customer;
		return $this;
	}

	/**
	 * @param mixed $additional_info
	 */
	private function setAdditionalInfo($additional_info)
	{
		$this->_additional_info = $additional_info;
	}

	/**
	 * @return mixed
	 */
	private function getCallbackUrl()
	{
		return $this->_callback_url;
	}

	/**
	 * @param mixed $callback_url
	 */
	private function setCallbackUrl($callback_url)
	{
		$this->_callback_url = $callback_url;
	}

	/**
	 * @return mixed
	 */
	private function getReturnUrl()
	{
		return $this->_return_url;
	}

	/**
	 * @param mixed $return_url
	 */
	private function setReturnUrl($return_url)
	{
		$this->_return_url = $return_url;
	}

	/**
	 * @return mixed
	 */
	private function getMerchantName()
	{
		return $this->_merchant_name;
	}

	/**
	 * @param mixed $merchant_name
	 */
	private function setMerchantName($merchant_name)
	{
		$this->_merchant_name = $merchant_name;
	}

	/**
	 * @return mixed
	 */
	private function getKey() : string
	{
		return $this->_key;
	}

	/**
	 * @param mixed $key
	 */
	private function setKey($key)
	{
		$this->_key = $key;
	}

	/**
	 * @return mixed
	 */
	private function getApiUrl() : string
	{
		return $this->_api_url;
	}

	/**
	 * @param mixed $api_url
	 */
	private function setApiUrl($api_url)
	{
		$this->_api_url = $api_url;

	}

	/**
	 * @return mixed
	 */
	public function getTimezone() : string
	{
		return $this->_timezone;
	}

	/**
	 * @param mixed $timezone
	 * http://php.net/manual/ru/timezones.europe.php
	 *
	 */
	public function setTimezone($timezone)
	{
		$this->_timezone = $timezone;

	}

}
