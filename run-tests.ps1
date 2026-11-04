$php = 'C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe'
$artisan = 'C:\Users\hp\Downloads\deshmaya.com\deshmaya\artisan'
& $php $artisan test --filter=SettingsTest > 'C:\Users\hp\Downloads\deshmaya.com\deshmaya\test-output.txt' 2>&1
Write-Host "EXIT:$LASTEXITCODE"
