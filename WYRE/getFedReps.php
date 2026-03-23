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
#
#	Get this : https://github.com/cisagov/dotgov-data/blob/main/current-full.csv
#
#	Look into this: https://catalog.data.gov/dataset/?
#		tags=building&organization_type=Federal+Government&res_format=EXCEL&page=1
#
#	https://files.usaspending.gov/reference_data/agency_codes.csv
#	https://datacatalogfiles.worldbank.org/ddh-published/0037962/1/DR0046058/figure-s3.1.1_agencies.csv
#	https://get.gov/about/data/
#	https://catalog.data.gov/dataset/
#	https://s3-us-gov-west-1.amazonaws.com/cg-0817d6e3-93c4-4de8-8b32-da6919464e61/open_data_us.csv
#
#	Sanctions list:	https://sanctionslist.ofac.treas.gov/Home/SdnList
#	Trade:	https://www.trade.gov/consolidated-screening-list
#	List of .gov domains:	https://www.kaggle.com/datasets/yamqwe/the-complete-list-of-gov-domains
#	OIG:	https://oig.hhs.gov/exclusions/exclusions_list.asp
#
	$cwd = getcwd();

	scrStart();

	$info = <<<EOD

            _   ______       _ _____                
           | | |  ____|     | |  __ \               
  __ _  ___| |_| |__ ___  __| | |__) |___ _ __  ___ 
 / _` |/ _ \ __|  __/ _ \/ _` |  _  // _ \ '_ \/ __|
