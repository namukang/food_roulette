<?php
	//name, and phone number
	//person's email and password
	//zip code
	//restaurant type

	/*************************
		remove the lines below
	************************/

	$_REQUEST['addr'] = '192 Beale Street';
	$_REQUEST['city'] = 'San Francisco';
	$_REQUEST['zip'] = '94105';


	/********************
	to here
	******************/

	require_once('Ordrin/Ordrin.php');
	require('dump_my_array.php');

	$dt = new dT('asap');
	$dt->asap();

	$api = new Ordrin('xHcvG3Ps4BGuKP2Ku8bTaA', 'https://r-test.ordr.in');







	$restaurant = get_restaurant_by_zip_code_and_type('Mexican Italian');

	//dump_my_array($restaurant);

	$restaurant = $restaurant[ rand(0, count($restaurant) - 1) ];
	echo $restaurant->id . ': ' . $restaurant->na . '<br /><br />';

	$menu = get_menu_by_restaurant_id($restaurant->id);
	//dump_my_array($menu);
	$my_menu = choose_random_food($menu, '20');


	dump_my_array( $my_menu );
	//dump_name( $my_menu );

	function dump_name($array, $original_layer = true) {
		foreach ($array as $object) {
			echo $object->name;
			if (isset($object->children)) {
				echo ' -> ';
				dump_name($object->children, false);
			}
			if ($original_layer == true)
				echo '<br />';
		}
	}

	$price = 0;
	foreach($my_menu as $item) {
		$price += $item->price;
	}
	echo '<br />YOUR COST: ' . $price;







	function get_restaurant_by_zip_code_and_type($type = null) {
		global $dt;
		global $api;
		$a = new Address($_REQUEST['addr'], $_REQUEST['city'], $_REQUEST['zip'], $_REQUEST['addr2'], $_REQUEST['state'], $_REQUEST['phone'], $_REQUEST['nick']);

		$r = new Restaurant();

		$list = $r->deliveryList($dt, $a);

		$type_array = explode(' ', $type);

		if ($type !== null && $type !== '') {
			foreach ($list as $restaurant) {
				foreach ($type_array as $type) {
					if (array_search($type, $restaurant->cu) !== false) {
						$filtered_list[] = $restaurant;
					}
				}
			}
			return $filtered_list;
		}
		else {
			return $list;
		}
	}

	function get_menu_by_restaurant_id($rid) {
		global $dt;
		global $api;

		$r = new Restaurant();
		$list = $r->details($rid);

		return $list->menu;
	}

	function choose_random_food($menu, $budget) {
		$appetizers = array('Appetizers', 'Starters', 'Appys', 'Appies', 'Chips', 'Sides');
		$drinks = array('Drinks', 'Sodas', 'Beverages');
		$desserts = array('Desserts');

		$attempts = 0;
		$category_counter;
		$items_selected = 0;
		$original_budget = $budget;

		while ($budget > 0 && $attempts < 100) {
			$random = rand(0, count($menu) - 1);

			switch ($items_selected % 4) {
				case 0:
					$pass = true;
					foreach($appetizers as $appetizer) {
						if ($appetizer == $menu[$random]->name)
							$pass = false;
					}
					if (!$pass) {
						foreach($desserts as $dessert) {
							if ($dessert == $menu[$random]->name)
								$pass = false;
						}
					}
					if (!$pass) {
						foreach($drinks as $drink) {
							if ($drink == $menu[$random]->name)
								$pass = false;
						}
					}
					break;
				case 1:
					$pass = false;
					foreach($appetizers as $appetizer) {
						if ($appetizer == $menu[$random]->name)
							$pass = true;
					}
					break;
				case 2:
					$pass = false;
					foreach($desserts as $dessert) {
						if ($dessert == $menu[$random]->name)
							$pass = true;
					}
					break;
				case 3:
					$pass = false;
					foreach($drinks as $drink) {
						if ($drink == $menu[$random]->name)
							$pass = true;
					}
					break;
			}
			if ($pass) {
				$new_item = select_next_node($menu[$random], $budget);
				if ($new_item != null) {
					$my_menu[] = $new_item;
					$budget -= $my_menu[count($my_menu) - 1]->price;
					$items_selected++;
					$attempts = 0;
					$category_counter = 0;
				}
			}

			$attempts++;
			$category_counter++;
			if ($category_counter > 10) {
				$items_selected++;
				$category_counter = 0;
			}
		}

		if (actual_budget($my_menu) > $original_budget)
			$my_menu = choose_random_food($menu, $original_budget);

		return $my_menu;
	}
	function select_next_node($menu_division, $budget) {
		$attempts = 0;
		while ($attempts < 20) {
			$random = rand(0, count($menu_division->children) - 1);
			//echo $random . '<br />';
			//var_dump($menu_division->children);
			//dump_my_array($menu_division->children[$random]);
			//echo '<br /><br />' . isset($menu_division->children[$random]->price) . '<br /><br />';
			if (isset($menu_division->children[$random]->price) && $menu_division->children[$random]->price != 0) {
				if ($menu_division->children[$random]->price < $budget) {
					$value_to_return = $menu_division->children[$random]; // take whole object with its children

					if (isset($menu_division->children[$random]->children) &&
						$menu_division->children[$random]->children[0]->price == 0) { //  selected meal has free options...say...soup
							$first_level_children = $menu_division->children[$random]->children;
							if (isset($first_level_children[0]->children)) {  //  ie, there are different types of soup
								for ($i=0; $i < count($first_level_children); $i++) {
									$menu_division->children[$random]->children[$i]->children = array($first_level_children[$i]->children[ rand(0, count($first_level_children[$i]->children) - 1) ]);
								}
							}
							else {  //  no children - we're at the bottom level
								$menu_division->children[$random]->children = array($first_level_children[ rand(0, count($first_level_children) - 1) ]);
							}
					}
					return $menu_division->children[$random];
				}
			}
			else {
				$menu[] = select_next_node($menu[$random], $budget);
			}

			//echo '&nbsp;&nbsp' . $attempts . '<br />';
			$attempts++;
		}
		return null;
	}
	function actual_budget($array) {
		$budget = 0;
		foreach ($array as $object) {
			$budget += $object->price;
			if (isset($object->children)) {
				$budget += actual_budget($object->children);
			}
		}

		return $budget;
	}
?>