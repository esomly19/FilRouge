<?php

namespace App\exception;

final class AuthentificationException extends \Exception{

	
	public function __construct($exception_message){
		parent::__construct($exception_message);
    }
	
}