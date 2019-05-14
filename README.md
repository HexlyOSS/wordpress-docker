# Docker Developer Sandbox: Wordpress + Mysql 8

This container is a jumping point to start with vanilla Wordpress development and MySQL 8. 

Notes:
 - Configuration files are in `/conf` and should be easily recognizable 
 - MySQL data is persisted to `/mysql/data`, of which the contents are gitignored. So your data will survive container restarts 
 and not thrash with other's commits
 - MySQL Config is overridden to support MySQL 8 + PHP's MySQLi native authentication. It was a PITA to figure out
 - Logs will pile up in `/logs/apache2`. You can tail them easily via `npm run local:tail` 
 - If you need to expose this to the web, just run `npm run local:tunnel`