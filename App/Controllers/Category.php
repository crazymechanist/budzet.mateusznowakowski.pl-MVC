<?php

namespace App\Controllers;

use \App\Models\MoneyFlow;
use \Core\View;
use App\Flash;
use App\Controllers\Authenticated;

/**
 * Category controller
 *
 * PHP version 7.0
 */
 
class Category extends Authenticated{
	
	/**
	 * Exclude function from required authentication
	 *
	 * @return void
	 */
	public function __call($name, $args){
		$method = $name . 'Action';

		if ($method == 'validateCategoryAction') {
			call_user_func_array([$this, $method], $args);
		} elseif($method != 'validateCategoryAction') {
			parent::__call($name, $args);
		} else {
			throw new \Exception("Method $method not found in controller " . get_class($this));
		}
	}
	
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
	
	/**
	 * Show, edit and delete expense categories
	 *
	 * @return void
	 */
	public function showExpenseAction(){
		$type = "expense";
		$categories = MoneyFlow::returnAllCategoriesNamesOfCurrUser($type);

		View::renderTemplate('Category/show.html',[
			'type' => $type,
			'categories' => $categories
		]);
	}
	
	/**
	 * Show income categories
	 *
	 * @return void
	 */
	public function showIncomeAction(){
		$type = "income";
		$categories = MoneyFlow::returnAllCategoriesNamesOfCurrUser($type);

		View::renderTemplate('Category/show.html',[
			'type' => $type,
			'categories' => $categories
		]);
	}
	
	/**
	 * New category
	 *
	 * @return void
	 */
	public function newAction(){
		$name = $_POST['newCategory'];
		$type = $_POST['type'];
		if(MoneyFlow::newMoneyFlowCategory($name , $type)){
			Flash::addMessage('Operation successful');
		} else {
			Flash::addMessage('Operation unsuccessful' , Flash::WARNING);
		}
		$this->redirect("/category/show-$type");
	}

	/**
	 * Edit category
	 *
	 * @return void
	 */
	public function renameAction(){
		$type = $_GET['type'];
		$name = $_GET['name'];
		$id = MoneyFlow::findIdByCategoryName($name,$type)->id;
		View::renderTemplate('Category/rename.html',[
			'type'	=>	$type,
			'name'	=>	$name,
			'id'	=>	$id
		]);
	}
	
	/**
	 * Update category
	 *
	 * @return void
	 */
	public function updateAction(){
		$type = $_POST['type'];
		$name = $_POST['name'];
		$id = $_POST['id'];
		if	(MoneyFlow::updateMoneyFlowCategory($id , $name , $type)){
		
		Flash::addMessage('Changes saved');
		
		$this->redirect("/category/show-$type");
		} else {
		View::renderTemplate('category/rename.html', [
			'type'	=>	$type,
			'name'	=>	$name,
			'id'	=>	$id
		]);
		}
	}
	
	/**
	 * Confirm delete money flow
	 *
	 * @return void
	 */
	public function confirmDeleteAction(){
		$type = $_GET['type'];
		$name = $_GET['name'];
		$id = MoneyFlow::findIdByCategoryName($name , $type)->id;
		View::renderTemplate('category/confirm_delete.html', [
			'type'	=>	$type,
			'name'	=>	$name,
			'id'	=>	$id
		]);
	}
	
	/**
	 * Delete money flow
	 *
	 * @return void
	 */
	public function deleteAction(){
		$type = $_POST['type'];
		if(MoneyFlow::deleteMoneyFlowCategoryAndConnectedMF($_POST['id'])){
			Flash::addMessage('Operation successful');
		} else {
			Flash::addMessage('Operation unsuccessful' , Flash::WARNING);
		}
		$this->redirect("/category/show-$type");
	}
}