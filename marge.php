#!/usr/bin/env php
<?php
/*	Thanks to Freelancer for making the framework of this bot.
	You can find him at GitHub.com/Visnew
	Please read the README file for help.	*/
	
/*	
==Revision History==
	0.0.1 Initial Reversion - iYorkie - http://github.com/iYorkie
	0.0.2 Major Revision - Neo-Desktop - http://github.com/neo-Desktop
		Code documentation
		POSIX/ANSI console colors
		Outlined Helpful debugging (needs work)
		Fixed tabs
		Propper coding convntions
		Outlined 'Route-Based' system (needs work)
		Awareness of channels and nicks
		Will listen in commands reguardles of input type (notice, privmsg etc.) -- Yes this is a 'feature'
		Added Some Commands
*/

/*
==Route Modes Table:==
0 - Way it came
--Force--
1 - PRIVMSG
2 - ACTION
3 - NOTICE
4 - CTCP
5 - DCC
--Anything Else--
Same as 0
*/

/*
==Debug Modes Table:==
0 = Will show up always
1 = Level one [Commands Right Now]
2 = Level two [important thinks like ping pong]
*/

include "config.php";
set_time_limit(0);


class IRCBot {
	var $socket;
	var $version = "0.0.2";
	var $ex = array();
	
	var $colors = array("red" => "\x1b[31m", "yellow" => "\x1b[33m", "green" => "\x1b[32m", "blue" => "\x1b[34m", "white" => "\x1b[37m", "black" => "\x1b[30m", "magenta" => "\x1b[35m", "cyan" => "\x1b[36m", "reset" =>"\x1b[0m", "bold" => "\x1b[1m", "blink" => "\x1b[5m");
	
	/*** Logging Functions ***/
	function timestamp() {
		return $this->colors["yellow"]."[". $this->colors["green"] . date('H:m:s') . $this->colors["yellow"] ."]". $this->colors["reset"];
	}
	
	function cprintf($payload) {
		print $this->timestamp() ." ". $this->colors["green"] . $payload . $this->colors["reset"] ."\n";
	}
	
	function inprintf($payload) {
		print $this->timestamp() ." ". $this->colors["reset"] . $payload . $this->colors["reset"];
	}
	function outprintf($payload) {
		print $this->timestamp() ." ". $this->colors["blue"] . $payload . $this->colors["reset"] ."\n";
	}
	
	function debugprint($payload, $level = 1) {
		if (!$config["__DEBUG__"]) {
			return; // Don't want to bother people who don't want debug
		}
		if ($config["__DEBUG__"] >= 1 && $level <= 1) {
			print $this->timestamp() ." ". $this->colors["yellow"] ."DEBUG: ". $payload . $this->colors["reset"];
		}
		if ($config["__DEBUG__"] >= 2 && $level >= 2) {
			print $this->timestamp() ." ". $this->colors["yellow"] ."DEBUG: ". $payload . $this->colors["reset"];
		}
	}
	
	function fatalprint($payload) {
		print $this->timestamp() ." ". $this->colors["red"] ."FATAL: ". $payload . $this->colors["reset"];
	}
	
	function dtrace() {
		cprintf($this->colors["yellow"] ."DEBUG TRACE: ");
		debug_print_backtrace();
	}
	/*** End Logging Functions ***/

	/*** Communication Functions ***/
	function raw($payload) {
		fputs($this->socket, trim($payload) ."\r\n");
		$this->outprintf($payload);
	}
	
	function message($payload, $route) {
		$this->raw("PRIVMSG ". $route ." :". $payload);
	}
	
	function notice($payload, $route) {
		$this->raw("NOTICE ". $route ." :" .$payload);
	}
	
	function join($payload) {
		$this->raw("JOIN ". $payload);
	}
	
	function part($payload, $message = NULL) {
		if ($message == NULL) {
			$message = $config["partmessage"];
		}
		$this->raw("PART ". $payload .": ". $message);
	}
	
