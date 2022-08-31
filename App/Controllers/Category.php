<?php

namespace App\Controllers;

use \App\Models\MoneyFlow;

/**
 * Category controller
 *
 * PHP version 7.0
 */
 
class Category extends \Core\Controller
{
	/**
	 * Validate if email is available (AJAX) for a new signup.
	 *
	 * @return void
	 */
	 
	public function validateCategoryAction(){
		 $is_valid = false;
		if(MoneyFlow::findIdByCategoryName($_GET['category'] ?? null)){
			$is_valid = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode($is_valid);
	}
}