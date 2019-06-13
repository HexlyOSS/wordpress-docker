# Docker Developer Sandbox: Wordpress + Mysql 8

This container is a jumping point to start with vanilla Wordpress development and MySQL 8.

## Folder Structure

`www` - contains the core wordpress install

`conf` - configuration files for apache, php, and mysql

`src/<plugins | themes>` - for plugin and theme development. These directories get mounted into the wp root under `wp-content/plugins` and `wp-content/themes`

`logs/apache2` - apache logs.

The mysql `data` directory will be created on first run and will show up as `/mysql/data`. This will be ignored (in the .gitignore)

## Running

```
docker-compose up
```

This will start apache and mysql. Mysql will initialize it's database and then apache will connect and wordpress will be ready to run.

Apache will be listening on port `8091` by default (see the ports section of `wp` in the `docker-compose.yaml` file). Access via:

http://127.0.0.1:8091

On First run you will be prompted to execute the wp install. Select an easy to remember admin passowrd. If you forget this it is a pain to reset and will likely be easier to simply delete and re-install.

## FTP

The repo contains a very basic ftp server that is spun up as part of the docker-compose up process. This will allow you to upload/update/install plugins into your running wp server.

You access the ftp server at:
```
ftp:2121
```

`ftp` is the name of the service in docker-compose, so all images inside of that network will access it via that service name. The port it listens on is `2121`

The username is `admin` and the password is `123456`

## Bootnotes:
 - Configuration files are in `/conf` and should be easily recognizable 
 - MySQL data is persisted to `/mysql/data`, of which the contents are gitignored. So your data will survive container restarts 
 and not thrash with other's commits
 - MySQL Config is overridden to support MySQL 8 + PHP's MySQLi native authentication. It was a PITA to figure out
 - Logs will pile up in `/logs/apache2`. You can tail them easily via `npm run local:tail` 
 - If you need to expose this to the web, just run `npm run local:tunnel`