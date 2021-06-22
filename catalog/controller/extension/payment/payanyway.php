<?php
class ControllerExtensionPaymentPayanyway extends Controller {

    private function cleanProductName($value)
    {
        $result = preg_replace('/[^0-9a-zA-Zа-яА-Я ]/ui', '', htmlspecialchars_decode($value));
        $result = trim(mb_substr($result, 0, 20));
        return $result;
    }

    public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		if (isset($order_info['email']) && $order_info['email']) {
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('config_order_status_id'));
		}

        if ($this->config->get('payment_payanyway_demo') == true)
            $data['action'] = 'https://demo.moneta.ru/assistant.htm';
        else
            $data['action'] = 'https://www.payanyway.ru/assistant.htm';

		$data['sid'] = $this->config->get('payment_payanyway_account');
		$payanyway_secret = $this->config->get('payment_payanyway_secret');

		$data['currency_code'] = $order_info['currency_code'];
        if ($data['currency_code'] == 'RUR') {
            $data['currency_code'] = 'RUB';
        }

		$data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $data['amount'] = number_format($order_info['total'], 2, '.', '');

		$data['cart_order_id'] = $this->session->data['order_id'];
		$data['card_holder_name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		$data['street_address'] = $order_info['payment_address_1'];
		$data['city'] = $order_info['payment_city'];

		if ($order_info['payment_iso_code_2'] == 'US' || $order_info['payment_iso_code_2'] == 'CA') {
			$data['state'] = $order_info['payment_zone'];
		} else {
			$data['state'] = 'XX';
		}

		$data['zip'] = $order_info['payment_postcode'];
		$data['country'] = $order_info['payment_country'];
		$data['email'] = $order_info['email'];
		$data['phone'] = $order_info['telephone'];

		if ($this->cart->hasShipping()) {
			$data['ship_street_address'] = $order_info['shipping_address_1'];
			$data['ship_city'] = $order_info['shipping_city'];
			$data['ship_state'] = $order_info['shipping_zone'];
			$data['ship_zip'] = $order_info['shipping_postcode'];
			$data['ship_country'] = $order_info['shipping_country'];
		} else {
			$data['ship_street_address'] = $order_info['payment_address_1'];
			$data['ship_city'] = $order_info['payment_city'];
			$data['ship_state'] = $order_info['payment_zone'];
			$data['ship_zip'] = $order_info['payment_postcode'];
			$data['ship_country'] = $order_info['payment_country'];
		}

		$data['products'] = array();

        $clientEmail = $order_info['email'];
        $inventory = array();

		$products = $this->cart->getProducts();
		foreach ($products as $product) {
			$data['products'][] = array(
				'product_id'  => $product['product_id'],
				'name'        => $product['name'],
				'description' => $product['name'],
				'quantity'    => $product['quantity'],
				'price'       => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value'], false)
			);

            // собрать данные для чека
            $inventory[] = array("n" => $this->cleanProductName($product['name']), "p" => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value'], false), "q" => $product['quantity'], "t" => 1105);

		}

        $productsTototal = 0;
        foreach ($inventory AS $item) {
            $productsTototal = $productsTototal + floatval($item['p']) * floatval($item['q']);
        }

        if (floatval($productsTototal) != floatval($data['amount'])) {
            $discountRate = floatval($data['amount']) / floatval($productsTototal);
            $newInvenrory = array();
            foreach ($inventory AS $item) {
                $item['p'] = round(floatval($item['p']) * $discountRate, 5);
                $newInvenrory[] = $item;
            }
            $inventory = $newInvenrory;
        }

        $kassaData = array("customer" => $clientEmail, "items" => $inventory);

        $jsonData = preg_replace_callback('/\\\\u(\w{4})/', function ($matches) {
            return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
        }, json_encode($kassaData));

        $jsonData = str_replace('"', "'", $jsonData);

        $data['payanyway_kassa_data'] = $jsonData;

        if ($this->config->get('payment_payanyway_test')) {
			$data['demo'] = 'Y';
            $payanyway_test = '1';
		} else {
			$data['demo'] = '';
            $payanyway_test = '0';
		}

        $data['payanyway_test'] = $payanyway_test;

        // prepare signature
        // MNT_SIGNATURE = MD5(MNT_ID + MNT_TRANSACTION_ID + MNT_AMOUNT + MNT_CURRENCY_CODE + MNT_SUBSCRIBER_ID + ТЕСТОВЫЙ РЕЖИМ + КОД ПРОВЕРКИ ЦЕЛОСТНОСТИ ДАННЫХ)
        $data['MNT_SIGNATURE'] = md5( $data['sid'] . $data['cart_order_id'] . $data['amount'] . $data['currency_code'] . $payanyway_test . $payanyway_secret );

		$data['lang'] = $this->session->data['language'];

		$data['return_url'] = $this->url->link('extension/payment/payanyway/callback', '', true);
//        $data['mnt_return_url'] = $this->url->link('common/home', '', true);
		$data['mnt_success_url'] = $this->url->link('extension/payment/payanyway/success');
		$data['mnt_fail_url'] = $this->url->link('checkout/checkout', '', 'SSL');
		$data['mnt_return_url'] = $this->url->link('checkout/checkout', '', 'SSL');

		return $this->load->view('extension/payment/payanyway', $data);
	}

	public function success()
	{
		$this->load->model('checkout/order');
		/* set pending status if not payed yet order will be confirmed otherway confirm will do nothing */
		$this->model_checkout_order->addOrderHistory($_REQUEST['MNT_TRANSACTION_ID'], $this->config->get('payment_payanyway_order_status_id'), 'Order confirmed');

		$this->response->redirect($this->url->link('checkout/success'));
	}

    const paw_kassa_VAT0     = 1104;  // НДС 0%
    const paw_kassa_VAT10    = 1103;  // НДС 10%
    const paw_kassa_VAT18    = 1102;  // НДС 18%
    const paw_kassa_VATNOVAT = 1105;  // НДС не облагается
    const paw_kassa_VATWR10  = 1107;  // НДС с рассч. ставкой 10%
    const paw_kassa_VATWR18  = 1106;  // НДС с рассч. ставкой 18%

    private static function monetaPayURLResponse($mnt_id, $mnt_transaction_id, $mnt_data_integrity_code, $success = false,
                                                 $repeatRequest = false, $echo = true, $die = true, $kassa_inventory = null, $kassa_customer = null, $kassa_delivery = null)
    {
        if ($success === true)
            $resultCode = '200';
        elseif ($repeatRequest === true)
            $resultCode = '402';
        else
            $resultCode = '500';
        $mnt_signature = md5($resultCode.$mnt_id.$mnt_transaction_id.$mnt_data_integrity_code);
        $response = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        $response .= "<MNT_RESPONSE>\n";
        $response .= "<MNT_ID>{$mnt_id}</MNT_ID>\n";
        $response .= "<MNT_TRANSACTION_ID>{$mnt_transaction_id}</MNT_TRANSACTION_ID>\n";
        $response .= "<MNT_RESULT_CODE>{$resultCode}</MNT_RESULT_CODE>\n";
        $response .= "<MNT_SIGNATURE>{$mnt_signature}</MNT_SIGNATURE>\n";
        if (!empty($kassa_inventory) || !empty($kassa_customer) || !empty($kassa_delivery))
        {
            $response .= "<MNT_ATTRIBUTES>\n";
            foreach (array('INVENTORY' => $kassa_inventory, 'CUSTOMER' => $kassa_customer, 'DELIVERY' => $kassa_delivery) as $k => $v)
                if (!empty($v))
                    $response .= "<ATTRIBUTE><KEY>{$k}</KEY><VALUE>{$v}</VALUE></ATTRIBUTE>\n";
            $response .= "</MNT_ATTRIBUTES>\n";
        }
        $response .= "</MNT_RESPONSE>\n";
        if ($echo === true)
        {
            header("Content-type: application/xml");
            echo $response;
        }
        else
            return $response;
        if ($die === true)
            die;
        return '';
    }

    // Pay URL script
    // http://opencart-site/index.php?route=extension/payment/payanyway/callback
	public function callback() {
		$this->load->model('checkout/order');

        // MNT_SIGNATURE = MD5(MNT_ID + MNT_TRANSACTION_ID + MNT_OPERATION_ID + MNT_AMOUNT + MNT_CURRENCY_CODE + MNT_SUBSCRIBER_ID + MNT_TEST_MODE + КОД ПРОВЕРКИ ЦЕЛОСТНОСТИ ДАННЫХ)

        // get Pay URL data
        $MNT_ID = $this->getVar('MNT_ID');
        $MNT_TRANSACTION_ID = $this->getVar('MNT_TRANSACTION_ID');
        $MNT_OPERATION_ID = $this->getVar('MNT_OPERATION_ID');
        $MNT_AMOUNT = $this->getVar('MNT_AMOUNT');
        $MNT_CURRENCY_CODE = $this->getVar('MNT_CURRENCY_CODE');
        $MNT_TEST_MODE = $this->getVar('MNT_TEST_MODE');
        $MNT_SIGNATURE = $this->getVar('MNT_SIGNATURE');

        $showResult = 'FAIL';
        if ($MNT_TRANSACTION_ID && $MNT_SIGNATURE) {
            $mnt_dataintegrity_code = $this->config->get('payment_payanyway_secret');
            $check_signature = md5($MNT_ID . $MNT_TRANSACTION_ID . $MNT_OPERATION_ID . $MNT_AMOUNT . $MNT_CURRENCY_CODE . $MNT_TEST_MODE . $mnt_dataintegrity_code);

            if ($MNT_SIGNATURE == $check_signature) {
                $order_id = $MNT_TRANSACTION_ID;
                $order_info = $this->model_checkout_order->getOrder($order_id);
                if ($order_info) {
                    $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_payanyway_order_status_id'), 'Payment complete');

                    // begin of kassa.payanyway.ru code
                    $inventoryPositions = array();
                    $this->load->model('account/order');
                    /** @var ModelAccountOrder $modelAccountOrder */
                    $modelAccountOrder = $this->model_account_order;
                    $products = $modelAccountOrder->getOrderProducts($order_id);
                    $orderTotals = $modelAccountOrder->getOrderTotals($order_id);
                    foreach ($products as $product)
                    {
                        $inventoryPositions[] = array(
                            'name' => trim(preg_replace("/&?[a-z0-9]+;/i", "", htmlspecialchars($product['name']))),
                            'price' => $product['price'],
                            'quantity' => $product['quantity'],
                            'vatTag' => self::paw_kassa_VATNOVAT,
                        );
                    }
                    $kassa_delivery = null;
                    foreach ($orderTotals as $orderTotal)
                        if ($orderTotal['code'] == 'shipping')
                        {
                            $kassa_delivery = $orderTotal['value'];
                            break;
                        }
                    $kassa_inventory = json_encode($inventoryPositions);
                    self::monetaPayURLResponse($_REQUEST['MNT_ID'], $_REQUEST['MNT_TRANSACTION_ID'],
                        $mnt_dataintegrity_code, true, false, true, true, $kassa_inventory, $order_info['email'],
                        $kassa_delivery);

                    exit;
                }
            }
        }

        echo $showResult;
	}

    private function getVar($name) {
        $value = false;
        if (isset($_POST[$name])) {
            $value = $_POST[$name];
        }
        else if (isset($_GET[$name])) {
            $value = $_GET[$name];
        }

        return $value;
    }
}