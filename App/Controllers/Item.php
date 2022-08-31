<?php
	
	namespace App\Controllers;
	
	use Core\View;
	use App\Flash;
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
			$moneyFlows = MoneyFlow::returnAllMoneyFlowsOfCurrentUser();
			View::renderTemplate('Item/show.html',[
				'moneyFlows' => $moneyFlows
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
				$categories = MoneyFlow::returnAllCategoriesNames($_SESSION['type']);
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
		* Edit a new expense item
		*
		* @return void
		*/
		public function editAction(){
			echo $_GET['id'];
		}
		
	}	