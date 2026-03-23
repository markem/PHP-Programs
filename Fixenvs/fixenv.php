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
#--------------------------------------------------------------------------------
#
#	To permanently remove a variable from the USER environment area use:
#
#		REG delete HKCU\Environment /F /V <name>
#
#--------------------------------------------------------------------------------
#
#	To permanently remove a variable from the SYSTEM environment area use:
#
#	REG delete "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V <name>
#
#--------------------------------------------------------------------------------
#
#	To set the variable in a batch file use:
#
#		set <var>=<text>
#
#--------------------------------------------------------------------------------
#
#	To set a variable in the USER environment area PERMANENTLY use:
#
#		setx <var> <text>
#
#--------------------------------------------------------------------------------
#
#	To CLEAR a variable in the USER environment area PERMANENTLY use:
#
#		setx <var> ""
#
#--------------------------------------------------------------------------------
#
#	From PowerShell, on Windows, you can use the .NET
#		[System.Environment]::SetEnvironmentVariable() method:
#
#--------------------------------------------------------------------------------
#
#	To remove a user environment variable named FOO:
#
#	[Environment]::SetEnvironmentVariable('FOO', [NullString]::Value, 'User')
#
#	Note that [NullString]::Value is needed to pass a genuine
#	null value to the method, whereas a PowerShell $null value
#	would be passed as the empty string. Up to .NET 8, passing
#	the empty string is treated the same as null and results
#	in removal; .NET 9+ (PowerShell 7.5+) now removes the variable
#	only when null is passed, whereas passing the empty string
#	retains / defines the variable without a value (i.e. it
#	makes the empty string the variable's value).
#
#--------------------------------------------------------------------------------
#
#	To remove a system (machine-level) environment variable named
#	FOO - requires elevation (must be run as administrator):
#
#	[Environment]::SetEnvironmentVariable('FOO', [NullString]::Value, 'Machine')
#
#	Note that to run the above you must use in an exec() command:
#
#	powershell.exe -Command "[System.Environment]::SetEnvironmentVariable('FOO', $null, 'Machine')"
#
#--------------------------------------------------------------------------------
#
#	Taken from:
#
#		https://stackoverflow.com/questions/13222724/command-line-to-remove-
#			an-environment-variable-from-the-os-level-configuration
#
#	This is also reload explorer.exe. It is the 'setx dummy ""' followed by deleting
#	the DUMMY variable from the registry that makes explorer.exe reload everything.
#
#	rem remove from current cmd instance
#	  SET FOOBAR=
#	rem remove from the registry if it's a user variable
#	  REG delete HKCU\Environment /F /V FOOBAR
#	rem remove from the registry if it's a system variable
#	  REG delete "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V FOOBAR
#	rem tell Explorer.exe to reload the environment from the registry
#	  SETX DUMMY ""
#	rem remove the dummy
#	  REG delete HKCU\Environment /F /V DUMMY
#

#
#	The following will update the environment variables (both user and system)
#
#	Taken from:
#
#		https://stackoverflow.com/questions/13222724/command-line-to-remove-
#			an-environment-variable-from-the-os-level-configuration
#
#		$powershell = <<<EOD
#	if (-not ("win32.nativemethods" -as [type])) {
#	    add-type -Namespace Win32 -Name NativeMethods -MemberDefinition @"
#	        [DllImport("user32.dll", SetLastError = true, CharSet = CharSet.Auto)]
#	        public static extern IntPtr SendMessageTimeout(
#	            IntPtr hWnd, uint Msg, UIntPtr wParam, string lParam,
#	            uint fuFlags, uint uTimeout, out UIntPtr lpdwResult);
#	        "@
#	}
#	
#	\$HWND_BROADCAST = [intptr]0xffff;
#	\$WM_SETTINGCHANGE = 0x1a;
#	\$result = [uintptr]::zero
#	
#	[win32.nativemethods]::SendMessageTimeout(\$HWND_BROADCAST,
#		\$WM_SETTINGCHANGE,[uintptr]::Zero, "Environment", 2, 5000, [ref]\$result);
#	EOD;

	$pr = new class_pr();
	$version = array( 1, 0, 14 );
	$ver = "v$version[0].$version[1].$version[2]";
