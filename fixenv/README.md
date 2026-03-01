# fixenv
A program to try to fix Windows' Environment Variables. The
program itself does not do anything other than the
following:

1. Create a current environment variable list. Some
additional information will be displayed are REM statements
since this is a batch file.

2. It will test each key (which is called a variable in
other languages) to see if the key is a USER variable or
a SYSTEM variable.

	- Please note that, as far as I have found,
	the only difference between a USER variable and
	a SYSTEM variable is that SYSTEM variables should
	always be all UPPERCASE.

	- In the Windows environment, there are five major
	keys in the registry. These are:

		- HKCR, HKCU, HKLM, HKU, HKCC

	The HKCU and the HKU registry areas relate to
	the USER environment variables while the rest are
	delegated to the SYSTEM environment variables
	EXCEPT when Microsoft just feels like mucking
	things up. Not only that, but there are environment
	variables that are just not in the registry. I
	do not know why that is - but it is. I am still
	searching for more information about why some
	variables (which are found by just doing a SET
	command on a DOS Command Line Interface or CLI
	and if you go to

	Control Panel->System->Advanced System Settings->
	Advanced Tab->Environment Variables Button

	You will see that there are TWO sections. The USER
	and SYSTEM variables. Which is all good and fine
	but there is no information on HOW they decided to
	put those variables into those two areas and WHY
	some of those variables are just not found. More
	as I find out more.

3. It also creates a badPaths.bat file. This contains all
of the paths which were not found on YOUR SYSTEM. It does
this via the PHP command "file_exists". If it does not
exist - it is considered a bad path.

	- PLEASE NOTE!!!! The path may not exist because it
	is on a NETWORK DRIVE or on an external disk drive
	that is currently not hooked up to your system. SO
	BE CAREFUL. The default, when determining if a
	path is bad does first test to make sure there is
	a single letter (like "C") followed by a colon
	(:) and also it looks for the forward slash and
	backwards slash so you can get "C:", or "C:/", or
	"C:\" which is how Windows depicts disk drives
	normally. Anything else - not tested. So if you
	have a disk drive labeled as "SamsBar:" - that
	will not be tested. It is just ignored.

4. Then the program creates a new batch file called
newEnvs.bat. This has the environment variables all set
up like they should be (IMHO). You may not like how I have
set them up. That's fine. Don't use the program or better
yet - change it so it does the changes how >YOU< want them
to be. This is how I would like them to be made. Some of
the things these changes do is:

	- The registry can create keys. You can name them
	whatever you want (within reason). These keys are
	then reference by putting percent signs around
	the name of the key. Like so:

	First - you create the key and the value like so:

		my_path = "XYZ:/This/is/the/path/information"

	Then - you reference the above like so:

		kor_path = %my_path%

	So now "kor_path" has the same value as "my_path". The
	thing is - if you change what "my_path" is - then you
	automatically change what "kor_path" is. This is the key
	to what I am doing.

5. To get what I am trying to do you should realize that
Windows used to have a really bad problem when it came
to environment variables. The problem was - each variable
could only hold up to 256 characters. A hold over from the
old QBasic days. Thus, you could put in 32,000 characters
but only the first 256 characters would be used. This
caused all sorts of headaches. The problem persisted
all the way up to Windows 10 when Microsoft changed how
many characters could be in a string. The problem is -
not every program went with this change causing even more
problems. So I went "Well, maybe we can circumvent the
problem by breaking up the various paths". Or, in other
words, take the USER PATH and the SYSTEM PATH variables,
split them up via the semicolon (; - which is what is used
to delineate one path from another) and then put them into
their own environment variable and then just add in each
of these new variables into the USER PATH or SySTEM PATH
variables. Like so:

	PATH = %my_path%;

	The above could be put in the USER Enviroment Variable
	list or the SYSTEM Environment Variable list depending up
	on where it should go.

	This should reduce the size of both PATH statements.

6. So then I went "How do you not overwrite some other
environment variable"? Then I remembered the old "Use
two underscores if you want to have a variable that
other people do not normally use". I tried it and it
worked. So now I can make USER Environment Varibles by
using "__USER-#####" where the two underscores says this
is a special variable, the USER part means it is meant
to be in the USER Environment Variable area, the dash is
just a separator, and the "#####" is the number of the
variable. Example: __USER-00001.

	I am hoping that this will make life easier for
	everyone. I know it will for me.

7. Files that are created are:

	- curEnvs.bat - The current environment variables
	you currently have on your system.

	- newEnvs.bat - the NEW environment variables that
	will be made IF you run the batch file.

		- PLEASE NOTE : The newEnvs.bat file WILL
		DELETE your old variables and change your
		system!!!! So be CAREFUL using it.

	- badPaths.bat - These are the bad paths found in your
	registry. A "bad" path is a path that does not exist
	on your computer. IF THE PATH IS TO A NETWORK DRIVE OR
	EXTERNAL DISK DRIVE - then do not run the badPaths.bat
	file as it will delete those paths!!!!!

	- PLEASE NOTE : The program WILL ASK YOU if you want
	to delete these bad paths. I would tell the program NO
	- but if you say YES - they are gone. Period. YOU HAVE
	BEEN WARNED!

