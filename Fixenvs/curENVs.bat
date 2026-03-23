
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
rem
rem	7zip_path - USER variable
rem
setx 7zip_path=C:\Program Files\7-Zip

rem
rem	ERROR : Did not find ALLUSERSPROFILE in either of the locations. So I am
rem		setting ALLUSERSPROFILE to be of type SYSTEM VARIABLE.
rem
rem
rem	ALLUSERSPROFILE - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V ALLUSERSPROFILE /D C:\ProgramData

rem
rem	ERROR : Did not find APPDATA in either of the locations. So I am
rem		setting APPDATA to be of type SYSTEM VARIABLE.
rem
rem
rem	APPDATA - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V APPDATA /D C:\Users\marke\AppData\Roaming

rem
rem	autoit_path - USER variable
rem
setx autoit_path=C:\Program Files (x86)\AutoIt3

rem
rem	autoit_path - USER variable
rem
setx autoit_path=C:\Program Files (x86)\AutoIt3\DLLs

rem
rem	autoit_path - USER variable
rem
setx autoit_path=C:\Program Files (x86)\AutoIt3\Include

rem
rem	borland_path - USER variable
rem
setx borland_path=C:\Program_Files\BC5\BIN

rem
rem	bullzip_path - USER variable
rem
setx bullzip_path=C:\Program Files\Bullzip\PDF Printer

rem
rem	ChocolateyInstall - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V ChocolateyInstall /D C:\ProgramData\chocolatey

rem
rem	ChocolateyLastPathUpdate - USER variable
rem
setx ChocolateyLastPathUpdate=133751445482133265

rem
rem	ERROR : Did not find CommonProgramFiles in either of the locations. So I am
rem		setting CommonProgramFiles to be of type USER VARIABLE.
rem
rem
rem	CommonProgramFiles - USER VARIABLE
rem
setx CommonProgramFiles=C:\Program Files\Common Files

rem
rem	ERROR : Did not find CommonProgramFiles(x86) in either of the locations. So I am
rem		setting CommonProgramFiles(x86) to be of type USER VARIABLE.
rem
rem
rem	CommonProgramFiles(x86) - USER VARIABLE
rem
setx CommonProgramFiles(x86)=C:\Program Files (x86)\Common Files

rem
rem	ERROR : Did not find CommonProgramW6432 in either of the locations. So I am
rem		setting CommonProgramW6432 to be of type USER VARIABLE.
rem
rem
rem	CommonProgramW6432 - USER VARIABLE
rem
setx CommonProgramW6432=C:\Program Files\Common Files

rem
rem	ERROR : Did not find COMPUTERNAME in either of the locations. So I am
rem		setting COMPUTERNAME to be of type SYSTEM VARIABLE.
rem
rem
rem	COMPUTERNAME - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V COMPUTERNAME /D MITHRANDIR

rem
rem	ComSpec - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V ComSpec /D C:\Windows\system32\cmd.exe

rem
rem	ERROR : Did not find cs in either of the locations. So I am
rem		setting cs to be of type USER VARIABLE.
rem
rem
rem	cs - USER VARIABLE
rem
setx cs=php

rem
rem	dc_path - USER variable
rem
setx dc_path=C:\DOS\dependency-check-8.4.2-release\dependency-check\bin

rem
rem	dnSpy32_path - USER variable
rem
setx dnSpy32_path=C:\Program_Files\dnSpy-net-win32

rem
rem	dnSpy64_path - USER variable
rem
setx dnSpy64_path=C:\Program_Files\dnSpy-net-win64

rem
rem	DokanLibrary2 - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V DokanLibrary2 /D C:\Program Files\Dokan\Dokan Library-2.2.1\

rem
rem	DokanLibrary2_LibraryPath_x64 - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V DokanLibrary2_LibraryPath_x64 /D C:\Program Files\Dokan\Dokan Library-2.2.1\lib\

rem
rem	DokanLibrary2_LibraryPath_x86 - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V DokanLibrary2_LibraryPath_x86 /D C:\Program Files\Dokan\Dokan Library-2.2.1\x86\lib\

rem
rem	dos_path - USER variable
rem
setx dos_path=c:/DOS

rem
rem	dos_path - USER variable
rem
setx dos_path=c:/DOS/dd

rem
rem	DriverData - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V DriverData /D C:\Windows\System32\Drivers\DriverData

rem
rem	fb32_path - USER variable
rem
setx fb32_path=C:\Program_Files\FreeBASIC

