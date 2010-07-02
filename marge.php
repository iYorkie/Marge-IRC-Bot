/* Thanks to Freelancer for making the framework of this bot. */
/* You can find him at GitHub.com/Visnew                      */
/* Please read the README file for help.                      */
<?php

include "config.php";

	set_time_limit(0);
	class IRCBot {
		var $socket;
		var $ex = array();

		function __construct()
		{
			global $config;
			$this->socket = fsockopen($config['server'], $config['port']);
			$this->send_data('USER', $config['ident'].' 8 * :'.$config['realname']);
			$this->send_data('NICK', $config['nick']);
                     $this->main();
		}
		function main()
		{
	$mask = str_replace(':', '', $this->ex[0]);
	$host = split('@', $this->ex[0]);
	$host = $host[1];
	$chan = $this->ex[2];
	$nick = split('!', $this->ex[0]);
	$nick = split(':', $nick[0]);
	$nick = $nick[1];
	$numeric = $this->ex[1];
	$message = split($this->ex[2].' :', $data);
	$message = $message[1];
	$year = date('Y');
	$month = date('m');
	$cmd = strtolower(str_replace(":", "", $this->ex[3]));

			global $config;
			while (true)
			{
				if (!$this->socket)
				{
					die("Could not connect to the specified server. Please try another host or IP address.\r\n");
				}

				$data = fgets($this->socket, 4096);
				flush();
				$this->ex = explode(' ', $data);

				foreach ($this->ex as &$trim)
				{
					$trim = trim($trim);
				}

				if ($this->ex[0] == 'PING')
				{
					$this->send_data('PONG', $this->ex[1]);
				}

				if ($this->ex[1] == '376' || $this->ex[1] == '422')
				{
					$this->send_data('JOIN', $config['channels']);
                                   $this->send_data('PRIVMSG', 'NickServ :identify '.$config['password'].'');
                            
                          
                            }

        
                            if ($cmd == '!uptime') {
                            $output = shell_exec('uptime');
                            $output = trim($output);
                            $chan = $this->ex[2];
                            $this->send_data('PRIVMSG', $chan.' :'.$output);

                            }

                           if ($cmd == '!sha256hash') {
                            $password = split($this->ex[3].' ', $data);
				$password = trim($password[1]);
                            $hashpass = hash('sha256', $password);
                            $chan = $this->ex[2];
                            $this->send_data('PRIVMSG', ''.$chan.' :Your hash has been created. Your SHA256 Hash is '.$hashpass.'');

                            }

                            if ($cmd == '!md5hash') {
                            $password = split($this->ex[3].' ', $data);
				$password = trim($password[1]);
                            $hashpass = hash('md5', $password);
                            $chan = $this->ex[2];
                            $this->send_data('PRIVMSG', ''.$chan.' :Your hash has been created. Your MD5 Hash is '.$hashpass.'');

                            }

                             if ($cmd == '!sha1hash') {
                            $password = split($this->ex[3].' ', $data);
				$password = trim($password[1]);
                            $hashpass = hash('sha1', $password);
                            $chan = $this->ex[2];
                            $this->send_data('PRIVMSG', ''.$chan.' :Your hash has been created. Your SHA1 Hash is '.$hashpass.'');

                            }


                            if ($cmd == 'sha256hash') {
                            $password = split($this->ex[3].' ', $data);
				$password = trim($password[1]);
                            $hashpass = hash('sha256', $password);
                            $nick = split('!', $this->ex[0]);
	                     $nick = split(':', $nick[0]);
	                     $nick = $nick[1];
                            $this->send_data('PRIVMSG', ''.$nick.' :Your hash has been created. Your SHA256 Hash is '.$hashpass.'');

                            }

                            if ($cmd == 'md5hash') {
                            $password = split($this->ex[3].' ', $data);
				$password = trim($password[1]);
                            $hashpass = hash('md5', $password);
                            $nick = split('!', $this->ex[0]);
	                     $nick = split(':', $nick[0]);
	                     $nick = $nick[1];
                            $this->send_data('PRIVMSG', ''.$nick.' :Your hash has been created. Your MD5 Hash is '.$hashpass.'');

                            }

                             if ($cmd == 'sha1hash') {
                            $password = split($this->ex[3].' ', $data);
				$password = trim($password[1]);
                            $hashpass = hash('sha1', $password);
                            $nick = split('!', $this->ex[0]);
	                     $nick = split(':', $nick[0]);
	                     $nick = $nick[1];
                            $this->send_data('PRIVMSG', ''.$nick.' :Your hash has been created. Your SHA1 Hash is '.$hashpass.'');

                            }

if ($cmd == $config['prefix'].'dns') {
              $chan = $this->ex[2];
		if (isset($this->ex[4])) {
			if (is_numeric(str_replace('.', '', $this->ex[4]))) {
				$hostname = gethostbyaddr($this->ex[4]);
				$this->send_data('PRIVMSG', $chan.' :Reverse DNS resolved '.$this->ex[4].' to the hostname: '.$hostname);
			}
			else {
				$this->ex[4] = str_replace('http://', '', $this->ex[4]);
				$this->ex[4] = str_replace('https://', '', $this->ex[4]);
				$this->ex[4] = str_replace('/', '', $this->ex[4]);
				$hostname = gethostbyname($this->ex[4]);
				$this->send_data('PRIVMSG', $chan.' :DNS resolved '.$this->ex[4].' to the IP address: '.$hostname);
			}
		}
		else {
			$this->send_data('PRIVMSG', $chan.' :Please specify an IP address or hostname.');
		}
	}

if ($cmd == '!restart' && strtolower($this->ex[2]) == strtolower($config['controlchan'])) {
				$this->send_data('QUIT', ':Restart has been ordered. Restarting...');
				sleep(1);
				$output = shell_exec('php marge.php && kill '.getmypid());
         
			}

	if ($cmd == $config['prefix'].'qdbadd') {
		$quote = split(' :!qdbadd ', $data);
		$quote = $quote[1];
              $chan = $this->ex[2];
		if (isset($quote)) {
			/* - */
			$fh = fopen($this->directory."quotenum.txt", 'r');
			$cur_quotenum = fread($fh, filesize($this->directory."quotenum.txt")) + 1;
			fclose($fh);
			/* - */
			$fh = fopen($this->directory.'quote_'.$cur_quotenum.".txt", 'w') or die("can't open file");
			fwrite($fh, $quote);
			fclose($fh);

			$fh = fopen($this->directory."quotenum.txt", 'w') or die("can't open file");
			fwrite($fh, $cur_quotenum);
			fclose($fh);
			/* - */
			$this->send_data('PRIVMSG', $chan.' :Added quote number '.$cur_quotenum.'/'.$cur_quotenum.': '.$quote);
		}
	}

	if ($cmd == $config['prefix'].'qdb') {
		/* - */
		$fh = fopen($this->directory."quotenum.txt", 'r');
              $chan = $this->ex[2];
		$cur_quotenum = fread($fh, filesize($this->directory."quotenum.txt"));
		fclose($fh);
		/* - */
		$fh = fopen($this->directory."quote_".$this->ex[4].".txt", 'r');
		$cur_quote = fread($fh, filesize($this->directory."quote_".$this->ex[4].".txt"));
		fclose($fh);
		if (file_exists($this->directory."quote_".$this->ex[4].".txt"))
		{
			$this->send_data('PRIVMSG', $chan.' :Quote number '.$this->ex[4].'/'.$cur_quotenum.': '.$cur_quote);
		}
		else {
			$this->send_data('PRIVMSG', $chan.' :I cant find that quote!. There have been '.trim($cur_quotenum).' quote(s) added to this system so far.');


		}
	}

	if ($cmd == $config['prefix'].'qdbtotal') {
              $chan = $this->ex[2];
		$fh = fopen($this->directory."quotenum.txt", 'r');
		$cur_quotenum = fread($fh, filesize($this->directory."quotenum.txt"));
		fclose($fh);
		$this->send_data('PRIVMSG', $chan.' :There are '.trim($cur_quotenum).' quote(s) on this system so far.');
	}

	if ($cmd == $config['prefix'].'randqdb') {
              $chan = $this->ex[2];
		$fh = fopen($this->directory."quotenum.txt", 'r');
		$cur_quotenum = fread($fh, filesize($this->directory."quotenum.txt"));
		fclose($fh);
		$rand = rand(1, $cur_quotenum);
		$fh = fopen($this->directory."quote_".$rand.".txt", 'r');
		$cur_quote = fread($fh, filesize($this->directory."quote_".$rand.".txt"));
		fclose($fh);
		if (file_exists($this->directory."quote_".$rand.".txt"))
		{
			$this->send_data('PRIVMSG', $chan.' :Quote number '.$rand.'/'.$cur_quotenum.': '.$cur_quote);
		}
		else {
			$this->send_data('PRIVMSG', $chan.' :I cant find that quote! There have been '.trim($cur_quotenum).' quote(s) added to this system so far.');
		}
	}



if ($cmd == '!identify' && strtolower($this->ex[2]) == strtolower($config['controlchan'])) {
   $this->send_data('PRIVMSG', 'NickServ :identify '.$config['password'].'');
}
if ($cmd == '!shutdown' && strtolower($this->ex[2]) == strtolower($config['controlchan'])) {
   $this->send_data('QUIT', ':'.$config['quitmessage'].'');
   die("Shutdown command has been issued from IRC. Exiting...");
}
if ($cmd == '!nick' && strtolower($this->ex[2]) == strtolower($config['controlchan'])) {
  $newnick = split($this->ex[3].' ', $data);
  $newnick = trim($newnick[1]);
   $this->send_data('NICK', ':'.$newnick.'');
}
if ($cmd == '!join' && strtolower($this->ex[2]) == strtolower($config['controlchan'])) {
  $joinchan = split($this->ex[3].' ', $data);
  $joinchan = trim($joinchan[1]);
   $this->send_data('JOIN', ':'.$joinchan.'');
}
if ($cmd == '!part' && strtolower($this->ex[2]) == strtolower($config['controlchan'])) {
  $partchan = split($this->ex[3].' ', $data);
  $partchan = trim($partchan[1]);
   $this->send_data('PART', ':'.$partchan.'');
}
if ($cmd == '!qdbdel' && strtolower($this->ex[2]) == strtolower($config['controlchan'])) {
$chan = $this->ex[2];
$cur_quotenum = fread($fh, filesize($this->directory."quotenum.txt")) - 1;
unlink($this->directory."quote_".$this->ex[4].".txt");
$this->send_data('PRIVMSG', $chan.' :Quote number '.$this->ex[4].' has been removed from my database.');


				}
			}
		}



		function send_data($cmd, $msg = null) 
		{
			fputs($this->socket, trim($cmd.' '.$msg)."\r\n");
			echo trim($cmd.' '.$msg)."\r\n";
		}
	}
	$bot = new IRCBot();
?>  }