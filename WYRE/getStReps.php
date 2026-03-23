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

	$pr = new class_pr();

	$cwd = getcwd();

	scrStart();

	$info = <<<EOD

            _    _____ _   _____                
           | |  / ____| | |  __ \               
  __ _  ___| |_| (___ | |_| |__) |___ _ __  ___ 
 / _` |/ _ \ __|\___ \| __|  _  // _ \ '_ \/ __|
| (_| |  __/ |_ ____) | |_| | \ \  __/ |_) \__ \
 \__, |\___|\__|_____/ \__|_|  \_\___| .__/|___/
  __/ |                              | |        
 |___/                               |_|  

getStReps or Get the State's Representatives will get both
the House and Senate members who work in each state.

EOD;

	scrMsg( 0, 0, $info );
	$a = scrAsk( 0, 20, "Continue", array("Default:Yes", "Exit:q") );
	$info = <<<EOD

            _    _____ _   _____                
           | |  / ____| | |  __ \               
  __ _  ___| |_| (___ | |_| |__) |___ _ __  ___ 
 / _` |/ _ \ __|\___ \| __|  _  // _ \ '_ \/ __|
| (_| |  __/ |_ ____) | |_| | \ \  __/ |_) \__ \
 \__, |\___|\__|_____/ \__|_|  \_\___| .__/|___/
  __/ |                              | |        
 |___/                               |_|  

Please wait while I get the list...Be Right Back!

EOD;

	scrMsg( 0, 0, $info );
#
#	First - see if the State directory is there.
#
	$states = "States";
	$dir = "$cwd/$states";
#
#	Is the States directory already there? If not - create it.
#
	scrMsg( 15, 0, "Checking to make sure the STATES directory is there" );

	if( !file_exists($dir) ){ mkdir( $dir, 0777); }
#
#	Now get the webpage at : https://open.pluralpolicy.com/data/legislator-csv/
#		OR	Get the file and read it. Note that we use the word "reps"
#		not only for the variable (ie: $reps) but the filename used
#		(ie: Reps.html") also.
#
	scrMsg( 16, 0, "Getting the state information" );

	$reps = "states.html";
	if( !file_exists("$dir/$reps") ){
		$webpage = "https://open.pluralpolicy.com/data/legislator-csv/";
		$page = file_get_contents( $webpage );
		file_put_contents( "$dir/$reps", $page );
		}
		else { $page = file_get_contents( "$dir/$reps" ); }
#
#	Remove any Control-M's and the standard "\n".
#
	scrMsg( 17, 0, "Removing any Control-M's or Newlines" );

	$page = str_replace( "", "", $page );
	$page = str_replace( "\n", "", $page );
#
#	First we break everything apart with an HTML command on each line.
#
	scrMsg( 18, 0, "Breaking up the lines so I can find things" );

	$page = explode( "<", $page );
#
#	Now we need to put the "<" back. Mainly just because.
#
	scrMsg( 19, 0, "Putting the '<'s back on to the front of each line" );

	foreach( $page as $k=>$v ){ $page[$k] = "<$v"; }
#
#	Ok. So now we have to look for the "<body" so we get past the
#	header area.
#
	scrCls();
	list( $found, $page ) = webFind( "<body", $page );
#
#	Now we look for the abbreviations
#
	list( $found, $page ) = webFind( "<div", $page );
#
#	https://data.openstates.org/people/current/[ABBR].csv
#
	scrMsg( 20, 0, "Getting all of the abbreviations for the states" );
	scrMsg( 21, 0, "and making individual directories for each state" );

	$abbv = [];
	while( true ){
		list( $found, $page ) = webFind( "<option", $page, "</select" );
$pr->pr( $found, "Found = " );
		if( preg_match(";</select;i", $found) ){ break; }

		$a = explode( '"', $found );
		if( strlen($a[1]) < 1 ){ continue; }
		$abbv[] = $a[1];
		if( !file_exists("$dir/$a[1]") ){ mkdir( "$dir/$a[1]", 0777 ); }
		}
#
#	Save the abbreviations
#
	$a = implode( ":", $abbv );
	file_put_contents( "$cwd/abbv.dat", $a );