rem
rem	fb64_path - USER variable
rem
setx fb64_path=C:\Program_Files\FreeBASIC

rem
rem	fbc_path - USER variable
rem
setx fbc_path=C:\Program_Files\FreeBASIC-1.09.0-winlibs-gcc-9.3.0

rem
rem	fbc_path - USER variable
rem
setx fbc_path=C:\Program_Files\FreeBASIC-1.09.0-winlibs-gcc-9.3.0\inc

rem
rem	FONTCONFIG_FILE - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V FONTCONFIG_FILE /D C:\Windows\fonts.conf

rem
rem	fpdf_path - USER variable
rem
setx fpdf_path=C:\xampp\php\fpdf186

rem
rem	github_path - USER variable
rem
setx github_path=C:\Users\marke\AppData\Local\GitHubDesktop\bin

rem
rem	git_path - USER variable
rem
setx git_path=C:\Users\marke\AppData\Local\GitHubDesktop\bin

rem
rem	GIT_PYTHON_GIT_EXECUTABLE - USER variable
rem
setx GIT_PYTHON_GIT_EXECUTABLE=C:\Program Files\Git\cmd

rem
rem	GNUPLOT_LIB - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V GNUPLOT_LIB /D C:\Program Files\gnuplot\demo

rem
rem	GNUPLOT_LIB - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V GNUPLOT_LIB /D C:\Program Files\gnuplot\share

rem
rem	GNUTERM - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V GNUTERM /D windows

rem
rem	gnu_path - USER variable
rem
setx gnu_path=C:\Program Files (x86)\GnuWin32\bin

rem
rem	ERROR : Did not find HOMEDRIVE in either of the locations. So I am
rem		setting HOMEDRIVE to be of type SYSTEM VARIABLE.
rem
rem
rem	HOMEDRIVE - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V HOMEDRIVE /D C:

rem
rem	ERROR : Did not find HOMEPATH in either of the locations. So I am
rem		setting HOMEPATH to be of type SYSTEM VARIABLE.
rem
rem
rem	HOMEPATH - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V HOMEPATH /D \Users\marke

rem
rem	imagick_path - USER variable
rem
setx imagick_path=C:\Program Files\ImageMagick-7.1.1-Q16

rem
rem	include - USER variable
rem
setx include=c:\program files (x86)\dsv5\vc\include

rem
rem	include - USER variable
rem
setx include=c:\program files (x86)\dsv5\vc\atl\include

rem
rem	include - USER variable
rem
setx include=c:\program files (x86)\dsv5\vc\mfc\include

rem
rem	inform - USER variable
rem
setx inform=C:\Program_Files\InForm-1.3\InForm

rem
rem	INTEL_DEV_REDIST - SYSTEM variable
rem
setx INTEL_DEV_REDIST=C:\Program Files (x86)\Common Files\Intel\Shared Libraries\

rem
rem	javafx-sdk-19-lib_path - USER variable
rem
setx javafx-sdk-19-lib_path=C:\Program Files\Java\javafx-sdk-19\lib

rem
rem	javafx_jmods_path - USER variable
rem
setx javafx_jmods_path=C:\Program Files\Java\javafx-jmods-19

rem
rem	javafx_sdk_path - USER variable
rem
setx javafx_sdk_path=C:\Program Files\Java\javafx-sdk-19

rem
rem	java_jdk_path - USER variable
rem
setx java_jdk_path=C:\Program Files\Java\jdk-19

rem
rem	java_path - USER variable
rem
setx java_path=C:\Program Files\Java

rem
rem	KMP_DUPLICATE_LIB_OK - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V KMP_DUPLICATE_LIB_OK /D TRUE

rem
rem	lib - USER variable
rem
setx lib=c:\program files (x86)\dsv5\vc\lib

rem
rem	lib - USER variable
rem
setx lib=c:\program files (x86)\dsv5\vc\mfc\lib

rem
rem	lib - USER variable
rem
setx lib=%lib%

rem
rem	ERROR : Did not find LOCALAPPDATA in either of the locations. So I am
rem		setting LOCALAPPDATA to be of type SYSTEM VARIABLE.
rem
rem
rem	LOCALAPPDATA - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V LOCALAPPDATA /D C:\Users\marke\AppData\Local

rem
rem	ERROR : Did not find LOGONSERVER in either of the locations. So I am
rem		setting LOGONSERVER to be of type SYSTEM VARIABLE.
rem
rem
rem	LOGONSERVER - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V LOGONSERVER /D \\MITHRANDIR

