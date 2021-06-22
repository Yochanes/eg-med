<?php
class ControllerCheckoutGetsdek extends Controller {
    public function index() {

        $this->load->model('checkout/getsdek');

        $json = [];
        if($this->request->post['getcity']){

            $json = $this->model_checkout_getsdek->getCity($this->request->post['getcity']);
            $this->response->setOutput(json_encode($json));

        }
    }

    public function getcustomer(){

        $customer_data = [];

        if ($this->customer->isLogged()) {

            $this->load->model('account/address');
            $this->load->model('account/customer');

            $address_user = $this->model_account_address->getAddresses();
            $customer = $this->model_account_customer->getCustomer($this->customer->getId());


            foreach($address_user as $address){
                $customer_data['address_id'] = $address['address_id'];
                $customer_data['firstname'] = $address['firstname'];
                $customer_data['lastname'] = $address['lastname'];

                if(empty($this->session->data['cdek']['pvz'])){

                    if (empty($address['address_1'])):
                        $customer_data['address_1'] = '';
                    else:
                        $customer_data['address_1'] = $address['address_1'];
                    endif;

                }
            }

            $this->load->model('account/customer');
            $customer = $this->model_account_customer->getCustomer($this->customer->getId());

            $customer_data['customer_id'] = $customer['customer_id'];
            $customer_data['telephone'] = $customer['telephone'];
            $customer_data['email'] = $customer['email'];
            $customer_data['auth'] = true;

        }else{
            $customer_data['firstname'] = '';
            $customer_data['lastname'] = '';
            if(empty($this->session->data['cdek']['pvz'])){
                $customer_data['address_1'] = '';
            }
            $customer_data['address_1'] = '';
            $customer_data['customer_id'] = '';
            $customer_data['telephone'] = '';
            $customer_data['email'] = '';
            $customer_data['auth'] = false;

        }

        $this->response->setOutput(json_encode($customer_data));

//        print_r($address_user);
//        print_r($customer_data);
    }


    public function getcustomerdelivery(){

        $getcustomerdelivery = [];

        if($this->session->data['shipping_method']['title']){
            $getcustomerdelivery['title'] = $this->session->data['shipping_method']['title'];
        }

        if($this->session->data['shipping_method']['code']){
            $getcustomerdelivery['code'] = $this->session->data['shipping_method']['code'];
        }

        if($this->session->data['shipping_method']['cost']){
            $getcustomerdelivery['cost'] = $this->session->data['shipping_method']['cost'];
        }

        if($this->session->data['shipping_method']['text']){
            $getcustomerdelivery['text'] = $this->session->data['shipping_method']['text'];
        }

        if($this->session->data['cdek']['city']){
            $getcustomerdelivery['id_city'] = $this->session->data['cdek']['city'];
        }

        if(!empty($this->session->data['cdek']['pvz'])){
            $getcustomerdelivery['pvz'] = $this->session->data['cdek']['pvz'];
        }

        if(!empty($this->session->data['cdek']['pvzaddress'])){
            $getcustomerdelivery['pvzaddress'] = $this->session->data['cdek']['pvzaddress'];
        }
        if(!empty($this->session->data['cdek']['tariff'])){
            $getcustomerdelivery['tariff'] = $this->session->data['cdek']['tariff'];
        }


        $this->response->setOutput(json_encode($getcustomerdelivery));

    }