#
#	Get the USER version of the PATH file
#
	$a = sysQuery( "PATH" );
	$retval = exec( $a, $output, $user_flag );
	while( !preg_match("/path/i", $output[0]) ){ array_shift( $output ); }
	$loc = stripos( $output[0], "c:" );
	$output[0] = substr( $output[0], $loc, strlen($output[0]) );
	$user = explode( ';', $output[0] );
#
#	For unknown reasons - you have to get rid of the OUTPUT array
#	or else it just adds on to the array.
#
	unset( $output );
#
#	Get the SYSTEM version of the PATH file
#
	$a = usrQuery( "PATH" );
	$retval = exec( $a, $output, $user_flag );
	while( !preg_match("/path/i", $output[0]) ){ array_shift( $output ); }
	$loc = stripos( $output[0], "c:" );
	$output[0] = substr( $output[0], $loc, strlen($output[0]) );
	$system = explode( ';', $output[0] );
#
#	Note : your environment variables are ONLY updated by getting out of this
#	program and then back in again.
#
	$origEnvs = getenv();
#
#	Save the original environemt variables.
#
	$fp = fopen( "./getenv.dat", "w" );
	foreach( $origEnvs as $k=>$v ){
		fprintf( $fp, "%s=%s\n", $k, $v );
		}

	fclose( $fp );

	foreach( $origEnvs as $k=>$v ){
		if( $k == "PATH" ){ unset( $origEnvs[$k] ); }
		}

#	$pr->pr( $origEnvs, "origEnvs = " ); exit;
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ_";
#
#	echo str_repeat( '-', 80 ) . "\n";
#	print_r( $origEnvs ); echo "\n";
#
#	Now remove the map_* stuff. For unknown reasons - VIM just sticks these
#	in the environment variables.
#
	unset( $origEnvs['map_h'] );
	unset( $origEnvs['map_i'] );
	unset( $origEnvs['map_u'] );

	scrCls();
	$info = <<<EOD
  __ _      ______                
 / _(_)    |  ____|               
| |_ ___  _| |__   _ ____   _____ 
|  _| \ \/ /  __| | '_ \ \ / / __|
| | | |>  <| |____| | | \ V /\__ \
|_| |_/_/\_\______|_| |_|\_/ |___/ $ver
		by Mark Manning

\n
EOD;

	$c = 0;
	$flags = [];
	scrMsg( 0, 0, $info );
	sleep( 3 );

