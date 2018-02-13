<?php
defined('WENXIAOCMS') or exit('Access denied!');
class email {
	/* Public Variables */
	var $smtp_port;
	var $time_out;
	var $host_name;
	var $log_file;
	var $relay_host;
	var $debug;
	var $auth;
	var $user;
	var $pass;
	
	/* Private Variables */
	var $sock;
	var $from;
	
	//构造函数
	function __construct($user = '', $pwd = '' , $smtp = '',$from = ''){
		if($user == '' or $pwd == '') exit('Email config error!');
		$this->user       = $user;
		$this->pass       = $pwd;
		$this->smtp_port  = 25;
		$this->relay_host = $smtp == ''?'smtp.'.substr($user,strpos($user,'@')+1):$smtp;
		$this->time_out   = 30; // is used in fsockopen()
		$this->auth       = TRUE; // auth
		$this->host_name  = "localhost"; // is used in HELO command
		$this->log_file   = "";
		$this->sock       = FALSE;
		$this->form		  = $from == ''?$this->user:$from.'<'.$this->user.'>';
	}
	
	/*发送邮件*/
	public function send_mail($email,$title = '邮件标题',$content = '邮件内容',$debug = FALSE,$html = TRUE ){
		if(!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email))return false;
		$smtpemailto    = $email;//发送给谁
		$mailsubject    = $title;//邮件主题
		$mailbody       = $content;//邮件内容
		$mailtype       = $html ? 'HTML':'TXT';//邮件格式（HTML/TXT）,TXT为文本邮件
		$this->debug    = $debug;//是否显示发送的调试信息
		$result         = self::sendmail($smtpemailto, $this->form, $mailsubject, $mailbody, $mailtype);
		return $result ? TRUE : FALSE;
	}
	
	/**
	 * 群发邮件,一个一个的发
	 */
	public function send_qun_mail($mails,$title,$content){
		ini_set('max_execution_time', '999');
		if(is_array($mails)){
			foreach($mails as $v){
				$this->send_mail($v,$title,$content);
			}
		}else{
			$this->send_mail($mails,$title,$content);
		}
		return TRUE;
	}

		/* Main Function */
	private function sendmail($to, $from, $subject = "", $body = "", $mailtype, $cc = "", $bcc = "", $additional_headers = "") {
		$header = "";
		$mail_from = $this->get_address ( $this->strip_comment ( $from ) );
		$body = preg_replace ( "/(^|(\r\n))(\\.)/", "\\1.\\3", $body );
		$header .= "MIME-Version:1.0\r\n";
		if ($mailtype == "HTML") {
			$header .= "Content-Type:text/html\r\n";
		}
		$header .= "To: " . $to . "\r\n";
		if ($cc != "") {
			$header .= "Cc: " . $cc . "\r\n";
		}
		$header .= "From:" . $from . "\r\n";
		$header .= "Subject: " . $subject . "\r\n";
		$header .= $additional_headers;
		$header .= "Date: " . date ( "r" ) . "\r\n";
		$header .= "X-Mailer:By Redhat (PHP/" . phpversion () . ")\r\n";
		list ( $msec, $sec ) = explode ( " ", microtime () );
		$header .= "Message-ID: <" . date ( "YmdHis", $sec ) . "." . ($msec * 1000000) . "." . $mail_from . ">\r\n";
		$TO = explode ( ",", $this->strip_comment ( $to ) );
		
		if ($cc != "") {
			$TO = array_merge ( $TO, explode ( ",", $this->strip_comment ( $cc ) ) );
		}
		
		if ($bcc != "") {
			$TO = array_merge ( $TO, explode ( ",", $this->strip_comment ( $bcc ) ) );
		}
		
		$sent = TRUE;
		foreach ( $TO as $rcpt_to ) {
			$rcpt_to = $this->get_address ( $rcpt_to );
			if (! $this->smtp_sockopen ( $rcpt_to )) {
				$this->log_write ( "Error: Cannot send email to " . $rcpt_to . "\n" );
				$sent = FALSE;
				continue;
			}
			if ($this->smtp_send ( $this->host_name, $mail_from, $rcpt_to, $header, $body )) {
				$this->log_write ( "E-mail has been sent to <" . $rcpt_to . ">\n" );
			} else {
				$this->log_write ( "Error: Cannot send email to <" . $rcpt_to . ">\n" );
				$sent = FALSE;
			}
			fclose ( $this->sock );
			$this->log_write ( "Disconnected from remote host\n" );
		}
		// echo "<br>";
		// echo $header;
		return $sent;
	}
	
	/* Private Functions */
	function smtp_send($helo, $from, $to, $header, $body = "") {
		if (! $this->smtp_putcmd ( "HELO", $helo )) {
			return $this->smtp_error ( "sending HELO command" );
		}
		if ($this->auth) {
			if (! $this->smtp_putcmd ( "AUTH LOGIN", base64_encode ( $this->user ) )) {
				return $this->smtp_error ( "sending HELO command" );
			}
			
			if (! $this->smtp_putcmd ( "", base64_encode ( $this->pass ) )) {
				return $this->smtp_error ( "sending HELO command" );
			}
		}
		if (! $this->smtp_putcmd ( "MAIL", "FROM:<" . $from . ">" )) {
			return $this->smtp_error ( "sending MAIL FROM command" );
		}
		if (! $this->smtp_putcmd ( "RCPT", "TO:<" . $to . ">" )) {
			return $this->smtp_error ( "sending RCPT TO command" );
		}
		if (! $this->smtp_putcmd ( "DATA" )) {
			return $this->smtp_error ( "sending DATA command" );
		}
		if (! $this->smtp_message ( $header, $body )) {
			return $this->smtp_error ( "sending message" );
		}
		if (! $this->smtp_eom ()) {
			return $this->smtp_error ( "sending <CR><LF>.<CR><LF> [EOM]" );
		}
		if (! $this->smtp_putcmd ( "QUIT" )) {
			return $this->smtp_error ( "sending QUIT command" );
		}
		return TRUE;
	}
	function smtp_sockopen($address) {
		if ($this->relay_host == "") {
			return $this->smtp_sockopen_mx ( $address );
		} else {
			return $this->smtp_sockopen_relay ();
		}
	}
	function smtp_sockopen_relay() {
		$this->log_write ( "Trying to " . $this->relay_host . ":" . $this->smtp_port . "\n" );
		$this->sock = @fsockopen ( $this->relay_host, $this->smtp_port, $errno, $errstr, $this->time_out );
		if (! ($this->sock && $this->smtp_ok ())) {
			$this->log_write ( "Error: Cannot connenct to relay host " . $this->relay_host . "\n" );
			$this->log_write ( "Error: " . $errstr . " (" . $errno . ")\n" );
			return FALSE;
		}
		$this->log_write ( "Connected to relay host " . $this->relay_host . "\n" );
		return TRUE;
		;
	}
	function smtp_sockopen_mx($address) {
		$domain = preg_replace ( "/^.+@([^@]+)$/", "\\1", $address );
		if (! @getmxrr ( $domain, $MXHOSTS )) {
			$this->log_write ( "Error: Cannot resolve MX \"" . $domain . "\"\n" );
			return FALSE;
		}
		foreach ( $MXHOSTS as $host ) {
			$this->log_write ( "Trying to " . $host . ":" . $this->smtp_port . "\n" );
			$this->sock = @fsockopen ( $host, $this->smtp_port, $errno, $errstr, $this->time_out );
			if (! ($this->sock && $this->smtp_ok ())) {
				$this->log_write ( "Warning: Cannot connect to mx host " . $host . "\n" );
				$this->log_write ( "Error: " . $errstr . " (" . $errno . ")\n" );
				continue;
			}
			$this->log_write ( "Connected to mx host " . $host . "\n" );
			return TRUE;
		}
		$this->log_write ( "Error: Cannot connect to any mx hosts (" . implode ( ", ", $MXHOSTS ) . ")\n" );
		return FALSE;
	}
	function smtp_message($header, $body) {
		fputs ( $this->sock, $header . "\r\n" . $body );
		$this->smtp_debug ( "> " . str_replace ( "\r\n", "\n" . "> ", $header . "\n> " . $body . "\n> " ) );
		return TRUE;
	}
	function smtp_eom() {
		fputs ( $this->sock, "\r\n.\r\n" );
		$this->smtp_debug ( ". [EOM]\n" );
		return $this->smtp_ok ();
	}
	function smtp_ok() {
		$response = str_replace ( "\r\n", "", fgets ( $this->sock, 512 ) );
		$this->smtp_debug ( $response . "\n" );
		if (! preg_match ( "/^[23]/", $response )) {
			fputs ( $this->sock, "QUIT\r\n" );
			fgets ( $this->sock, 512 );
			$this->log_write ( "Error: Remote host returned \"" . $response . "\"\n" );
			return FALSE;
		}
		return TRUE;
	}
	function smtp_putcmd($cmd, $arg = "") {
		if ($arg != "") {
			if ($cmd == "")
				$cmd = $arg;
			else
				$cmd = $cmd . " " . $arg;
		}
		fputs ( $this->sock, $cmd . "\r\n" );
		$this->smtp_debug ( "> " . $cmd . "\n" );
		return $this->smtp_ok ();
	}
	function smtp_error($string) {
		$this->log_write ( "Error: Error occurred while " . $string . ".\n" );
		return FALSE;
	}
	function log_write($message) {
		$this->smtp_debug ( $message );
		if ($this->log_file == "") {
			return TRUE;
		}
		$message = date ( "M d H:i:s " ) . get_current_user () . "[" . getmypid () . "]: " . $message;
		if (! @file_exists ( $this->log_file ) || ! ($fp = @fopen ( $this->log_file, "a" ))) {
			$this->smtp_debug ( "Warning: Cannot open log file \"" . $this->log_file . "\"\n" );
			return FALSE;
		}
		flock ( $fp, LOCK_EX );
		fputs ( $fp, $message );
		fclose ( $fp );
		return TRUE;
	}
	function strip_comment($address) {
		$comment = "/\\([^()]*\\)/";
		while ( preg_match ( $comment, $address ) ) {
			$address = preg_replace ( $comment, "", $address );
		}
		return $address;
	}
	function get_address($address) {
		$address = preg_replace ( "/([ \t\r\n])+/", "", $address );
		$address = preg_replace ( "/^.*<(.+)>.*$/", "\\1", $address );
		return $address;
	}
	function smtp_debug($message) {
		if ($this->debug) {
			echo $message . "<br>";
		}
	}
	function get_attach_type($image_tag) { //
		$filedata = array ();
		$img_file_con = fopen ( $image_tag, "r" );
		unset ( $image_data );
		while ( $tem_buffer = AddSlashes ( fread ( $img_file_con, filesize ( $image_tag ) ) ) )
			$image_data .= $tem_buffer;
		fclose ( $img_file_con );
		$filedata ['context'] = $image_data;
		$filedata ['filename'] = basename ( $image_tag );
		$extension = substr ( $image_tag, strrpos ( $image_tag, "." ), strlen ( $image_tag ) - strrpos ( $image_tag, "." ) );
		switch ($extension) {
			case ".gif" :
				$filedata ['type'] = "image/gif";
				break;
			case ".gz" :
				$filedata ['type'] = "application/x-gzip";
				break;
			case ".htm" :
				$filedata ['type'] = "text/html";
				break;
			case ".html" :
				$filedata ['type'] = "text/html";
				break;
			case ".jpg" :
				$filedata ['type'] = "image/jpeg";
				break;
			case ".tar" :
				$filedata ['type'] = "application/x-tar";
				break;
			case ".txt" :
				$filedata ['type'] = "text/plain";
				break;
			case ".zip" :
				$filedata ['type'] = "application/zip";
				break;
			default :
				$filedata ['type'] = "application/octet-stream";
				break;
		}
		return $filedata;
	}
}