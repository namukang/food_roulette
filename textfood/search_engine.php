<?php
    require_once('Ordrin/Ordrin.php');
	
    session_start();

	/*************************
		remove the lines below
	************************/

	$_REQUEST['email'] = 'shawn@shawn.com';
	$_REQUEST['first'] = 'shawn';
	$_REQUEST['last'] = 'shawn';
	
	$_REQUEST['addr'] = '192 Beale Street';
	$_REQUEST['city'] = 'San Francisco';
	$_REQUEST['zip'] = '94105';

	/********************
	to here
	******************/

	$dt = new dT("asap");
	$dt->asap();

	$api = new Ordrin('xHcvG3Ps4BGuKP2Ku8bTaA', 'https://r-test.ordr.in');
	
    main();
    orderASAP('323','', new Money(15));
	
	function orderASAP($rid, $menu_items, $tip) 
	{
		global $dt;
		global $api;
		$api->setCurrAcct('shawn@shawn.com','shawn');
		$user = new User();
		
		//$addr = new Address('126 1st Street','San Francisco','94105','', 'CA','4255907862','shawn');
		$addr = $user->getAddress('shawn');
		//$card = json_decode(json_decode($user->getCard($_REQUEST['cardnick']))['cc']);
		$card = $user->getCard('shawn');
		$order = new Order();
		$order->submit($rid, $menu_items, $tip, $dt, $_REQUEST['email'], $_REQUEST['first'], $_REQUEST['last'], $addr, 'shawn', 'shawn', 'shawn', 'shawn', $addr);
		// hopefully this works.
	}
	
    function parseOrder($request)
    {
        $chunks = explode(" ", $request);
        $food_type = $_REQUEST['food_type'];
        $max_money = $_REQUEST['money_amount'];
    
        for ($i = 0; $i < sizeof($chunks); $i++)
        {
            if (intval($chunks[$i]) != 0)
            {
                $max_money = $chunks[$i];
            }
            else if(substr($chunks[$i], 0, 1) == "$")
            {
                if (intval(substr($chunks[$i], 1)) != 0)
                {
                    $max_money = substr($chunks[$i], 1);
                }
            }
            else if ($food_type)
            {
                if ($chunks[$i])
                {
                    $food_type = $food_type." ".$chunks[$i];
                }
            }
            else
            {
                $food_type = $chunks[$i];
            }
        }
        
        $arr = array($food_type, $max_money);
        return $arr;
    }
    
	function get_restaurant_by_zip_code_and_type($type = null) {
		global $dt;
		global $api;
		$a = new Address($_REQUEST['addr'], $_REQUEST['city'], $_REQUEST['zip'], $_REQUEST['addr2'], $_REQUEST['state'], $_REQUEST['phone'], $_REQUEST['nick']);

		$r = new Restaurant();

		$list = $r->deliveryList($dt, $a);

		$type_array = explode(' ', $type);

		if ($type !== null && $type !== '') {
			foreach ($list as $restaurant) {
				foreach ($type_array as $genre) {
					if (array_search(strtoupper($genre), array_map('strtoupper', $restaurant->cu)) !== false) {
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

	function get_menu_by_restaurant_id($rid) 
	{
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
	
	function select_next_node($menu_division, $budget) 
	{
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
	
	function actual_budget($array) 
	{
        $budget = 0;
        if (count($array) == 0) {
          return $budget;
        }
		foreach ($array as $object) {
			$budget += $object->price;
			if (isset($object->children)) {
				$budget += actual_budget($object->children);
			}
		}

		return $budget;
	}
	
	function print_bill($array, $indent = '') {
	    $counter = 0;
		foreach ($array as $object) {
		    $counter++;
		    if ($indent == '')
		        echo '(' . $counter . ') ';
			echo $indent . $object->name;
			if ($object->price != 0)
			    echo ' -- $' . number_format($object->price, 2);
			if (isset($object->children)) {
				echo '<br />';
				print_bill($object->children, $indent . '    ');
			}
			echo '<br />';
		}
		if ($indent == '')
		{
		    echo "<br/>-------------------<br/>";
		    $tip = actual_budget($array) * .10;
		    echo "Tip:   $".number_format($tip, 2)."<br/>";
		    echo 'Total: $' . number_format(actual_budget($array)+$tip, 2)."<br/><br/>";
		}
	}
    
    function main()
    {
        echo "<block>";
        echo "<message><content>";
        
        if (!$_REQUEST['email'] ||
            !$_REQUEST['password'])
        {
            echo "Finish logging in first.";
            echo "</content></message>";
        }
        else
        {
            $arr = parseOrder($_REQUEST['sys_argument']);
            $food_type = $arr[0];
            $max_money = $arr[1];

            if (!$max_money)
            {
                echo "How much money would you like to spend?";
                echo "</content>";
                echo "<input name='money_amount'>";
                echo "<engine href='textfood/search_engine.php'/>";
                echo "</input>";
                echo "</message>";
                echo "<set name='food_type'>".$food_type."</set>";
            }
            else
            {
                $restaurants = get_restaurant_by_zip_code_and_type($food_type);
                if (sizeof($restaurants) == 0)
                {
                    echo "No results were found.";
                }
                else
                {
                    $restaurant = $restaurants[ rand(0, count($restaurants) - 1) ];
                    $menu = get_menu_by_restaurant_id($restaurant->id);
                    $foods = choose_random_food($menu, intval($max_money/1.10));
                    
                    echo "Prepared menu from ".$restaurant->na . ":<br/>";
                    
                    if (sizeof($foods) != 0) 
                    {
                        print_bill($foods);
                        echo "Reply:<br/>";
                        echo "<a query='dskang order' /> Accept <br/>";
                        echo "<a query='dskang find " . $food_type . " " . $max_money . "' /> Reshuffle";
                    }
                    else
                    {
                        echo "No results.<br/>";
                        echo "Perhaps food on menu at the restaurant was above your price range.<br/>";
                        echo "Please try again.<br/>";
                        echo "Reply:<br/>";
                        echo "<a query='dskang find " . $food_type . " " . $max_money . "' /> Reshuffle";
                    }
                }
                
                echo "</content></message>";
                echo "<set name='money_amount'></set>";
                echo "<set name='food_type'></set>";
            }
        }
        echo "</block>";
    } 
?>