if( true ){
#
################################################################################
#	Now write out the current environment set up
################################################################################
#
	$regedit = "(Windows|dotnet|Intel|Oracle|windir|SQL|Paragon)";
	if( ($fp = fopen("./curENVs.bat", "w")) === false ){
		die( "Could not open file curENVS.bat - aborting.\n" );
		}

	$info = <<<EOD

rem
rem	This file contains your original environment variable setup.
rem	You can use this file to restore your original environment
rem	variables. Be careful though. I've tried to make sure everything
rem	is set up properly - but be sure to check it thoroughly.
rem
rem	If the variable name is all UPPERCASE, then it is a SYSTEM variable.
rem	If the variable name has lowercase letters in it then it is a USER
rem		variable.
rem
EOD;

#
#	See if the variable is a USER environment variable
#
	fprintf( $fp, "%s\n", $info );

	scrCls();
	$info = <<<EOD
fixEnvs $ver

First, we have to test all of your environment variables
and find out where all of them are located. These are all
of the commands YOU would normally have to do in order
to figure this out. According to the tech people online -
System variables are all uppercase while the user variables
can be upper or lowercase.

\n
EOD;

	$c = 0;
	$flags = [];
	scrMsg( 0, 0, $info );
	foreach( $origEnvs as $k=>$v ){
		$sub = explode( ';', $v );
		foreach( $sub as $k1=>$v1 ){
			if( strlen($v1) < 1 ){ continue; }
			$both = 0;
			$line = $ans_line = 14;
			$flags[$k] = [];
			$flags[$k][$k1] = [];

			scrMove( 1, $line );
			scrCls( 0 );

			scrMsg( 1, $line++, "Testing : $k" );
			scrMsg( 1, $line++, "Looking through the registry for $k...Please wait" );
#
#	First, we see if the word is all UPPERCASE or lowercase or mixed.
#
			$a = preg_replace( "/\W/", "", $k );
			$upper_flag = ctype_upper( $a );
			$flags[$k][$k1]['upper_flag'] = $upper_flag;
#
#	Ok - so now set the case flag. If all UPPERCASE - it is supposed to be a SYSTEM
#	variable. Otherwise it is a USER variable. This is what it SHOULD BE. Not what
#	it is.
#
			if( $upper_flag ){ $case_flag = "SYSTEM VARIABLE"; }
				else { $case_flag = "USER VARIABLE"; }

			$flags[$k][$k1]['case_flag'] = $case_flag;
#
#	Create the first command. This checks if it is a USER variable.
#
if( true ){
			$cmd = "REG query HKCU\Environment /V $k";
			scrMsg( 1, $line++, "$cmd\n" );
			$retval = exec( $cmd, $output, $user_flag );
			$user_flag = ($user_flag ? false : true );
			$flags[$k][$k1]['user_flag'] = $user_flag;
#
#	Try just finding the variable
#
}
else {
			echo "\n";
			$hks = array( "HKCR", "HKCU", "HKLM", "HKU", "HKCC" );
			foreach( $hks as $k1=>$v1 ){
				echo "Checking $v1 for $k...Please wait\n";
				$output = null;
				$user_flag = null;
				$retvavl = null;

				$cmd = "REG query $v1 /f $k /s 2> error.dat";
				scrMsg( 1, $line+2, "$cmd\n" );
				$retval = exec( $cmd, $output, $user_flag );
				echo str_repeat( "-", 80 ) . "\n";
				echo "Cmd = \n"; print_r( $cmd );
				echo "retval = \n"; print_r( $retval );
				echo "output = \n"; print_r( $output );
				echo "user_flag = \n"; print_r( $user_flag );
#				$pr->pr( $cmd, "cmd = " );
#				$pr->pr( $retval, "retval = " );
#				$pr->pr( $output, "output = " );
				$pr->pr( $user_flag, "user_flag = " );
				}
}
	#
	#	Now that we have the return from the ExEC command, we can show what we have
	#	learned. Remember - we also show what the case_flag is set to.
	#
			if( $user_flag ){
				$type_flag = "USER variable";
				scrMsg( 40, $ans_line, "YES - It is a USER variable. It should be a $case_flag" );
				}
				else {
					$both++;
					scrMsg( 40, $ans_line, "NO - It is NOT a USER variable. It should be a $case_flag" );
					scrMove( 1, 25 );
					}
	#
	#	Ok, on to the next step - test this against the SYSTEM variable area.
	#
			$line = $ans_line = 19;
			scrMsg( 1, $line++, "Testing if this is a SYSTEM variable" );
			$cmd = "REG query " .
				'"HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /V ' .
				$k . " 2>> error.dat";
			scrMsg( 1, $line++, "$cmd\n" );
	#
	#	See if the variable is a SYSTEM environment variable
	#
			$retval = exec( $cmd, $output, $system_flag );
			$system_flag = ($system_flag ? false : true );
			$flags[$k][$k1]['system_flag'] = $system_flag;
	#
	#	Now display what the EXEC command told us. Again, show the case_flag and what it thinks
	#	the variable should be.
	#
			if( $system_flag ){
				$type_flag = "SYSTEM variable";
				scrMsg( 40, $ans_line, "YES - It is a SYSTEM variable. It should be a $case_flag" );
				}
				else {
					$both++;
					scrMsg( 40, $ans_line, "NO - It is NOT a SYSTEM variable. It should be a $case_flag" );
					scrMove( 1, 25 );
					}
	#
	#	Ok, so we have been adding up which type of variable this is. If BOTH the USER
	#	and SYSTEM tests are negative, then we make the variable what our case_flag says
	#	it should be. Remember - ALL UPPERCASE letters in a name means it should be a
	#	SYSTEM variable and otherwise the variable should be a USER variable.
	#
			if( $both > 1 ){
				$type_flag = $case_flag;
				scrMsg( 0, $line+2, "Since this is not found I will make this a $case_flag" );
				}

			$info ="";
			$flags[$k][$k1]['type_flag'] = $type_flag;
			if( !$user_flag && !$system_flag ){
				$info = <<<EOD
	rem
	rem	ERROR : Did not find $k in either of the locations. So I am
	rem		setting $k to be of type $type_flag.
	rem\n
	EOD;
				}

			$info .= <<<EOD
	rem
	rem	$k - $type_flag
	rem
	EOD;

			if( $user_flag ){ $info .= "\nsetx $k=$v1\n"; }
				else if( $system_flag ){
					$info .= "\nREG add " .
						'"HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment"' .
						" /F /V $k /D $v1\n";
					}
				else if( preg_match("/user/i", $type_flag) ){
					$info .= "\nsetx $k=$v1\n";
					}
				else {
					$info .= "\nREG add " .
						'"HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment"' .
						" /F /V $k /D $v1\n";
					}

			fprintf( $fp, "%s\n", $info );
			sleep( 1 );
			}
		}

	fclose( $fp );
exit;
#
################################################################################
#	End of write original stuff. We will correct this later on.
################################################################################
#
}
	$newEnvs = [];
	foreach( $origEnvs as $k=>$v ){
		$newEnvs[$k] = [];
		$b = explode( ";", $v );
		foreach( $b as $k1=>$v1 ){
			if( strlen(trim($v1)) > 0 ){ $newEnvs[$k][] = $v1; }
			}
		}

