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
	* Error messagesmessages
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
	public function __construct($data = [])	{
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
		$id=0;
		if(!($id=$this->saveMoneyFlow())){
			$this->error = 'Failed to save';
			return false;
		}
		if(!($this->assignMoneyFlow($id))){
			$this->error = 'Failed to save';
			return false;
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
		if($this->typeValidate($this->type)){
			$this->errors[] = 'Type can be only expense or income';
		}
		
		// Category
		if(!$this->findIdByCategoryName($this->category)){
			$this->errors[] = 'Unknown category';
		} 
		
		// Date
		if ($this->dateValidate($this->date)) {
			$this->errors[] = 'Date invalid';
		}
		
		//Amount
		if($this->amountValidate($this->amount)){
			$this->errors[] = 'Amount invalid';
		}
	}
	
	/**
	* Validate current type value
	*
	* @return boolean
	*/
	protected function typeValidate($type)	{
		$types = array("expense", "income");
		if(in_array($type, $types)){
			return false;
		}
		return true;
	}
	
	/**
	* Validate current date value
	*
	* @return boolean
	*/
	protected function dateValidate($date)	{
		if((strtotime($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date))){
			return false;
		}
		return true;
	}
	
	/**
	* Validate current amount value
	*
	* @return boolean
	*/
	protected function amountValidate($amount)	{
		if((preg_match('/^\d+\.{0,1}\d*$/m', $amount) && ($amount>0))){
			return false;
		}
		return true;
	}
	
	/**
	* Finding category name by category id
	*
	* @return mixed MoneyFlow if found, false otherwise
	*/
	public static function findIdByCategoryName($name , $type='')	{
		$sql = 'SELECT *
				FROM money_flows_categories
				WHERE 	name		=	:name
				AND		user_id		=	:user_id';
		if($type != ''){
			$sql .= "\nAND type = :type";
		}
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $_SESSION['user_id'] , PDO::PARAM_INT);
		
		if($type != ""){
			$stmt->bindParam(':type', $type, PDO::PARAM_STR);
		}
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$stmt->execute();
		
		return $stmt->fetch();	
	}
	
	/**
	* Returning names of mone flows categories
	*
	* @return  array of string
	*/
	public static function returnAllCategoriesNamesOfCurrUser($type)	{
		$sql = 'SELECT name 
				FROM money_flows_categories 
				WHERE	type		=	:type
				AND		user_id		=	:user_id';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':user_id', $_SESSION['user_id'] , PDO::PARAM_INT);
		$stmt->bindParam(':type', $type, PDO::PARAM_STR);
		
		$stmt->execute();
		
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}
	
	/**
	 * Returning all money flows of current user
	 *
	 * @return  array of string
	 */
	public static function returnMoneyFlowsOfCurrentUser($id='')	{
		$sql = 'SELECT		flow.id , flow.name , cat.name AS category, flow.type , date , description, amount, id_user
		FROM		money_flows				AS		flow 
		INNER JOIN	money_flows_categories	AS		cat 
		ON			flow.category_id		=		cat.id
		INNER JOIN	users_money_flows		AS		user_mf
		ON			flow.id					=		user_mf.id_money_flows
		WHERE		id_user					=		:id_user';
		
		if($id != ''){
			$sql .=	"\nAND	flow.id	=	:id";
		}
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		if($id != ''){
			$stmt->bindValue(':id', $id , PDO::PARAM_INT);
		}
		
		$stmt->bindValue(':id_user', $_SESSION['user_id'] , PDO::PARAM_INT);
		
		$stmt->execute();
		
		return $stmt->fetchAll(PDO::FETCH_CLASS);
	}

	/**
	 * Update the money flow
	 *
	 * @param array $data Data from the money flow profile form
	 *
	 * @return boolean True if the data was updated, false otherwise
	 */
	public function updateMoneyFlow($data){
		$this->name			=	$data['name'];
		$this->category		=	$data['category'];
		$this->amount		=	$data['amount'];
		$this->type			=	$data['type'];
		$this->date			=	$data['date'];
		$this->description	=	$data['description'];
		$this->id			=	$data['id'];
		
		$this->validate(false);
		
		if	(empty($this->errors) && $this->returnMoneyFlowsOfCurrentUser($this->id)){
			
			$sql = 'UPDATE	money_flows 
					SET		name = :name, category_id = :category_id, amount = :amount, date = :date, description = :description
					WHERE	id	=	:id';
											  
			$db = static::getDB();
			$stmt = $db->prepare($sql);
												  
			$stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
			$category= $this->findIdByCategoryName($this->category);
			$stmt->bindValue(':category_id', $category->id, PDO::PARAM_INT);
			$stmt->bindValue(':amount', $this->amount);
			$stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
			$stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
			$stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
			
			return $stmt->execute();
		}
		return false;
	}
	
	/**
	 * Delete the money flow
	 *
	 * @param array $data Data from the money flow profile form
	 *
	 * @return boolean True if the data was updated, false otherwise
	 */
	public function deleteProfile($id){
		if	($this->returnMoneyFlowsOfCurrentUser($id)){
			
			$sql = 'DELETE mf, umf 
					FROM money_flows AS mf 
					INNER JOIN users_money_flows AS umf
					ON umf.id_money_flows = mf.id
					WHERE mf.id = :id';
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			
			return $stmt->execute();
		}
		return false;
	}
	
	/**
	 * Update the money flow category
	 *
	 * @param array $data Data from the money flow profile form
	 *
	 * @return boolean True if the data was updated, false otherwise
	 */
	public static function updateMoneyFlowCategory($id , $name , $type){
		
		if	(!MoneyFlow::findIdByCategoryName($name , $type)){
			
			$sql = 'UPDATE	money_flows_categories
					SET		name = :name
					WHERE	id		=	:id
					AND		user_id	=	:id_user
					AND		type	=	:type';
											  
			$db = static::getDB();
			$stmt = $db->prepare($sql);
												  
			$stmt->bindValue(':name', $name, PDO::PARAM_STR);
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$stmt->bindValue(':id_user', $_SESSION['user_id'] , PDO::PARAM_INT);
			$stmt->bindParam(':type', $type, PDO::PARAM_STR);

			
			return $stmt->execute();
		}
		return false;
	}
	
	/**
	 * Delete the money flow category and conected with it money flows
	 *
	 * @param array $data Data from the money flow category delete form 
	 *
	 * @return boolean True if the data was updated, false otherwise
	 */
	public static function deleteMoneyFlowCategoryAndConnectedMF($id){
		$sql = 'DELETE mf , mfc
				FROM money_flows_categories AS mfc
				LEFT OUTER JOIN money_flows AS mf
				ON mfc.id = mf.category_id
				WHERE mfc.id = :id
				AND mfc.user_id = :id_user';
										  
		$db = static::getDB();
		$stmt = $db->prepare($sql);
											  
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->bindValue(':id_user', $_SESSION['user_id'] , PDO::PARAM_INT);

		
		return $stmt->execute();
	}
	
	/**
	 * Add new money flow category
	 *
	 * @param array $data Data from the money flow profile form
	 *
	 * @return boolean True if the data was updated, false otherwise
	 */
	public static function newMoneyFlowCategory($name , $type){
		
		if	(!MoneyFlow::findIdByCategoryName($name , $type)){
			
			$sql = 'INSERT INTO `money_flows_categories` (`id`, `user_id`, `type`, `name`) 
			VALUES (NULL, :id_user , :type , :name)';
											  
			$db = static::getDB();
			$stmt = $db->prepare($sql);
												  
			$stmt->bindValue(':name', $name, PDO::PARAM_STR);
			$stmt->bindValue(':id_user', $_SESSION['user_id'] , PDO::PARAM_INT);
			$stmt->bindParam(':type', $type, PDO::PARAM_STR);

			
			return $stmt->execute();
		}
		return false;
	}
}
