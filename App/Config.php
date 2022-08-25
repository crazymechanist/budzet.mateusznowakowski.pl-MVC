<?php

namespace App;

/**
	* Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'mvclogin';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'mvcuser';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'secret';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;
	
	/**
     * Secret key for hashing
     * @var string
     */
    const SECRET_KEY = 'fdHOEvOMVh1pgUo2pMYs2NsRAjNz0kx4';
	
	/**
	 * Mail adress
 	 * @var string
	 */
	const MAIL_ADDRESS = 'mateusz_nowakowski@outlook.com';
	
	/**
	 * Mmail password
	 * @var string
	 */
	const MAIL_PASSWORD = 'idncfzswkcdxbnxq';
	
	/**
	 * Mail smtp server
	 * @var string
	 */
	const SMTP_SERVER_ADRESS = 'smtp-mail.outlook.com';
	
	/**
	 * Mail smtp port
	 * @var string
	 */
	const SMTP_SERVER_PORT = '587';
}
