SET NEWLINE=^& echo.

FIND /C /I "zotune" %WINDIR%\system32\drivers\etc\hosts
IF %ERRORLEVEL% NEQ 0 ECHO %NEWLINE%^127.0.0.1                   zotune>>%WINDIR%\system32\drivers\etc\hosts

FIND /C /I "yoursol" %WINDIR%\system32\drivers\etc\hosts
IF %ERRORLEVEL% NEQ 0 ECHO %NEWLINE%^127.0.0.1                   yoursol>>%WINDIR%\system32\drivers\etc\hosts

FIND /C /I "music" %WINDIR%\system32\drivers\etc\hosts
IF %ERRORLEVEL% NEQ 0 ECHO ^127.0.0.1                   music>>%WINDIR%\system32\drivers\etc\hosts

FIND /C /I "html5" %WINDIR%\system32\drivers\etc\hosts
IF %ERRORLEVEL% NEQ 0 ECHO ^127.0.0.1                   html5>>%WINDIR%\system32\drivers\etc\hosts