rem
rem	lua53_path - USER variable
rem
setx lua53_path=C:\ProgramData\chocolatey\lib\lua53\tools

rem
rem	lua54_path - USER variable
rem
setx lua54_path=C:\Program_Files\Lua\lua-5.4.2_Win64_bin

rem
rem	macos7_path - USER variable
rem
setx macos7_path=C:\Program_Files\MacSys761\Programs

rem
rem	macos9_path - USER variable
rem
setx macos9_path=C:\Program_Files\MacOS9\Programs

rem
rem	MAILENABLE_PATH - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V MAILENABLE_PATH /D C:\PROGRA~2\MAILEN~1

rem
rem	mame_path - USER variable
rem
setx mame_path=C:\Program_Files\Mame Gaming

rem
rem	mingw_path - USER variable
rem
setx mingw_path=C:\Program_Files\MinGWStudio\MinGW\bin

rem
rem	MOZ_PLUGIN_PATH - USER variable
rem
setx MOZ_PLUGIN_PATH=C:\PROGRAM FILES\FOXIT SOFTWARE\FOXIT PDF READER\plugins\

rem
rem	MSDevDir - USER variable
rem
setx MSDevDir=C:\Program Files (x86)\DSv5\SharedIDE

rem
rem	msvscode_path - USER variable
rem
setx msvscode_path=C:\Users\marke\AppData\Local\Programs\Microsoft VS Code\bin

rem
rem	ERROR : Did not find MYVIMRC in either of the locations. So I am
rem		setting MYVIMRC to be of type SYSTEM VARIABLE.
rem
rem
rem	MYVIMRC - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V MYVIMRC /D C:\Program Files (x86)\Vim\_vimrc

rem
rem	my_libs - USER variable
rem
setx my_libs=D:\My Programs\PHP\lib

rem
rem	nightrain_path - USER variable
rem
setx nightrain_path=C:\Program_Files\nightrain_4.0x86

rem
rem	nightrain_path - USER variable
rem
setx nightrain_path=C:\Program_Files\nightrain_4.0x86\lib\php\ext

rem
rem	nir_path - USER variable
rem
setx nir_path=C:\Program Files (x86)\NirSoft

rem
rem	nir_path - USER variable
rem
setx nir_path=C:\Program_Files\nircmd-x64

rem
rem	nir_path - USER variable
rem
setx nir_path=C:\Program_Files\NirSoft

rem
rem	npm_path - USER variable
rem
setx npm_path=C:\Users\marke\AppData\Roaming\npm

rem
rem	ERROR : Did not find nt_dir in either of the locations. So I am
rem		setting nt_dir to be of type USER VARIABLE.
rem
rem
rem	nt_dir - USER VARIABLE
rem
setx nt_dir=C:/Program_Files/nightrain_4.0x86

rem
rem	NUMBER_OF_PROCESSORS - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V NUMBER_OF_PROCESSORS /D 8

rem
rem	OneDrive - USER variable
rem
setx OneDrive=C:\Users\marke\OneDrive

rem
rem	OS - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V OS /D Windows_NT

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\Microsoft\jdk-11.0.16.101-hotspot\bin

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\ActiveState Komodo IDE 12\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\Common Files\Oracle\Java\javapath

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\Common Files\Oracle\Java\java8path

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\Common Files\Oracle\Java\javapath

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\Common Files\Intel\Shared Libraries\redist\intel64_win\compiler

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\Intel\iCLS Client\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\Intel\iCLS Client\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Windows\system32

rem
rem	Path - SYSTEM variable
rem
setx Path=%windir%

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Windows\System32\Wbem

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Windows\System32\WindowsPowerShell\v1.0\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Windows\System32\OpenSSH\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\Intel\Intel(R) Management Engine Components\DAL

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\Intel\Intel(R) Management Engine Components\DAL

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\Intel\Intel(R) Management Engine Components\IPT

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\Intel\Intel(R) Management Engine Components\IPT

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\dotnet\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program_Files\Free_Pascal\bin\i386-Win32

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program_Files\Strawberry_Perl\c\bin

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program_Files\Strawberry_Perl\perl\site\bin

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program_Files\Strawberry_Perl\perl\bin

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\QuickTime\QTSystem\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\Microsoft SQL Server\90\Tools\binn\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\Microsoft SQL Server\130\Tools\Binn\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\dotnet\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\CMake\bin

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\WinMerge

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\Microsoft SQL Server\150\Tools\Binn\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\Microsoft SQL Server\Client SDK\ODBC\170\Tools\Binn\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\gnuplot\bin

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\Git\cmd

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\ProgramData\chocolatey\bin

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\nodejs\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\MySQL\MySQL Utilities 1.6\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\ProgramData\ComposerSetup\bin

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\NVIDIA Corporation\PhysX\Common

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\ProgramData\chocolatey\lib\lua53\tools

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\Common Files\Autodesk Shared\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\HDF_Group\HDF5\1.14.6\bin\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\PROGRA~1\CONDUS~1\DISKEE~1\

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\Paragon Software\Paragon ExtFS for Windows

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\AOMEI Backupper

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files\Inkscape\bin

