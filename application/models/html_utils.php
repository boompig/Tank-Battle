<?php

/**
 * Static class to extend CI's HTML generation.
 */
class HTML_Utils extends CI_Model {
	/**
	 * Return a JS comment-string if pentesting is turned on, else return empty string.
	 */
	function pentestComment () {
		if (isset($_SESSION['pentest']) && $_SESSION['pentest']) {
			return "//";
		} else {
			// return "console.log('" . $_SESSION['pentest'] . "');";
			return "";
			// return "console.log('" . $_SESSION['pentest'] . "');";
		}	
	}
	
	/**
	 * Replicates form_open from CI form-helper, but also passed 
	 */
	function form_open($location) {
		// first attribute is location
		// second is a list of attributes
		// third is a list of hidden fields
		
		if (func_num_args() === 3 && func_get_arg(2)) {
			$hidden = func_get_arg(2);
		} else {
			$hidden = array();
		}
		
		if (func_num_args() >= 2 && func_get_arg(1)) {
			$attrs = func_get_arg(1);
		} else {
			$attrs = array();
		}
		
		$attrs["method"] = "POST";
		
		if (isset($_SESSION['pentest']) && $_SESSION['pentest']) {
			$attrs["pentest"] = $_SESSION['pentest'];
			$attrs['novalidate'] = $_SESSION['pentest'];
		}
		
		return form_open($location, $attrs, $hidden);
	}
	
	/**
	 * Return an associative array for everything that's needed for a text input with the given name.
	 * @param input_name The name of the input field.
	 */
	function get_input_array($input_name) {
		return array("name" => $input_name, "id" => $input_name);
	}
	
	/**
	 * Convert an associative array of attributes to a string.
	 * So given something like array('x' => 'y', 'z' => 'y'), return "x='y' z='y'"
	 */
	function attr_array_to_str ($arr) {
		$a2 = array();
		
		foreach ($arr as $key => $value) {
			if ($key == "class" && is_array($value)) {
				$a2[] = "class='" . implode(" ", $value) . "'";
			} else {
				$a2[] = "$key='$value'";
			}
		}
		
		return join(" ", $a2);
	}
	
	/**
	 * Given an associative array, return an options string to add as 4th parameter to input_dropdown.
	 * The options string simply glues together the key-value pairs of the associative array, and glues the array together with spaces.
	 * Add an HTML5 'required' attribute as well.
	 * @param $arr The associative array
	 */
	function get_dropdown_options($arr) {
		// $arr['required'] = 'required';
		return HTML_Utils::attr_array_to_str($arr);
	}
	
	/**
	 * Surround the given item with the given tag.
	 */
	function surround($item, $tag) {
		return "<$tag>$item</$tag>";
	}
	
	/**
	 * Improved version of CI's li method.
	 * @param $item Name of the list item
	 * @param $attrs Associative list of attributes
	 */
	function li($item, $attrs) {
		// parameter is an associative array of attributes for the li
		$listAttrs = HTML_Utils::attr_array_to_str($attrs);
		return "<li $listAttrs>$item</li>";
	}
	
	function open_div () {
		if (func_num_args() > 0) {
			$arr = func_get_arg(0);
			$attrs = " " . HTML_Utils::attr_array_to_str($arr);
		} else {
			$attrs = "";
		}
		
		
		return "<div" . $attrs . ">";
	}
	
	function close_div() {
		return "</div>";
	}
	
	function span ($contents, $arr) {
		$attrs = HTML_Utils::attr_array_to_str($arr);
		return "<span $attrs>$contents</span>";
	}
}

?>