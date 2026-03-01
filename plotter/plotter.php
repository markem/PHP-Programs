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

	global $cwd, $cf, $pr, $exe, $process, $pipes, $circuit;

	$circuit = array(
		0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
		1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
		2 => array("pipe", "w"),  // stderr is a pipe that the child will write to
#		2 => array("file", "c:/tmp/stderr.txt", "w"),  // stderr is a pipe that the child will write to
		);

	$cwd = getcwd();
	$bas = "inkey.bas";
	$exe = "inkey.exe";
	$qb64_path = getenv( "qb64_path" );
	if( strlen(trim($qb64_path)) < 1 ){
		die( "***** ERROR : You need to install QB64.\n" );
		}

	$dq = '"';
	$env = null;
	$pipes = null;

	echo "Making the inkey.bas file...please wait\n";
	makeQBKey( $bas ); sleep( 3 );

	echo "Compiling the inkey.bas file and creating the inkey.exe file...please wait\n";
	exec( "$qb64_path/qb64.exe $bas -c -o $dq$cwd/$exe$dq" ); sleep( 3 );

	$cf = new class_files();
	$cr = new class_rgb();
	$pr = new class_pr();

#	$papers = $cf->fget_csv( "paperTypes.ini" );
	$papers = papers();
$pr->pr( $papers, "Papers = " ); exit;

	$alpha = sprintf( "%02x%02x%02x%02x", 127, 0, 0, 0 );
	$alpha = 'black';

	if( is_null("$cwd/$exe") || !file_exists("$cwd/$exe") ){
		die( "***** ERROR : No such file ('$cwd/$exe') or FILE is NULL\n" );
		}

	$cmd = "$dq$cwd/$exe$dq";
	$process = proc_open( $cmd, $circuit, $pipes, $cwd, $env );

	if( !is_resource($process) ){
		die( "***** ERROR : Could not open a process via PROC_OPEN - aborting.\n" );
		}

	foreach( $pipes as $k=>$v ){
		if( !is_resource($pipes[$k]) ){
			die( "***** ERROR : Did not get the pipe #$k - aborting.\n" );
			}
		}

	pipes_close(); exit;
#
#	Get all of the paper types
#
	$papers = file_get_contents( "./paperTypes.ini" );
	$papers = explode( "\n", $papers );
	list( $sizeX, $sizeY ) = getSCRSize();
	echo "sizeX = $sizeX\n";
#
#	Now clear the screen and start working on the paper information.
#
if( false ){
	scrCls();
	$info = <<<EOD
 _____  _       _   _            
|  __ \| |     | | | |           
| |__) | | ___ | |_| |_ ___ _ __ 
|  ___/| |/ _ \| __| __/ _ \ '__|
| |    | | (_) | |_| ||  __/ |   
|_|    |_|\___/ \__|\__\___|_|   
                               
EOD;

	scrMsg( 0, 0, $info );
}
#	$cf->rem_iccp( $cwd );

	$file = "$cwd/Apple Logo-1.png";
	echo "Looking at : $file\n";

	$pathinfo = pathinfo( $file );
	$svgfile = $cwd . "/" . $pathinfo['filename'] . ".svg";

	$cf->rem_iccp( $file );
	$gd = $cf->get_image( $file );
#
#	Get the size of the image.
#
	$w = trim( sprintf("%12.6f", imagesx($gd)) );
	$h = trim( sprintf("%12.6f", imagesy($gd)) );
	$wpt = $w . "pt";
	$hpt = $h . "pt";
#
#	<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
#
	$svg = <<<EOD
<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20010904//EN"
 "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">
<svg version="1.0" xmlns="http://www.w3.org/2000/svg"
 width="$wpt" height="$hpt" viewBox="0 0 $w $h"
 preserveAspectRatio="xMidYMid meet">
<metadata>
Created by Mark Manning
</metadata>

<g stroke='green'>
<circle cx='0' cy='0' r='1' fill='green'/>
<circle cx='$w' cy='0' r='1' fill='green'/>
<circle cx='$w' cy='$h' r='1' fill='green'/>
<circle cx='0' cy='$h' r='1' fill='green'/>\n

EOD;

	for( $y=0; $y<$w; $y++ ){
		$s = $e = null;
		for( $x=0; $x<$w; $x++ ){
			list( $color, $a, $r, $g, $b ) = $cr->get_pixel( $gd, $x, $y, 'a' );
#
#	Is the color transparent?
#
			if( ($a < 1) || ($a > 127) ){
#
#	Have we set the start location?
#
				if( is_null($s) ){
					$s = [];
					$e = [];
					$s[0] = $x;
					$s[1] = $y;
					$s[2] = $color;
					$e[0] = $x;
					$e[1] = $y;
					continue;
					}
					else {
						$e[0] = $x;
						$e[1] = $y;
						}
				}
				else if( !is_null($s) && (count($e) > 0) ){
					if( $s[0] == $e[0] ){ continue; }
					$hex = sprintf( "%08x", $s[2] );
					$hex = "00ffffff";
					$svg .= "	<line x1='$s[0]' y1='$s[1]' x2='$e[0]' " .
							"y2='$e[1]' stroke-width='1' />\n";
#							"y2='$e[1]' stroke='$hex' stroke-width='1' />\n";

					$s = $e = null;
					}
			}
		}

	$svg .= "</g>\n</svg>\n";

	file_put_contents( $svgfile, $svg );

	exit();

################################################################################
#	webFind(). Look for something in the web page, kick out useless stuff.
################################################################################
function webFind( $find, $page, $stop=null )
{
	while( true ){
		$found = array_shift( $page );
		if( count($page) < 1 ){ break; }
		if( preg_match("/$find/i", $found) ){ break; }
		if( !is_null($stop) && preg_match(";$stop;i", $found) ){ break; }
		}

	return array( $found, $page );
}
################################################################################
#	msg(). Print out a message.
################################################################################
function scrMsg( $x, $y, $info )
{
	if( $x < 1 ){ $x = 1; }
	if( $y < 1 ){ $y = 1; }

	scrMove( $x, $y );

	$info = explode( "\n", $info );
	foreach( $info as $k=>$v ){
		scrMove( $x, $k+$y+1 );
		scrPrint( $v );
		}
}
################################################################################
#	NCurses kind of stuff
################################################################################
function scrStart(){ scrMove(); scrClear(); }
function scrS(){ scrMove(); scrClear(); }

function scrClear( $opt=2 ){ echo "\e[" . $opt . "J"; }
function scrCls( $opt=2 ){ echo "\e[" . $opt . "J"; }

function scrPrint( $string ){ echo $string; }
function scrPrt( $string ){ echo $string; }
################################################################################
#	scrAsk(). Ask the person a question.
#	NOTES:	Options is always an array. These are put together to show all of
#		the options.
#
#		Example: $opt = array( "Default"=>"Yes", "q"=>"Quit", "e"=>"Exit"....)
################################################################################
function scrAsk( $x, $y, $msg, $opt )
{
	$opts = "[";
	$options = [];
	foreach( $opt as $k=>$v ){
		$opts .= "$v, ";
		$a = explode( ':', $v );
		$options[] = $a[1];
		}

	$opts = substr( $opts, 0, -2 ) . "]";
	$msg = "$msg $opts ?";

	scrMove( $x, $y );
	scrPrint( $msg );
	$ans = fgets(STDIN);
	$o = implode( ",", $options );

sleep( 1 );
scrMsg( 0, 15, "OPTIONS = $o" );
sleep( 1 );

	return $ans;
}

function scrEnd(){ scrMove(0,20); scrSGR(0); echo "\n"; }

function scrUp( $y=0 ){ echo "\e[" . $y . "A"; }
function scrU( $y=0 ){ echo "\e[" . $y . "A"; }

function scrDown( $y=0 ){ echo "\e[" . $y . "B"; }
function scrD( $y=0 ){ echo "\e[" . $y . "B"; }

function scrRight( $x=0 ){ echo "\e[" . $x . "C"; }
function scrR( $x=0 ){ echo "\e[" . $x . "C"; }

function scrLeft( $x=0 ){ echo "\e[" . $x . "D"; }
function scrL( $x=0 ){ echo "\e[" . $x . "D"; }

function scrNextLine( $count ){ echo "\e[" . $count . "E"; }
function scrNLe( $count ){ echo "\e[" . $count . "E"; }

function scrPrevLine( $count ){ echo "\e[" . $count . "F"; }
function scrPLine( $count ){ echo "\e[" . $count . "F"; }

function scrCursorHorizontalAbsolute( $num ){ echo "\e[" . $num . "G"; }
function scrCHA( $num ){ scrCursorHorizontalAbsolute( $num ); }

function scrCursorPosition( $x=0, $y=0 ){ echo "\e[" . $x . ";" . $y . "H"; }
function scrMove( $x=0, $y=0 )
{
	if( $y < 1 ){ $y = 1; }
		else if( $y > 30 ){ $y = 30; }

	if( $x < 1 ){ $x = 0; }
		else if( $x > 80 ){ $x = 80; }

	scrCursorPosition( $y, $x );
}

function scrEraseInLine( $opt=2 ){ echo "\e[" . $opt . "K"; }
function scrEL( $opt=2 ){ scrEraseInLine($opt); }

function scrScrollUp( $lines=0 ){ echo "\e[" . $lines . "S"; }
function scrSU( $lines=0 ){ echo "\e[" . $lines . "S"; }

function scrScrollDown( $lines=0 ){ echo "\e[" . $lines . "T"; }
function scrSD( $lines=0 ){ echo "\e[" . $lines . "T"; }

function scrSGR( $string ){ echo "\e[" . $string . "m"; }	#	0->107

function scrDeviceStatusReport(){ echo "\e[6n"; }
function scrDSR(){ return scrDeviceStatusReport(); }

function scrSaveCurrentCursorPosition(){ echo "\e[s"; }
function scrSCP(){ scrSaveCurrentCursorPosition(); }

function scrRestoreSavedCursorPosition(){ echo "\e[u"; }
function scrRCP(){ scrRestoreSavedCursorPosition(); }

function scrVT220Cursor(){ echo "\e[25h"; }
function scrVTC(){ echo "\e[25h"; }

function scrHideCursor(){ echo "\e[25l"; }
function scrHC(){ echo "\e[25l"; }

function scrEnableReporting(){ echo "\e[?1004h"; }
function scrER(){ echo "\e[?1004h"; }

function scrDisableReporting(){ echo "\e[?1004l"; }
function scrDR(){ echo "\e[?1004l"; }

function scrEnableAltBuf(){ echo "\e[?1049h"; }
function scrEAB(){ echo "\e[?1049h"; }

function scrDisableAltBuf(){ echo "\e[?1049l"; }
function scrDAB(){ echo "\e[?1049l"; }

function scrTurnOnPaste(){ echo "\e[?2004h"; }
function scrTOnP(){ echo "\e[?2004h"; }

function scrTurnOffPaste(){ echo "\e[?2004l"; }
function scrTOffP(){ echo "\e[?2004l"; }

$c = 0;
define( "SGR_Normal", $c++ );
define( "SGR_Bold", $c++ );
define( "SGR_Dim", $c++ );
define( "SGR_Italic", $c++ );
define( "SGR_Underline", $c++ );
define( "SGR_SlowBlink", $c++ );
define( "SGR_FastBlink", $c++ );
define( "SGR_Reverse", $c++ );
define( "SGR_Hide", $c++ );
define( "SGR_Strike", $c++ );
define( "SGR_PrimaryFont", $c++ );
define( "SGR_AltFont", $c++ );
define( "SGR_Fraktur", $c++ );
define( "SGR_DoubleUnderline", $c++ );
define( "SGR_NormalIntensity", $c++ );
define( "SGR_ResetItalic", $c++ );
define( "SGR_NotDefined", $c++ );
define( "SGR_NoBlink", $c++ );
define( "SGR_EnablePorportionalSpacing", $c++ );
define( "SGR_NotReversed", $c++ );
define( "SGR_Reveal", $c++ );
define( "SGR_NoStrike", $c++ );

$b = 0;
define( "SGR_Foreground" . $b++, $c++ );
define( "SGR_Foreground" . $b++, $c++ );
define( "SGR_Foreground" . $b++, $c++ );
define( "SGR_Foreground" . $b++, $c++ );
define( "SGR_Foreground" . $b++, $c++ );
define( "SGR_Foreground" . $b++, $c++ );
define( "SGR_Foreground" . $b++, $c++ );
define( "SGR_Foreground" . $b++, $c++ );
define( "SGR_FGColor", $c++ );
define( "SGR_DefaultFGC", $c++ );

$b = 0;
define( "SGR_Background" . $b++, $c++ );
define( "SGR_Background" . $b++, $c++ );
define( "SGR_Background" . $b++, $c++ );
define( "SGR_Background" . $b++, $c++ );
define( "SGR_Background" . $b++, $c++ );
define( "SGR_Background" . $b++, $c++ );
define( "SGR_Background" . $b++, $c++ );
define( "SGR_Background" . $b++, $c++ );
define( "SGR_BGC", $c++ );
define( "SGR_DefaultBGC", $c++ );
define( "SGR_DisablePorportionalSpacing", $c++ );
define( "SGR_Framed", $c++ );
define( "SGR_Encircled", $c++ );
define( "SGR_Overlined", $c++ );
define( "SGR_NotFramedEncircled", $c++ );
define( "SGR_NotOverlined", $c++ );
define( "SGR_UnderlineColor", $c++ );
define( "SGR_DefaultUnderlineColor", $c++ );
define( "SGR_IdeogramDoubleLine", $c++ );
define( "SGR_IdeogramStressMarking", $c++ );
define( "SGR_NoIdeogram", $c++ );
define( "SGR_Superscript", $c++ );
define( "SGR_Subscript", $c++ );
define( "SGR_NoSupSub", $c++ );
define( "SGR_BrightFGC", $c++ );
define( "SGR_BrightBGC", $c++ );

function scrDECDoubleHeightLettersTopHalf(){ echo "\e[#3"; }
function scrDECDHLTH(){ echo "\e[#3"; }

function scrDECDoubleHeightLettersBottomHalf(){ echo "\e[#4"; }
function scrDECDHLBH(){ echo "\e[#4"; }

function scrDECSingleWidthLine(){ echo "\e[#4"; }
function scrDECDSWL(){ echo "\e[#4"; }

function scrDECDoubleWidthLine(){ echo "\e[#5"; }
function scrDECDWL(){ echo "\e[#6"; }

function getSCRSize()
{
	echo "\e[9999;9999H";
	sleep( 1 );
	echo "\e[6n";

	$str = "";
	while( ($c = fgetc(STDIN)) != "R" ){ $str .= $c; sleep( 1 ); }
	echo "STR = $str\n";

	return array( $str, 0 );
}
################################################################################
#	makeQBKey(). Make the QB64 program to handle getting keys from the
#		console.
################################################################################
function makeQBKey( $file="inkey.bas" )
{
	$cwd = getcwd();
	$basFile = "$cwd/$file";

	$code = <<<EOD
'
'	Simple inkey$ program
'
'	input the escape character so we can test against it
'	IF you want to use the escape key - then just choose another key
'	and put it in this next line.
'
	esc$ = ""

	open "key.dat" for output as #1
	close #1

	while 1
		mykey$ = inkey$
		if mykey$ = esc$ then
			end
			endif

		if len(mykey$) > 0 then
			print mykey$;
			open "key.dat" for append as #1
			print #1, mykey$;
			close #1
			endif
		wend
	end

EOD;

	echo "Now that the file has been made you must compile it with QB64.\n";
	echo "AFTER COMPILING, you should wind up with something like 'inkey.exe'\n";
	echo "The source code is in file : $basFile\n";

	return file_put_contents( $basFile, $code );
}