#	echo str_repeat( '-', 80 ) . "\n";
#	$pr->pr( $newEnvs, "newEnvs = " ); echo "\n";

	if( ($fp = fopen("./newENVs.bat", "w")) === false ){
		die( "Could not open file newENVS.bat- aborting.\n" );
		}
#
#	First check each path to make sure that that path is still valid
#
	$badPath = [];
	foreach( $newEnvs as $k=>$v ){
		foreach( $v as $k1=>$v1 ){
#
#	Remove ending \'s or /'s
#
#			if( preg_match(";\\|/$;", $v1) ){
#				$v1 = substr( $v1, 0, -1 );
#				}
#
#	And get rid of it
#
			if( !file_exists($v1) && preg_match("/.:/", $v1) ){
				if( !isset($badPath[$k]) ){ $badPath[$k] = []; }

				$badPath[$k][] = $v1;
				unset( $newEnvs[$k][$k1] );
				}
			}
		}

if( true ){
	scrCls();
	$info = <<<EOD
fixEnvs $ver

The following are all of the bad paths found in your
environment variables. You can look them over and mark
each of the entries with a yes(Y) or a no(N) and I'll
remove the ones you want to get rid of.

Bad Pathways found in your environment variables....
\n
EOD;

	scrMsg( 0, 0, $info );
	$a = array( "Y"=>"Default:Yes", "N"=>"No", "q"=>"Q)uit" );
	foreach( $badPath as $k=>$v ){
		foreach( $v as $k1=>$v1 ){
			scrMsg( 1, 14, "Environment variable : $k" );
			scrMsg( 1, 15, "$k1 = $v1" );
			$ans = scrAsk( 1, 17, "Can I delete this environment variable", $a );
			if( preg_match("/n/i", $ans) ){
				scrMsg( 1, 19, "Ok. NOT deleteing this variable" );
				$newEnvs[$k][$k1] = $badPath[$k][$k1];
				unset( $badPath[$k][$k1] );
				sleep( 3 );
				}
				else if( preg_match("/q/i", $ans) ){
					scrCls( 1, 1 );
					echo "\nExiting....\n";
					exit;
					}
				else if( preg_match("/y/i", $ans) || (strlen(trim($ans)) < 1) ){
					scrMsg( 1, 19, "Ok, ADDing the enviroment variable" );
					scrMsg( 1, 20, "to the list to be deleted." );
					sleep( 2 );
					}

			scrMove( 1, 14 );
			scrCls( 0 );
			}
		}

	fclose( $fp );
#
#	Ok - so now we have our list. Build the commands to get rid of these
#	paths
#--------------------------------------------------------------------------------
#
#	To permanently remove a variable from the USER environment area use:
#
#		REG query HKCU\Environment /F /V <name>	- See what is there.
#		REG delete HKCU\Environment /F /V <name>
#
#--------------------------------------------------------------------------------
#
#	To permanently remove a variable from the SYSTEM environment area use:
#
#	REG query "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V <name>
#	REG delete "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V <name>
#
#--------------------------------------------------------------------------------
#
	if( ($fp = fopen("./badPath.bat", "w")) === false ){
		die( "Could not open file curENVS.bat - aborting.\n" );
		}

	foreach( $badPath as $k=>$v ){
		$c = count( $newEnvs[$k] );
		$w = $v[0];
		if( $c > 0 ){
			$info = <<<EOD
rem
rem	Can not delete the entry because there are $c
rem	entries in the $k variable. So I'm just
rem	not going to put this entry back into $k which
rem	basically still deletes it.
rem
rem	$k = $w
rem
EOD;
			fprintf( $fp, "%s\n", $info );
			continue;
			}

		foreach( $v as $k1=>$v1 ){
			if( $flags[$k][$k1]['user_flag'] ){
				$a = usrDelete( $k );
				$info = <<<EOD
rem
rem	$k = $v1
rem
$a\n
EOD;
				fprintf( $fp, "%s\n", $info );
				}
				else if( $flags[$k][$k1]['system_flag'] ){
					$a = sysDelete( $k );
					$info = <<<EOD
rem
rem	$k = $v1
rem
$a\n
EOD;
					fprintf( $fp, "%s\n", $info );
					}
				else if( $flags[$k][$k1]['upper_flag'] ){
					$a = sysDelete( $k );
					$info = <<<EOD
rem
rem	$k = $v1
rem
$a\n
EOD;
					fprintf( $fp, "%s\n", $info );
					}
				else {
					$a = usrDelete( $k );
					$info = <<<EOD
rem
rem	$k = $v1
rem
$a\n
EOD;
				fprintf( $fp, "%s\n", $info );
				}
			}
		}

	fclose( $fp );
}
#
#	Ok, so now we are going to create the new paths
#	First, collect up all of the paths.
#
	$paths = [];
	$path_types = [];
	foreach( $origEnvs as $k=>$v ){
		if( preg_match("/[__]*path$/i", $k) ){
			$b = explode( ";", $v );
			foreach( $b as $k1=>$v1 ){
				$paths[] = $v1;
				$path_types[] = $flags[$k][$k1];
				}
			}
		}
