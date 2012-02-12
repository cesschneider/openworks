<?php

class Smtp
{
	var $socket;
	var $username;
	var $password;

	function connect ($hostname, $username, $password, $port = 25, $timeout = 10)
	{
		$this->username = $username;
		$this->password = $password;

		$this->socket = @fsockopen($hostname, $port, $errno, $errstr, $timeout);

		if ($errno) {
			return FALSE;
		}

		return TRUE;
	}
	
	function send ($from, $to, $subject, $body)
	{
		set_time_limit(0);
		$date = 'Date: '. date('r',time());
		
		// Verifica Conexão (Depuração)
		fgets($this->socket, 1024)."<br>";
		
		// Autorização / Usuário / Senha
		fputs($this->socket,"AUTH LOGIN\r\n", 512);
		fgets($this->socket, 512)."<br>";
		fputs($this->socket,base64_encode($this->username)."\r\n", 512);
		fgets($this->socket, 512)."<br>";
		fputs($this->socket,base64_encode($this->password)."\r\n", 512);
		fgets($this->socket, 512)."<br>";
		
		// Email de
		fputs($this->socket, "MAIL FROM: <$from>\r\n", 512);
		fgets($this->socket, 512)."<br>";
		
		// Email para
		fputs($this->socket, "RCPT TO: <$to>\r\n", 512);
		fgets($this->socket, 512)."<br>";

		// Data
		fputs($this->socket, "DATA\r\n", 512);
		fgets($this->socket, 512)."<br>";
		
		// Headers
		fputs($this->socket, "MIME-Version: 1.0\r\n");
		fputs($this->socket, "Content-Type: text/html; charset=iso-8859-1\r\n");
		fputs($this->socket, "Date: $date \r\n");
		fputs($this->socket, "From: $from \r\n");
		fputs($this->socket, "To: $to \r\n");
		fputs($this->socket, "Subject: $subject \r\n");
		fputs($this->socket, "\r\n");
		fputs($this->socket, "$body \r\n.\r\n");
		fgets($this->socket, 512)."<br>";
	}

	function close ()
	{
		// Fechando conexao
		fputs($this->socket, "QUIT\r\n", 512);
		fgets($this->socket, 512)."<br>";

        return fclose($this->socket);
	}
}

?>