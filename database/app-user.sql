-- Least-privilege database account for the web application.
-- Run as a database administrator after importing schema.sql, with a strong
-- password of your own. The application only needs to read and write rows;
-- it can never alter tables, grant rights or read files on the server.

CREATE USER 'efisioner_app'@'localhost' IDENTIFIED BY 'replace-with-a-long-random-password';
GRANT SELECT, INSERT, UPDATE, DELETE ON efisioner.* TO 'efisioner_app'@'localhost';
