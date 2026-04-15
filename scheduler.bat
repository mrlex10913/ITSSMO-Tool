@echo off
cd /d C:\inetpub\wwwroot\ITSSMO-Tool
php artisan schedule:run >> storage\logs\scheduler.log 2>&1
