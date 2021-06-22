<?php
class ModelCheckoutGetsdek extends Model {
    public function addCity($data) {

        $this->db->query("INSERT INTO `" . DB_PREFIX . "delivery_sdek_city` 
            SET 
                city_name = '" . $data['cityName'] . "', 
                city_code = '" . (int)$data['city_code'] . "', 
                city_uuid = '" . $data['city_uuid'] . "', 
                country = '" . $data['country'] . "', 
                countryCode = '" . $data['countryCode'] . "',
                region = '" . $data['region'] . "', 
                regionCode = '" . (int)$data['regionCode'] . "', 
                regionCodeExt = '" . (int)$data['regionCodeExt'] . "', 
                regionFiasGuid = '" . $data['regionFiasGuid'] . "', 
                subRegion = '" . $data['subRegion'] . "', 
                paymentLimit = '" . $data['paymentLimit']. "'") ;

        $city_id = $this->db->getLastId();

        return $city_id;
    }
    public function addRegion($data) {

        $this->db->query("INSERT INTO 
                        `" . DB_PREFIX . "delivery_sdek_regions` 
                    SET 
                        regionName = '" . $data['regionName'] . "', 
                        prefix = '" . $data['prefix'] . "', 
                        regionCode = '" . $data['regionCode'] . "'") ;

        $region_id = $this->db->getLastId();

        return $region_id;
    }
    public function getCity($city) {
        $query = $this->db->query("
            SELECT * 
            FROM 
                " . DB_PREFIX . "delivery_sdek_city 
            LEFT JOIN 
                " . DB_PREFIX . "delivery_sdek_regions 
            ON 
                " . DB_PREFIX . "delivery_sdek_city.regionCode=" . DB_PREFIX . "delivery_sdek_regions.regionCode 
            WHERE 
                city_name LIKE '" . $city . "%'
        ");

        return $query->rows;
    }



    public function updateRegion($address_id, $country_id, $zone_id, $city) {
        $this->db->query("UPDATE " . DB_PREFIX . "address SET 
            city = '" . $city . "',
            country_id = '" . (int)$country_id . "',
            zone_id = '" . (int)$zone_id . "'   
         WHERE address_id  = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");

//        if (!empty($data['default'])) {
//            $this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
//        }
    }

    public function updateAddress($address_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "address SET 
            firstname = '" . $data['firstname'] . "',
            lastname = '" . $data['lastname'] . "',
            address_1 = '" . $data['address_1'] . "',
            address_2 = '" . $data['address_2'] . "'  
         WHERE address_id  = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
    }

    public function updatecustomerdelivery($customer_id, $data) {

        $this->db->query("UPDATE " . DB_PREFIX . "customer 
            SET 
                firstname = '" . $data['firstname'] . "', 
                lastname = '" . $data['lastname'] . "', 
                telephone = '" . $data['telephone'] . "'
            WHERE customer_id = '" . (int)$customer_id . "'
        ");

    }

    public function issetemailcustomer($email) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE email = '" . trim($email) . "'");

        return $query->row;
    }
}