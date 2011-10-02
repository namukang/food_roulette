<?php
	function dump_my_array($your_array, $current_offset = '') {
		foreach($your_array as $key => $value){
			if (is_array($value)) {
				echo $current_offset . $key . ' => <br />';
				dump_my_array($value, $current_offset . '&nbsp;&nbsp;&nbsp;&nbsp;');
			}
			elseif (is_object($value)) {
				echo $current_offset . $key . ' (obj)-> <br />';
				dump_my_array($value, $current_offset . '&nbsp;&nbsp;&nbsp;&nbsp;');
			}
			else {	
				echo $current_offset . $key . ' => ' . $value . '<br />';
			}
		}
	}
?>