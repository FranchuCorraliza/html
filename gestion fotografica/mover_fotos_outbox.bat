@echo off 
xcopy "\\192.168.1.20\departamento web\OUTBOX\*-4.jpg" \\FOTOGRAFIA-PC\ftp\outbox-cuarta-foto  /s/c/y
xcopy "\\192.168.1.20\departamento web\OUTBOX" \\FOTOGRAFIA-PC\ftp\outbox  /s/c/y
del /S /Q "\\192.168.1.20\departamento web\OUTBOX\"
exit /B