<?php

$f3=require('lib/base.php');
// smsc api
require 'smsc_api.php';

$f3->config('config.ini');

// db
$db = new DB\SQL('sqlite:../party.sqlite');
$f3->set('db', $db);

// user hop
$user = '';
if($f3->get('COOKIE._phone')) {
	$phone = $f3->get('COOKIE._phone');
	$pass = $f3->get('COOKIE._pass');
	$man = new DB\SQL\Mapper($db, 'people');
	$man->load(array('phone=?', $phone));
	if($man->pass === $pass) {
		$user = $man;
	}
}
$f3->set('user', $user);

// randpass
function randomPassword() {
    return substr(str_shuffle(str_repeat('ABCDEFGHIJKLMOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,6);
}

// lulz
function greeting($name) {
	$greet = array(
		'Нигматуллина Альфия' 	=> 'Привет, Альфиюша, солнышко :*',
		'Бекиров Артур' 		=> 'Rum, Rum, Rum, Yaarr, Rum, Rum, Ahoy!!!',
		'Гилязетдинов Дамир' 	=> 'Йухуху, Гиляз! Бухаем! <i class="icon-beer"></i>',
		'Латыпов Роман' 		=> 'Думай, что жрать и пить будем, Латыпов',
		'Якупов Тимур' 			=> '<i class="icon-arrow-left"></i> Вы с Кузьмичом отлично смотритесь, Тимурка',
		'Мухутдинов Дмитрий' 	=> 'Приветствую, хозяин',
		'Дусаева Яна' 			=> 'Ты обязана пойти. Без вариантов.',
		'Добренький Антон' 		=> 'А вот и специальный гость нашего мероприятия - Добренькииииииий Аааантооон!',
		'Ковалев Иван' 			=> 'Ни хера се, Вань. И ты туда же.',
		'Дунюшкина Мария' 		=> 'Будь осторожнее с Ширяевым, Маша.',
		'Ширяев Роман' 			=> 'Удачи тебе, Ширяев.',
		'Абубакиров Азат' 		=> 'Нам всем будет очень тебя не хватать, Азыч.',
		'Бабичев Радик' 		=> 'Лысый на патихарде - к удаче',
		'Насретдинов Тимур'		=> 'Тимурка, дай пять!',
		'Насретдинова Диана'	=> 'Мы обязательно споим твоего брата, Диан.',
		'Мухаметшин Тимур'		=> 'Если ты увидишь это сообщение, то я должен тебе 500 рублей. Ей-богу, не вру.',
		'Скрынникова Арина'		=> 'C наступающим днем рожденья, Арина! :3',
	);
	if(!isset($greet[$name])) return 'Ну здравствуй, '.$name;
	return $greet[$name];
}

function gtfo() {
	http_response_code(403);
	echo View::instance()->render('403.html');
	die;
}

$f3->route('GET /',
	function() {
		echo View::instance()->render('index.html');
	}
);

$f3->route('POST /login',
	function($f3) {
		header('Content-Type: application/json');
		$phone = $f3->get('POST.phone');
		$pass = $f3->get('POST.pass');
		$man = new DB\SQL\Mapper($f3->get('db'), 'people');
		$man->load(array('phone=?', $phone));
		if( $man and ($man->pass === md5($pass)) ) {
			echo json_encode(array(
				'success' => true,
				'phone' => $phone,
				'pass' => $man->pass,
			));
		} else {
			echo json_encode(array('success' => false));
		}
	}
);

$f3->route('POST /addgood',
	function($f3) {
		$user = $f3->get('user');
		if(!$user or !$user->is_admin) gtfo();
		$good = new DB\SQL\Mapper($f3->get('db'), 'goods');
		$good->name = $f3->get('POST.name');
		$good->price = $f3->get('POST.price');
		$good->desc = $f3->get('POST.desc');
		$good->img = $f3->get('POST.img');
		$good->save();
	}
);

$f3->route('POST /delgood/@id',
	function($f3) {
		$user = $f3->get('user');
		if(!$user or !$user->is_admin) gtfo();
		$id = $f3->get('PARAMS.id');
		$f3->get('db')->exec("DELETE FROM goods WHERE id=$id;");
	}
);

$f3->route('POST /wannago/@going',
	function($f3) {
		$user = $f3->get('user');
		$phone = $f3->get('POST.phone');
		if( !( $user and ( ($user->phone === $phone) or $user->is_admin ) ) ) gtfo();
		$is_going = $f3->get('PARAMS.going');
		$f3->get('db')->exec("UPDATE people SET is_going=$is_going where phone='$phone';");
	}
);

$f3->route('POST /addpeople',
	function($f3) {
		$user = $f3->get('user');
		if(!$user or !$user->is_admin) gtfo();
		$man = new DB\SQL\Mapper($f3->get('db'), 'people');
		$man->phone = $f3->get('POST.phone');
		$man->name = $f3->get('POST.name');
		$man->pass = md5($f3->get('POST.pass'));
		$man->is_going = 0;
		$man->paid = 0;
		$man->is_admin = 0;
		$man->save();
	}
);

$f3->route('POST /pay/@sum',
	function($f3) {
		$user = $f3->get('user');
		$sum = $f3->get('PARAMS.sum');
		$phone = $f3->get('POST.phone');
		if(!$user or !$user->is_admin) gtfo();
		$f3->get('db')->exec("UPDATE people SET paid=$sum where phone='$phone';");
	}
);

$f3->route('GET /passinit',
	function($f3) {
		$user = $f3->get('user');
		if(!$user or !$user->is_admin) gtfo();
		$guys = $f3->get('db')->exec('SELECT phone FROM people WHERE is_admin=0;');
		foreach ($guys as $guy) {
			$pass_orig = randomPassword();
			$pass = md5($pass_orig);
			$f3->get('db')->exec("UPDATE people SET pass='$pass' WHERE phone='{$guy["phone"]}';");
			$send = send_sms('+7'.$guy['phone'], "Твой пароль от патихарда: $pass_orig. Подробности в группе 11В.");
		}
	}
);

$f3->route('GET /payremind',
	function($f3) {
		$user = $f3->get('user');
		if(!$user or !$user->is_admin) gtfo();
		$debters = $f3->get('db')->exec('SELECT phone, name FROM people WHERE is_going=1 AND paid=0;');
		foreach($debters as $d) {
			$fname = substr($d['name'], strpos($d['name'], ' '));
			$send = send_sms('+7'.$d['phone'], "{$d['name']}, сдай 1000 р на патихард 25го Мухутдинову или Латыпову");
		}
		print_r($debters);
	}
);

$f3->run();