#
#	Now get each states' csv file
#
	scrMsg( 22, 0, "Now getting all of the CSV files for each state" );

	foreach( $abbv as $k=>$v ){
$pr->pr( $k, "K = " );
$pr->pr( $v, "V = " );
		$csv = file_get_contents(
			"https://data.openstates.org/people/current/$v.csv"
			);

		file_put_contents( "$dir/$v/$v.csv", $csv );
		}

	scrCls();
	$info = <<<EOD

            _    _____ _   _____                
           | |  / ____| | |  __ \               
  __ _  ___| |_| (___ | |_| |__) |___ _ __  ___ 
 / _` |/ _ \ __|\___ \| __|  _  // _ \ '_ \/ __|
| (_| |  __/ |_ ____) | |_| | \ \  __/ |_) \__ \
 \__, |\___|\__|_____/ \__|_|  \_\___| .__/|___/
  __/ |                              | |        
 |___/                               |_|  

Now I am going to get all of the committees, executive
information, legislature that is going through each
state, municipalities, and who has retired from being
a representative. All of these files are in what is
called the YAML format. Which really is just a text
file with commands embedded in the text. (So like id:,
jurisdiction:, name:, url:, and so forth.) Why do you need
these files? Because then you know who to write to for
various areas. Like one of the first committees is the
"Armed Services Committee" but you may know it as the
"Joint Armed Services". Both names are for the same
committee. This is why we are downloading them to your
computer. Because if they are taken down - you will at
least still have them.

EOD;

	scrMsg( 0, 0, $info );
#
#	For later:	https://github.com/openstates/people/data/ABBRV
#
	$site = "https://github.com/openstates/people/data";
	$site = "https://github.com/openstates/people/tree/main/data";
#
#	Go through the abbreviations and get all of the information
#
	$backup_types = array( "committees", "executive", "legislature", "municipalities", "retired" );

	foreach( $abbv as $k=>$v ){
		$site_2 = "$site/$v";
		$html = file_get_contents( $site_2 );
		$html = str_replace( "", "", $html );
		$html = str_replace( "\n", "", $html );
		$html = explode( "<", $html );
		foreach( $html as $k1=>$v1 ){ $html[$k] = "<$v1"; }

		list( $found, $html ) = webFind( '"path":', $html );

echo "\n\n";
		$types = [];
		$a = explode( '"path":', $found );
		foreach( $a as $k1=>$v1 ){
			if( preg_match(";\"data/\w+/\w+;i", $v1) ){
				$b = explode( ",", $v1 );
$pr->pr( $b, "B = " );
				$b = explode( "/", $b[0] );
				if( preg_match("/yml/i", $b[2]) ){ continue; }
$pr->pr( $b, "B = " );
				$types[] = substr( $b[2], 0, -1 );
$pr->pr( $types, "types = " );
#  LINE(223) @ <class_pr>[pr] - [STRING][2](68) = "data/al/committees","contentType":"directory"},{"name":"executive",
				}
			}

		$local = "$dir/$v1";
		if( !file_exists($local) ){ mkdir( $local, 0777 ); }
		$type = implode( ":", $types );
		$filename = "$local/types.dat";
		file_put_contents( $filename, $type );
#
#	Make sure the directory is there.
#	Now get the YAML (YML) files and save them
#
		foreach( $types as $k1=>$v1 ){
			$site_3 = "$site_2/$v1";
			$filename = "$local/$v1$v1.dat";
			$a = file_get_contents( $site_3 );
			file_put_contents( $filename, $a );
echo "\n\n";
$pr->pr( $a, "A = " );
			}
#
echo "\n\n";
$pr->pr( $a, "A = " );
echo "\n\n";
$pr->pr( $found, "Found = " ); exit;
echo "\n\n";
		}
#	
#

	scrCls();
	$info = <<<EOD

            _    _____ _   _____                
           | |  / ____| | |  __ \               
  __ _  ___| |_| (___ | |_| |__) |___ _ __  ___ 
 / _` |/ _ \ __|\___ \| __|  _  // _ \ '_ \/ __|
| (_| |  __/ |_ ____) | |_| | \ \  __/ |_) \__ \
 \__, |\___|\__|_____/ \__|_|  \_\___| .__/|___/
  __/ |                              | |        
 |___/                               |_|  

Done! Now you have a directory called STATES in which
you have each state's House and Senators listed in a CSV
file which you can look at with a spreadsheet program
like Excel.

Please wait while I get the list...Be Right Back!
EOD;

	scrMsg( 0, 0, $info );
	scrEnd();

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

	return $ans;
}

function scrEnd(){ scrCls(); scrMove(0,20); scrSGR(0); echo "\n"; }

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
?>
