<?php

namespace App\Controllers;

use Core\View;
use App\Flash;
use App\Models\MoneyFlow;
use App\Models\MoneyFlowFilteringSorting;
use App\Controllers\Authenticated;

/**
* Items controller (example)
*
* PHP version 7.0
*/

class Item extends Authenticated{
	
	/**
	 * Add a new expense item
	 *
	 * @return void
	 */
	public function newExpenseAction(){
		$type = "expense";
		$categories = MoneyFlow::returnAllCategoriesNamesOfCurrUser($type);

		View::renderTemplate('Item/new.html',[
			'type' => $type,
			'categories' => $categories
		]);
	}
	
	/**
	 * Add a new expense item
	 *
	 * @return void
	 */
	public function newIncomeAction(){
		$type = "income";
		$categories = MoneyFlow::returnAllCategoriesNamesOfCurrUser($type);

		View::renderTemplate('Item/new.html',[
			'type' => $type,
			'categories' => $categories
		]);
	}
	
	/**
	 * Show an item
	 *
	 * @return void
	 */
	public function showAction(){
		$moneyFlowsSnF = new MoneyFlowFilteringSorting($_GET);
		$moneyFlows = $moneyFlowsSnF->returnMoneyFlows();
		$pages = floor((count($moneyFlows)+20)/20);
		var_dump($moneyFlowsSnF);
		View::renderTemplate('Item/show.html',[
			'moneyFlows' => $moneyFlows,
			'filters' => $moneyFlowsSnF,
			'pages'	=> $pages
		]);
	}
	
	/**
	 * Add a new money flow
	 *
	 * @return void
	 */
	public function createAction()		{
		if( isset($_POST['type'])){
			$moneyFlow = new MoneyFlow($_POST);
			$_SESSION['type'] = $moneyFlow->type;
			$url = '/item/new-' . $_SESSION['type'];
			$categories = MoneyFlow::returnAllCategoriesNamesOfCurrUser($_SESSION['type']);
			if ($moneyFlow->save()) {
				Flash::addMessage('Create successful');
				View::renderTemplate('Item/new.html', [
					'moneyFlow' => $moneyFlow,
					'type' => $_SESSION['type'],
					'categories' => $categories
				]);
			} else {
				
				View::renderTemplate('Item/new.html', [
					'moneyFlow' => $moneyFlow,
					'type' => $_SESSION['type'],
					'categories' => $categories
				]);
			}
		} else {
			if (isset($_SESSION['type'])){
				$url = '/item/new-' . $_SESSION['type'];
				$this->redirect($url);
			} else {
				$this->redirect('/');
			}
			
		}
	}
	
	/**
	 * Edit money flow item
	 *
	 * @return void
	 */
	public function editAction(){
		if($moneyFlows= MoneyFlow::returnMoneyFlowsOfCurrentUser($_GET['id'])){
			$moneyFlow=$moneyFlows[0];
			$categories = MoneyFlow::returnAllCategoriesNamesOfCurrUser($moneyFlow->type);
			View::renderTemplate('Item/edit.html',[
				'moneyFlow' => $moneyFlow,
				'categories' => $categories
			]);
		} else {
			$this->redirect('/item/show');
		}
	}
	
	/**
	 * Update money flow item
	 *
	 * @return void
	 */
	public function updateAction(){
		$moneyFlow = new MoneyFlow;
		if	($moneyFlow->updateMoneyFlow($_POST)){
		
		Flash::addMessage('Changes saved');
		
		$this->redirect('/item/show');
		} else {
		View::renderTemplate('item/edit.html', [
			'moneyFlow' => $moneyFlow,
			'categories' => MoneyFlow::returnAllCategoriesNamesOfCurrUser($moneyFlow->type)
		]);
		}
	}
	
	/**
	 * Confirm delete money flow item
	 *
	 * @return void
	 */
	public function confirmDelete(){
		if($moneyFlows= MoneyFlow::returnMoneyFlowsOfCurrentUser($_GET['id'])){
			$moneyFlow=$moneyFlows[0];
			View::renderTemplate('Item/confirm_delete.html',[
				'moneyFlow' => $moneyFlow
			]);
		} else {
			$this->redirect('/item/show');
		}
	}
	
	/**
	 * Update money flow item
	 *
	 * @return void
	 */
	public function deleteAction(){
		$moneyFlow = new MoneyFlow;
		if	($moneyFlow->deleteProfile($_POST['id'])){
		
		Flash::addMessage('Item deleted');
		
		$this->redirect('/item/show');
		} else {
			View::renderTemplate('item/edit.html', [
				'moneyFlow' => $moneyFlow,
				'categories' => MoneyFlow::returnAllCategoriesNamesOfCurrUser($moneyFlow->type)
			]);
		}
	}
}	