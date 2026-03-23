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
		die( "Error #$errno IN $errfile @$errline\nContent: " . $errstring. "\n" );
		});

	ini_set( 'memory_limit', -1 );
	date_default_timezone_set( "UTC" );
#
#	$libs is where my libraries are located.
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

		$libs = getenv( "my_libs" );
		$libs = str_replace( "\\", "/", $libs );

		if( file_exists("./$class") ){ $libs = "."; }
			else if( file_exists("../$class") ){ $libs = ".."; }
			else if( !file_exists("$libs/$class") ){
				die( "Can't find $libs/$class - aborting\n" );
				}

		include "$libs/$class";
		});

	$cwd = getcwd();
	$git = "C:\Users\marke\Documents\GitHub\PHP-Classes";
	$git = str_replace( "\\", "/", $git );
	$bsd = "BSD-3-Patent.txt";
	$lib = "D:\My Programs\PHP\lib";
	$lib = str_replace( "\\", "/", $lib );

	$cf = new class_files();
	$pr = new class_pr();

if( true ){
	list( $g, $b ) = $cf->get_files( $cwd, "/class_.*.php$/i", false );
	foreach( $g as $k=>$v ){
		if( preg_match("/php-class/i", $v) ){ continue; }
		$pathinfo = pathinfo( $v );
		$file = $pathinfo['basename'];
		$curfile = $pathinfo['filename'];
		$curdir = "$git/$curfile";
		$dir = $pathinfo['dirname'];
		if( !file_exists($curdir) ){ mkdir( $curdir, 0777 ); }

		echo "Copying $v to $curdir/$file\n";
		copy( $v, "$curdir/$file" );

		echo "Copying $lib/$bsd to $curdir/$bsd\n";
		copy( "$lib/$bsd", "$curdir/$bsd" );
		}
}
#
#	Now put the other programs to the GitHub stuff
#	First is to change where the files go.
#
	$g = explode( "/", $git );
	array_pop( $g );
	$g[] = "PHP-Programs";
	$git = implode( "/", $g );

	$files = array( "fixenv.php", "putGIT.php", "plotter.php", "paperTypes.php",
		"getFedReps.php", "getStReps.php" );

	$fromDirs = array(
		"D:\My Programs\PHP\Fixenvs",
		"D:\My Programs\PHP\lib",
		"D:/My Programs/PHP/Plotter",
		"D:\My Programs\PHP\Paper Types",
		"D:\My Programs\PHP\WYRE",
		"D:\My Programs\PHP\WYRE"
		);

	$curFiles = array( "fixenv", "putGIT", "plotter", "paperTypes",
		"getFedReps", "getStReps" );

	foreach( $files as $k=>$v ){
		$file = $v;
		$fromDir = $fromDirs[$k];
		$curfile = $curFiles[$k];
		if( !preg_match("/(php|csv|exe|bas)$/i", $v) ){ continue; }

		$toDir = "$git/$curfile";
		if( !file_exists($toDir) ){ mkdir( $toDir, 0777 ); }

		echo "Copying $fromDir/$file to $toDir/$file\n";
		copy( "$fromDir/$file", "$toDir/$file" );

		echo "Copying $lib/$bsd to $toDir/$bsd\n";
		copy( "$lib/$bsd", "$toDir/$bsd" );
		}

	echo "Finished\n";
?>
