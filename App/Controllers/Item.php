<?php
	
	namespace App\Controllers;
	
	use Core\View;
	use App\Models\MoneyFlow;
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
			$categories = MoneyFlow::returnAllCategoriesNames($type);

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
			$categories = MoneyFlow::returnAllCategoriesNames($type);

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
			View::renderTemplate('Item/show.html');
		}
		
		/**
		* Add a new money flow
		*
		* @return void
		*/
		public function createAction()		{
			$moneyFlow = new MoneyFlow($_POST);

			if ($moneyFlow->save()) {
				
				
			} else {
				View::renderTemplate('Item/new.html', [
				'moneyFlow' => $moneyFlow
				]);
				
			}
		}
	}	