<?php
	
	require_once("Ordrin.php");
	$api = new Ordrin('xHcvG3Ps4BGuKP2Ku8bTaA', 'https://r-test.ordr.in');
	$api->_url = 'http://localhost';
	//
	// $menu_items:
	// 		[[ menu_id, # qty, price ]
	//		 ...]
	//
	
	function orderASAP($rid, $menu_items, $tip, $credit_card_num) 
	{
		$dt = new dT();
		$dt->asap();
		$addr = $_REQUEST['address'];
		$user = new User();
		$card = json_decode(json_decode($user->getCard($_REQUEST['cardnick']))['cc']);
		$order = new Order();
		$order->submit($rid, $tip, $dt, $_REQUEST['email'], $_REQUEST['first'], $_REQUEST['last'], $addr, $card['name'], $credit_card_num, $card['cvc'], $card['expiry'], $addr);
		// hopefully this works.
	}
?>