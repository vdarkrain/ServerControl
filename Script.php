<?php

date_default_timezone_set('Europe/Moscow');

require_once "vendor/autoload.php";
require_once "src/Frago9876543210/Query/Query.php";

const green = "\033[32m{text}\033[37m\r\n";
const yellow = "\033[33m{text}\033[37m\r\n";


function prg($text){
	$message = date("[H:i:s] ") . $text;
	$text = str_replace("{text}", $message, green);
	echo($text);
}

function prr($text){
	$message = date("[H:i:s] ") . $text;
	$text = str_replace("{text}", $message, yellow);
	echo($text);
}

$class = new Main();
$class->Start();

class Main{

	public $ip = "king-land.ru"; // айпи
	public $port = 19132; // порт

	public $players = [];
	public $targets = [];

	public $targetmode = false; //не изменять

	public function Start(){
		prr("API 1.0 включается");
		
		$this->Init();

		if($this->targetmode){
			$text = "игроками: ". implode(", ", $this->targets);
		}else{
			$text = "всеми игроками";
		}
		prr("Идёт наблюдение за $text");

		while(true){
			$query = (new \Frago9876543210\Query\Query($this->ip, $this->port))->getResult();

			if(!isset($query["players"])){
				sleep(2);
				continue;
			}

			foreach($query["players"] as $nickname){
				if($this->targetmode && !in_array($nickname, $this->targets))
					continue;
				if(!isset($this->players[$nickname])){
					$this->players[$nickname] = time();
					prg($nickname ." присоединился к игре");
					continue;
				}
			}
			foreach($this->players as $nickname => $time){
				if($this->targetmode && !in_array($nickname, $this->targets))
					continue;
				if(!in_array($nickname, $query["players"])){
					prr($nickname ." покинул игру. Он был в игре ". gmdate("H часов, i минут, s секунд.", time() - $this->players[$nickname]));
					unset($this->players[$nickname]);
					continue;
				}
			}

			sleep(2);
		}
	}

	public function Init(){

		if($this->targets != [])
			$this->targetmode = true;

		$query = (new \Frago9876543210\Query\Query($this->ip, $this->port))->getResult();
		foreach($query["players"] as $nickname){
			if($this->targetmode && !in_array($nickname, $this->targets))
					continue;
			$this->players[$nickname] = time();
		}
	}
}