rem
rem	Path - SYSTEM variable
rem
setx Path=C:\Program Files (x86)\Vim\vim91

rem
rem	PATHEXT - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PATHEXT /D .COM

rem
rem	PATHEXT - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PATHEXT /D .EXE

rem
rem	PATHEXT - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PATHEXT /D .BAT

rem
rem	PATHEXT - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PATHEXT /D .CMD

rem
rem	PATHEXT - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PATHEXT /D .VBS

rem
rem	PATHEXT - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PATHEXT /D .VBE

rem
rem	PATHEXT - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PATHEXT /D .JS

rem
rem	PATHEXT - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PATHEXT /D .JSE

rem
rem	PATHEXT - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PATHEXT /D .WSF

rem
rem	PATHEXT - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PATHEXT /D .WSH

rem
rem	PATHEXT - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PATHEXT /D .MSC

rem
rem	pdftopng_path - USER variable
rem
setx pdftopng_path=C:\Program_Files\PDFtoPNG

rem
rem	perl_path - USER variable
rem
setx perl_path=C:\xampp\perl

rem
rem	php_path - USER variable
rem
setx php_path=C:/xampp/php

rem
rem	PROCESSOR_ARCHITECTURE - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PROCESSOR_ARCHITECTURE /D AMD64

rem
rem	PROCESSOR_IDENTIFIER - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PROCESSOR_IDENTIFIER /D Intel64 Family 6 Model 158 Stepping 9, GenuineIntel

rem
rem	PROCESSOR_LEVEL - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PROCESSOR_LEVEL /D 6

rem
rem	PROCESSOR_REVISION - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PROCESSOR_REVISION /D 9e09

rem
rem	ERROR : Did not find ProgramData in either of the locations. So I am
rem		setting ProgramData to be of type USER VARIABLE.
rem
rem
rem	ProgramData - USER VARIABLE
rem
setx ProgramData=C:\ProgramData

rem
rem	ERROR : Did not find ProgramFiles in either of the locations. So I am
rem		setting ProgramFiles to be of type USER VARIABLE.
rem
rem
rem	ProgramFiles - USER VARIABLE
rem
setx ProgramFiles=C:\Program Files

rem
rem	ERROR : Did not find ProgramFiles(x86) in either of the locations. So I am
rem		setting ProgramFiles(x86) to be of type USER VARIABLE.
rem
rem
rem	ProgramFiles(x86) - USER VARIABLE
rem
setx ProgramFiles(x86)=C:\Program Files (x86)

rem
rem	ERROR : Did not find ProgramW6432 in either of the locations. So I am
rem		setting ProgramW6432 to be of type USER VARIABLE.
rem
rem
rem	ProgramW6432 - USER VARIABLE
rem
setx ProgramW6432=C:\Program Files

rem
rem	ERROR : Did not find PROMPT in either of the locations. So I am
rem		setting PROMPT to be of type SYSTEM VARIABLE.
rem
rem
rem	PROMPT - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PROMPT /D $P$G

rem
rem	ps6_path - USER variable
rem
setx ps6_path=C:\Program_Files\Adobe\Photoshop 6.0

rem
rem	ps7_path - USER variable
rem
setx ps7_path=C:\Program_Files\Adobe\Photoshop 7.0

rem
rem	PSModulePath - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PSModulePath /D C:\Program Files (x86)\WindowsPowerShell\Modules

rem
rem	PSModulePath - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PSModulePath /D C:\Windows\system32\WindowsPowerShell\v1.0\Modules

rem
rem	PSModulePath - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PSModulePath /D C:\Program Files (x86)\AutoIt3\AutoItX

