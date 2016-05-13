<?php
class NMI{
    private $username = "demo";
    private $password = "password";
    public function nmiVoid($transactionid){
        $username = $this->username;
        $password = $this->password;
        $query = "username=$username&password=$password&type=void&transactionid=$transactionid";
        return $this->nmiPost($query);
    }
    public function nmiAuth($amount, $cc, $ccexp, $firstname, $lastname, $address, $city, $state, $zip, $phone, $email){
        $firstname = urlencode($firstname);
        $lastname = urlencode($lastname);
        $address = urlencode($address);
        $city = urlencode($city);
        $username = $this->username;
        $password = $this->password;
        $ip = $_SERVER['REMOTE_ADDR'];
        $query = "username=$username&password=$password&amount=$amount&tax=0.00&shipping=0.00&type=auth&ipaddress=$ip&ccnumber=$cc&ccexp=$ccexp&";
        $query.= "firstname=$firstname&lastname=$lastname&address1=$address&city=$city&state=$state&zip=$zip&phone=$phone&email=$email";
        return $this->nmiPost($query);
    }
    function nmiCapture($transaction_id, $amount){
        $username = $this->username;
        $password = $this->password;
        $query = "username=$username&password=$password&type=capture&transactionid=$transaction_id&amount=$amount";
        return $this->nmiPost($query, true);    
    }
    private function nmiPost($post, $debug = false){
        if($debug){
            echo $post; die;
        }
        $ch = curl_init("https://secure.networkmerchants.com/api/transact.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $res = curl_exec($ch);
        if(!empty($res)){
            $result = array();
            $response = explode("&", $res);
            foreach($response as $v){
                $pieces = explode("=", $v);
                $result[$pieces[0]] = $pieces[1];
            }
            return $result;
        }else{
            return false;
        }   
    }
}
?>