    public function setaddress(){

        $res_data = [];
        $res_data['error'] = false;

        if(isset($this->request->post['address'])){
            $addr = trim($this->request->post['address']);
        }else{
            if(!empty($this->session->data['cdek']['pvzaddress'])){
                $addr = $this->session->data['cdek']['pvzaddress'];
            }
        }

        $telephone = trim($this->request->post['telephone']);
        $firstname = trim($this->request->post['firstname']);
        $lastname = trim($this->request->post['lastname']);
        $email = trim($this->request->post['email']);

//        $adr = 'Новочеркасская 36';

        if(isset($addr) && empty($addr)){
            $res_data['error'] = true;
            $res_data['address'] = 'Обязательное поле';
        }

        if(empty($telephone) || !is_numeric($telephone)){
            $res_data['error'] = true;
            $res_data['telephone'] = 'Обязательное поле и должно содержать цифры';
//            $res_data['telephone'] = gettype((int)$telephone);
        }

        if(empty($firstname)){
            $res_data['error'] = true;
            $res_data['firstname'] = 'Обязательное поле';
        }

        if(empty($lastname)){
            $res_data['error'] = true;
            $res_data['lastname'] = 'Обязательное поле';
        }


        if($res_data['error']){
            $this->response->setOutput(json_encode($res_data));
        }else{

            unset($res_data['error']);

            $addresdata = [];

            $addresdata['address_1'] = $addr;
            $addresdata['address_2'] = $addr;
            $addresdata['firstname'] = $firstname;
            $addresdata['lastname'] = $lastname;
            $addresdata['email'] = $email;

            $this->load->model('account/customer');
            $this->load->model('account/address');

            $customer_total = $this->model_account_customer->getTotalCustomersByEmail($addresdata['email']);
            $res_data['totalcustomer'] = $customer_total;


            //Если пользователь зарегистирован
            if ($this->customer->isLogged()) {

                $this->load->model('account/address');
                $address_user = $this->model_account_address->getAddresses();

                foreach($address_user as $address){
                    $addres_id = $address['address_id'];
                }

                if ($this->customer->isLogged()) { //Если пользователь зарегестрирован обновляем данные его адреса
                    $this->load->model('checkout/getsdek');
                    $this->model_checkout_getsdek->updateAddress($addres_id, $addresdata);
                }

            }
            //Если пользователь НЕ зарегистрирован
            else{

                $addresdata['password'] = 'test';
                $addresdata['telephone'] = $telephone;

                $customer_id = $this->model_account_customer->addCustomer($addresdata);

                $addresdata['company'] = '';
                $addresdata['postcode'] = '';
                $addresdata['city'] = $this->session->data['shipping_address']['city'] ;
                $addresdata['zone_id'] = $this->session->data['shipping_address']['zone_id'];
                $addresdata['country_id'] = $this->session->data['payment_address']['country_id'];

                if($customer_total > 1){
                    $res_data['error'] = true;
                    $res_data['email'] = 'Пользователь с таким email зарегистрирован, авторизуйтесь или используйте другой email';
                }else{

                    $res_data['error'] = false;
                    $res_data['email'] = '';

                    $address_id = $this->model_account_address->addAddress($customer_id, $addresdata);

                    // Clear any previous login attempts for unregistered accounts.
                    $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

                    $this->customer->login($addresdata['email'], $addresdata['password'] );


                    //Формирование письма об успешном заказе=================
                    $mailSMTP = new SendMailSmtpClass('robot@eg-med.ru', 'ZqaXwsCed123456', 'ssl://smtp.yandex.ru', 465, "utf-8");
                    $headers  = "MIME-Version: 1.1" . PHP_EOL;
                    $headers .= "Content-type: text/html; charset=utf-8" . PHP_EOL;
                    $subject = "EG-med.ru - Успешная регистрация";
                    $body = "<p>Благодарим за регистрацию в интернет-магазине Екатерины Голдиной EG-med.ru! Вход был выполнен автоматически.</p>";
                    $body .= "<p>Вы можете войти с другого устройства, используя свой E-mail и пароль, по <a href='https://eg-med.ru/index.php?route=account/login'>ссылке</a></p>";
                    $from[0] = "ROBOT"; // Имя отправителя
                    $from[1] = "robot@eg-med.ru"; // почта отправителя
                    $to = $this->request->post['email'];

                    $result =  $mailSMTP->send($to, $subject, $body, $from);

                    unset($this->session->data['guest']);
                }


            }



            $this->response->setOutput(json_encode($res_data));
        }

    }

    public function issetemailcustomer(){

        $this->load->model('checkout/getsdek');

        $issetemail = $this->model_checkout_getsdek->issetemailcustomer($this->request->post['email']);


        if($issetemail){
            $json['error'] = 'Пользователь с таким Email зарегистрирован';
            $json['email'] = $issetemail;
            $this->response->setOutput(json_encode($json));
        }else{
            $json['success'] = '';
            $json['email'] = $issetemail;
            $this->response->setOutput(json_encode($json));

        }

    }

    public function setcustomerdelivery(){

        $datacustomer = [];

        $datacustomer['telephone'] = $this->request->post['telephone'];
        $datacustomer['firstname'] = $this->request->post['firstname'];
        $datacustomer['lastname'] = $this->request->post['lastname'];
        $datacustomer['address'] = $this->request->post['address'];
        $datacustomer['address_id'] = $this->request->post['address_id'];

        if ($this->customer->isLogged()) {

            $this->load->model('checkout/getsdek');
            $this->model_checkout_getsdek->updatecustomerdelivery($this->customer->getId(), $datacustomer);
            $this->model_checkout_getsdek->updateAddress($datacustomer['address_id'], $datacustomer['address']);

        }else{

            //Если пользователь не зарегистрирован то регистрируем
            $this->load->model('account/customer');
            $customer_id = $this->model_account_customer->addCustomer($this->request->post);


        }

    }

    public function addcust(){

        $datacustomer = [];

        $datacustomer['telephone'] = "999999999999";
        $datacustomer['firstname'] = "ggggggggggg";
        $datacustomer['lastname'] = 'oooooooooo';
        $datacustomer['password'] = '';

        $datacustomer['email'] = "1231@23123.123";

        $this->load->model('account/customer');
        $customer_id = $this->model_account_customer->addCustomer($datacustomer);

        $datacustomer['address_1'] = 'address';
        $datacustomer['city'] = 'city';
        $datacustomer['country_id'] = '176';
        $datacustomer['zone_id'] = '2780';
        $datacustomer['company'] = '';
        $datacustomer['address_2'] = '';
        $datacustomer['postcode'] = '';


        $this->load->model('account/address');

        if($customer_id){
            $address_id = $this->model_account_address->addAddress($customer_id, $datacustomer);
        }



        echo $customer_id;
        echo $address_id;

    }

