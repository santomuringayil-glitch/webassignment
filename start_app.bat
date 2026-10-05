@echo off
title CampusVote - Online Voting System
cls
echo =====================================================================
echo           CampusVote - Secure Online Voting System
echo =====================================================================
echo.
echo [1/2] Initializing PHP Development Server...

:: Check PHP in PATH or Winget package directory
set PHP_BIN=php
where php >nul 2>nul
if %errorlevel% neq 0 (
    set PHP_BIN="%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
)

echo [2/2] Launching Web Browser at http://localhost:8000 ...
start http://localhost:8000

echo.
echo =====================================================================
echo   SERVER RUNNING at: http://localhost:8000
echo =====================================================================
echo.
echo   Demo Quick Credentials:
echo   - Student Voter: STU202601 / password123
echo   - Admin Panel:   admin@campusvote.org / password123
echo.
echo   Presentation Slides:
echo   - Web Slides:    http://localhost:8000/presentation/index.html
echo   - PowerPoint:    presentation.pptx
echo.
echo   Press Ctrl + C in this terminal window to stop the server anytime.
echo =====================================================================
echo.

%PHP_BIN% -S localhost:8000 -t "%~dp0"
pause
