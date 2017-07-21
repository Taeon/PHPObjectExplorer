<?php

// Show all errors
error_reporting(-1);
ini_set('display_errors', 1);
ini_set('default_charset', 'UTF-8');

// Avoid strict errors when timezone not set
date_default_timezone_set( 'Europe/London' );

require_once( 'autoloader.php' );

class Bar{
	public static $con = 1;
	protected $bar = array();
	//public $foo;
	public function SetFoo( $foo ){
		$this->bar[] = $foo;
	}
}

$bar = new Bar();
$baz = new Bar();
$bar->foo = $baz;
$baz->foo = $bar;
$baz->plim = 1;
$bar->SetFoo(1);
$bar->SetFoo(2);
$bar->SetFoo(3);
$foo = new \PhpObjectExplorer\Explorer( $bar );

echo($foo->Render());
?>
