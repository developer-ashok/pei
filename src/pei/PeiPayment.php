<?php 

namespace Pei;

class PeiPayment
{
    public $data;

    public function __construct($config, $mode){
        $this->data = $config[$mode];
    }

    // Get authorization header from Pei
    public function authorize() {        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL =>$this->data['auth_url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=client_credentials&scope=externalapi",
            CURLOPT_HTTPHEADER => array(
                "authorization: Basic " . base64_encode("{$this->data['client_id']}:{$this->data['secret_key']}"),
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            return false;
        } else {
            $json_response = json_decode($response);
            return $json_response->token_type . ' ' . $json_response->access_token;
        }
    }

    public function create_order_json($ssn, $order_info) {

        if((int)$ssn < 1000000000){
            return ['message'=> 'invalid Social Security Number!', 'error'=> true, 'data'=> []];
        }
        if((string)trim(@$order_info['order_id']) == "" || 
           (string)trim(@$order_info['user']['firstname']) == "" ||
           (string)trim(@$order_info['user']['lastname']) == "" || 
            !isset($order_info['items']) ||
            !is_array($order_info['items']) ||
            count($order_info['items']) == 0){
            return ['message'=> 'Something wrong with order!', 'error'=> true, 'data'=> []];
        }

        // Personal info
        $first_name = html_entity_decode($order_info['user']['firstname'], ENT_QUOTES, 'UTF-8');
        $last_name = html_entity_decode($order_info['user']['lastname'], ENT_QUOTES, 'UTF-8');
        $email = $order_info['user']['email'];

        $fields = array(
            'merchantId' => $this->data['merchantid'],
            'amount' => 0,
            // Redirect here if it is a success
            'successReturnUrl' => $this->data['success_url'],
            // Redirect here if the user cancels
            'cancelReturnUrl' => $this->data['cancel_url'],
            // Url that Pei postbacks to
            'postbackUrl' => $this->data['postback_url'],
            'reference' => $order_info['order_id'],
            'buyer' => array(
                'name' => "{$first_name} {$last_name}",
                'email' => $email,
                'ssn' => $ssn
            )
        );

        $total = 0;
        
        // Get and add product info to $fields
        foreach ($order_info['items'] as $product) {
            $amount = $product['price'] * $product['quantity'];

            $fields['items'][] = array(
                'name' => $product['name'],
                'quantity' => $product['quantity'],
                'amount' => $amount,
                'unitPrice' => $product['price']
            );      

            $total = $total + $amount;    
        }

        // checked $total > 0 here to make sure that the item are exists
        if(isset($order_info['tax']['amount']) && (int)$order_info['tax']['amount'] > 0 && $total > 0){
            $total = $total + $order_info['tax']['amount'];
            $fields['items'][] = array(
                'name' => $order_info['tax']['label'],
                'quantity' => 1,
                'amount' => $order_info['tax']['amount'],
                'unitPrice' => $order_info['tax']['amount']
            );   
        }

        // checked $total > 0 here to make sure that the item are exists
        if(isset($order_info['shipping']['amount']) && (int)$order_info['shipping']['amount'] > 0 && $total > 0){
            $total = $total + $order_info['shipping']['amount'];
            $fields['items'][] = array(
                'name' => $order_info['shipping']['label'],
                'quantity' => 1,
                'amount' => $order_info['shipping']['amount'],
                'unitPrice' => $order_info['shipping']['amount']
            );   
        }

        $fields['amount'] = $total;

        return json_encode($fields);
    }

    public function get_order_id($json_order, $auth_token) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->data['order_url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $json_order,
            CURLOPT_HTTPHEADER => array(
                "authorization: " . $auth_token,
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            return false;
        } else {
            $json_response = json_decode($response);
            return $json_response->orderId;
        }
    }

    // redirect to pei with order detail for payment
    public function pay($ssn, $order_info){
        // get auth token
        $auth_token = $this->authorize();
        $json_order = $this->create_order_json($ssn, $order_info);
        
        if(is_array($json_order) && isset($json_order['error']) && $json_order['error'] == true){
            return $json_order; // error
        }else{
            $order_id = $this->get_order_id($json_order, $auth_token);    
            header('Location: '.$this->data['payment_url'] . $order_id);
        }
    }

}