rem
rem	ERROR : Did not find PUBLIC in either of the locations. So I am
rem		setting PUBLIC to be of type SYSTEM VARIABLE.
rem
rem
rem	PUBLIC - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V PUBLIC /D C:\Users\Public

rem
rem	python310 - USER variable
rem
setx python310=C:\Users\marke\AppData\Local\Programs\Python\Python310\

rem
rem	python310 - USER variable
rem
setx python310=C:\Users\marke\AppData\Local\Programs\Python\Python310\Scripts\

rem
rem	python312 - USER variable
rem
setx python312=c:/python312

rem
rem	python312 - USER variable
rem
setx python312=c:/python312/Scripts

rem
rem	qb64_path - USER variable
rem
setx qb64_path=C:\Program_Files\qb64

rem
rem	ERROR : Did not find r in either of the locations. So I am
rem		setting r to be of type USER VARIABLE.
rem
rem
rem	r - USER VARIABLE
rem
setx r=r.bat

rem
rem	rapidexe_path - USER variable
rem
setx rapidexe_path=C:\Program_Files\RapidExe

rem
rem	rar_path - USER variable
rem
setx rar_path=C:\Program Files\WinRAR

rem
rem	rembg_path - USER variable
rem
setx rembg_path=C:\Users\marke\AppData\Local\Programs\Python\Python310\Scripts

rem
rem	ERROR : Did not find r_dir in either of the locations. So I am
rem		setting r_dir to be of type USER VARIABLE.
rem
rem
rem	r_dir - USER VARIABLE
rem
setx r_dir=C:/DOS

rem
rem	ERROR : Did not find r_exe in either of the locations. So I am
rem		setting r_exe to be of type USER VARIABLE.
rem
rem
rem	r_exe - USER VARIABLE
rem
setx r_exe=C:/DOS/r.bat

rem
rem	SCITE_USERHOME - USER variable
rem
setx SCITE_USERHOME=C:\Users\marke\AppData\Local\AutoIt v3\SciTE

rem
rem	ERROR : Did not find SESSIONNAME in either of the locations. So I am
rem		setting SESSIONNAME to be of type SYSTEM VARIABLE.
rem
rem
rem	SESSIONNAME - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V SESSIONNAME /D Console

rem
rem	SheepShaver_path - USER variable
rem
setx SheepShaver_path=C:\Program_Files\SheepShaver

rem
rem	ERROR : Did not find SystemDrive in either of the locations. So I am
rem		setting SystemDrive to be of type USER VARIABLE.
rem
rem
rem	SystemDrive - USER VARIABLE
rem
setx SystemDrive=C:

rem
rem	ERROR : Did not find SystemRoot in either of the locations. So I am
rem		setting SystemRoot to be of type USER VARIABLE.
rem
rem
rem	SystemRoot - USER VARIABLE
rem
setx SystemRoot=C:\Windows

rem
rem	tcc_path - USER variable
rem
setx tcc_path=C:\Program_Files\tcc

rem
rem	TEMP - SYSTEM variable
rem
setx TEMP=C:\Users\marke\AppData\Local\Temp

rem
rem	tessdata_path - USER variable
rem
setx tessdata_path=C:\Program Files\Tesseract-OCR\tessdata

rem
rem	Tesseract_path - USER variable
rem
setx Tesseract_path=C:\Program Files\Tesseract-OCR

rem
rem	ERROR : Did not find test in either of the locations. So I am
rem		setting test to be of type USER VARIABLE.
rem
rem
rem	test - USER VARIABLE
rem
setx test=0

rem
rem	TMP - SYSTEM variable
rem
setx TMP=C:\Users\marke\AppData\Local\Temp

rem
rem	unix_path - USER variable
rem
setx unix_path=C:\DOS\UnxUtils\bin

rem
rem	unix_path - USER variable
rem
setx unix_path=C:\DOS\UnxUtils\usr\local\wbin

rem
rem	ERROR : Did not find USERDOMAIN in either of the locations. So I am
rem		setting USERDOMAIN to be of type SYSTEM VARIABLE.
rem
rem
rem	USERDOMAIN - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V USERDOMAIN /D MITHRANDIR

rem
rem	ERROR : Did not find USERDOMAIN_ROAMINGPROFILE in either of the locations. So I am
rem		setting USERDOMAIN_ROAMINGPROFILE to be of type USER VARIABLE.
rem
rem
rem	USERDOMAIN_ROAMINGPROFILE - USER VARIABLE
rem
setx USERDOMAIN_ROAMINGPROFILE=MITHRANDIR