#
#	Once we have all of the paths, we want to flip the array and keys
#	so duplicate paths are eliminated.
#
	$a = array_flip( $paths );
	$paths = array_flip( $a );
	asort( $paths );

	scrCls();
	$info = <<<EOD
fixEnvs $ver

The following is the list of everything that was in
the PATH variable in the USER environment and the PATH
variable in the SYSTEM environment. I am creating a USRPATH
variable and a SYSPATH variable. There are certain rules
for where a variable should go.  I'll do my best to put
things into the right areas. You can look at what I did by
editing the newENVs.bat file after I am through.


\n
EOD;

	scrMsg( 0, 0, $info );

	$usrCnt = 0;
	$sysCnt = 0;
	$usrpath = [];
	$syspath = [];
	foreach( $paths as $k=>$v ){
		if( strlen($v) < 1 ){ continue; }
		if( preg_match("/user/i", $flags[$k][$k1]['type_flag']) ){
			$usrpath[$usrCnt] = [];
			$name = sprintf( "__USRPath-%05d", $usrCnt );
			$usrpath[$usrCnt][0] = usrSet( $name, $v );
			$usrpath[$usrCnt++][1] = usrAdd( $name, $v );
			}
			else if( preg_match("/system/i", $flags[$k][$k1]['type_flag']) ){
				$name = sprintf( "__SYSPath-%05d", $sysCnt );
				$syspath[$sysCnt++] = sysAdd( $name, $v );
				}

		}

	if( ($fp = fopen("./newENVs.bat", "w")) === false ){
		die( "Could not open file newENVS.bat - aborting.\n" );
		}

	$info = <<<EOD
