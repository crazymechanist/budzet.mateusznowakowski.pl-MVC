<?php

namespace App\Models;

use PDO;


/**
	* Money Flws model
	*
	* PHP version 7.0
*/
class MoneyFlow extends \Core\Model{
	
	/**
	* Error messages
	*
	* @var array
	*/
	public $errors = [];
	
	/**
	* Class constructor
	*
	* @param array $data  Initial property values
	*
	* @return void
	*/
	public function __construct($data = [])
	{
		foreach ($data as $key => $value) {
			$this->$key = $value;
		};
	}
	
	/**
	 * Save money flow
	 *
	 * @return boolean  True if it was saved, false otherwise
	 */
	public function save()	{
		$this->validate();

		if($id=$this->saveMoneyFlow()){
			if($this->assignMoneyFlow($id)){
				$this->errors[] = 'Failed to save';
			}
		}
		
		return true;
	}
	
	/**
	 * Save in table money flows
	 *
	 * @return boolean  True if the user was saved, false otherwise
	 */
	public function saveMoneyFlow()	{
		if (empty($this->errors)) {

			$sql = 'INSERT INTO money_flows (name, type, category_id, amount, date, description)
					VALUES (:name, :type, :category_id, :amount, :date, :description)';
											  
			$db = static::getDB();
			$stmt = $db->prepare($sql);
												  
			$stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
			$stmt->bindValue(':type', $this->type, PDO::PARAM_STR);
			$category= $this->findIdByCategoryName($this->category);
			$stmt->bindValue(':category_id', $category->id, PDO::PARAM_INT);
			$stmt->bindValue(':amount', $this->amount);
			$stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
			$stmt->bindValue(':description', $this->description, PDO::PARAM_STR);


			if ($stmt->execute()){
				return $db->lastInsertId();
			} else {
				return false;
			}
		}
		return false;
	}
	
	/**
	 * Save in table users money flows
	 *
	 * @return boolean  True if the user was assigned, false otherwise
	 */
	public function assignMoneyFlow($idMoneyFlow)	{

		if (empty($this->errors)) {

			$sql = 'INSERT INTO users_money_flows (id_user , id_money_flows)
					VALUES (:id_user, :id_money_flows)';
											  
			$db = static::getDB();
			$stmt = $db->prepare($sql);
												  
			$stmt->bindValue(':id_user', $_SESSION['user_id'] , PDO::PARAM_INT);
			$stmt->bindValue(':id_money_flows', $idMoneyFlow , PDO::PARAM_INT);

			return $stmt->execute();
		}

		return false;
	}
	
	
	/**
	* Validate current property values, adding valiation error messages to the errors array property
	*
	* @return void
	*/
	public function validate()	{
		// Name
		if ($this->name == '') {
			$this->errors[] = 'Name is required';
		}
		
		// Type
		$types = array("expense", "income");
		if(!in_array($this->type, $types)){
			$this->errors[] = 'Type can be only expense or income';
		}
		
		// Category
		if(!$this->findIdByCategoryName($this->category)){
			$this->errors[] = 'Unknown category';
		} 
		
		// Date
		if (!(strtotime($this->date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->date))) {
			$this->errors[] = 'Date invalid';
		}
		
		//Amount
		if(!preg_match('/^\d+\.{0,1}\d*$/m', $this->amount)){
			$this->errors[] = 'Amount invalid';
		}
	}
	
	/**
	 * Finding category name by id
	 *
	 * @return mixed MoneyFlow if found, false otherwise
	 */
	protected function findIdByCategoryName($name)	{
		$sql = 'SELECT * FROM money_flows_categories WHERE name = :name';

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$stmt->execute();

		return $stmt->fetch();	
	}
	
	/**
	 * Returning names of mone flows categories
	 *
	 * @return  array of string
	 */
	public static function returnAllCategoriesNames($type)	{
		$sql = 'SELECT name FROM money_flows_categories WHERE type=:type';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':type', $type, PDO::PARAM_STR);
		
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}
}	