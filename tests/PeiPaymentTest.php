<?php 
error_reporting(E_ALL);

if(isset($_POST)){
	var_dump($_POST);
}

require_once "../src/pei/PeiPayment.php";

// use Pie\PeiPayment as PeiPayment;
use Pei\PeiPayment as PeiPayment;

$config = [
        'live' => [
            'auth_url' => 'https://auth.pei.is/core/connect/token/',
            'order_url' => 'https://api.pei.is/api/orders/',
            'payment_url' => 'https://gattin.pei.is/',
            'client_id' => 'XXXXXXXXXXXXXXX',
            'secret_key' => 'XXXXXXXXXXXXXXX',
            'merchantid' => 1, //XXXXX
            'postback_url' => '',
            'cancel_url' => '',
            'success_url' => ''
        ],
        'sandbox' => [
            'auth_url' => 'https://authstaging.pei.is/core/connect/token/',
            'order_url' => 'https://externalapistaging.pei.is/api/orders/',
            'payment_url' => 'https://gattinstaging.pei.is/',
            'client_id' => 'democlient',
            'secret_key' => 'demosecret',
            'merchantid' => 1,
            'postback_url' => '',
            'cancel_url' => '',
            'success_url' => 'http://localhost/packages/pei/tests/PeiPaymentTest.php'
        ]
    ];

$PeiPayment = new PeiPayment($config, 'sandbox');


//check postback and order detail using order id
// $postback_data = json_decode(file_get_contents('php://input'));
// $orderId = "Un6luxbtVYnBkNRcb5OSOuSQO8ZRlwXj"; //$postback_data->orderId
// $returnData = $PeiPayment->postback($orderId);
// echo "<pre>";
// print_r($returnData);
// exit();

// get auth token
//$auth_token = $PeiPayment->authorize();

$ssn = 5554452454;

$order_info = [
	'order_id' => uniqid(),
	'user' => [
			'firstname' => 'alex',
			'lastname' => 'martin',
			'email' => 'mail@malinator.com'
	],
	'tax' => [
		'label' => 'Vat',
		'amount' => 100
	],
	'shipping' => [
		'label' => 'Shipping',
		'amount' => 50
	],
	'items' => [
		[
			'name' => 'item 1',
			'code' => '#FFER23', //optional
	        'quantity' => 20,	        
	        'price' => 100
	    ],
	    [
			'name' => 'item 2',	        
	        'quantity' => 30,	        
	        'price' => 150
	    ]    
	]	
];

$PeiPayment->pay($ssn, $order_info);


//create order json
//$json_order = $PeiPayment->create_order_json($ssn, $order_info);
//$get_order_id = $PeiPayment->get_order_id($json_order, $auth_token); 
//echo $get_order_id;

exit();