rem
rem	The following are al of the USER paths I am creating.
rem	PLEASE - be sure to examine them BEFORE using them.
rem	Thank you.
rem
rem	First, the standard way to add a user variable:
rem\n
EOD;

	fprintf( $fp, "%s\n", $info );
	foreach( $usrpath as $k=>$v ){
		fprintf( $fp, "%s\n", $v[0] );

		$info = <<<EOD
rem
rem	Now the registry add option. Please DO NOT use this
rem	it is here just so you can see how this is done.
rem\n
EOD;

		fprintf( $fp, "%s\n", $v[1] );
		}

	$info = <<<EOD
rem
rem	The following are al of the SYSTEM paths I am creating.
rem	PLEASE - be sure to examine them BEFORE using them.
rem	Thank you.
rem\n
EOD;

	fprintf( $fp, "%s\n", $info );
	foreach( $syspath as $k=>$v ){
		fprintf( $fp, "%s\n", $v );
		}

	fclose( $fp );
#
#	Set up what MUST be in the SYSTEM variables.
#
	$system = array( "Windows", "dotnet", "Intel", "Oracle", "windir", "SQL", "Paragon" );
#	$pr->pr( $newEnvs, "newEnvs = " ); echo "\n";

	exit();

################################################################################
#	usrSet(). Generate a SETX command or clear it if $TEXT is null
################################################################################
function usrSet( $name, $text=null )
{
	if( is_null($text) ){ $text = '""'; }

	return "setx $name $text";
}
################################################################################
#	usrAdd(). Produces a REG command so you can add a registry entry.
################################################################################
function usrAdd( $name )
{
	return "REG add HKCU\Environment /F /V $name";
}
################################################################################
#	usrQuery(). Produces a REG command so you can query a registry entry.
################################################################################
function usrQuery( $name )
{
	return "REG query HKCU\Environment /V $name";
}
################################################################################
#	usrDelete(). Produces a REG command so you can delete a registry entry.
################################################################################
function usrDelete( $name )
{
	return "REG delete HKCU\Environment /F /V $name";
}
################################################################################
#	usrAdd(). Produces a REG command so you can add a registry entry.
################################################################################
function sysAdd( $name )
{
	return "REG add " .
		"'HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment' " .
		"/F /V $name";
}
################################################################################
#	sysQuery(). Produces a REG command so you can query a registry entry.
################################################################################
function sysQuery( $name )
{
	return "REG query " .
		'"HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" ' .
		"/V $name";
}
################################################################################
#	sysDelete(). Produces a REG command so you can delete a registry entry.
################################################################################
function sysDelete( $name )
{
	return "REG delete " .
		"'HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment' " .
		"/F /V $name";
}
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
	foreach( $opt as $k=>$v ){
		$opts .= "$v, ";
		}

	$opts = substr( $opts, 0, -2 ) . "]";
	$msg = "$msg $opts ?";

	scrMsg( $x, $y, $msg );
	$ans = fgets(STDIN);
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