################################################################################
#	pipes_close(). Close the connection.
################################################################################
function pipes_close( $opt=true )
{
	global $cwd, $cf, $pr, $exe, $process, $pipes, $circuit;

	$dq = '"';

	if( !is_array($pipes) ){
#		die( "Finished\n" );
		}
#
#	Because we can have several processes - we have to terminate each process
#	and under Windows - we have to stop each one of these
#
	if( is_resource($process) ){
#
#	First get rid of the pipes
#
		if( !is_null($pipes[0]) || is_resource($pipes[0]) ){
			echo "Closing pipe #0\n";
			@fclose( $pipes[0] );
			}

		if( !is_null($pipes[1]) || is_resource($pipes[1]) ){
			echo "Closing pipe #1\n";
			@fclose( $pipes[1] );
			}

		if( !is_null($pipes[2]) || isset($pipes[2]) && is_resource($pipes[2]) ){
			echo "Closing pipe #2\n";
			@fclose( $pipes[2] );
			}
#
#	We can close the pipes but we must NOT kill the process if we
#	want to continue doing things.
#
		if( $opt ){
#
#	Now get rid of the process
#
			echo "Closing Processes\n";
			$ret = proc_terminate( $process );

			echo "Proc_terminate returned value = $ret\n";
#
#	Under Windows - we have to physically kill the executable.
#
			$cmd = "tasklist /fi " . $dq . "imagename eq $exe" . $dq . '"';
			exec( $cmd, $output );
			if( !preg_match("/no tasks/i", $output[0]) ){
				if( preg_match("/win/i", PHP_OS) ){
					system( "taskkill /IM $exe /F" );
					}
				}

			$process = null;
			}
		}

	return;
}
################################################################################
#	papers(). Get the various paper types.
################################################################################
function papers()
{
	$data = <<<EOD
Type,Name,Width-mm,Height-mm,Width-in,Height-in,Width-MM2IN,Height-MM2IN,Width-IN2MM,Height-IN2MM,Ratio-MM,Ratio-IN
"A Paper",2A0,1189,1682,46.8,66.2,46.811023622047,66.220472440945,1188.72,1681.48,0.70689655172414,0.70694864048338
"A Paper",4A0,1682,2378,66.2,93.6,66.220472440945,93.622047244094,1681.48,2377.44,0.70731707317073,0.70726495726496
"A Paper",A0,841,1189,33.1,46.8,33.110236220472,46.811023622047,840.74,1188.72,0.70731707317073,0.70726495726496
"A Paper",A0+,914,1292,36,50.9,35.984251968504,50.866141732283,914.4,1292.86,0.70743034055728,0.70726915520629
"A Paper",A1,594,841,23.4,33.1,23.385826771654,33.110236220472,594.36,840.74,0.70630202140309,0.70694864048338
"A Paper",A1+,609,914,24,36,23.976377952756,35.984251968504,609.6,914.4,0.66630196936543,0.66666666666667
"A Paper",A10,26,37,1.0,1.5,1.0236220472441,1.4566929133858,25.4,38.1,0.7027027027027,0.66666666666667
"A Paper",A11,18,26,0.7,1,0.70866141732283,1.0236220472441,17.78,25.4,0.69230769230769,0.7
"A Paper",A12,13,18,0.5,0.7,0.51181102362205,0.70866141732283,12.7,17.78,0.72222222222222,0.71428571428571
"A Paper",A13,9,13,0.4,0.5,0.35433070866142,0.51181102362205,10.16,12.7,0.69230769230769,0.8
"A Paper",A2,420,594,16.5,23.4,16.535433070866,23.385826771654,419.1,594.36,0.70707070707071,0.70512820512821
"A Paper",A3,297,420,11.7,16.5,11.692913385827,16.535433070866,297.18,419.1,0.70714285714286,0.70909090909091
"A Paper",A3+,329,483,13,19,12.952755905512,19.015748031496,330.2,482.6,0.68115942028986,0.68421052631579
"A Paper",A4,210,297,8.3,11.7,8.2677165354331,11.692913385827,210.82,297.18,0.70707070707071,0.70940170940171
"A Paper",A5,148,210,5.8,8.3,5.8267716535433,8.2677165354331,147.32,210.82,0.7047619047619,0.69879518072289
"A Paper",A6,105,148,4.1,5.8,4.1338582677165,5.8267716535433,104.14,147.32,0.70945945945946,0.70689655172414
"A Paper",A7,74,105,2.9,4.1,2.9133858267717,4.1338582677165,73.66,104.14,0.7047619047619,0.70731707317073
"A Paper",A8,52,74,2.0,2.9,2.0472440944882,2.9133858267717,50.8,73.66,0.7027027027027,0.68965517241379
"A Paper",A9,37,52,1.5,2.0,1.4566929133858,2.0472440944882,38.1,50.8,0.71153846153846,0.75
"Australian Poster Paper","Australian Movie Poster Daybill",660.4,762,26,30,26,30,660.4,762,0.86666666666667,0.86666666666667
"Australian Poster Paper","Australian Movie Poster Lobby Card",279.4,355.6,11,14,11,14,279.4,355.6,0.78571428571429,0.78571428571429
"Australian Poster Paper","Australian Movie Poster One Sheet",685.8,1016,27,40,27,40,685.8,1016,0.675,0.675
"Australian Poster Paper","Australian Movie Poster Three Sheet",1041.4,2057.4,41,81,41,81,1041.4,2057.4,0.50617283950617,0.50617283950617
"Austrian Billboard Paper","Austrian Billboard Brandboard",1500,2000,59.1,78.7,59.055118110236,78.740157480315,1501.14,1998.98,0.75,0.75095298602287
"Austrian Billboard Paper","Austrian Billboard Centerboard",10000,4780,393.7,188.2,393.70078740157,188.18897637795,9999.98,4780.28,2.092050209205,2.0919234856536
"Austrian Billboard Paper","Austrian Billboard Dachflache",10000,2000,393.7,78.7,393.70078740157,78.740157480315,9999.98,1998.98,5,5.002541296061
"Austrian Billboard Paper","Austrian Billboard Megaboard",8000,5000,315.0,196.9,314.96062992126,196.85039370079,8001,5001.26,1.6,1.5997968511935
"B Paper",B0,1000,1414,39.4,55.7,39.370078740157,55.669291338583,1000.76,1414.78,0.70721357850071,0.70736086175943
"B Paper",B0+,1118,1580,44,62.2,44.015748031496,62.204724409449,1117.6,1579.88,0.70759493670886,0.70739549839228
"B Paper",B1,707,1000,27.8,39.4,27.834645669291,39.370078740157,706.12,1000.76,0.707,0.70558375634518
"B Paper",B1+,720,1020,28.3,40.2,28.346456692913,40.157480314961,718.82,1021.08,0.70588235294118,0.70398009950249
"B Paper",B10,31,44,1.2,1.7,1.2204724409449,1.7322834645669,30.48,43.18,0.70454545454545,0.70588235294118
"B Paper",B11,22,31,0.9,1.2,0.86614173228346,1.2204724409449,22.86,30.48,0.70967741935484,0.75
"B Paper",B12,15,22,0.6,0.9,0.59055118110236,0.86614173228346,15.24,22.86,0.68181818181818,0.66666666666667
"B Paper",B13,11,15,0.4,0.6,0.43307086614173,0.59055118110236,10.16,15.24,0.73333333333333,0.66666666666667
"B Paper",B2,500,707,19.7,27.8,19.685039370079,27.834645669291,500.38,706.12,0.70721357850071,0.70863309352518
"B Paper",B2+,520,720,20.5,28.3,20.472440944882,28.346456692913,520.7,718.82,0.72222222222222,0.7243816254417
"B Paper",B2+,530,750,20.9,29.5,20.866141732283,29.527559055118,530.86,749.3,0.70666666666667,0.70847457627119
"B Paper",B3,353,500,13.9,19.7,13.897637795276,19.685039370079,353.06,500.38,0.706,0.70558375634518
"B Paper",B4,250,353,9.8,13.9,9.8425196850394,13.897637795276,248.92,353.06,0.70821529745042,0.70503597122302
"B Paper",B5,176,250,6.9,9.8,6.9291338582677,9.8425196850394,175.26,248.92,0.704,0.70408163265306
"B Paper",B6,125,176,4.9,6.9,4.9212598425197,6.9291338582677,124.46,175.26,0.71022727272727,0.71014492753623
"B Paper",B7,88,125,3.5,4.9,3.4645669291339,4.9212598425197,88.9,124.46,0.704,0.71428571428571
"B Paper",B8,62,88,2.4,3.5,2.4409448818898,3.4645669291339,60.96,88.9,0.70454545454545,0.68571428571429
"B Paper",B9,44,62,1.7,2.4,1.7322834645669,2.4409448818898,43.18,60.96,0.70967741935484,0.70833333333333
"Billboard Paper","1 Sheet",508,762,20,30,20,30,508,762,0.66666666666667,0.66666666666667
"Billboard Paper","12 Sheet",3048,1524,120,60,120,60,3048,1524,2,2
"Billboard Paper","16 Sheet",2032,3048,80,120,80,120,2032,3048,0.66666666666667,0.66666666666667
"Billboard Paper","2 Sheet",762,1016,30,40,30,40,762,1016,0.75,0.75
"Billboard Paper","32 Sheet",4064,3048,160,120,160,120,4064,3048,1.3333333333333,1.3333333333333
"Billboard Paper","4 Sheet",1016,1524,40,60,40,60,1016,1524,0.66666666666667,0.66666666666667
"Billboard Paper","48 Sheet",6096,3048,240,120,240,120,6096,3048,2,2
"Billboard Paper","6 Sheet",1200,1800,47.2,70.9,47.244094488189,70.866141732283,1198.88,1800.86,0.66666666666667,0.6657263751763
"Billboard Paper","64 Sheet",8128,3048,320,120,320,120,8128,3048,2.6666666666667,2.6666666666667
"Billboard Paper","96 Sheet",12192,3048,480,120,480,120,12192,3048,4,4
"Book Paper",12mo,127,187.325,5,7.4,5,7.375,127,187.96,0.67796610169492,0.67567567567568
"Book Paper",16mo,101.6,171.45,4,6.8,4,6.75,101.6,172.72,0.59259259259259,0.58823529411765
"Book Paper",18mo,101.6,165.1,4,6.5,4,6.5,101.6,165.1,0.61538461538462,0.61538461538462
"Book Paper",32mo,88.9,139.7,3.5,5.5,3.5,5.5,88.9,139.7,0.63636363636364,0.63636363636364
"Book Paper",48mo,63.5,101.6,2.5,4,2.5,4,63.5,101.6,0.625,0.625
"Book Paper",64mo,50.8,76.2,2,3,2,3,50.8,76.2,0.66666666666667,0.66666666666667
"Book Paper","A Format",110,178,4.3,7,4.3307086614173,7.007874015748,109.22,177.8,0.61797752808989,0.61428571428571
"Book Paper","B Format",129,198,5.1,7.8,5.0787401574803,7.7952755905512,129.54,198.12,0.65151515151515,0.65384615384615
"Book Paper","C Format",135,216,5.3,8.5,5.3149606299213,8.503937007874,134.62,215.9,0.625,0.62352941176471
"Book Paper","Crown Octavo",136.525,203.2,5.4,8,5.375,8,137.16,203.2,0.671875,0.675
"Book Paper",Folio,304.8,482.6,12,19,12,19,304.8,482.6,0.63157894736842,0.63157894736842
"Book Paper","Imperial Octavo",209.55,292.1,8.3,11.5,8.25,11.5,210.82,292.1,0.71739130434783,0.72173913043478
"Book Paper","Medium Octavo",165.1,234.95,6.5,9.3,6.5,9.25,165.1,236.22,0.7027027027027,0.6989247311828
"Book Paper",Octavo,152.4,228.6,6,9,6,9,152.4,228.6,0.66666666666667,0.66666666666667
"Book Paper",Quarto,241.3,304.8,9.5,12,9.5,12,241.3,304.8,0.79166666666667,0.79166666666667
"Book Paper","Royal Octavo",165.1,254,6.5,10,6.5,10,165.1,254,0.65,0.65
"Book Paper","Super Octavo",177.8,279.4,7,11,7,11,177.8,279.4,0.63636363636364,0.63636363636364
"British Cut Writing Paper","British Imperial Cut Writing Paper Albert",101.6,152.4,4.0,6.0,4,6,101.6,152.4,0.66666666666667,0.66666666666667
"British Cut Writing Paper","British Imperial Cut Writing Paper Duchess",114.3,152.4,4.5,6.0,4.5,6,114.3,152.4,0.75,0.75
"British Cut Writing Paper","British Imperial Cut Writing Paper Duke",139.7,177.8,5.5,7.0,5.5,7,139.7,177.8,0.78571428571429,0.78571428571429
"British Cut Writing Paper","British Imperial Cut Writing Paper Foolscap Folio",203.2,330.2,8.0,13.0,8,13,203.2,330.2,0.61538461538462,0.61538461538462
"British Cut Writing Paper","British Imperial Cut Writing Paper Foolscap Quarto",152.4,203.2,6.5,8.0,6,8,165.1,203.2,0.75,0.8125
"British Cut Writing Paper","British Imperial Cut Writing Paper Large Post Octavo",127.0,203.2,5.0,8.0,5,8,127,203.2,0.625,0.625
"British Cut Writing Paper","British Imperial Cut Writing Paper Large Post Quarto",203.2,254.0,8.0,10.0,8,10,203.2,254,0.8,0.8
"British Cut Writing Paper","British Imperial Cut Writing Paper Small Post Octavo",114.3,177.8,4.5,7.0,4.5,7,114.3,177.8,0.64285714285714,0.64285714285714
"British Cut Writing Paper","British Imperial Cut Writing Paper Small Post Quarto",177.8,228.6,7.0,9.0,7,9,177.8,228.6,0.77777777777778,0.77777777777778
"British Cut Writing Paper","British Imperial Uncut Writing Paper Copy",412.8,508.0,16.25,20.0,16.251968503937,20,412.75,508,0.81259842519685,0.8125
"British Cut Writing Paper","British Imperial Uncut Writing Paper Double Foolscap",419.1,673.1,16.5,26.5,16.5,26.5,419.1,673.1,0.62264150943396,0.62264150943396
"British Cut Writing Paper","British Imperial Uncut Writing Paper Double Large Post",527.1,838.2,20.75,33.0,20.751968503937,33,527.05,838.2,0.62884753042233,0.62878787878788
"British Cut Writing Paper","British Imperial Uncut Writing Paper Double Post",482.6,774.7,19.0,30.5,19,30.5,482.6,774.7,0.62295081967213,0.62295081967213
"British Cut Writing Paper","British Imperial Uncut Writing Paper Double Pott",381.0,635.0,15.0,25.0,15,25,381,635,0.6,0.6
"British Cut Writing Paper","British Imperial Uncut Writing Paper Foolscap",336.6,419.1,13.25,16.5,13.251968503937,16.5,336.55,419.1,0.80314960629921,0.8030303030303
"British Cut Writing Paper","British Imperial Uncut Writing Paper Foolscap and Half",336.6,628.7,13.25,24.75,13.251968503937,24.751968503937,336.55,628.65,0.53539048830921,0.53535353535354
"British Cut Writing Paper","British Imperial Uncut Writing Paper Foolscap and Third",336.6,558.8,13.25,22.0,13.251968503937,22,336.55,558.8,0.60236220472441,0.60227272727273
"British Cut Writing Paper","British Imperial Uncut Writing Paper Large Post",419.1,527.1,16.5,20.75,16.5,20.751968503937,419.1,527.05,0.79510529311326,0.79518072289157
"British Cut Writing Paper","British Imperial Uncut Writing Paper Medium",457.2,520.7,18.0,20.5,18,20.5,457.2,520.7,0.8780487804878,0.8780487804878
"British Cut Writing Paper","British Imperial Uncut Writing Paper Pinched Post",368.3,469.9,14.5,18.5,14.5,18.5,368.3,469.9,0.78378378378378,0.78378378378378
"British Cut Writing Paper","British Imperial Uncut Writing Paper Post",387.4,482.6,15.25,19.0,15.251968503937,19,387.35,482.6,0.80273518441774,0.80263157894737
"British Cut Writing Paper","British Imperial Uncut Writing Paper Pott",317.5,381.0,12.5,15.0,12.5,15,317.5,381,0.83333333333333,0.83333333333333
"British Cut Writing Paper","Imperial Uncut Book & Drawing Paper Antiquarian",787.4,1346.2,31.0,53.0,31,53,787.4,1346.2,0.58490566037736,0.58490566037736
"British Cut Writing Paper","Imperial Uncut Book & Drawing Paper Atlas",666.75,863.6,26.25,34.0,26.25,34,666.75,863.6,0.77205882352941,0.77205882352941
"British Cut Writing Paper","Imperial Uncut Book & Drawing Paper Columbier",596.9,622.3,23.5,24.5,23.5,24.5,596.9,622.3,0.95918367346939,0.95918367346939
"British Cut Writing Paper","Imperial Uncut Book & Drawing Paper Demy",393.7,508.0,15.5,20,15.5,20,393.7,508,0.775,0.775
"British Cut Writing Paper","Imperial Uncut Book & Drawing Paper Double Elephant",673.1,1016.0,26.5,40.0,26.5,40,673.1,1016,0.6625,0.6625
"British Cut Writing Paper","Imperial Uncut Book & Drawing Paper Elephant",584.2,711.2,23.0,28.0,23,28,584.2,711.2,0.82142857142857,0.82142857142857
"British Cut Writing Paper","Imperial Uncut Book & Drawing Paper Foolscap",355.6,476.3,14.0,18.75,14,18.751968503937,355.6,476.25,0.74658828469452,0.74666666666667
"British Cut Writing Paper","Imperial Uncut Book & Drawing Paper Imperial",558.8,768.4,22.0,30.25,22,30.251968503937,558.8,768.35,0.72722540343571,0.72727272727273
"British Cut Writing Paper","Imperial Uncut Book & Drawing Paper Medium",444.5,571.5,17.5,22.5,17.5,22.5,444.5,571.5,0.77777777777778,0.77777777777778
"British Cut Writing Paper","Imperial Uncut Book & Drawing Paper Royal",482.6,609.6,19.0,24.0,19,24,482.6,609.6,0.79166666666667,0.79166666666667
"British Cut Writing Paper","Imperial Uncut Printing Paper Crown",412.8,533.4,16.25,21.0,16.251968503937,21,412.75,533.4,0.77390326209224,0.77380952380952
"British Cut Writing Paper","Imperial Uncut Printing Paper Demy",450.9,571.5,17.75,22.5,17.751968503937,22.5,450.85,571.5,0.78897637795276,0.78888888888889
"British Cut Writing Paper","Imperial Uncut Printing Paper Double Crown",508.0,762.0,20.0,30.0,20,30,508,762,0.66666666666667,0.66666666666667
"British Cut Writing Paper","Imperial Uncut Printing Paper Double Demy",571.5,901.7,22.5,35.5,22.5,35.5,571.5,901.7,0.63380281690141,0.63380281690141
"British Cut Writing Paper","Imperial Uncut Printing Paper Double Foolscap",431.8,685.8,17.0,27.0,17,27,431.8,685.8,0.62962962962963,0.62962962962963
"British Cut Writing Paper","Imperial Uncut Printing Paper Double Post",482.6,774.7,19.0,30.5,19,30.5,482.6,774.7,0.62295081967213,0.62295081967213
"British Cut Writing Paper","Imperial Uncut Printing Paper Double Pott",381.0,635.0,15.0,25.0,15,25,381,635,0.6,0.6
"British Cut Writing Paper","Imperial Uncut Printing Paper Foolscap",342.9,431.8,13.5,17.0,13.5,17,342.9,431.8,0.79411764705882,0.79411764705882
"British Cut Writing Paper","Imperial Uncut Printing Paper Medium",463.6,584.2,18.25,23.0,18.251968503937,23,463.55,584.2,0.79356384799726,0.79347826086957
"British Cut Writing Paper","Imperial Uncut Printing Paper Quad",762.0,1016.0,30.0,40.0,30,40,762,1016,0.75,0.75
"British Cut Writing Paper","Imperial Uncut Printing Paper Royal",508.0,635.0,20.0,25.0,20,25,508,635,0.8,0.8
"British Cut Writing Paper","Imperial Uncut Printing Paper Super Royal",533.4,685.8,21.0,27.0,21,27,533.4,685.8,0.77777777777778,0.77777777777778
"British Paper","British Maximum",139.7,88.9,5.5,3.5,5.5,3.5,139.7,88.9,1.5714285714286,1.5714285714286
"British Paper","British Maximum 1925",149.225,104.775,5.875,4.125,5.875,4.125,149.225,104.775,1.4242424242424,1.4242424242424
"British Paper","British Minimum",82.55,82.55,3.25,3.25,3.25,3.25,82.55,82.55,1,1
"British Paper","British Minimum 1906",101.6,69.85,4.0,2.75,4,2.75,101.6,69.85,1.4545454545455,1.4545454545455
"British Poster Paper","British Poster 1 Sheet",508,762,20,30,20,30,508,762,0.66666666666667,0.66666666666667
"British Poster Paper","British Poster 2 Sheet",762,1016,30,40,30,40,762,1016,0.75,0.75
"British Poster Paper","British Poster 4 Sheet",1016,1524,40,60,40,60,1016,1524,0.66666666666667,0.66666666666667
"Business Card Paper",China,90,54,3.5,2.1,3.5433070866142,2.1259842519685,88.9,53.34,1.6666666666667,1.6666666666667
"Business Card Paper",European,85,55,3.3,2.2,3.3464566929134,2.1653543307087,83.82,55.88,1.5454545454545,1.5
"Business Card Paper",Hungary,90,50,3.5,2,3.5433070866142,1.9685039370079,88.9,50.8,1.8,1.75
"Business Card Paper","ISO 216",74,52,2.9,2,2.9133858267717,2.0472440944882,73.66,50.8,1.4230769230769,1.45
"Business Card Paper","ISO 7810 ID-1",85.6,54,3.4,2.1,3.3700787401575,2.1259842519685,86.36,53.34,1.5851851851852,1.6190476190476
"Business Card Paper",Iran,85,48,3.3,1.9,3.3464566929134,1.8897637795276,83.82,48.26,1.7708333333333,1.7368421052632
"Business Card Paper",Japan,91,55,3.6,2.2,3.5826771653543,2.1653543307087,91.44,55.88,1.6545454545455,1.6363636363636
"Business Card Paper",Scandinavia,90,55,3.5,2.2,3.5433070866142,2.1653543307087,88.9,55.88,1.6363636363636,1.5909090909091
"Business Card Paper",US/Canada,88.9,50.8,3.5,2,3.5,2,88.9,50.8,1.75,1.75
"C Envelope Paper",C0,917,1297,36.1,51.5,36.102362204724,51.062992125984,916.94,1308.1,0.70701619121049,0.70097087378641
"C Envelope Paper",C1,648,917,25.5,36.1,25.511811023622,36.102362204724,647.7,916.94,0.70665212649945,0.70637119113573
"C Envelope Paper",C10,28,40,1.1,1.6,1.1023622047244,1.5748031496063,27.94,40.64,0.7,0.6875
"C Envelope Paper",C2,458,648,18.0,25.5,18.031496062992,25.511811023622,457.2,647.7,0.70679012345679,0.70588235294118
"C Envelope Paper",C3,324,458,12.8,18.0,12.755905511811,18.031496062992,325.12,457.2,0.70742358078603,0.71111111111111
"C Envelope Paper",C4,229,324,9.0,12.8,9.0157480314961,12.755905511811,228.6,325.12,0.70679012345679,0.703125
"C Envelope Paper",C5,162,229,6.4,9.0,6.3779527559055,9.0157480314961,162.56,228.6,0.70742358078603,0.71111111111111
"C Envelope Paper",C6,114,162,4.5,6.4,4.488188976378,6.3779527559055,114.3,162.56,0.7037037037037,0.703125
"C Envelope Paper",C7,81,114,3.2,4.5,3.1889763779528,4.488188976378,81.28,114.3,0.71052631578947,0.71111111111111
"C Envelope Paper",C8,57,81,2.2,3.2,2.244094488189,3.1889763779528,55.88,81.28,0.7037037037037,0.6875
"C Envelope Paper",C9,40,57,1.6,2.2,1.5748031496063,2.244094488189,40.64,55.88,0.70175438596491,0.72727272727273
"Canadian Paper",P1,560,860,22,33.9,22.047244094488,33.858267716535,558.8,861.06,0.65116279069767,0.64896755162242
"Canadian Paper",P2,430,560,16.9,22,16.929133858268,22.047244094488,429.26,558.8,0.76785714285714,0.76818181818182
"Canadian Paper",P3,280,430,11,16.9,11.023622047244,16.929133858268,279.4,429.26,0.65116279069767,0.6508875739645
"Canadian Paper",P4,215,280,8.5,11,8.4645669291339,11.023622047244,215.9,279.4,0.76785714285714,0.77272727272727
"Canadian Paper",P5,140,215,5.5,8.5,5.511811023622,8.4645669291339,139.7,215.9,0.65116279069767,0.64705882352941
"Canadian Paper",P6,107,140,4.2,5.5,4.2125984251969,5.511811023622,106.68,139.7,0.76428571428571,0.76363636363636
"Chinese Paper",D0,764,1064,30.1,41.9,30.07874015748,41.889763779528,764.54,1064.26,0.71804511278195,0.71837708830549
"Chinese Paper",D1,532,760,20.9,29.9,20.944881889764,29.92125984252,530.86,759.46,0.7,0.69899665551839
"Chinese Paper",D2,380,528,15,20.8,14.96062992126,20.787401574803,381,528.32,0.71969696969697,0.72115384615385
"Chinese Paper",D3,264,376,10.4,14.8,10.393700787402,14.803149606299,264.16,375.92,0.70212765957447,0.7027027027027
"Chinese Paper",D4,188,260,7.4,10.2,7.4015748031496,10.236220472441,187.96,259.08,0.72307692307692,0.72549019607843
"Chinese Paper",D5,130,184,5.1,7.2,5.1181102362205,7.244094488189,129.54,182.88,0.70652173913043,0.70833333333333
"Chinese Paper",D6,92,126,3.6,5,3.6220472440945,4.9606299212598,91.44,127,0.73015873015873,0.72
"Chinese Paper",RD0,787,1092,31,43,30.984251968504,42.992125984252,787.4,1092.2,0.72069597069597,0.72093023255814
"Chinese Paper",RD1,546,787,21.5,31,21.496062992126,30.984251968504,546.1,787.4,0.69377382465057,0.69354838709677
"Chinese Paper",RD2,393,546,15.5,21.5,15.472440944882,21.496062992126,393.7,546.1,0.71978021978022,0.72093023255814
"Chinese Paper",RD3,273,393,10.7,15.5,10.748031496063,15.472440944882,271.78,393.7,0.69465648854962,0.69032258064516
"Chinese Paper",RD4,196,273,7.7,10.7,7.7165354330709,10.748031496063,195.58,271.78,0.71794871794872,0.7196261682243
"Chinese Paper",RD5,136,196,5.4,7.7,5.3543307086614,7.7165354330709,137.16,195.58,0.69387755102041,0.7012987012987
"Chinese Paper",RD6,98,136,3.9,5.4,3.8582677165354,5.3543307086614,99.06,137.16,0.72058823529412,0.72222222222222
"Colombian Paper","1/2 pliego",500,700,19.7,27.6,19.685039370079,27.55905511811,500.38,701.04,0.71428571428571,0.71376811594203
"Colombian Paper","1/4 pliego",350,500,13.8,19.7,13.779527559055,19.685039370079,350.52,500.38,0.7,0.7005076142132
"Colombian Paper","1/8 pliego",250,350,9.8,13.8,9.8425196850394,13.779527559055,248.92,350.52,0.71428571428571,0.71014492753623
"Colombian Paper",Carta,216,279,8.5,11,8.503937007874,10.984251968504,215.9,279.4,0.7741935483871,0.77272727272727
"Colombian Paper","Extra Tabloide",304,457.2,12,18,11.968503937008,18,304.8,457.2,0.66491688538933,0.66666666666667
"Colombian Paper",Oficio,216,330,8.5,13,8.503937007874,12.992125984252,215.9,330.2,0.65454545454545,0.65384615384615
"Colombian Paper",Pliego,700,1000,27.6,39.4,27.55905511811,39.370078740157,701.04,1000.76,0.7,0.7005076142132
"French Billboard Paper","French Billboard 12m2",4000,3000,157.5,118.1,157.48031496063,118.11023622047,4000.5,2999.74,1.3333333333333,1.3336155800169
"French Billboard Paper","French Billboard Abribus 2m2",1756,1191,69.1,46.9,69.133858267717,46.889763779528,1755.14,1191.26,1.4743912678421,1.4733475479744
"French Paper",Carré,450,560,17.7,22,17.716535433071,22.047244094488,449.58,558.8,0.80357142857143,0.80454545454545
"French Paper",Cavalier,460,620,18.1,24.4,18.110236220472,24.409448818898,459.74,619.76,0.74193548387097,0.74180327868852
"French Paper",Cloche,300,400,11.8,15.7,11.811023622047,15.748031496063,299.72,398.78,0.75,0.7515923566879
"French Paper","Colombier affiche",600,800,23.6,31.5,23.622047244094,31.496062992126,599.44,800.1,0.75,0.74920634920635
"French Paper","Colombier commercial",630,900,24.8,35.4,24.803149606299,35.433070866142,629.92,899.16,0.7,0.70056497175141
"French Paper",Coquille,440,560,17.3,22,17.322834645669,22.047244094488,439.42,558.8,0.78571428571429,0.78636363636364
"French Paper","Couronne écriture",360,360,14.2,14.2,14.173228346457,14.173228346457,360.68,360.68,1,1
"French Paper","Couronne édition",370,470,14.6,18.5,14.566929133858,18.503937007874,370.84,469.9,0.78723404255319,0.78918918918919
"French Paper",Demi-raisin,325,500,12.8,19.7,12.795275590551,19.685039370079,325.12,500.38,0.65,0.6497461928934
"French Paper","Double Raisin",650,1000,25.6,39.4,25.590551181102,39.370078740157,650.24,1000.76,0.65,0.6497461928934
"French Paper","Grand Aigle",750,1050,29.5,41.3,29.527559055118,41.338582677165,749.3,1049.02,0.71428571428571,0.71428571428571
"French Paper","Grand Monde",900,1260,35.4,49.6,35.433070866142,49.606299212598,899.16,1259.84,0.71428571428571,0.71370967741935
"French Paper",Jésus,560,760,22,29.9,22.047244094488,29.92125984252,558.8,759.46,0.73684210526316,0.73578595317726
"French Paper","Petit Aigle",700,940,27.6,37,27.55905511811,37.007874015748,701.04,939.8,0.74468085106383,0.74594594594595
"French Paper","Pot, écolier",310,400,12.2,15.7,12.204724409449,15.748031496063,309.88,398.78,0.775,0.77707006369427
"French Paper",Raisin,500,650,19.7,25.6,19.685039370079,25.590551181102,500.38,650.24,0.76923076923077,0.76953125
"French Paper",Roberto,390,500,15.4,19.7,15.354330708661,19.685039370079,391.16,500.38,0.78,0.78172588832487
"French Paper",Soleil,600,800,23.6,31.5,23.622047244094,31.496062992126,599.44,800.1,0.75,0.74920634920635
"French Paper",Tellière,340,440,13.4,17.3,13.385826771654,17.322834645669,340.36,439.42,0.77272727272727,0.77456647398844
"French Paper",Univers,1000,1130,39.4,44.5,39.370078740157,44.488188976378,1000.76,1130.3,0.88495575221239,0.88539325842697
"French Paper",Écu,400,520,15.7,20.5,15.748031496063,20.472440944882,398.78,520.7,0.76923076923077,0.76585365853659
"French Poster Paper","French Movie Poster Demi-Grande",800,1200,31.5,47.2,31.496062992126,47.244094488189,800.1,1198.88,0.66666666666667,0.66737288135593
"French Poster Paper","French Movie Poster Double Grande",1600,2400,63.0,94.5,62.992125984252,94.488188976378,1600.2,2400.3,0.66666666666667,0.66666666666667
"French Poster Paper","French Movie Poster Grande",1200,1600,47.2,63.0,47.244094488189,62.992125984252,1198.88,1600.2,0.75,0.74920634920635
"French Poster Paper","French Movie Poster Moyenne",600,800,23.6,31.5,23.622047244094,31.496062992126,599.44,800.1,0.75,0.74920634920635
"French Poster Paper","French Movie Poster Pantalon",600,1600,23.6,63.0,23.622047244094,62.992125984252,599.44,1600.2,0.375,0.37460317460317
"French Poster Paper","French Movie Poster Petite",400,600,15.7,23.6,15.748031496063,23.622047244094,398.78,599.44,0.66666666666667,0.66525423728814
"German Billboard Paper","German Billboard City Star",3560,2520,140.2,99.2,140.15748031496,99.212598425197,3561.08,2519.68,1.4126984126984,1.4133064516129
"German Billboard Paper","German Billboard Superpostern",5260,3720,207.1,146.5,207.08661417323,146.45669291339,5260.34,3721.1,1.4139784946237,1.4136518771331
"German Paper",B5,176,250,6.9,9.8,6.9291338582677,9.8425196850394,175.26,248.92,0.704,0.70408163265306
"German Paper","DIN D0",771,1090,30.4,42.9,30.354330708661,42.913385826772,772.16,1089.66,0.70733944954128,0.70862470862471
"German Paper","DIN D1",545,771,21.5,30.4,21.456692913386,30.354330708661,546.1,772.16,0.70687418936446,0.70723684210526
"German Paper","DIN D2",385,545,15.2,21.5,15.157480314961,21.456692913386,386.08,546.1,0.70642201834862,0.70697674418605
"German Paper","DIN D3",272,385,10.7,15.2,10.708661417323,15.157480314961,271.78,386.08,0.70649350649351,0.70394736842105
"German Paper","DIN D4",192,272,7.6,10.7,7.5590551181102,10.708661417323,193.04,271.78,0.70588235294118,0.71028037383178
"German Paper","DIN D5",136,192,5.4,7.6,5.3543307086614,7.5590551181102,137.16,193.04,0.70833333333333,0.71052631578947
"German Paper","DIN D6",96,136,3.8,5.4,3.7795275590551,5.3543307086614,96.52,137.16,0.70588235294118,0.7037037037037
"German Paper","DIN D7",68,96,2.7,3.8,2.6771653543307,3.7795275590551,68.58,96.52,0.70833333333333,0.71052631578947
"German Paper","DIN D8",48,68,1.9,2.7,1.8897637795276,2.6771653543307,48.26,68.58,0.70588235294118,0.7037037037037
"German Paper","SIS D0",1091,1542,43,60.7,42.952755905512,60.708661417323,1092.2,1541.78,0.70752269779507,0.70840197693575
"German Paper","SIS D1",771,1091,30.4,43,30.354330708661,42.952755905512,772.16,1092.2,0.70669110907424,0.70697674418605
"German Paper","SIS D10",34,48,1.3,1.9,1.3385826771654,1.8897637795276,33.02,48.26,0.70833333333333,0.68421052631579
"German Paper","SIS D2",545,771,21.5,30.4,21.456692913386,30.354330708661,546.1,772.16,0.70687418936446,0.70723684210526
"German Paper","SIS D3",386,545,15.2,21.5,15.196850393701,21.456692913386,386.08,546.1,0.70825688073394,0.70697674418605
"German Paper","SIS D4",273,386,10.7,15.2,10.748031496063,15.196850393701,271.78,386.08,0.70725388601036,0.70394736842105
"German Paper","SIS D5",193,273,7.6,10.7,7.5984251968504,10.748031496063,193.04,271.78,0.70695970695971,0.71028037383178
"German Paper","SIS D6",136,193,5.4,7.6,5.3543307086614,7.5984251968504,137.16,193.04,0.70466321243523,0.71052631578947
"German Paper","SIS D7",96,136,3.8,5.4,3.7795275590551,5.3543307086614,96.52,137.16,0.70588235294118,0.7037037037037
"German Paper","SIS D8",68,96,2.7,3.8,2.6771653543307,3.7795275590551,68.58,96.52,0.70833333333333,0.71052631578947
"German Paper","SIS D9",48,68,1.9,2.7,1.8897637795276,2.6771653543307,48.26,68.58,0.70588235294118,0.7037037037037
"German Paper","SIS E0",878,1242,34.6,48.9,34.566929133858,48.897637795276,878.84,1242.06,0.70692431561997,0.70756646216769
"German Paper","SIS E1",621,878,24.4,34.6,24.448818897638,34.566929133858,619.76,878.84,0.70728929384966,0.70520231213873
"German Paper","SIS E10",27,39,1.1,1.5,1.0629921259843,1.5354330708661,27.94,38.1,0.69230769230769,0.73333333333333
"German Paper","SIS E2",439,621,17.3,24.4,17.283464566929,24.448818897638,439.42,619.76,0.70692431561997,0.70901639344262
"German Paper","SIS E3",310,439,12.2,17.3,12.204724409449,17.283464566929,309.88,439.42,0.70615034168565,0.70520231213873
"German Paper","SIS E4",220,310,8.7,12.2,8.6614173228346,12.204724409449,220.98,309.88,0.70967741935484,0.71311475409836
"German Paper","SIS E5",155,220,6.1,8.7,6.1023622047244,8.6614173228346,154.94,220.98,0.70454545454545,0.70114942528736
"German Paper","SIS E6",110,155,4.3,6.1,4.3307086614173,6.1023622047244,109.22,154.94,0.70967741935484,0.70491803278689
"German Paper","SIS E7",78,110,3.1,4.3,3.0708661417323,4.3307086614173,78.74,109.22,0.70909090909091,0.72093023255814
"German Paper","SIS E8",55,78,2.2,3.1,2.1653543307087,3.0708661417323,55.88,78.74,0.70512820512821,0.70967741935484
"German Paper","SIS E9",39,55,1.5,2.2,1.5354330708661,2.1653543307087,38.1,55.88,0.70909090909091,0.68181818181818
"German Paper","SIS F0",958,1354,37.7,53.3,37.716535433071,53.307086614173,957.58,1353.82,0.70753323485968,0.70731707317073
"German Paper","SIS F1",677,958,26.7,37.7,26.653543307087,37.716535433071,678.18,957.58,0.70668058455115,0.70822281167109
"German Paper","SIS F10",30,42,1.2,1.7,1.1811023622047,1.6535433070866,30.48,43.18,0.71428571428571,0.70588235294118
"German Paper","SIS F2",479,677,18.9,26.7,18.858267716535,26.653543307087,480.06,678.18,0.70753323485968,0.70786516853933
"German Paper","SIS F3",339,479,13.3,18.9,13.346456692913,18.858267716535,337.82,480.06,0.70772442588727,0.7037037037037
"German Paper","SIS F4",239,339,9.4,13.3,9.4094488188976,13.346456692913,238.76,337.82,0.70501474926254,0.70676691729323
"German Paper","SIS F5",169,239,6.7,9.4,6.6535433070866,9.4094488188976,170.18,238.76,0.7071129707113,0.71276595744681
"German Paper","SIS F6",120,169,4.7,6.7,4.7244094488189,6.6535433070866,119.38,170.18,0.71005917159763,0.70149253731343
"German Paper","SIS F7",85,120,3.3,4.7,3.3464566929134,4.7244094488189,83.82,119.38,0.70833333333333,0.70212765957447
"German Paper","SIS F8",60,85,2.4,3.3,2.3622047244094,3.3464566929134,60.96,83.82,0.70588235294118,0.72727272727273
"German Paper","SIS F9",42,60,1.7,2.4,1.6535433070866,2.3622047244094,43.18,60.96,0.7,0.70833333333333
"German Paper","SIS G0",1044,1477,41.1,58.1,41.102362204724,58.149606299213,1043.94,1475.74,0.70683818551117,0.70740103270224
"German Paper","SIS G1",738,1044,29.1,41.1,29.055118110236,41.102362204724,739.14,1043.94,0.70689655172414,0.70802919708029
"German Paper","SIS G10",33,46,1.3,1.8,1.2992125984252,1.8110236220472,33.02,45.72,0.71739130434783,0.72222222222222
"German Paper","SIS G2",522,738,20.6,29.1,20.551181102362,29.055118110236,523.24,739.14,0.70731707317073,0.70790378006873
"German Paper","SIS G3",369,522,14.5,20.6,14.527559055118,20.551181102362,368.3,523.24,0.70689655172414,0.70388349514563
"German Paper","SIS G4",261,369,10.3,14.5,10.275590551181,14.527559055118,261.62,368.3,0.70731707317073,0.71034482758621
"German Paper","SIS G5",185,261,7.3,10.3,7.2834645669291,10.275590551181,185.42,261.62,0.7088122605364,0.70873786407767
"German Paper","SIS G6",131,185,5.2,7.3,5.1574803149606,7.2834645669291,132.08,185.42,0.70810810810811,0.71232876712329
"German Paper","SIS G7",92,131,3.6,5.2,3.6220472440945,5.1574803149606,91.44,132.08,0.70229007633588,0.69230769230769
"German Paper","SIS G8",65,92,2.6,3.6,2.5590551181102,3.6220472440945,66.04,91.44,0.70652173913043,0.72222222222222
"German Paper","SIS G9",46,65,1.8,2.6,1.8110236220472,2.5590551181102,45.72,66.04,0.70769230769231,0.69230769230769
"ISO Poster Paper","ISO Poster 2A0",1189,1682,46.8,66.2,46.811023622047,66.220472440945,1188.72,1681.48,0.70689655172414,0.70694864048338
"ISO Poster Paper","ISO Poster A0",841,1189,33.1,46.8,33.110236220472,46.811023622047,840.74,1188.72,0.70731707317073,0.70726495726496
"ISO Poster Paper","ISO Poster A1",594,841,23.4,33.1,23.385826771654,33.110236220472,594.36,840.74,0.70630202140309,0.70694864048338
"ISO Poster Paper","ISO Poster A2",420,594,16.5,23.4,16.535433070866,23.385826771654,419.1,594.36,0.70707070707071,0.70512820512821
"ISO Poster Paper","ISO Poster A3",297,420,11.7,16.5,11.692913385827,16.535433070866,297.18,419.1,0.70714285714286,0.70909090909091
"ISO Poster Paper","ISO Poster A4",210,297,8.3,11.7,8.2677165354331,11.692913385827,210.82,297.18,0.70707070707071,0.70940170940171
"Imperial Paper",Antiquarian,787,1346,31,53,30.984251968504,52.992125984252,787.4,1346.2,0.58469539375929,0.58490566037736
"Imperial Paper",Atlas,660,864,26,34,25.984251968504,34.015748031496,660.4,863.6,0.76388888888889,0.76470588235294
"Imperial Paper",Brief,343,406,13.5,16,13.503937007874,15.984251968504,342.9,406.4,0.8448275862069,0.84375
"Imperial Paper",Broadsheet,457,610,18,24,17.992125984252,24.015748031496,457.2,609.6,0.74918032786885,0.75
"Imperial Paper",Cartridge,533,660,21,26,20.984251968504,25.984251968504,533.4,660.4,0.80757575757576,0.80769230769231
"Imperial Paper",Columbier,597,876,23.5,34.5,23.503937007874,34.488188976378,596.9,876.3,0.68150684931507,0.68115942028986
"Imperial Paper","Copy Draught",406,508,16,20,15.984251968504,20,406.4,508,0.7992125984252,0.8
"Imperial Paper",Crown,381,508,15,20,15,20,381,508,0.75,0.75
"Imperial Paper",Demy,445,572,17.5,22.5,17.51968503937,22.51968503937,444.5,571.5,0.77797202797203,0.77777777777778
"Imperial Paper","Double Demy",572,902,22.5,35.5,22.51968503937,35.511811023622,571.5,901.7,0.63414634146341,0.63380281690141
"Imperial Paper","Double Elephant",678,1016,26.7,40,26.692913385827,40,678.18,1016,0.66732283464567,0.6675
"Imperial Paper","Double Large Post",533,838,21,33,20.984251968504,32.992125984252,533.4,838.2,0.63603818615752,0.63636363636364
"Imperial Paper","Double Post",483,762,19,30,19.015748031496,30,482.6,762,0.63385826771654,0.63333333333333
"Imperial Paper",Elephant,584,711,23,28,22.992125984252,27.992125984252,584.2,711.2,0.82137834036568,0.82142857142857
"Imperial Paper",Emperor,1219,1829,48,72,47.992125984252,72.007874015748,1219.2,1828.8,0.6664844177146,0.66666666666667
"Imperial Paper",Foolscap,343,432,13.5,17,13.503937007874,17.007874015748,342.9,431.8,0.79398148148148,0.79411764705882
"Imperial Paper","Grand Eagle",730,1067,28.7,42,28.740157480315,42.007874015748,728.98,1066.8,0.68416119962512,0.68333333333333
"Imperial Paper",Imperial,559,762,22,30,22.007874015748,30,558.8,762,0.73359580052493,0.73333333333333
"Imperial Paper","Large Post",394,508,15.5,20,15.511811023622,20,393.7,508,0.7755905511811,0.775
"Imperial Paper",Medium,470,584,18.5,23,18.503937007874,22.992125984252,469.9,584.2,0.80479452054795,0.80434782608696
"Imperial Paper",Monarch,184,267,7.2,10.5,7.244094488189,10.511811023622,182.88,266.7,0.68913857677903,0.68571428571429
"Imperial Paper","Pinched Post",375,470,14.8,18.5,14.763779527559,18.503937007874,375.92,469.9,0.79787234042553,0.8
"Imperial Paper",Post,394,489,15.5,19.3,15.511811023622,19.251968503937,393.7,490.22,0.80572597137014,0.80310880829016
"Imperial Paper",Pott,318,381,12.5,15,12.51968503937,15,317.5,381,0.83464566929134,0.83333333333333
"Imperial Paper",Princess,546,711,21.5,28,21.496062992126,27.992125984252,546.1,711.2,0.76793248945148,0.76785714285714
"Imperial Paper","Quad Demy",889,1143,35,45,35,45,889,1143,0.77777777777778,0.77777777777778
"Imperial Paper",Quarto,229,279,9,11,9.0157480314961,10.984251968504,228.6,279.4,0.82078853046595,0.81818181818182
"Imperial Paper",Royal,508,635,20,25,20,25,508,635,0.8,0.8
"Imperial Paper","Sheet, Half Post",495,597,19.5,23.5,19.488188976378,23.503937007874,495.3,596.9,0.82914572864322,0.82978723404255
"Imperial Paper","Small Foolscap",337,419,13.3,16.5,13.267716535433,16.496062992126,337.82,419.1,0.80429594272076,0.80606060606061
"Imperial Paper","Super Royal",483,686,19,27,19.015748031496,27.007874015748,482.6,685.8,0.70408163265306,0.7037037037037
"International Envelope Paper",B6,125,176,4.9,6.9,4.9212598425197,6.9291338582677,124.46,175.26,0.71022727272727,0.71014492753623
"International Envelope Paper",C3,324,458,12.8,18,12.755905511811,18.031496062992,325.12,457.2,0.70742358078603,0.71111111111111
"International Envelope Paper",C4,229,324,9,12.8,9.0157480314961,12.755905511811,228.6,325.12,0.70679012345679,0.703125
"International Envelope Paper",C4M,318,229,12.5,9,12.51968503937,9.0157480314961,317.5,228.6,1.3886462882096,1.3888888888889
"International Envelope Paper",C5,162,229,6.4,9,6.3779527559055,9.0157480314961,162.56,228.6,0.70742358078603,0.71111111111111
"International Envelope Paper",C6,114,162,4.5,6.4,4.488188976378,6.3779527559055,114.3,162.56,0.7037037037037,0.703125
"International Envelope Paper",C6/C5,114,229,4.5,9,4.488188976378,9.0157480314961,114.3,228.6,0.49781659388646,0.5
"International Envelope Paper",C64M,318,114,12.5,4.5,12.51968503937,4.488188976378,317.5,114.3,2.7894736842105,2.7777777777778
"International Envelope Paper",C7,81,114,3.2,4.5,3.1889763779528,4.488188976378,81.28,114.3,0.71052631578947,0.71111111111111
"International Envelope Paper",C7/C6,81,162,3.2,6.4,3.1889763779528,6.3779527559055,81.28,162.56,0.5,0.5
"International Envelope Paper",CE4,229,310,9,12.2,9.0157480314961,12.204724409449,228.6,309.88,0.73870967741935,0.73770491803279
"International Envelope Paper",CE64,114,310,4.5,12.2,4.488188976378,12.204724409449,114.3,309.88,0.36774193548387,0.36885245901639
"International Envelope Paper",DL,110,220,4.3,8.7,4.3307086614173,8.6614173228346,109.22,220.98,0.5,0.49425287356322
"International Envelope Paper",E4,220,312,8.7,12.3,8.6614173228346,12.283464566929,220.98,312.42,0.70512820512821,0.70731707317073
"International Envelope Paper",E5,115,220,4.5,8.7,4.5275590551181,8.6614173228346,114.3,220.98,0.52272727272727,0.51724137931034
"International Envelope Paper",E56,155,155,6.1,6.1,6.1023622047244,6.1023622047244,154.94,154.94,1,1
"International Envelope Paper",E6,110,155,4.3,6.1,4.3307086614173,6.1023622047244,109.22,154.94,0.70967741935484,0.70491803278689
"International Envelope Paper",E65,110,220,4.3,8.7,4.3307086614173,8.6614173228346,109.22,220.98,0.5,0.49425287356322
"International Envelope Paper",EC45,220,229,8.7,9,8.6614173228346,9.0157480314961,220.98,228.6,0.96069868995633,0.96666666666667
"International Envelope Paper",EC5,155,229,6.1,9,6.1023622047244,9.0157480314961,154.94,228.6,0.67685589519651,0.67777777777778
"International Envelope Paper",EX5,155,216,6.1,8.5,6.1023622047244,8.503937007874,154.94,215.9,0.71759259259259,0.71764705882353
"International Envelope Paper",R7,120,135,4.7,5.3,4.7244094488189,5.3149606299213,119.38,134.62,0.88888888888889,0.88679245283019
"International Envelope Paper",S4,250,330,9.8,13,9.8425196850394,12.992125984252,248.92,330.2,0.75757575757576,0.75384615384615
"International Envelope Paper",S5,185,255,7.3,10,7.2834645669291,10.03937007874,185.42,254,0.72549019607843,0.73
"International Envelope Paper",S65,110,225,4.3,8.9,4.3307086614173,8.8582677165354,109.22,226.06,0.48888888888889,0.48314606741573
"International Envelope Paper",X5,105,216,4.1,8.5,4.1338582677165,8.503937007874,104.14,215.9,0.48611111111111,0.48235294117647
"Italian Poster Paper","Italian Movie Poster Due Fogli",1000,1400,39.4,55.1,39.370078740157,55.11811023622,1000.76,1399.54,0.71428571428571,0.71506352087114
"Italian Poster Paper","Italian Movie Poster Locandina",330,700,13.0,27.6,12.992125984252,27.55905511811,330.2,701.04,0.47142857142857,0.47101449275362
"Italian Poster Paper","Italian Movie Poster Photobusta",500,700,19.7,27.6,19.685039370079,27.55905511811,500.38,701.04,0.71428571428571,0.71376811594203
"Italian Poster Paper","Italian Movie Poster Quattro Fogli",1400,2000,55.1,78.7,55.11811023622,78.740157480315,1399.54,1998.98,0.7,0.70012706480305
"Italian Poster Paper","Italian Movie Poster Un Foglio",700,1000,27.6,39.4,27.55905511811,39.370078740157,701.04,1000.76,0.7,0.7005076142132
"Japanese Paper",JB0,1030,1456,40.6,57.3,40.551181102362,57.322834645669,1031.24,1455.42,0.70741758241758,0.70855148342059
"Japanese Paper",JB1,728,1030,28.7,40.6,28.661417322835,40.551181102362,728.98,1031.24,0.70679611650485,0.70689655172414
"Japanese Paper",JB10,32,45,1.3,1.8,1.259842519685,1.7716535433071,33.02,45.72,0.71111111111111,0.72222222222222
"Japanese Paper",JB11,22,32,0.9,1.3,0.86614173228346,1.259842519685,22.86,33.02,0.6875,0.69230769230769
"Japanese Paper",JB12,16,22,0.6,0.9,0.62992125984252,0.86614173228346,15.24,22.86,0.72727272727273,0.66666666666667
"Japanese Paper",JB2,515,728,20.3,28.7,20.275590551181,28.661417322835,515.62,728.98,0.70741758241758,0.70731707317073
"Japanese Paper",JB3,364,515,14.3,20.3,14.330708661417,20.275590551181,363.22,515.62,0.70679611650485,0.70443349753695
"Japanese Paper",JB4,257,364,10.1,14.3,10.11811023622,14.330708661417,256.54,363.22,0.70604395604396,0.70629370629371
"Japanese Paper",JB5,182,257,7.2,10.1,7.1653543307087,10.11811023622,182.88,256.54,0.70817120622568,0.71287128712871
"Japanese Paper",JB6,128,182,5,7.2,5.0393700787402,7.1653543307087,127,182.88,0.7032967032967,0.69444444444444
"Japanese Paper",JB7,91,128,3.6,5,3.5826771653543,5.0393700787402,91.44,127,0.7109375,0.72
"Japanese Paper",JB8,64,91,2.5,3.6,2.5196850393701,3.5826771653543,63.5,91.44,0.7032967032967,0.69444444444444
"Japanese Paper",JB9,45,64,1.8,2.5,1.7716535433071,2.5196850393701,45.72,63.5,0.703125,0.72
"Japanese Paper","Kiku 4",227,306,8.9,12,8.9370078740157,12.047244094488,226.06,304.8,0.74183006535948,0.74166666666667
"Japanese Paper","Kiku 5",151,227,5.9,8.9,5.9448818897638,8.9370078740157,149.86,226.06,0.66519823788546,0.66292134831461
"Japanese Paper","Shiroku ban 4",264,379,10.4,14.9,10.393700787402,14.92125984252,264.16,378.46,0.69656992084433,0.69798657718121
"Japanese Paper","Shiroku ban 5",189,262,7.4,10.3,7.4409448818898,10.314960629921,187.96,261.62,0.72137404580153,0.71844660194175
"Japanese Paper","Shiroku ban 6",127,188,5,7.4,5,7.4015748031496,127,187.96,0.67553191489362,0.67567567567568
"Netherlands Billboard Paper","Netherlands Billboard",3300,2400,129.9,94.5,129.92125984252,94.488188976378,3299.46,2400.3,1.375,1.3746031746032
"Newspaper Paper",Berliner,315,470,12.4,18.5,12.40157480315,18.503937007874,314.96,469.9,0.67021276595745,0.67027027027027
"Newspaper Paper","British Broadsheet",375,597,14.8,23.5,14.763779527559,23.503937007874,375.92,596.9,0.62814070351759,0.62978723404255
"Newspaper Paper",Broadsheet,597,749,23.5,29.5,23.503937007874,29.488188976378,596.9,749.3,0.79706275033378,0.79661016949153
"Newspaper Paper","Canadian Tabloid",260,368,10.2,14.5,10.236220472441,14.488188976378,259.08,368.3,0.70652173913043,0.70344827586207
"Newspaper Paper",Ciner,350,500,13.8,19.7,13.779527559055,19.685039370079,350.52,500.38,0.7,0.7005076142132
"Newspaper Paper",Compact,280,430,11,16.9,11.023622047244,16.929133858268,279.4,429.26,0.65116279069767,0.6508875739645
"Newspaper Paper","New York Times",305,559,12,22,12.007874015748,22.007874015748,304.8,558.8,0.54561717352415,0.54545454545455
"Newspaper Paper",Nordisch,400,570,15.7,22.4,15.748031496063,22.44094488189,398.78,568.96,0.70175438596491,0.70089285714286
"Newspaper Paper","Norwegian Tabloid",280,400,11,15.7,11.023622047244,15.748031496063,279.4,398.78,0.7,0.70063694267516
"Newspaper Paper",Rhenish,350,520,13.8,20.5,13.779527559055,20.472440944882,350.52,520.7,0.67307692307692,0.67317073170732
"Newspaper Paper","South African Broadsheet",410,578,16.1,22.8,16.141732283465,22.755905511811,408.94,579.12,0.70934256055363,0.70614035087719
"Newspaper Paper",Swiss,320,475,12.6,18.7,12.59842519685,18.700787401575,320.04,474.98,0.67368421052632,0.67379679144385
"Newspaper Paper",Tabloid,280,430,11,16.9,11.023622047244,16.929133858268,279.4,429.26,0.65116279069767,0.6508875739645
"Newspaper Paper","US Broadsheet",381,578,15,22.8,15,22.755905511811,381,579.12,0.65916955017301,0.65789473684211
"Newspaper Paper","Wall Street Journal",305,578,12,22.8,12.007874015748,22.755905511811,304.8,579.12,0.52768166089965,0.52631578947368
"Old English Paper","Board - Imperial",558.8,762,22,30,22,30,558.8,762,0.73333333333333,0.73333333333333
"Old English Paper","Board - Index",647.7,774.7,25.5,30.5,25.5,30.5,647.7,774.7,0.83606557377049,0.83606557377049
"Old English Paper","Board - Large Imperial",558.8,812.8,22,32,22,32,558.8,812.8,0.6875,0.6875
"Old English Paper","Board - Postal",571.5,723.9,22.5,28.5,22.5,28.5,571.5,723.9,0.78947368421053,0.78947368421053
"Old English Paper","Board - Royal",508,635,20,25,20,25,508,635,0.8,0.8
"Old English Paper","Book and Drawing Paper - Antiquarian",787.4,1346.2,31,53,31,53,787.4,1346.2,0.58490566037736,0.58490566037736
"Old English Paper","Book and Drawing Paper - Atlas",666.75,863.6,26.25,34,26.25,34,666.75,863.6,0.77205882352941,0.77205882352941
"Old English Paper","Book and Drawing Paper - Columbier",596.9,889,23.5,35,23.5,35,596.9,889,0.67142857142857,0.67142857142857
"Old English Paper","Book and Drawing Paper - Demy",393.7,508,15.5,20,15.5,20,393.7,508,0.775,0.775
"Old English Paper","Book and Drawing Paper - Double Elephant",673.1,1016,26.5,40,26.5,40,673.1,1016,0.6625,0.6625
"Old English Paper","Book and Drawing Paper - Elephant",584.2,711.2,23,28,23,28,584.2,711.2,0.82142857142857,0.82142857142857
"Old English Paper","Book and Drawing Paper - Foolscap",355.6,476.25,14,18.75,14,18.75,355.6,476.25,0.74666666666667,0.74666666666667
"Old English Paper","Book and Drawing Paper - Imperial",558.8,768.35,22,30.25,22,30.25,558.8,768.35,0.72727272727273,0.72727272727273
"Old English Paper","Book and Drawing Paper - Medium",444.5,571.5,17.5,22.5,17.5,22.5,444.5,571.5,0.77777777777778,0.77777777777778
"Old English Paper","Book and Drawing Paper - Royal",482.6,609.6,19,24,19,24,482.6,609.6,0.79166666666667,0.79166666666667
"Old English Paper","Book and Drawing Paper - Super Royal",488.95,685.8,19.25,27,19.25,27,488.95,685.8,0.71296296296296,0.71296296296296
"Old English Paper","Cartridge Paper - Demy",450.85,571.5,17.75,22.5,17.75,22.5,450.85,571.5,0.78888888888889,0.78888888888889
"Old English Paper","Cartridge Paper - Elephant",584.2,711.2,23,28,23,28,584.2,711.2,0.82142857142857,0.82142857142857
"Old English Paper","Cartridge Paper - Foolscap",355.6,476.25,14,18.75,14,18.75,355.6,476.25,0.74666666666667,0.74666666666667
"Old English Paper","Cartridge Paper - Imperial",533.4,660.4,21,26,21,26,533.4,660.4,0.80769230769231,0.80769230769231
"Old English Paper","Cartridge Paper - Royal",482.6,609.6,19,24,19,24,482.6,609.6,0.79166666666667,0.79166666666667
"Old English Paper","Cartridge Paper - Super Royal",488.95,698.5,19.25,27.5,19.25,27.5,488.95,698.5,0.7,0.7
"Old English Paper","Cut Writing Paper - Albert",152.4,101.6,6,4,6,4,152.4,101.6,1.5,1.5
"Old English Paper","Cut Writing Paper - Duchess",152.4,114.3,6,4.5,6,4.5,152.4,114.3,1.3333333333333,1.3333333333333
"Old English Paper","Cut Writing Paper - Duke",177.8,139.7,7,5.5,7,5.5,177.8,139.7,1.2727272727273,1.2727272727273
"Old English Paper","Cut Writing Paper - Foolscap 4 quarto",203.2,165.1,8,6.5,8,6.5,203.2,165.1,1.2307692307692,1.2307692307692
"Old English Paper","Cut Writing Paper - Foolscap Folio",330.2,203.2,13,8,13,8,330.2,203.2,1.625,1.625
"Old English Paper","Cut Writing Paper - Large Post 4 quarto",254,203.2,10,8,10,8,254,203.2,1.25,1.25
"Old English Paper","Cut Writing Paper - Large Post 8 octavo",203.2,127,8,5,8,5,203.2,127,1.6,1.6
"Old English Paper","Cut Writing Paper - Small Post 4 quarto",228.6,177.8,9,7,9,7,228.6,177.8,1.2857142857143,1.2857142857143
"Old English Paper","Cut Writing Paper - Small Post 8 octavo",177.8,114.3,7,4.5,7,4.5,177.8,114.3,1.5555555555556,1.5555555555556
"Old English Paper","Printing Paper - Crown",412.75,533.4,16.25,21,16.25,21,412.75,533.4,0.77380952380952,0.77380952380952
"Old English Paper","Printing Paper - Demy",450.85,571.5,17.75,22.5,17.75,22.5,450.85,571.5,0.78888888888889,0.78888888888889
"Old English Paper","Printing Paper - Double Crown",508,762,20,30,20,30,508,762,0.66666666666667,0.66666666666667
"Old English Paper","Printing Paper - Double Demy",571.5,901.7,22.5,35.5,22.5,35.5,571.5,901.7,0.63380281690141,0.63380281690141
"Old English Paper","Printing Paper - Double Foolscap",431.8,685.8,17,27,17,27,431.8,685.8,0.62962962962963,0.62962962962963
"Old English Paper","Printing Paper - Double Pott",381,635,15,25,15,25,381,635,0.6,0.6
"Old English Paper","Printing Paper - Medium",463.55,584.2,18.25,23,18.25,23,463.55,584.2,0.79347826086957,0.79347826086957
"Old English Paper","Printing Paper - Royal",508,635,20,25,20,25,508,635,0.8,0.8
"Old English Paper","Printing Paper - Super Royal",533.4,685.8,21,27,21,27,533.4,685.8,0.77777777777778,0.77777777777778
"Old English Paper","Writing Paper - Copy",412.75,508,16.25,20,16.25,20,412.75,508,0.8125,0.8125
"Old English Paper","Writing Paper - Double Foolscap",419.1,673.1,16.5,26.5,16.5,26.5,419.1,673.1,0.62264150943396,0.62264150943396
"Old English Paper","Writing Paper - Double Large Post",527.05,838.2,20.75,33,20.75,33,527.05,838.2,0.62878787878788,0.62878787878788
"Old English Paper","Writing Paper - Double Post",482.6,774.7,19,30.5,19,30.5,482.6,774.7,0.62295081967213,0.62295081967213
"Old English Paper","Writing Paper - Double Pott",381,635,15,25,15,25,381,635,0.6,0.6
"Old English Paper","Writing Paper - Foolscap",336.55,419.1,13.25,16.5,13.25,16.5,336.55,419.1,0.8030303030303,0.8030303030303
"Old English Paper","Writing Paper - Foolscap and Half",336.55,628.65,13.25,24.75,13.25,24.75,336.55,628.65,0.53535353535354,0.53535353535354
"Old English Paper","Writing Paper - Foolscap and Third",336.55,558.8,13.25,22,13.25,22,336.55,558.8,0.60227272727273,0.60227272727273
"Old English Paper","Writing Paper - Large Post",419.1,527.05,16.5,20.75,16.5,20.75,419.1,527.05,0.79518072289157,0.79518072289157
"Old English Paper","Writing Paper - Medium",457.2,571.5,18,22.5,18,22.5,457.2,571.5,0.8,0.8
"Old English Paper","Writing Paper - Pinched Post",368.3,469.9,14.5,18.5,14.5,18.5,368.3,469.9,0.78378378378378,0.78378378378378
"Old English Paper","Writing Paper - Post",387.35,482.6,15.25,19,15.25,19,387.35,482.6,0.80263157894737,0.80263157894737
"Old English Paper","Writing Paper - Pott",317.5,381,12.5,15,12.5,15,317.5,381,0.83333333333333,0.83333333333333
"Photography Paper",11R,279,356,11,14,10.984251968504,14.015748031496,279.4,355.6,0.78370786516854,0.78571428571429
"Photography Paper","2LD, DSCW",127,169,5,6.7,5,6.6535433070866,127,170.18,0.75147928994083,0.74626865671642
"Photography Paper",2LW,127,190,5,7.5,5,7.4803149606299,127,190.5,0.66842105263158,0.66666666666667
"Photography Paper",2R,64,89,2.5,3.5,2.5196850393701,3.503937007874,63.5,88.9,0.71910112359551,0.71428571428571
"Photography Paper","3R, L",89,127,3.5,5,3.503937007874,5,88.9,127,0.7007874015748,0.7
"Photography Paper","4R, KG",102,152,4,6,4.0157480314961,5.9842519685039,101.6,152.4,0.67105263157895,0.66666666666667
"Photography Paper","5R, 2L",127,178,5,7,5,7.007874015748,127,177.8,0.71348314606742,0.71428571428571
"Photography Paper",6R,152,203,6,8,5.9842519685039,7.992125984252,152.4,203.2,0.7487684729064,0.75
"Photography Paper","8R, 6P",203,254,8,10,7.992125984252,10,203.2,254,0.7992125984252,0.8
"Photography Paper","A3+ Super B",330,483,13,19,12.992125984252,19.015748031496,330.2,482.6,0.6832298136646,0.68421052631579
"Photography Paper",KGD,102,136,4,5.4,4.0157480314961,5.3543307086614,101.6,137.16,0.75,0.74074074074074
"Photography Paper","LD, DSC",89,119,3.5,4.7,3.503937007874,4.6850393700787,88.9,119.38,0.74789915966387,0.74468085106383
"Photography Paper",LW,89,133,3.5,5.2,3.503937007874,5.2362204724409,88.9,132.08,0.66917293233083,0.67307692307692
"Photography Paper",Passport,35,45,1.4,1.8,1.3779527559055,1.7716535433071,35.56,45.72,0.77777777777778,0.77777777777778
"Photography Paper","S8R, 6PW",203,305,8,12,7.992125984252,12.007874015748,203.2,304.8,0.6655737704918,0.66666666666667
"Raw Paper",A0U,880,1230,34.6,48.4,34.645669291339,48.425196850394,878.84,1229.36,0.71544715447154,0.71487603305785
"Raw Paper",A1U,625,880,24.6,34.6,24.606299212598,34.645669291339,624.84,878.84,0.71022727272727,0.71098265895954
"Raw Paper",A2U,450,625,17.7,24.6,17.716535433071,24.606299212598,449.58,624.84,0.72,0.71951219512195
"Raw Paper",A3U,330,450,13,17.7,12.992125984252,17.716535433071,330.2,449.58,0.73333333333333,0.73446327683616
"Raw Paper",A4U,240,330,9.4,13,9.4488188976378,12.992125984252,238.76,330.2,0.72727272727273,0.72307692307692
"Raw Paper",RA0,860,1220,33.9,48,33.858267716535,48.031496062992,861.06,1219.2,0.70491803278689,0.70625
"Raw Paper",RA1,610,860,24,33.9,24.015748031496,33.858267716535,609.6,861.06,0.7093023255814,0.70796460176991
"Raw Paper",RA2,430,610,16.9,24,16.929133858268,24.015748031496,429.26,609.6,0.70491803278689,0.70416666666667
"Raw Paper",RA3,305,430,12,16.9,12.007874015748,16.929133858268,304.8,429.26,0.7093023255814,0.71005917159763
"Raw Paper",RA4,215,305,8.5,12,8.4645669291339,12.007874015748,215.9,304.8,0.70491803278689,0.70833333333333
"Raw Paper",SRA0,900,1280,35.4,50.4,35.433070866142,50.393700787402,899.16,1280.16,0.703125,0.70238095238095
"Raw Paper",SRA1,640,900,25.2,35.4,25.196850393701,35.433070866142,640.08,899.16,0.71111111111111,0.71186440677966
"Raw Paper",SRA1+,660,920,26,36.2,25.984251968504,36.220472440945,660.4,919.48,0.71739130434783,0.71823204419889
"Raw Paper",SRA2,450,640,17.7,25.2,17.716535433071,25.196850393701,449.58,640.08,0.703125,0.70238095238095
"Raw Paper",SRA2+,480,650,18.9,25.6,18.897637795276,25.590551181102,480.06,650.24,0.73846153846154,0.73828125
"Raw Paper",SRA3,320,450,12.6,17.7,12.59842519685,17.716535433071,320.04,449.58,0.71111111111111,0.71186440677966
"Raw Paper",SRA3+,320,460,12.6,18.1,12.59842519685,18.110236220472,320.04,459.74,0.69565217391304,0.69613259668508
"Raw Paper",SRA3++,320,464,12.6,18.3,12.59842519685,18.267716535433,320.04,464.82,0.68965517241379,0.68852459016393
"Raw Paper",SRA4,225,320,8.9,12.6,8.8582677165354,12.59842519685,226.06,320.04,0.703125,0.70634920634921
"Traditional British Paper",Dukes,140,178,5.5,7,5.511811023622,7.007874015748,139.7,177.8,0.78651685393258,0.78571428571429
"Traditional British Paper",Foolscap,203,330,8,13,7.992125984252,12.992125984252,203.2,330.2,0.61515151515152,0.61538461538462
"Traditional British Paper",Imperial,178,229,7,9,7.007874015748,9.0157480314961,177.8,228.6,0.77729257641921,0.77777777777778
"Traditional British Paper",Kings,165,203,6.5,8,6.496062992126,7.992125984252,165.1,203.2,0.8128078817734,0.8125
"Traditional British Paper",Quarto,203,254,8,10,7.992125984252,10,203.2,254,0.7992125984252,0.8
"Transitional Paper",F0,841,1321,33.1,52,33.110236220472,52.007874015748,840.74,1320.8,0.63663890991673,0.63653846153846
"Transitional Paper",F1,660,841,26,33.1,25.984251968504,33.110236220472,660.4,840.74,0.78478002378121,0.78549848942598
"Transitional Paper",F10,26,41,1,1.6,1.0236220472441,1.6141732283465,25.4,40.64,0.63414634146341,0.625
"Transitional Paper",F2,420,660,16.5,26,16.535433070866,25.984251968504,419.1,660.4,0.63636363636364,0.63461538461538
"Transitional Paper",F3,330,420,13,16.5,12.992125984252,16.535433070866,330.2,419.1,0.78571428571429,0.78787878787879
"Transitional Paper",F4,210,330,8.3,13,8.2677165354331,12.992125984252,210.82,330.2,0.63636363636364,0.63846153846154
"Transitional Paper",F5,165,210,6.5,8.3,6.496062992126,8.2677165354331,165.1,210.82,0.78571428571429,0.78313253012048
"Transitional Paper",F6,105,165,4.1,6.5,4.1338582677165,6.496062992126,104.14,165.1,0.63636363636364,0.63076923076923
"Transitional Paper",F7,82,105,3.2,4.1,3.2283464566929,4.1338582677165,81.28,104.14,0.78095238095238,0.78048780487805
"Transitional Paper",F8,52,82,2,3.2,2.0472440944882,3.2283464566929,50.8,81.28,0.63414634146341,0.625
"Transitional Paper",F9,41,52,1.6,2,1.6141732283465,2.0472440944882,40.64,50.8,0.78846153846154,0.8
"Transitional Paper",PA0,840,1120,33.1,44.1,33.070866141732,44.094488188976,840.74,1120.14,0.75,0.75056689342404
"Transitional Paper",PA1,560,840,22,33.1,22.047244094488,33.070866141732,558.8,840.74,0.66666666666667,0.66465256797583
"Transitional Paper",PA10,26,35,1,1.4,1.0236220472441,1.3779527559055,25.4,35.56,0.74285714285714,0.71428571428571
"Transitional Paper",PA2,420,560,16.5,22,16.535433070866,22.047244094488,419.1,558.8,0.75,0.75
"Transitional Paper",PA3,280,420,11,16.5,11.023622047244,16.535433070866,279.4,419.1,0.66666666666667,0.66666666666667
"Transitional Paper",PA4,210,280,8.3,11,8.2677165354331,11.023622047244,210.82,279.4,0.75,0.75454545454545
"Transitional Paper",PA5,140,210,5.5,8.3,5.511811023622,8.2677165354331,139.7,210.82,0.66666666666667,0.66265060240964
"Transitional Paper",PA6,105,140,4.1,5.5,4.1338582677165,5.511811023622,104.14,139.7,0.75,0.74545454545455
"Transitional Paper",PA7,70,105,2.8,4.1,2.755905511811,4.1338582677165,71.12,104.14,0.66666666666667,0.68292682926829
"Transitional Paper",PA8,52,70,2,2.8,2.0472440944882,2.755905511811,50.8,71.12,0.74285714285714,0.71428571428571
"Transitional Paper",PA9,35,52,1.4,2,1.3779527559055,2.0472440944882,35.56,50.8,0.67307692307692,0.7
"UK Billboard Paper","UK Billboard 12 Sheet",3050,1520,120,60,120.07874015748,59.842519685039,3048,1524,2.0065789473684,2
"UK Billboard Paper","UK Billboard 16 Sheet",2030,3050,80,120,79.92125984252,120.07874015748,2032,3048,0.6655737704918,0.66666666666667
"UK Billboard Paper","UK Billboard 32 Sheet",4060,3050,160,120,159.84251968504,120.07874015748,4064,3048,1.3311475409836,1.3333333333333
"UK Billboard Paper","UK Billboard 4 Sheet",1020,1520,40,60,40.157480314961,59.842519685039,1016,1524,0.67105263157895,0.66666666666667
"UK Billboard Paper","UK Billboard 48 Sheet",6100,3050,240,120,240.15748031496,120.07874015748,6096,3048,2,2
"UK Billboard Paper","UK Billboard 6 Sheet",1200,1800,47.24,70.87,47.244094488189,70.866141732283,1199.896,1800.098,0.66666666666667,0.66657259771412
"UK Billboard Paper","UK Billboard 64 Sheet",8130,3050,320,120,320.07874015748,120.07874015748,8128,3048,2.6655737704918,2.6666666666667
"UK Billboard Paper","UK Billboard 96 Sheet",12190,3050,480,120,479.92125984252,120.07874015748,12192,3048,3.9967213114754,4
"UK Movie Poster Paper","UK Movie Poster Cards",203.2,254.0,8,10,8,10,203.2,254,0.8,0.8
"UK Movie Poster Paper","UK Movie Poster Double Crown",508,762,20,30,20,30,508,762,0.66666666666667,0.66666666666667
"UK Movie Poster Paper","UK Movie Poster One Sheet",685.8,1016,27,40,27,40,685.8,1016,0.675,0.675
"UK Movie Poster Paper","UK Movie Poster Quad",762,1016,30,40,30,40,762,1016,0.75,0.75
"UK Movie Poster Paper","UK Movie Poster Six Sheet",2032,2057.4,80,81,80,81,2032,2057.4,0.98765432098765,0.98765432098765
"UK Movie Poster Paper","UK Movie Poster Three Sheet",1016,2057.4,40,81,40,81,1016,2057.4,0.49382716049383,0.49382716049383
"US Billboard Paper","US Billboard 12 x 6 ft",3660,1830,144,72,144.09448818898,72.047244094488,3657.6,1828.8,2,2
"US Billboard Paper","US Billboard 12 x 8 ft",3660,2440,144,96,144.09448818898,96.062992125984,3657.6,2438.4,1.5,1.5
"US Billboard Paper","US Billboard 22 x 10 ft",3660,1830,264,120,144.09448818898,72.047244094488,6705.6,3048,2,2.2
"US Billboard Paper","US Billboard 24 x 10 ft",3660,2440,288,120,144.09448818898,96.062992125984,7315.2,3048,1.5,2.4
"US Billboard Paper","US Billboard 25 x 12 ft",7620,3660,300,144,300,144.09448818898,7620,3657.6,2.0819672131148,2.0833333333333
"US Billboard Paper","US Billboard 30 Sheet",6910,3170,272,125,272.04724409449,124.8031496063,6908.8,3175,2.1798107255521,2.176
"US Billboard Paper","US Billboard 36 x 10.5 ft",10970,3200,432,126,431.88976377953,125.9842519685,10972.8,3200.4,3.428125,3.4285714285714
"US Billboard Paper","US Billboard 40 x 12 ft",12190,3660,480,144,479.92125984252,144.09448818898,12192,3657.6,3.3306010928962,3.3333333333333
"US Billboard Paper","US Billboard 48 x 14 ft",14630,4270,576,168,575.9842519685,168.11023622047,14630.4,4267.2,3.4262295081967,3.4285714285714
"US Billboard Paper","US Billboard 50 x 20 ft",15240,6100,600,240,600,240.15748031496,15240,6096,2.4983606557377,2.5
"US Billboard Paper","US Billboard 60 x 16 ft",18290,4880,720,192,720.07874015748,192.12598425197,18288,4876.8,3.7479508196721,3.75
"US Billboard Paper","US Billboard 8 Sheet",3350,1520,132,60,131.88976377953,59.842519685039,3352.8,1524,2.2039473684211,2.2
"US Envelope Paper",1,229,152,9,6,9.0157480314961,5.9842519685039,228.6,152.4,1.5065789473684,1.5
"US Envelope Paper","10 1/2",305,229,12,9,12.007874015748,9.0157480314961,304.8,228.6,1.3318777292576,1.3333333333333
"US Envelope Paper","12 1/2",318,241,12.5,9.5,12.51968503937,9.488188976378,317.5,241.3,1.3195020746888,1.3157894736842
"US Envelope Paper","13 1/2",330,254,13,10,12.992125984252,10,330.2,254,1.2992125984252,1.3
"US Envelope Paper","14 1/2",368,292,14.5,11.5,14.488188976378,11.496062992126,368.3,292.1,1.2602739726027,1.2608695652174
"US Envelope Paper","15 1/2",394,305,15.5,12,15.511811023622,12.007874015748,393.7,304.8,1.2918032786885,1.2916666666667
"US Envelope Paper",3,254,178,10,7,10,7.007874015748,254,177.8,1.4269662921348,1.4285714285714
"US Envelope Paper",6,267,191,10.5,7.5,10.511811023622,7.5196850393701,266.7,190.5,1.3979057591623,1.4
"US Envelope Paper",7,171.5,95.3,6.75,3.75,6.751968503937,3.751968503937,171.45,95.25,1.7995802728227,1.8
"US Envelope Paper",7,172,95,6.8,3.7,6.7716535433071,3.740157480315,172.72,93.98,1.8105263157895,1.8378378378378
"US Envelope Paper",8,286,210,11.3,8.3,11.259842519685,8.2677165354331,287.02,210.82,1.3619047619048,1.3614457831325
"US Envelope Paper",9,225,98,8.9,3.9,8.8582677165354,3.8582677165354,226.06,99.06,2.2959183673469,2.2820512820513
"US Envelope Paper",9,225.4,98.4,8.875,3.875,8.8740157480315,3.8740157480315,225.425,98.425,2.2906504065041,2.2903225806452
"US Envelope Paper",10,241,104,9.5,4.1,9.488188976378,4.0944881889764,241.3,104.14,2.3173076923077,2.3170731707317
"US Envelope Paper",10,241.3,104.8,9.5,4.125,9.5,4.1259842519685,241.3,104.775,2.3024809160305,2.3030303030303
"US Envelope Paper",11,263.5,114.3,10.375,4.5,10.374015748031,4.5,263.525,114.3,2.3053368328959,2.3055555555556
"US Envelope Paper",11,264,114,10.4,4.5,10.393700787402,4.488188976378,264.16,114.3,2.3157894736842,2.3111111111111
"US Envelope Paper",12,279,121,11,4.8,10.984251968504,4.7637795275591,279.4,121.92,2.3057851239669,2.2916666666667
"US Envelope Paper",12,279.4,120.7,11.0,4.75,11,4.751968503937,279.4,120.65,2.3148301574151,2.3157894736842
"US Envelope Paper",14,292,127,11.5,5,11.496062992126,5,292.1,127,2.2992125984252,2.3
"US Envelope Paper",14,292.1,127.0,11.5,5.0,11.5,5,292.1,127,2.3,2.3
"US Envelope Paper",15,381,254,15,10,15,10,381,254,1.5,1.5
"US Envelope Paper",16,304.8,152.4,12.0,6.0,12,6,304.8,152.4,2,2
"US Envelope Paper",16,305,152,12,6,12.007874015748,5.9842519685039,304.8,152.4,2.0065789473684,2
"US Envelope Paper","6 1/4",152.4,88.9,6.0,3.5,6,3.5,152.4,88.9,1.7142857142857,1.7142857142857
"US Envelope Paper","6 3/4",165.1,92.1,6.5,3.625,6.5,3.6259842519685,165.1,92.075,1.7926167209555,1.7931034482759
"US Envelope Paper","7 3/4",190.5,98.4,7.5,3.875,7.5,3.8740157480315,190.5,98.425,1.9359756097561,1.9354838709677
"US Envelope Paper","7 3/4 Monarch",191,98,7.5,3.9,7.5196850393701,3.8582677165354,190.5,99.06,1.9489795918367,1.9230769230769
"US Envelope Paper","8 5/8",219.1,92.1,8.625,3.625,8.6259842519685,3.6259842519685,219.075,92.075,2.3789359391965,2.3793103448276
"US Envelope Paper","9 3/4",286,222,11.3,8.7,11.259842519685,8.740157480315,287.02,220.98,1.2882882882883,1.2988505747126
"US Envelope Paper","A Long",225,98,8.9,3.9,8.8582677165354,3.8582677165354,226.06,99.06,2.2959183673469,2.2820512820513
"US Envelope Paper",A1,92,130,3.6,5.1,3.6220472440945,5.1181102362205,91.44,129.54,0.70769230769231,0.70588235294118
"US Envelope Paper","A10 Willow",241,152,9.5,6,9.488188976378,5.9842519685039,241.3,152.4,1.5855263157895,1.5833333333333
"US Envelope Paper","A2 Lady Grey",146,111,5.7,4.4,5.748031496063,4.3700787401575,144.78,111.76,1.3153153153153,1.2954545454545
"US Envelope Paper",A4,159,108,6.3,4.3,6.259842519685,4.251968503937,160.02,109.22,1.4722222222222,1.4651162790698
"US Envelope Paper","A6 Thompsons Standard",165,121,6.5,4.8,6.496062992126,4.7637795275591,165.1,121.92,1.3636363636364,1.3541666666667
"US Envelope Paper","A7 Besselheim",184,133,7.2,5.2,7.244094488189,5.2362204724409,182.88,132.08,1.3834586466165,1.3846153846154
"US Envelope Paper","A8 Carrs",206,140,8.1,5.5,8.1102362204724,5.511811023622,205.74,139.7,1.4714285714286,1.4727272727273
"US Envelope Paper","A9 Diplomat",222,146,8.7,5.7,8.740157480315,5.748031496063,220.98,144.78,1.5205479452055,1.5263157894737
"US Envelope Paper","Envelope 1",228.6,152.4,9.0,6.0,9,6,228.6,152.4,1.5,1.5
"US Envelope Paper","Envelope 1 3/4",241.3,165.1,9.5,6.5,9.5,6.5,241.3,165.1,1.4615384615385,1.4615384615385
"US Envelope Paper","Envelope 10 1/2",304.8,228.6,12.0,9.0,12,9,304.8,228.6,1.3333333333333,1.3333333333333
"US Envelope Paper","Envelope 12 1/2",317.5,241.3,12.5,9.5,12.5,9.5,317.5,241.3,1.3157894736842,1.3157894736842
"US Envelope Paper","Envelope 13 1/2",330.2,254.0,13.0,10.0,13,10,330.2,254,1.3,1.3
"US Envelope Paper","Envelope 14 1/2",368.3,292.1,14.5,11.5,14.5,11.5,368.3,292.1,1.2608695652174,1.2608695652174
"US Envelope Paper","Envelope 15",381.0,254.0,15.0,10.0,15,10,381,254,1.5,1.5
"US Envelope Paper","Envelope 15 1/2",393.7,304.8,15.5,12.0,15.5,12,393.7,304.8,1.2916666666667,1.2916666666667
"US Envelope Paper","Envelope 3",254.0,177.8,10.0,7.0,10,7,254,177.8,1.4285714285714,1.4285714285714
"US Envelope Paper","Envelope 6",1266.7,190.5,10.5,7.5,49.870078740157,7.5,266.7,190.5,6.649343832021,1.4
"US Envelope Paper","Envelope 8",285.8,209.6,11.25,8.25,11.251968503937,8.251968503937,285.75,209.55,1.3635496183206,1.3636363636364
"US Envelope Paper","Envelope 9 3/4",285.8,222.3,11.25,8.75,11.251968503937,8.751968503937,285.75,222.25,1.2856500224921,1.2857142857143
"US Envelope Paper","Envelope A Long",225.4,98.4,8.875,3.875,8.8740157480315,3.8740157480315,225.425,98.425,2.2906504065041,2.2903225806452
"US Envelope Paper","Envelope A1",92.1,130.2,3.625,5.125,3.6259842519685,5.1259842519685,92.075,130.175,0.7073732718894,0.70731707317073
"US Envelope Paper","Envelope A10",241.3,152.4,9.5,6.0,9.5,6,241.3,152.4,1.5833333333333,1.5833333333333
"US Envelope Paper","Envelope A2",146.1,111.1,5.75,4.375,5.751968503937,4.3740157480315,146.05,111.125,1.3150315031503,1.3142857142857
"US Envelope Paper","Envelope A2 Lady Grey",146.1,111.1,5.75,4.375,5.751968503937,4.3740157480315,146.05,111.125,1.3150315031503,1.3142857142857
"US Envelope Paper","Envelope A4",158.7,108.0,6.25,4.25,6.248031496063,4.251968503937,158.75,107.95,1.4694444444444,1.4705882352941
"US Envelope Paper","Envelope A6",165.1,120.7,6.5,4.75,6.5,4.751968503937,165.1,120.65,1.3678541839271,1.3684210526316
"US Envelope Paper","Envelope A7",184.2,133.4,7.25,5.25,7.251968503937,5.251968503937,184.15,133.35,1.3808095952024,1.3809523809524
"US Envelope Paper","Envelope A8",206.4,139.7,8.125,5.5,8.1259842519685,5.5,206.375,139.7,1.4774516821761,1.4772727272727
"US Envelope Paper","Envelope A9",222.3,146.1,8.75,5.75,8.751968503937,5.751968503937,222.25,146.05,1.5215605749487,1.5217391304348
"US Envelope Paper","Envelope Besselheim",184.2,133.4,7.25,5.25,7.251968503937,5.251968503937,184.15,133.35,1.3808095952024,1.3809523809524
"US Envelope Paper","Envelope Carrs",206.4,139.7,8.125,5.5,8.1259842519685,5.5,206.375,139.7,1.4774516821761,1.4772727272727
"US Envelope Paper","Envelope Diplomat",222.3,146.1,8.75,5.75,8.751968503937,5.751968503937,222.25,146.05,1.5215605749487,1.5217391304348
"US Envelope Paper","Envelope Thompsons Standard",165.1,120.7,6.5,4.75,6.5,4.751968503937,165.1,120.65,1.3678541839271,1.3684210526316
"US Envelope Paper","Envelope Willow",241.3,152.4,9.5,6.0,9.5,6,241.3,152.4,1.5833333333333,1.5833333333333
"US Envelope Paper",Monarch,190.5,98.4,7.5,3.875,7.5,3.8740157480315,190.5,98.425,1.9359756097561,1.9354838709677
"US Paper",A,216,279,8.5,11.0,8.503937007874,10.984251968504,215.9,279.4,0.7741935483871,0.77272727272727
"US Paper","ANSI A",216,279,8.5,11,8.503937007874,10.984251968504,215.9,279.4,0.7741935483871,0.77272727272727
"US Paper","ANSI B",279,432,11,17,10.984251968504,17.007874015748,279.4,431.8,0.64583333333333,0.64705882352941
"US Paper","ANSI C",432,559,17,22,17.007874015748,22.007874015748,431.8,558.8,0.77280858676208,0.77272727272727
"US Paper","ANSI D",559,864,22,34,22.007874015748,34.015748031496,558.8,863.6,0.64699074074074,0.64705882352941
"US Paper","ANSI E",864,1118,34,44,34.015748031496,44.015748031496,863.6,1117.6,0.77280858676208,0.77272727272727
"US Paper","Arch A",229,305,9.0,12.0,9.0157480314961,12.007874015748,228.6,304.8,0.75081967213115,0.75
"US Paper","Arch B",305,457,12.0,18.0,12.007874015748,17.992125984252,304.8,457.2,0.66739606126915,0.66666666666667
"US Paper","Arch C",457,610,18.0,24.0,17.992125984252,24.015748031496,457.2,609.6,0.74918032786885,0.75
"US Paper","Arch D",610,914,24.0,36.0,24.015748031496,35.984251968504,609.6,914.4,0.66739606126915,0.66666666666667
"US Paper","Arch E",914,1219,36.0,48.0,35.984251968504,47.992125984252,914.4,1219.2,0.74979491386382,0.75
"US Paper","Arch E1",762,1067,30.0,42.0,30,42.007874015748,762,1066.8,0.71415182755389,0.71428571428571
"US Paper","Arch E2",660,965,26,38,25.984251968504,37.992125984252,660.4,965.2,0.6839378238342,0.68421052631579
"US Paper","Arch E3",686,991,27,39,27.007874015748,39.015748031496,685.8,990.6,0.69223007063572,0.69230769230769
"US Paper",B,279,432,11.0,17.0,10.984251968504,17.007874015748,279.4,431.8,0.64583333333333,0.64705882352941
"US Paper",C,432,559,17.0,22.0,17.007874015748,22.007874015748,431.8,558.8,0.77280858676208,0.77272727272727
"US Paper",D,559,864,22.0,34.0,22.007874015748,34.015748031496,558.8,863.6,0.64699074074074,0.64705882352941
"US Paper",E,864,1118,34.0,44.0,34.015748031496,44.015748031496,863.6,1117.6,0.77280858676208,0.77272727272727
"US Paper","Government Legal",216,330,8.5,13,8.503937007874,12.992125984252,215.9,330.2,0.65454545454545,0.65384615384615
"US Paper","Government Letter",203,267,8,10.5,7.992125984252,10.511811023622,203.2,266.7,0.76029962546816,0.76190476190476
"US Paper","Half Letter",140,216,5.5,8.5,5.511811023622,8.503937007874,139.7,215.9,0.64814814814815,0.64705882352941
"US Paper","Half Letter",140,216,5.5,8.5,5.511811023622,8.503937007874,139.7,215.9,0.64814814814815,0.64705882352941
"US Paper","Junior Legal",127,203,5,8,5,7.992125984252,127,203.2,0.6256157635468,0.625
"US Paper","Junior Legal",127,203,5.0,8.0,5,7.992125984252,127,203.2,0.6256157635468,0.625
"US Paper",Ledger,279,432,11.0,17.0,10.984251968504,17.007874015748,279.4,431.8,0.64583333333333,0.64705882352941
"US Paper",Legal,216,356,8.5,14.0,8.503937007874,14.015748031496,215.9,355.6,0.60674157303371,0.60714285714286
"US Paper",Letter,216,279,8.5,11.0,8.503937007874,10.984251968504,215.9,279.4,0.7741935483871,0.77272727272727
"US Paper",Tabloid,279,432,11.0,17.0,10.984251968504,17.007874015748,279.4,431.8,0.64583333333333,0.64705882352941
"US Poster Paper","US Movie Poster 30 x 40 Drive In",762,1016,30,40,30,40,762,1016,0.75,0.75
"US Poster Paper","US Movie Poster 40 x 60 Drive In",1016,1524,40,60,40,60,1016,1524,0.66666666666667,0.66666666666667
"US Poster Paper","US Movie Poster Door Panels",508,1524,20,60,20,60,508,1524,0.33333333333333,0.33333333333333
"US Poster Paper","US Movie Poster Half Sheet",558.8,711.2,22,28,22,28,558.8,711.2,0.78571428571429,0.78571428571429
"US Poster Paper","US Movie Poster Insert",355.6,914.4,14,36,14,36,355.6,914.4,0.38888888888889,0.38888888888889
"US Poster Paper","US Movie Poster Lobby Card",279.4,355.6,11,14,11,14,279.4,355.6,0.78571428571429,0.78571428571429
"US Poster Paper","US Movie Poster One Sheet",685.8,1016,27,40,27,40,685.8,1016,0.675,0.675
"US Poster Paper","US Movie Poster Six Sheet",2057.4,2057.4,81,81,81,81,2057.4,2057.4,1,1
"US Poster Paper","US Movie Poster Three Sheet",1041.4,2057.4,41,81,41,81,1041.4,2057.4,0.50617283950617,0.50617283950617
"US Poster Paper","US Movie Poster Window Card",355.6,558.8,14,22,14,22,355.6,558.8,0.63636363636364,0.63636363636364
"US Poster Paper","US Poster Large",609.6,914.4,24,36,24,36,609.6,914.4,0.66666666666667,0.66666666666667
"US Poster Paper","US Poster Letter",215.9,279.4,8.5,11,8.5,11,215.9,279.4,0.77272727272727,0.77272727272727
"US Poster Paper","US Poster Medium",457.2,609.6,18,24,18,24,457.2,609.6,0.75,0.75
"US Poster Paper","US Poster Small",279.4,431.8,11,17,11,17,279.4,431.8,0.64705882352941,0.64705882352941
Unknown,B1XL,750,1050,29.5,41.3,29.527559055118,41.338582677165,749.3,1049.02,0.71428571428571,0.71428571428571
Unknown,Berliner,315,470,12.4,18.5,12.40157480315,18.503937007874,314.96,469.9,0.67021276595745,0.67027027027027
Unknown,Bond,558.8,431.8,22.0,17.0,22,17,558.8,431.8,1.2941176470588,1.2941176470588
Unknown,Book,965.2,635.0,38.0,25.0,38,25,965.2,635,1.52,1.52
Unknown,Broadsheet,600,750,23.5,29.5,23.622047244094,29.527559055118,596.9,749.3,0.8,0.79661016949153
Unknown,"Candian P1",560,860,22.0,33.9,22.047244094488,33.858267716535,558.8,861.06,0.65116279069767,0.64896755162242
Unknown,"Candian P2",430,560,16.9,22.0,16.929133858268,22.047244094488,429.26,558.8,0.76785714285714,0.76818181818182
Unknown,"Candian P3",280,430,11.0,16.9,11.023622047244,16.929133858268,279.4,429.26,0.65116279069767,0.6508875739645
Unknown,"Candian P4",215,280,8.5,11.0,8.4645669291339,11.023622047244,215.9,279.4,0.76785714285714,0.77272727272727
Unknown,"Candian P5",140,210,5.5,8.3,5.511811023622,8.2677165354331,139.7,210.82,0.66666666666667,0.66265060240964
Unknown,"Candian P6",105,140,4.1,5.5,4.1338582677165,5.511811023622,104.14,139.7,0.75,0.74545454545455
Unknown,"Court Cards",120.65,88.9,4.75,3.5,4.75,3.5,120.65,88.9,1.3571428571429,1.3571428571429
Unknown,Cover,660.4,508.0,26.0,20.0,26,20,660.4,508,1.3,1.3
Unknown,Index,774.7,647.7,30.5,25.5,30.5,25.5,774.7,647.7,1.1960784313725,1.1960784313725
Unknown,"Japanese AB",210,257,8.27,10.12,8.2677165354331,10.11811023622,210.058,257.048,0.81712062256809,0.81719367588933
Unknown,"Japanese B0",1030,1456,40.6,57.3,40.551181102362,57.322834645669,1031.24,1455.42,0.70741758241758,0.70855148342059
Unknown,"Japanese B1",728,1030,28.7,40.6,28.661417322835,40.551181102362,728.98,1031.24,0.70679611650485,0.70689655172414
Unknown,"Japanese B10",32,45,1.3,1.8,1.259842519685,1.7716535433071,33.02,45.72,0.71111111111111,0.72222222222222
Unknown,"Japanese B2",515,728,20.3,28.7,20.275590551181,28.661417322835,515.62,728.98,0.70741758241758,0.70731707317073
Unknown,"Japanese B3",364,515,14.3,20.3,14.330708661417,20.275590551181,363.22,515.62,0.70679611650485,0.70443349753695
Unknown,"Japanese B4",257,364,10.1,14.3,10.11811023622,14.330708661417,256.54,363.22,0.70604395604396,0.70629370629371
Unknown,"Japanese B40",103,182,4.06,7.17,4.0551181102362,7.1653543307087,103.124,182.118,0.56593406593407,0.56624825662483
Unknown,"Japanese B5",182,257,7.2,10.1,7.1653543307087,10.11811023622,182.88,256.54,0.70817120622568,0.71287128712871
Unknown,"Japanese B6",128,182,5.0,7.2,5.0393700787402,7.1653543307087,127,182.88,0.7032967032967,0.69444444444444
Unknown,"Japanese B7",91,128,3.6,5.0,3.5826771653543,5.0393700787402,91.44,127,0.7109375,0.72
Unknown,"Japanese B8",64,91,2.5,3.6,2.5196850393701,3.5826771653543,63.5,91.44,0.7032967032967,0.69444444444444
Unknown,"Japanese B9",45,64,1.8,2.5,1.7716535433071,2.5196850393701,45.72,63.5,0.703125,0.72
Unknown,"Japanese Shikisen",84,148,3.31,5.83,3.3070866141732,5.8267716535433,84.074,148.082,0.56756756756757,0.56775300171527
Unknown,"Kiku 1",227,306,8.94,12.05,8.9370078740157,12.047244094488,227.076,306.07,0.74183006535948,0.74190871369295
Unknown,"Kiku 2",151,227,5.94,8.94,5.9448818897638,8.9370078740157,150.876,227.076,0.66519823788546,0.66442953020134
Unknown,Midi,315,470,12.4,18.5,12.40157480315,18.503937007874,314.96,469.9,0.67021276595745,0.67027027027027
Unknown,Newsprint,914.4,609.6,36.0,24.0,36,24,914.4,609.6,1.5,1.5
Unknown,Offset,965.2,635.0,38.0,25.0,38,25,965.2,635,1.52,1.52
Unknown,"Plakatwand Brandboard",1500,2000,59.1,78.7,59.055118110236,78.740157480315,1501.14,1998.98,0.75,0.75095298602287
Unknown,"Plakatwand Centerboard",10000,4780,393.7,188.2,393.70078740157,188.18897637795,9999.98,4780.28,2.092050209205,2.0919234856536
Unknown,"Plakatwand City Star",3560,2520,140.2,99.2,140.15748031496,99.212598425197,3561.08,2519.68,1.4126984126984,1.4133064516129
Unknown,"Plakatwand Dachflache",10000,2000,393.7,78.7,393.70078740157,78.740157480315,9999.98,1998.98,5,5.002541296061
Unknown,"Plakatwand Megaboard",8000,5000,315.0,196.9,314.96062992126,196.85039370079,8001,5001.26,1.6,1.5997968511935
Unknown,"Plakatwand Superpostern",5260,3720,207.1,146.5,207.08661417323,146.45669291339,5260.34,3721.1,1.4139784946237,1.4136518771331
Unknown,"Postcard Maximum",235,120,9.25,4.72,9.251968503937,4.7244094488189,234.95,119.888,1.9583333333333,1.9597457627119
Unknown,"Postcard Minimum",140,90,5.51,3.54,5.511811023622,3.5433070866142,139.954,89.916,1.5555555555556,1.5564971751412
Unknown,RA0,860,1220,33.9,48.0,33.858267716535,48.031496062992,861.06,1219.2,0.70491803278689,0.70625
Unknown,RA1,610,860,24.0,33.9,24.015748031496,33.858267716535,609.6,861.06,0.7093023255814,0.70796460176991
Unknown,RA2,430,610,16.9,24.0,16.929133858268,24.015748031496,429.26,609.6,0.70491803278689,0.70416666666667
Unknown,RA3,305,430,12.0,16.9,12.007874015748,16.929133858268,304.8,429.26,0.7093023255814,0.71005917159763
Unknown,RA4,215,305,8.5,12.0,8.4645669291339,12.007874015748,215.9,304.8,0.70491803278689,0.70833333333333
Unknown,RB0,1025,1449,40.4,57.0,40.354330708661,57.047244094488,1026.16,1447.8,0.70738440303658,0.70877192982456
Unknown,RB1,725,1025,28.5,40.4,28.543307086614,40.354330708661,723.9,1026.16,0.70731707317073,0.70544554455446
Unknown,RB2,513,725,20.2,28.5,20.196850393701,28.543307086614,513.08,723.9,0.70758620689655,0.70877192982456
Unknown,RB3,363,513,14.3,20.2,14.291338582677,20.196850393701,363.22,513.08,0.70760233918129,0.70792079207921
Unknown,RB4,257,363,10.1,14.3,10.11811023622,14.291338582677,256.54,363.22,0.70798898071625,0.70629370629371
Unknown,Reclamebord,3300,2400,129.9,94.5,129.92125984252,94.488188976378,3299.46,2400.3,1.375,1.3746031746032
Unknown,SRA0,900,1280,35.4,50.4,35.433070866142,50.393700787402,899.16,1280.16,0.703125,0.70238095238095
Unknown,SRA1,640,900,25.2,35.4,25.196850393701,35.433070866142,640.08,899.16,0.71111111111111,0.71186440677966
Unknown,SRA1+,660,920,26.0,36.2,25.984251968504,36.220472440945,660.4,919.48,0.71739130434783,0.71823204419889
Unknown,SRA2,450,640,17.7,25.2,17.716535433071,25.196850393701,449.58,640.08,0.703125,0.70238095238095
Unknown,SRA2+,480,650,18.9,25.6,18.897637795276,25.590551181102,480.06,650.24,0.73846153846154,0.73828125
Unknown,SRA3,320,450,12.6,17.7,12.59842519685,17.716535433071,320.04,449.58,0.71111111111111,0.71186440677966
Unknown,SRA3+,320,460,12.6,18.1,12.59842519685,18.110236220472,320.04,459.74,0.69565217391304,0.69613259668508
Unknown,SRA3++,320,464,12.6,18.3,12.59842519685,18.267716535433,320.04,464.82,0.68965517241379,0.68852459016393
Unknown,SRA4,225,320,8.9,12.6,8.8582677165354,12.59842519685,226.06,320.04,0.703125,0.70634920634921
Unknown,SRB0,1072,1516,42.2,59.9,42.204724409449,59.685039370079,1071.88,1521.46,0.70712401055409,0.70450751252087
Unknown,SRB1,758,1072,29.8,42.2,29.842519685039,42.204724409449,756.92,1071.88,0.70708955223881,0.70616113744076
Unknown,SRB2,536,758,21.1,29.8,21.102362204724,29.842519685039,535.94,756.92,0.70712401055409,0.70805369127517
Unknown,SRB3,379,536,14.9,21.1,14.92125984252,21.102362204724,378.46,535.94,0.70708955223881,0.70616113744076
Unknown,SRB4,268,379,10.6,14.9,10.551181102362,14.92125984252,269.24,378.46,0.70712401055409,0.71140939597315
Unknown,"Shirokuban 1",264,379,10.39,14.92,10.393700787402,14.92125984252,263.906,378.968,0.69656992084433,0.69638069705094
Unknown,"Shirokuban 2",189,262,7.44,10.31,7.4409448818898,10.314960629921,188.976,261.874,0.72137404580153,0.72162948593598
Unknown,"Shirokuban 3",127,188,5.00,7.40,5,7.4015748031496,127,187.96,0.67553191489362,0.67567567567568
Unknown,Text,965.2,635.0,38.0,25.0,38,25,965.2,635,1.52,1.52
Unknown,Tissue,914.4,609.6,36.0,24.0,36,24,914.4,609.6,1.5,1.5
Unknown,"US PostCard Maximum",152.4,107.9,6.0,4.25,6,4.248031496063,152.4,107.95,1.4124189063948,1.4117647058824
Unknown,"US PostCard Minimum",127.0,88.9,5.0,3.5,5,3.5,127,88.9,1.4285714285714,1.4285714285714
EOD;

	$data = explode( "\n", $data );

	$ary = [];
	foreach( $data as $k=>$v ){
		$ary[] = explode( ",", $v );
		}

	return $ary;
}

?>