    public function setdelivery(){


        unset($this->session->data['cdek']['pvz']);
        unset($this->session->data['cdek']['pvzaddress']);
        unset($this->session->data['cdek']['tariff']);

        $this->load->model('localisation/zone');
        $this->load->model('account/address');

        $address_user = $this->model_account_address->getAddresses();

        $addres = [];

        foreach($address_user as $address){
            $addres_id = $address['address_id'];
        }

        $zone_ru = $this->model_localisation_zone->getZonesByCountryId(176);

        if($this->request->post['city'] && $this->request->post['region']){
            $this->session->data['payment_address']['city'] = $this->request->post['city'] ;
            $this->session->data['shipping_address']['city'] = $this->request->post['city'] ;


            foreach($zone_ru as $zone){
                if($zone['name'] == $this->request->post['region'] && $zone['status'] == 1){

                    $this->session->data['payment_address']['zone_id'] = $zone['zone_id'];
                    $this->session->data['payment_address']['zone'] = $zone['name'];
                    $this->session->data['payment_address']['zone_code'] = $zone['code'];
                    $this->session->data['payment_address']['country_id'] = 176;
                    $this->session->data['payment_address']['country'] = 'Россия';
                    $this->session->data['payment_address']['iso_code_2'] = 'RU';
                    $this->session->data['payment_address']['iso_code_3'] = 'RUS';

                    $this->session->data['shipping_address']['zone_id'] = $zone['zone_id'];
                    $this->session->data['shipping_address']['zone'] = $zone['name'];
                    $this->session->data['shipping_address']['zone_code'] = $zone['code'];

                    if ($this->customer->isLogged()) { //Если пользователь зарегестрирован обновляем данные региона и города
                        $this->load->model('checkout/getsdek');
                        $this->model_checkout_getsdek->updateRegion($addres_id, 176, $zone['zone_id'], $this->request->post['city']);
                    }


                }
            }

            $this->response->setOutput($this->request->post['region']);

        }
    }

    public function getsession(){
        print_r($this->session->data);
    }



//    public function add() {
//
//        $this->load->model('checkout/getsdek');
//
////        $page_sity = 5973;
//        $page_сity = 10;
//        $page_size_MAX = 9;
//
//
//            $cities = simplexml_load_file('https://integration.cdek.ru/v1/location/cities?page='.$page_сity.'&size=4000&countryCode=RU');
////            $cities = simplexml_load_file('https://integration.cdek.ru/v1/location/cities?cityName=%D0%A1%D0%B0%D0%BD%D0%BA%D1%82-%D0%9F%D0%B5%D1%82%D0%B5%D1%80%D0%B1%D1%83%D1%80%D0%B3');
//
//            $city_one = [];
//
//            foreach($cities as $city){
//
//                $city_one['cityName'] = $city->attributes()->cityName;
//                $city_one['city_code'] = $city->attributes()->city_code;
//                $city_one['city_uuid'] = $city->attributes()->city_uuid;
//                $city_one['country'] = $city->attributes()->country;
//                $city_one['countryCode'] = $city->attributes()->countryCode;
//                $city_one['region'] = $city->attributes()->region;
//                $city_one['regionCode'] = $city->attributes()->regionCode;
//                $city_one['regionCodeExt'] = $city->attributes()->regionCodeExt;
//                $city_one['regionFiasGuid'] = $city->attributes()->regionFiasGuid;
//                $city_one['subRegion'] = $city->attributes()->subRegion;
//                $city_one['paymentLimit'] = $city->attributes()->paymentLimit;
//
////                $region_data = simplexml_load_file('https://integration.cdek.ru/v1/location/regions?countryCode=RU&regionCode='.$city->attributes()->regionCode);
////
////                $city_one['prefix'] = $region_data->Region->attributes()->prefix;
//
//                echo $page_сity." - ".$city_one['cityName']."<br>";
//
//                $this->model_checkout_getsdek->addCity($city_one);
//            }
//
//
//    }


//    public function addregion() {
//
//        echo "<button>Получить регионы</button>";
//
//        $region_data = simplexml_load_file('https://integration.cdek.ru/v1/location/regions?countryCode=RU');
//
////        echo "<pre>";
////        print_r($region_data);
////        echo "</pre>";
//
//        $this->load->model('checkout/getsdek');
//
//        $region_one = [];
//
//        foreach($region_data as $region){
//
//            $region_one['regionName'] = $region->attributes()->regionName;
//            $region_one['prefix'] = $region->attributes()->prefix;
//            $region_one['regionCode'] = $region->attributes()->regionCode;
//
//            $this->model_checkout_getsdek->addRegion($region_one);
//
//        }
//
//    }
//

}