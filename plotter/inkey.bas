'
'	Simple inkey$ program
'
'	Use the ESCape key as a way to get out of this routine.
'	REMEMBER! If you just type a key - this program will return
'	that key in the INKEY.DAT file. THEN you must read that
'	file in. This program also creates a INKEY.LOG file so you
'	know everything that was typed in to this program.
'
'	IF you DO NOT want to use the escape key - then just choose
'	another key and put it in this next line.
'
'	A LOT of this program (the technical stuff) was taken from
'	these locations. The simpler stuff I did.
'
'	Taken from : https://qb64.com/wiki/_SCREENMOVE.html
'	Taken from : https://qb64phoenix.com/forum/showthread.php?tid=4230&pid=38073
'
'	GET HANDLE TO THE PROGRAM WINDOW
'
Declare Dynamic Library "user32"
    Function GetForegroundWindow& ()
    Function GetWindowThreadProcessId& (ByVal hWnd As Long, ByVal lpdwProcessId As _Offset)
    Function AttachThreadInput& (ByVal idAttach As Long, ByVal idAttachTo As Long, ByVal fAttach As Long)
    Function SetForegroundWindow& (ByVal hWnd As Long)
    Function BringWindowToTop& (ByVal hWnd As Long)
End Declare

Declare Dynamic Library "kernel32"
    Function GetCurrentThreadId& ()
End Declare

'
'	Use the ESCape key to stop the program. The ESCape key
'	IS sent to the INKEY.DAT file.
'
	esc$ = ""
'
'	Start the loop
'
	while 1
		hTarget& = _WindowHandle
		_SCREENMOVE -2000, -2000

		if _WindowHasFocus = 0 then
			' ----------------------------
			'  Get thread IDs
			' ----------------------------
			hForeground& = GetForegroundWindow
			fgThread& = GetWindowThreadProcessId(hForeground&, 0)
			myThread& = GetCurrentThreadId
			' ----------------------------
			'  Attach input queues
			' ----------------------------
			result1& = AttachThreadInput(myThread&, fgThread&, 1)
			' ----------------------------
			'  Try to force activation
			' ----------------------------
			result2& = BringWindowToTop(hTarget&)
			result3& = SetForegroundWindow(hTarget&)
			' ----------------------------
			'  Detach input queues
			' ----------------------------
			result4& = AttachThreadInput(myThread&, fgThread&, 0)

'			Print "Attempted to activate window."
			_Delay .1 'give it time to swap focus before trying again
			end if

		mykey$ = inkey$

		if len(mykey$) > 0 then
'
'	Save the pressed key to the INKEY.DAT file.
'	REMEMBER! Just because the user has NOW pressed
'		a key DOES NOT mean they did not press another
'		key earlier. So we always APPEND to the file.
'	IT IS THE RESPONSIBILITY of the calling program
'		to GET RID of the INKEY.DAT file. Failure to
'		do so means you might get unexpected results
'		on your next read.
'
			print mykey$;
			open "INKEY.DAT" for append as #1
			print #1, mykey$;
			close #1

			open "INKEY.LOG" for append as #1
			print #1, mykey$;
			close #1

			if mykey$ = esc$ then
				end
				endif

			endif

		wend

	end
