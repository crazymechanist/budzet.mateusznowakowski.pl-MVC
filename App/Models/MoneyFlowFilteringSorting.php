<?php
namespace App\Models;


use PDO;



/**
	* Money Flws model
	*
	* PHP version 7.0
*/
class MoneyFlowFilteringSorting extends MoneyFlow{
	
	
	public function endDate(){
		return date("Y-m-d", time());
	}
		
	public function initialDate(){
		$date = new \DateTime('-30 days');
		return $date->format('Y-m-d');
	}
	

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
	 * Returning  money flows of current user according to specified filter
	 *
	 * @return  array of string
	 */
	public static function returnMoneyFlowsOfCurrentUser1()	{
		$sql = 'SELECT		flow.id , flow.name , flow.type , date , description, amount, id_user
		FROM		money_flows				AS		flow 
		INNER JOIN	money_flows_categories	AS		cat 
		ON			flow.category_id		=		cat.id
		INNER JOIN	users_money_flows		AS		user_mf
		ON			flow.id					=		user_mf.id_money_flows
		WHERE		id_user					=		:id_user';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':id_user', $_SESSION['user_id'] , PDO::PARAM_INT);
		
		$stmt->execute();
		
		return $stmt->fetchAll(PDO::FETCH_CLASS);
	}
}						
								