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

