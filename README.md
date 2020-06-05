
<p><a href="https://pei.is/" rel="nofollow noindex noopener external"><img src="https://pei.is/media/1689/pei_logo_.png" alt="PEI" height="100"></a> </p>
<h1>+</h1>
<p>
<a href="https://php.net/" rel="nofollow noindex noopener external"><img src="https://www.php.net/images/logos/php-logo.svg" alt="PHP" height="80"></a></p>

## About this Repo
PEI payment gateway integration with PHP. api.pei.is - iceland

## About pei
<p>
<font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Pei is a payment method owned by </font></font><a href="http://www.greidslumidlun.is/"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Greiðsluiðlun ehf</font></font></a><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"> . </font><font style="vertical-align: inherit;">Payment intermediation is the development and operation of payment solutions, credit purchases and debt management for companies, NGOs, pension funds and public bodies.</font></font><br/>
<font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Payment intermediation also operates the Norra registration and payment system, which enables sports clubs, organizations, municipalities and companies to simplify registration and payment for courses, events and various other services.</font></font>
</p>

## How to install package

- Go to your project directory in CLI

- Update composer using 
```
  $: `composer update`
```

- Run below command to your PHP project to install package.
```
  $: `composer require coderubix/php-pei`
```

- Package is install successfully. 

## Example

```
<?php

//If you are using core PHP then first include the class from relevent directory from your code.
require_once "../src/pei/PeiPayment.php";

use Pei\PeiPayment as PeiPayment;

$config = [
        'live' => [
            'auth_url' => 'https://auth.pei.is/core/connect/token/',
            'order_url' => 'https://api.pei.is/api/orders/',
            'payment_url' => 'https://gattin.pei.is/',
            'client_id' => 'XXXXXXXXXXXXXXX', // replace this with your client id provided by pei.is
            'secret_key' => 'XXXXXXXXXXXXXXX', // replace this with your secret provided by pei.is
            'merchantid' => 1, // replace this with your merchant id provided by pei.is
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
            'success_url' => 'http://localhost/xxxxx.php'
        ]
    ];

$PeiPayment = new PeiPayment($config, 'sandbox');

// get auth token
//$auth_token = $PeiPayment->authorize();

$ssn = 5554452454;

$order_info = [
	'order_id' => uniqid(),
	'user' => [
		'firstname' => 'ashok',
		'lastname' => 'chandrapal',
		'email' => 'developer7039@gmail.com'
	],
  	//optional
	'tax' => [
		'label' => 'Vat',
		'amount' => 100
	],
	//optional
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

```


## License

This repository is software licensed under the [MIT license](http://opensource.org/licenses/MIT).
