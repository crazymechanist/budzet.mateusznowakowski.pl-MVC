<?php

namespace App\Controllers;

use \App\Models\MoneyFlow;
use \App\Models\MoneyFlowCategory;
use \Core\View;
use \App\Flash;
use \App\Controllers\Authenticated;

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
		if(MoneyFlowCategory::findIdByName($_GET['category'] ?? null)){
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
		$categories = MoneyFlowCategory::returnAll($type);

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
		$categories = MoneyFlowCategory::returnAll($type);

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
		$category = new MoneyFlowCategory($_POST);
		if($category->save()){
			Flash::addMessage('Operation successful');
		} else {
			Flash::addMessage('Operation unsuccessful.', Flash::WARNING);
		}
		$this->redirect("/category/show-$category->type");
	}

	/**
	 * Edit category
	 *
	 * @return void
	 */
	public function renameAction(){
		$name = $_GET['name'];
		$type = $_GET['type'];
		$id = MoneyFlowCategory::findIdByName($name,$type)->id;
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
		$category = new MoneyFlowCategory;
		if	($category->update($_POST)){
		Flash::addMessage('Changes saved');
		$this->redirect('/Category/show-'.$_POST['type']);
		} else {
		View::renderTemplate('Category/rename.html', [
			'type'	=>	$_POST['type'],
			'name'	=>	$_POST['name'],
			'id'	=>	$_POST['id']
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
		$id = MoneyFlowCategory::findIdByName($name , $type)->id;
		View::renderTemplate('Category/confirm_delete.html', [
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
		$category = new MoneyFlowCategory($_POST);
		if($category->delete($category->id)){
			Flash::addMessage('Operation successful');
		} else {
			Flash::addMessage('Operation unsuccessful' , Flash::WARNING);
		}
		$this->redirect('/Category/show-'.$category->type);
	}

  /**
	 * Write out categories
	 *
	 * @return void
	 */
	public function listAction(){
		$categories = MoneyFlowCategory::returnLimits();
    header('Content-Type: application/json', true, 200);
    echo json_encode($categories);
	}

}
