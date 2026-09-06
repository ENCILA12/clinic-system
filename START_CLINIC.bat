@echo off
TITLE Clinic System Server
color 0A

echo ===================================================
echo             CLINIC SYSTEM SERVER                   
echo ===================================================
echo.
echo Starting the server...
echo.
echo Please DO NOT close this black window while using the system.
echo To turn off the system, simply close this window.
echo.
echo Opening your web browser now...

:: Open the browser
start http://localhost:8000

:: Start the PHP server
"C:\xampp\php\php.exe" -S localhost:8000
