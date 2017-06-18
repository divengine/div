@echo off

if "%DIV_HOME%"=="" goto error

php %DIV_HOME%\div-cli.php %*
goto end

:error
echo -  
echo ERROR: Enviroment variable DIV_HOME is not set in the system's properties.
echo -

:end