| (_| |  __/ |_| | |  __/ (_| | | \ \  __/ |_) \__ \
 \__, |\___|\__|_|  \___|\__,_|_|  \_\___| .__/|___/
  __/ |                                  | |        
 |___/                                   |_|        

Welcome. This program is called getFedReps.  What this
script does is to get all of the current representatives
listed as your House and Senate representatives. Nothing
more. Nothing less.

Please wait while I get the list...Be Right Back!
EOD;

	scrMsg( 0, 0, $info );
	$a = scrAsk( 0, 20, "Continue", array("Default:Yes", "Exit:q") );
#
#	First - see if the Feds directory is there.
#
	$feds = "Feds";
	$dir = "$cwd/$feds";
#
#	Is the Feds directory already there? If not - create it.
#
	if( !file_exists($dir) ){ mkdir( $dir, 0777); }
#
#	Now get the webpage at : https://www.house.gov/representatives
#		OR	Get the file and read it. Note that we use the word "reps"
#		not only for the variable (ie: $reps) but the filename used
#		(ie: Reps.html") also.
#
	$reps = "Reps.html";
	if( !file_exists("$dir/$reps") ){
		$webpage = "https://www.house.gov/representatives";
		$page = file_get_contents( $webpage );
		file_put_contents( "$dir/$reps", $page );
		}
		else { $page = file_get_contents( "$dir/$reps" ); }
#
#	Remove any Control-M's and the standard "\n".
#
	$page = str_replace( "", "", $page );
	$page = str_replace( "\n", "", $page );
#
#	First we break everything apart with an HTML command on each line.
#
	$page = explode( "<", $page );
#
#	Now we need to put the "<" back. Mainly just because.
#
	foreach( $page as $k=>$v ){ $page[$k] = "<$v"; }
#
#	Now go through and trim all of the lines
#
#	foreach( $page as $k=>$v ){ $page[$k] = trim( $v ); }
#
#	Ok. So now we have to look for the "<body" so we get past the
#	header area.
#
	$pageNum = count( $page );
	scrCls();
	list( $found, $page ) = webFind( "<body", $page );

	$csv = [];
	$csv_cnt = -1;
	while( true ){
#
#	If there isn't a new "<table" coming up - we need to get out of here.
#
		if( preg_match(";</tbody;i", $page[0]) ){
			$found = array_shift( $page );
			if( preg_match(";</table;i", $page[0]) ){
				$found = array_shift( $page );
				if( strlen(trim($found)) < 1 ){
					$found = array_shift( $page );
					}

				if( preg_match(";</div;i", $page[0]) ){ break; }
				}
			}

		list( $found, $page ) = webFind( "<table", $page );
		list( $found, $page ) = webFind( "<caption", $page );

		$a = explode( ">", $found );
		$found = trim( $a[1] );

		$state = trim( ucwords($found) );

		$csv[$state] = [];
#
#	Get the headers of this set of tables
#
		$headers = [];
		while( true ){
			list( $found, $page ) = webFind( "<th", $page, "</thead" );
			if( preg_match(";</thead;i", $found) ){ break; }
			$line = explode( ">", $found );
			$line = explode( "<", $line[1] );
			$headers[] = $line[0];
			}
#
#	This is for the hyperlink most representatives have.
#
		$headers[] = "Website Link";
#
#	Ok, so get rid of blank entries and otherwise move the headers
#	over to the CSV variable. This is the first line.
#
		$csv_cnt++;
		$csv[$state][$csv_cnt] = [];
		foreach( $headers as $k=>$v ){
			if( !is_null($v) && strlen(trim($v)) > 0 ){
				$csv[$state][$csv_cnt][] = $v;
				}
			}

		unset( $headers );
#
#	Now we need to build our tables
#
		$field = [];
		$field_cnt = 0;
		while( true ){
#
#	If there isn't a new "<table" coming up - we need to get out of here.
#
			if( strlen(trim($page[0])) < 1 ){
				$found = array_shift( $page );
				$found = array_shift( $page );
				if( preg_match(";</div;i", $found) ){
					break 2;
					}
				}

			list( $found, $page ) = webFind( "<td", $page, "</tr" );
			if( preg_match(";</tr;i", $found) ){
				$csv_cnt++;
				$field[] = $href;
				foreach( $field as $k=>$v ){
					$csv[$state][$csv_cnt][] = $v;
					}

				$field = [];
				$field_cnt = 0;
				if( preg_match(";</tbody>;i", $page[0]) ){
					break;
					}
				}
#
#	If the next line is the "<a..." line - we need to go to it.
#
			if( preg_match(";<a;i", $page[0]) ){ 
				$found = array_shift( $page );
				}

			$a = explode( "<", $found );
			array_shift( $a );
			foreach( $a as $k=>$v ){
				$b = explode( ">", $v );
#
#	Is this an "<a" line?
#
				if( preg_match(";a\s+href;i", $b[0]) ){
					$info = explode( '"', $b[0] );
					$href = $info[1];
					}

				$field[$field_cnt++] = trim( $b[1] );
				}
			}
		}

	foreach( $csv as $k=>$v ){
		if( ($fp = fopen("$dir/$k.csv", "w")) === false ){
			echo "Could not open $k.csv - Aborting\n";
			exit;
			}

		foreach( $v as $k1=>$v1 ){
			foreach( $v1 as $k2=>$v2 ){
				if( strlen(trim($v2)) < 1 || is_null($v2) ){
					unset( $csv[$k][$k1][$k2] );
					}
				}

			fputcsv( $fp, $csv[$k][$k1] );
			}

		fclose( $fp );
		}

	scrEnd();
	$info = <<<EOD

            _   ______       _ _____                
           | | |  ____|     | |  __ \               
  __ _  ___| |_| |__ ___  __| | |__) |___ _ __  ___ 
 / _` |/ _ \ __|  __/ _ \/ _` |  _  // _ \ '_ \/ __|
| (_| |  __/ |_| | |  __/ (_| | | \ \  __/ |_) \__ \
 \__, |\___|\__|_|  \___|\__,_|_|  \_\___| .__/|___/
  __/ |                                  | |        
 |___/                                   |_|        

Thank you. You now have a diretory called Feds. In
that directory you now have one CSV file for every
state. You can look at these with a spreadsheet program
like Excel. These files are referenced in some of the other
scripts. So please do not change these in anyway as that
would then affect the other scripts.

Thanks again for using this script!
Finished.

EOD;

	scrMsg( 0, 0, $info );
	exit;

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
