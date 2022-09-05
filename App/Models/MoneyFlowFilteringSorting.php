<?php
	namespace App\Models;
	
	
	use PDO;
	
	
	
	/**
		* Money Flws model
		*
		* PHP version 7.0
	*/
	class MoneyFlowFilteringSorting extends MoneyFlow{
		public $chartData;
	
		public $sortShortTypes = array(
		"name-asc",
		"name-desc",
		"amount-asc",
		"amount-desc",
		"date-asc",
		"date-desc"
		);
		
		public $sortDescTypes = array(
		"name-asc"		=>	"Name: A-Z",
		"name-desc"		=>	"Name: Z-A",
		"amount-asc"	=>	"Amount: From smallest to largest",
		"amount-desc"	=>	"Amount: From largest to smallest",
		"date-asc"		=>	"Oldest to newest",
		"date-desc"		=>	"Newest to oldest"
		);
		
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
		public function __construct($data = []){
			$this->min='';
			$this->max='';
			$this->sort='name-asc';
			$this->sDate='';
			$this->eDate='';
			$this->search='';
			$this->page=1;
			$this->itemsOnPage=20;
			foreach ($data as $key => $value) {
				$this->$key = $value;
			};
		}
		
		protected function generateSQL(){
			$sql = 'SELECT		flow.id , flow.name , cat.name AS category, flow.type , date , description, amount, id_user
			FROM		money_flows				AS		flow 
			INNER JOIN	money_flows_categories	AS		cat 
			ON			flow.category_id		=		cat.id
			INNER JOIN	users_money_flows		AS		user_mf
			ON			flow.id					=		user_mf.id_money_flows
			WHERE		id_user					=		:id_user';
			
			if($this->min != ''){
				$sql .= "\nAND amount >= :min";
			}
			
			if($this->max != ''){
				$sql .= "\nAND amount <= :max";
			}
			
			if($this->sDate != ''){
				$sql .= "\nAND date >= :sDate";
			}
			
			if($this->eDate != ''){
				$sql .= "\nAND date <= :eDate";
			}
			
			if($this->search != ''){
				$sql .= "\nAND (date LIKE :search
				OR flow.type LIKE :search
				OR description LIKE :search
				OR amount LIKE :search
				OR flow.name LIKE :search)";
			}
			
			return $sql;
		}
		
		protected function generateSQLsorting($sql){
			switch($this->sort)
			{
				case $this->sortShortTypes[0];
				$sql .= "\nORDER BY flow.name ASC";
				break;
				case $this->sortShortTypes[1];
				$sql .= "\nORDER BY flow.name DESC";
				break;
				case $this->sortShortTypes[2];
				$sql .= "\nORDER BY flow.amount ASC";
				break;
				case $this->sortShortTypes[3];
				$sql .= "\nORDER BY flow.amount DESC";
				break;
				case $this->sortShortTypes[4];
				$sql .= "\nORDER BY flow.date ASC";
				break;
				case $this->sortShortTypes[5];
				$sql .= "\nORDER BY flow.date DESC";
				break;
			}
			
			$sql .= "\nLIMIT :limit OFFSET :offset";
			
			return $sql;
		}
		
		protected function binding($stmt , $count = false){
			
			$stmt->bindValue(':id_user', $_SESSION['user_id'] , PDO::PARAM_INT);
			
			if($this->min != ''){
				$stmt->bindValue(':min', $this->min , PDO::PARAM_INT);
			}
			
			if($this->max != ''){
				$stmt->bindValue(':max',$this->max , PDO::PARAM_INT);
			} 
			
			if($this->sDate != ''){
				$stmt->bindValue(':sDate', $this->sDate , PDO::PARAM_STR);
			}
			
			if($this->eDate != ''){
				$stmt->bindValue(':eDate', $this->eDate , PDO::PARAM_STR);
			}
			
			if($this->search != ''){
				$s = '%'.$this->search.'%';
				$stmt->bindValue(':search', $s , PDO::PARAM_STR);
			}
			
			if(!$count){
				$stmt->bindValue(':limit',$this->itemsOnPage , PDO::PARAM_INT);
				$stmt->bindValue(':offset',(($this->page)-1)*$this->itemsOnPage , PDO::PARAM_INT);
			}
		}
		/**
			* Returning  money flows of current user according to specified filter
			*
			* @return  array of string
		*/
		public function returnMoneyFlows()	{
			$this->setCount();
			$sql = $this->generateSQL();
			$sql = $this->generateSQLsorting($sql);
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$this->binding($stmt , false);
			
			$stmt->execute();
			
			return $stmt->fetchAll(PDO::FETCH_CLASS);
		}
		
		private function setCount(){
			$sql = $this->generateSQL();
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$this->binding($stmt , true);
			
			$stmt->execute();
			$this->itemCount = $stmt->rowCount();
		}
		
		public function getChartData(){
			$sql = 'SELECT		cat.name AS category, flow.type AS type, sum(flow.amount) AS sum
					FROM		money_flows				AS		flow 
					INNER JOIN	money_flows_categories	AS		cat 
					ON			flow.category_id		=		cat.id
					INNER JOIN	users_money_flows		AS		user_mf
					ON			flow.id					=		user_mf.id_money_flows
					WHERE		id_user					=		:id_user';
			
			if($this->min != ''){
				$sql .= "\nAND amount >= :min";
			}
			
			if($this->max != ''){
				$sql .= "\nAND amount <= :max";
			}
			
			if($this->sDate != ''){
				$sql .= "\nAND date >= :sDate";
			}
			
			if($this->eDate != ''){
				$sql .= "\nAND date <= :eDate";
			}
			
			if($this->search != ''){
				$sql .= "\nAND (date LIKE :search
				OR flow.type LIKE :search
				OR description LIKE :search
				OR amount LIKE :search
				OR flow.name LIKE :search)";
			}
			$sql .= "\nGROUP BY category";
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$this->binding($stmt , true);
			
			$stmt->execute();
			
			return $stmt->fetchAll(PDO::FETCH_CLASS);
		}
	}						
