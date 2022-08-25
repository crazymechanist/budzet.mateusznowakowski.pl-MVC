<?php
	
namespace App;


/**
 *
 * Flash notyfication messages: messages for one-time display using the session
 * for storage between requests.
 *
 * PHP version 7.0
 */

class Flash{
	
	/**
	 * Success message type
	 * @var string
	 */
	const SUCCESS = 'success';
	
	/**
	 * Information message type
	 * @var string
	 */
	const INFO = 'info';
	
	/**
	 * Warning message type
	 * @var string
	 */
	const WARNING = 'warning';	
	
	/**
	 * Add a message
	 *
	 * @param string $message The message content
	 * @param string $type The optional message type, defaults to SUCCESS
	 *
	 * @return void
	 */
	public static function addMessage($message , $type = 'success'){
		// Create array in the session if doesn't aleready exist
		if (! isset($_SESSION['flash_notification'])){
			$_SESSION['flash_notification'] = [];
		}
		
		//Append the message to the array
		$_SESSION['flash_notification'][] = [
			'body' => $message,
			'type' => $type
		];
	}
	
	/**
	 * Get all the messages
	 *
	 *
	 * @return mixed  An array with all the messages or null if none set
	 */
	public static function getMessages(){
		if (isset($_SESSION['flash_notification'])){
			$messages = $_SESSION['flash_notification'];
			unset ($_SESSION['flash_notification']);
			
			return $messages;
		}
	}
}


