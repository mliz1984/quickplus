<?php

class Session{
	public function setsession($name,$value)
	{
		
		$_SESSION[$name] = $value;
	}

	public function checksession($name)
	{
		return $_SESSION[$name];
	}

	public function validateuser()
	{
		if($_SESSION['username']!=NULL&&trim($_SESSION['username'])!='')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}


?>
