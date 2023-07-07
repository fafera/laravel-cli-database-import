# Laravel Auto Dump and Import from Hostgator
A Command to run on Laravel artisan that will dump the current Mysql database from your Hosgator environment and import on your current localhost database.
**Important: This command needs that you have the ssh connection with Hostgator already configured on your environment**.

## Requirements
- phpseclib3\Net\SFTP.

## How to use
- Put this file on `/app/Console/Commands`.
- Set the following variables on your `.env`.
  - SSH_DOMAIN: Your domain used to connect to Hosgator.
  - SSH_USER= Your SSH user.
  - SSH_PASSWORD= The user password.
  - SSH_DB_USER= The database user.
  - SSH_DB_PASSWORD= The database user password.
  - SSH_DB_NAME= The database name.
  - SSH_PROJECT_PATH= The project path. Starts on `~`. You just need to set the url, like: "mycustomdomain.com".
- Run the following command on terminal: php artisan database:import {file_name}.

### Additional Comments
The dump .sql file on Hostgator is used only to be downloaded to localhost. After download it's deleted from environment.
The current Laravel storage path is defined to `public`.

 