	function quit($payload = NULL) {
		if ($payload == NULL) {
			$payload = $config["quitmessage"];
		}
		$this->raw("QUIT :". $payload);
	}
	
	function nick($payload) {
		$this->raw("NICK ". $payload);
	}
	
	function mode($route, $modes, $payload) {
		$this-> raw("MODE ". $route ." ". $modes ." ". $payload);
	}
	
	function action($payload, $route) {
		$this->message('\001' . " :$payload" . '\001', $route);
	}
	/*** End Communication Functions ***/
	
	/*** PHP Functions ***/
	function __construct() {
		global $config; // TODO: get rid of globals! -- Neo
		print "Welcome to". $this->colors["bold"] ."Marge IRC Bot" .$this->colors["reset"] ."Version". $this->colors["bold"] . $version . $this->colors["reset"] ."\r\n"; 
		$this->socket = fsockopen($config["server"], $config["port"]); // open socket
		$this->raw("USER ". $config["ident"] ." tolmoon tolsun :".$config["realname"]); // log in
		$this->nick($config["nick"]); // inital nickname
		$this->main(); // start handling
	}
	
	function __cleanup() {
		unset ($payload, $route, $ex, $nick, $user, $host, $socket, $data);
	}
	/*** End PHP Functions ***/
	
	function main() {
		global $config; //TODO: srsly
		while (true) { // infinate loop
			if (!$this->socket) { // if dead socket
				fatalprint("Could not connect to the specified server. Please try another host or IP address"); // stop here
				break; // break out of loop.
			}

			$data = fgets($this->socket, 4096); // get 2 KiB of buffer
			flush(); // write everything in console buffer to console screen
			
			if ($data) {
				$this->inprintf($data);
			}
			
			$this->ex = explode(' ', $data); // create array $ex of each word (seperated by ' ')
			$mask = ltrim($this->ex[0], ':'); // get rid of preceding :
			// split nick!user@host
			$nick = split('!', $this->ex[0]);
			$nick = split(':', $nick[0]);
			$host = split('@', $this->ex[0]);
			$host = $host[1];
			$chan = $this->ex[2]; // get chan
			
			$numeric = $this->ex[1]; //?
			$message = split($this->ex[2].' :', $data); //wait what?
			$message = $message[1];
			$year = date('Y'); // 4 digit year
			$month = date('m'); // 2 digit month
			$day = date('d'); // 2 digit day

			if ($this->ex[0] == "PING") {
				$this->debugprint("Ping?",2);
				$this->raw("PONG :". ltrim($this->ex[1], ':'));
				$this->debugprint("Pong!",2);
			}

			if ($this->ex[1] == "376" || $this->ex[1] == "422") {
				$this->join($config["channels"]);
				$this->message("identify " .$config['password'], "NickServ");
			}
			
			elseif (is_numeric(trim($this->ex[1]))) {
				$this->debugprint("WARNING: Numeric Not Handled: " .trim($this->ex[1]));
			}
			
			if (substr($this->ex[2],1) == "#") { //check to see if what we picked up was in a channel or not
				$this->in_chan = true;
				$route = $this->ex[2];
			}
			else {
				$this->in_chan = false;
				$route = $this->ex[2];
			}
			$cmd = ltrim(strtolower($this->ex[3]),':');
			if (substr($cmd,1) == $config["prefix"]) { // if the command starts with an !
				ltrim($cmd, $config["prefix"]); // trim it
			}
			else {
				if ( !$this->in_chan ) {
					//return; TODO: not fail here. return = bad.
				}
			}

			if ($cmd == "!uptime") {
				$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
				$payload = trim(shell_exec("uptime"));
				$this->message($payload, $route);
			}
			
			elseif ($cmd == "!md5hash") {
				$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
				$payload = split($this->ex[3] .' ', $data); //TODO: make this better.. can be fixed
				$payload = trim($payload[1]);
				$payload = hash('md5', $payload);
				$this->message("Your hash has been created. Your MD5 Hash is ". $payload, $route);
			}

			elseif ($cmd == "!sha1hash") {
				$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
				$payload = split($this->ex[3] .' ', $data); //TODO: make this better.. can be fixed
				$payload = trim($payload[1]);
				$payload = hash('sha1', $payload);
				$this->message("Your hash has been created. Your SHA1 Hash is ". $payload, $route);
			}

			elseif ($cmd == "!sha256hash") {
				$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
				$payload = split($this->ex[3] .' ', $data); //TODO: make this better.. can be fixed
				$payload = trim($payload[1]);
				$payload = hash("sha256", $payload);
				$this->message("Your hash has been created. Your SHA256 Hash is ". $payload, $route);
			}

			elseif ($cmd == '!dns') {
				$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
				$chan = $this->ex[2];
				if (isset($this->ex[4])) {
					if (is_numeric(str_replace('.', '', $this->ex[4]))) {	// Not to sure what the bloody hell goes on in this line
						$payload = gethostbyaddr($this->ex[4]);
						$this->message("Reverse DNS resolved ". $this->ex[4] ." to the hostname: ". $payload, $route);
					}
					else { // LAZY PEOPLE
						$this->ex[4] = str_replace('http://', '', $this->ex[4]);
						$this->ex[4] = str_replace('https://', '', $this->ex[4]);
						$this->ex[4] = str_replace('ftp://', '', $this->ex[4]);
						$this->ex[4] = str_replace('gopher://', '', $this->ex[4]);
						$this->ex[4] = str_replace('rsync://', '', $this->ex[4]);
						$this->ex[4] = str_replace('/', '', $this->ex[4]); //trailing slah i assume?
						$payload = gethostbyname($this->ex[4]);
						$this->message("DNS resolved ". $this->ex[4] ." to the IP address: ". $payload, $route);
					}
				}
				else {
					$this->message("Please specify an IP address or hostname.", $route);
				}
			}

			elseif ($cmd == "!qdbadd") {
				$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
				$quote = split("qdbadd ", $data);
				$quote = $quote[1];
				$chan = $this->ex[2];
				if (isset($quote)) {
					$fh = fopen($this->directory."quotenum.txt", "r");
					$cur_quotenum = fread($fh, filesize($this->directory ."quotenum.txt")) + 1;
					fclose($fh);
					
					$fh = fopen($this->directory."quote_". $cur_quotenum .".txt", "w") or die("can't open file");
					fwrite($fh, $quote);
					fclose($fh);

					$fh = fopen($this->directory."quotenum.txt", "w") or die("can't open file");
					fwrite($fh, $cur_quotenum);
					fclose($fh);
					
					$this->message("Added quote number ". $cur_quotenum ."/". $cur_quotenum .": ".$quote, $route);
				}
			}

			elseif ($cmd == "!qdb") {
				$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
				$fh = fopen($this->directory."quotenum.txt", "r");
				$chan = $this->ex[2];
				$cur_quotenum = fread($fh, filesize($this->directory."quotenum.txt"));
				fclose($fh);
				
				$fh = fopen($this->directory."quote_".$this->ex[4].".txt", "r");
				$cur_quote = fread($fh, filesize($this->directory."quote_". $this->ex[4] .".txt"));
				fclose($fh);
				
				if (file_exists($this->directory."quote_".$this->ex[4].".txt")) {
					$this->message("Quote number ". $this->ex[4] ."/". $cur_quotenum .": ". $cur_quote, $route);
				}
				else {
					$this->message("I cant find that quote! There have been ". trim($cur_quotenum) ." quotes added to this system so far.", $route);
				}
			}

			if ($cmd == "!qdbtotal") {
				$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
				$chan = $this->ex[2];
				$fh = fopen($this->directory."quotenum.txt", "r");
				$cur_quotenum = fread($fh, filesize($this->directory."quotenum.txt"));
				fclose($fh);
				$this->message("There are ". trim($cur_quotenum) ." quote(s) on this system so far.", $route);
			}

			if ($cmd == "!randqdb") {
				$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
				$chan = $this->ex[2];
				$fh = fopen($this->directory."quotenum.txt", "r");
				$cur_quotenum = fread($fh, filesize($this->directory."quotenum.txt"));
				fclose($fh);
				$rand = rand(1, $cur_quotenum);
				$fh = fopen($this->directory."quote_".$rand.".txt", "r");
				$cur_quote = fread($fh, filesize($this->directory."quote_".$rand.".txt"));
				fclose($fh);
				if (file_exists($this->directory."quote_".$rand.".txt")) {
					$this->message("Quote number ". $rand ."/". $cur_quotenum .": ". $cur_quote, $route);
				}
				else {
					$this->message("I cant find that quote! There have been ". trim($cur_quotenum) ." quotes added to this system so far.", $route);
				}
			}
			if (strtolower($this->ex[2]) == strtolower($config["controlchan"])) {
			
				if ($cmd == "!identify") {
					$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
					$this->message("identify ".$config["password"], "NickServ");
					$this->message("Successfully sent identification command to NickServ", $route);
				}
				
				if ($cmd == "!shutdown") {
					$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
					$payload = split($this->ex[3].' ', $data);
					$payload = trim($payload[1]);
					$this->message("Quitting as per to ". $nick ."'s Request", $route);
					sleep(2);
					$this->quit($payload);
                                   die("Shutdown comand used.");
				}
				
				if ($cmd == "!nick") {
					$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
					$payload = split($this->ex[3].' ', $data);
					$payload = trim($payload[1]);
					$this->message("Changing nickname to: ". $payload, $route);
					$this->nick($payload);
				}
				
				if ($cmd == "!join") {
					$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
					$payload = split($this->ex[3].' ', $data);
					$payload = trim($payload[1]);
					$this->message("Joining channel: ". $payload, $route);
					$this->join($payload);
				}
				
				if ($cmd == "!part") {
					$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
					$payload = split($this->ex[3].' ', $data);
					$payload = trim($payload[1]);
					$this->message("Parting channel: ". $payload, $route);
					$this->part($payload);
				}
				
				if ($cmd == "!trace") {
					$this->debugprint("Reccieved ". $cmd ."command from: ". $mask ."On line: ". __LINE__);
					$this->dtrace();
					$this->message("Debug trace sent to console");
				}
				
				if ($cmd == "qdbdel") {
					$chan = $this->ex[2];
					$cur_quotenum = fread($fh, filesize($this->directory."quotenum.txt")) - 1;
					unlink($this->directory."quote_".$this->ex[4].".txt");
					$this->message("Quote number ". $this->ex[4] ." has been removed from my database.", $route);
				}
				
				if ($cmd == "!restart" && strtolower($this->ex[2]) == strtolower($config["controlchan"])) {
					$this->quit("Restart has been ordered. Restarting...");
					sleep(2);
					$output = shell_exec("php ". $_SERVER["SCRIPT_NAME"] ."&& kill ". getmypid());
				}
			}
			else {
				if($config["__DEBUG__"] == 1) {
					$this->cprintf("Command Not Found: ". $cmd);
					$this->message("Command Not Found: " .$cmd,$route);
				}
				else {
					$this->cprintf("Command Not Found: ". $cmd);
				}
			}
		}
		$this->fatalprint("Something went wrong! Broken out of loop!");
		$this->dtrace();
		// hmm... something broke us out of the loop if we're here...
		// better log an error, wait for connection time out (setting it at 250 sec)
		// and re-init.
		$this->debugprint("Sleeping for 250 sec.");
		sleep(250);
		$this->__cleanup();
		$this->cfprint($this->colors["green"] ."Reconnecting...");
		$this->__construct();
	}
}

$bot = new IRCBot();
?>