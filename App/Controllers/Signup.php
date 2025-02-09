<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Config;
use \App\Models\MoneyFlowCategory;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Signup extends \Core\Controller
{

	/**
	 * Show the signup page
	 *
	 * @return void
	 */
	public function newAction()			{
		View::renderTemplate('Signup/new.html');
	}

	/**
	 * Sign up a new user
	 *
	 * @return void
	 */
	public function createAction()		{
		$user = new User($_POST);

		if ($user->save()) {
			
			foreach (Config::$initialCategories as $categoryTable) {
				$category = new MoneyFlowCategory($categoryTable);
				$user_id =User::findByEmail($user->email)->id;
				$category -> save($user_id);
			}
			
			$user->sendActivationEmail();
			
			$this->redirect('/Signup/success');
			
		} else {

			View::renderTemplate('Signup/new.html', [
				'user' => $user
			]);

		}
	}
	
	/**
	* Sign up a new user
	*
	* @return void
	*/
	public function successAction()		{
		View::renderTemplate('Signup/success.html');	 
	}
	
	/**
	 * Activate a new account
	 *
	 * @return void
	 */
	public function activateAction()	{
		User::activate($this->route_params['token']);
		
		$this->redirect('/Signup/activated');
	}
	
	/**
	 * Show the activation succes page
	 *
	 * @return void
	 */
	public function activatedAction()	{
		View::renderTemplate('Signup/activated.html');
	}
}
