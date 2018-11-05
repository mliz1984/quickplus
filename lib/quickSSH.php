<?php 
	class QuickSSH
	{
		protected $connection = null;
		protected $usenname = null;
		protected $password = null;
		function __construct($ip,$port,$username,$password)
		{
			$this->connection=ssh2_connect($ip,$port);
			$result = false;
			if($this->connection)
			{
			  $result = ssh2_auth_password($this->connection,$username,$password);
			}
			return $result;
		} 

		function exec($command)
		{
			$stream = ssh2_exec($this->connection,$command);
			stream_set_blocking($stream,true);
			$output = stream_get_contents($stream);
			fclose($stream);
			return $output;
		}
	}

?>