<html>
<head>
<title>Plotter v1.0 by Mark Manning</title>
</head>
<body style="background-color:black;foreground-color:white;">
<?php
#
#	Defines
#
	if( !defined("[]") ){ define( "[]", "array[]" ); }
#
#	Standard error function
#
	set_error_handler(function($errno, $errstring, $errfile, $errline ){
#		throw new ErrorException($errstring, $errno, 0, $errfile, $errline);
		scrPrint( "\e(B" ); # End the line drawing mode
		die( "Error #$errno IN $errfile @$errline\nContent: " . $errstring. "\n" );
		});

	ini_set( 'memory_limit', -1 );
	date_default_timezone_set( "UTC" );
#
#	$lib is where my libraries are located.
#	>I< have all of my libraries in one directory called "<NAME>/PHP/libs"
#	because of my UNIX background. So I used the following to find them
#	no matter where I was. I created an environment variable called "my_libs"
#	and then it could find my classes. IF YOU SET THINGS UP DIFFERENTLY then
#	you will have to modify the following.
#
	spl_autoload_register(function ($class){
#
#	This might seem stupid but it works. If X is there - get rid of it and then put
#	X onto the string. If X is not there - just put it onto the string. Get it?
#
		$class = str_ireplace( ".php", "", $class ) . ".php";

		$lib = getenv( "my_libs" );
		$lib = str_replace( "\\", "/", $lib );

		if( file_exists("./$class") ){ $lib = "."; }
			else if( file_exists("../$class") ){ $lib = ".."; }
			else if( !file_exists("$lib/$class") ){
				die( "Can't find $libs/$class - aborting\n" );
				}

		include "$lib/$class";
		});

	global $cwd, $cf, $pr, $exe, $process, $circuit;
	global $pipes, $pipe_length, $pipe_wait;
	global $windows, $base;
	global $brdFGC, $brdBGC;

	$dq = '"';
	$env = null;
	$pipes = null;
	$cwd = getcwd();
	$cwd = str_replace( "\\", "/", $cwd );
	$imgDir = "Images";

	$cf = new class_files();
	$cr = new class_rgb();
	$pr = new class_pr();

	$alpha = sprintf( "%02x%02x%02x%02x", 127, 0, 0, 0 );
	$alpha = 'black';
#
#	Now clear the screen and start working on the paper information.
#
	if( true || !file_exists("$imgDir/Title-1.png") ){
		$title = <<<EOD
 _____  _       _   _
|  __ \| |     | | | |
| |__) | | ___ | |_| |_ ___ _ __
|  ___/| |/ _ \| __| __/ _ \ '__|
| |    | | (_) | |_| ||  __/ |
|_|    |_|\___/ \__|\__\___|_|
           Version 1.0
           by Mark Manning

EOD;

		$gd = imagecreatetruecolor( 640, 480 );
		$grey = imagecolorallocate( $gd, 0xbb, 0xbb, 0xbb );
		$white = imagecolorallocate( $gd, 0xff, 0xff, 0xff );
#		$font = "monos.ttf";
		$font = "consola.ttf";
#		$font = "cour.ttf";

		imagefttext( $gd, 12, 0, 10, 10, $white, $font, $title );
		imagepng( $gd, "$imgDir/Title-1.png" );
		}

	echo <<<EOD
<div id="id_about" class="class_about" onload="func_about(10)">
<table>
<td><img src="$imgDir/Title-1.png" alt="Plotter v1.0 by Mark Manning"></td>
<td><button id="some_text" type='button'>Time remaining : 10</button></td>
</table>
</div>
<script>
function func_about(numsec)
{
	document.getElementById("some_text").innerHTML = numsec;
	if( numsec > 0 ){ sleep(1); func_about(numsec-1); return; }
	document.getElementById("id_about").style.display = "none";
	return;
}
EOD;
?>
</body>
</html>

