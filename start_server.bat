@echo off
REM Start PHP server
start "" php -S localhost:8000 -t .

REM Wait for server to start
ping localhost -n 2 > nul

REM Open HTML file in default web browser
start http://localhost:8000/TheXfile.Html

REM Grab the image count from PHP script
setlocal enabledelayedexpansion

REM URL of the PHP script
set "php_script=http://localhost:8000/upload.php"

REM Make a request to the PHP script and store the JSON response in a temporary file
echo Fetching image count from PHP script...
curl -s "%php_script%" > response.json

REM Check if the response contains an error message
findstr /C:"error" response.json > nul
if %errorlevel% equ 0 (
    echo Error: Failed to fetch image count from PHP script.
    del response.json
    pause
    exit /b
)

REM Extract the image count from the JSON response
for /f "tokens=2 delims=:,}" %%a in ('type response.json ^| findstr /C:"image_count"') do (
    set /a count=%%a
)

REM Display the image count
echo Number of image files: %count%

REM Clean up temporary files
del response.json

pause