rem
rem	USERNAME - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V USERNAME /D Mark

rem
rem	ERROR : Did not find USERPROFILE in either of the locations. So I am
rem		setting USERPROFILE to be of type SYSTEM VARIABLE.
rem
rem
rem	USERPROFILE - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V USERPROFILE /D C:\Users\marke

rem
rem	ERROR : Did not find VIM in either of the locations. So I am
rem		setting VIM to be of type SYSTEM VARIABLE.
rem
rem
rem	VIM - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V VIM /D C:\Program Files (x86)\Vim

rem
rem	ERROR : Did not find VIMRUNTIME in either of the locations. So I am
rem		setting VIMRUNTIME to be of type SYSTEM VARIABLE.
rem
rem
rem	VIMRUNTIME - SYSTEM VARIABLE
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V VIMRUNTIME /D C:\Program Files (x86)\Vim\vim91

rem
rem	vim_path - USER variable
rem
setx vim_path=C:\Program Files (x86)\Vim\vim91

rem
rem	VirtualBox_path - USER variable
rem
setx VirtualBox_path=C:\Program Files\Oracle\VirtualBox

rem
rem	whois_path - USER variable
rem
setx whois_path=C:\Program_Files\WhoIs

rem
rem	windir - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V windir /D C:\Windows

rem
rem	win_path - USER variable
rem
setx win_path=C:\Windows

rem
rem	xpdf_path - USER variable
rem
setx xpdf_path=C:\Program_Files\xpdf-tools-win-4.01.01\bin64

rem
rem	ZES_ENABLE_SYSMAN - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V ZES_ENABLE_SYSMAN /D 1

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program_Files\Mame Gaming

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program Files\7-Zip

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program_Files\BC5\BIN

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=c:/DOS

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=c:/DOS/dd

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program_Files\FreeBASIC-1.09.0-winlibs-gcc-9.3.0

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program_Files\FreeBASIC-1.09.0-winlibs-gcc-9.3.0\inc

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Users\marke\AppData\Local\GitHubDesktop\bin

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program Files\ImageMagick-7.1.1-Q16

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program_Files\InForm-1.3\InForm

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program Files\Java\jdk-19

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program Files\Java

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program Files\Java\javafx-jmods-19

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program Files\Java\javafx-sdk-19

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program Files\Java\javafx-sdk-19\lib

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program_Files\MinGWStudio\MinGW\bin

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=D:\My Programs\PHP\lib

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=%mycroft_path%

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program Files (x86)\NirSoft

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program_Files\nircmd-x64

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program_Files\NirSoft

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=%OneDrive%

rem
rem	__Path-00001 - USER variable
rem
setx __Path-00001=C:\Program_Files\PDFtoPNG

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\xampp\perl

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\xampp\php

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\xampp\php\inc\Ncurses

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Program_Files\RapidExe

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Program Files\WinRAR

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Users\marke\AppData\Local\Programs\Python\Python310\Scripts

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Program Files\Tesseract-OCR\tessdata

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Program Files\Tesseract-OCR

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\DOS\UnxUtils\bin

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\DOS\UnxUtils\usr\local\wbin

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Program_Files\WhoIs

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Program_Files\xpdf-tools-win-4.01.01\bin64

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Windows\TEMP

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Windows\TEMP

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=%SCITE_USER_HOME%

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Program Files (x86)\Common Files\Intel\Shared Libraries\

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\DOS\dependency-check-8.4.2-release\dependency-check\bin

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=%composer_path%

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Users\marke\AppData\Local\Programs\Python\Python310\

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Users\marke\AppData\Local\Programs\Python\Python310\Scripts\

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=%script311_path%

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Users\marke\AppData\Roaming\npm

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=%mac761_path%

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Program_Files\MacOS9\Programs

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Program_Files\Adobe\Photoshop 6.0

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Program_Files\Adobe\Photoshop 7.0

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Program Files\Oracle\VirtualBox

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=%comfyui_path%

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Users\marke\AppData\Local\GitHubDesktop\bin

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Users\marke\AppData\Local\Programs\Microsoft VS Code\bin

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Program_Files\dnSpy-net-win32

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Program_Files\dnSpy-net-win64

rem
rem	__Path-00002 - USER variable
rem
setx __Path-00002=C:\Program Files (x86)\Vim\vim91

rem
rem	__PSLockDownPolicy - SYSTEM variable
rem
REG add "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /F /V __PSLockDownPolicy /D 0

