-- Ensure the app user authenticates with mysql_native_password
-- so PHP's mysqlnd / PDO can connect without caching_sha2_password
-- full-auth handshake issues.
ALTER USER 'laravel'@'%' IDENTIFIED WITH mysql_native_password BY 'laravel';
FLUSH PRIVILEGES;
