@echo off 

set ano=%date:~6,4%
set mes=%date:~3,2%
set dia=%date:~0,2%
set fichero=%1
xcopy \\FOTOGRAFIA-PC\ftp\inbox\%fichero% "\\192.168.1.20\departamento web\BACKUPS-FOTOS\inbox\inbox-%ano%-%mes%-%dia%\" /s/c/y
del /S /Q \\FOTOGRAFIA-PC\ftp\inbox\%fichero%
exit /B