<?php
namespace App\Models;


use PDO;



/**
	* Money Flws model
	*
	* PHP version 7.0
*/
class MoneyFlowCategory extends \Core\Model{

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
		$this->name	= '';
		foreach ($data as $key => $value) {
			$this->$key = $value;
		};
	}

	/**
	 * Update the money flow category
	 *
	 * @param array $data Data from the money flow c form
	 *
	 * @return boolean True if the data was updated, false otherwise
	 */
	public function update($data){
		$this->name			=	$data['name'];
		$this->type			=	$data['type'];
		$this->id			=	$data['id'];

			$sql = 'UPDATE	money_flows_categories
					SET		name = :name
					WHERE	id		=	:id
					AND		user_id	=	:id_user
					AND		type	=	:type';

			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
			$stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
			$stmt->bindValue(':id_user', $_SESSION['user_id'] , PDO::PARAM_INT);
			$stmt->bindParam(':type', $this->type, PDO::PARAM_STR);

			return $stmt->execute();

	}

	/**
	 * Delete the money flow category and conected with it money flows
	 *
	 * @param array $data Data from the money flow category delete form
	 *
	 * @return boolean True if the data was updated, false otherwise
	 */
	public function delete($id){
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
	 * @param id (optional) id of user user category should be assigned to
	 *
	 * @return boolean True if the data was updated, false otherwise
	 */
	public function save($id = NULL){
		if	(!$this::findIdByName($this->name , $this->type, $id)){

			$sql = 'INSERT INTO `money_flows_categories` (`id`, `user_id`, `type`, `name`)
			VALUES (NULL, :id_user , :type , :name)';

			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
			if($id == NULL){
				$stmt->bindValue(':id_user', $_SESSION['user_id'] , PDO::PARAM_INT);
			}else{
				$stmt->bindValue(':id_user', $id , PDO::PARAM_INT);
			}

			$stmt->bindParam(':type', $this->type, PDO::PARAM_STR);

			return $stmt->execute();
		}
		return false;
	}

	/**
	* Returning names of mone flows categories
	*
	* @return  array of string
	*/
	public static function returnAll($type)	{
		$sql = 'SELECT *
				FROM money_flows_categories
				WHERE	type		=	:type
				AND		user_id		=	:user_id';

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':user_id', $_SESSION['user_id'] , PDO::PARAM_INT);
		$stmt->bindParam(':type', $type, PDO::PARAM_STR);

		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_CLASS);
	}

	/**
	* Finding category name by category id
	*
	* @return mixed MoneyFlow if found, false otherwise
	*/
	public static function findIdByName($name , $type=NULL, $id= NULL)	{
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

		if($type != NULL){
			$stmt->bindParam(':type', $type, PDO::PARAM_STR);
		}

		if($id == NULL){
		$stmt->bindValue(':user_id', $_SESSION['user_id'] , PDO::PARAM_INT);
		} else {
		$stmt->bindValue(':user_id', $id , PDO::PARAM_INT);
		}

		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

		$stmt->execute();

		return $stmt->fetch();
	}

}
