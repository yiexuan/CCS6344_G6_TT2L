USE master;
GO

CREATE MASTER KEY ENCRYPTION BY PASSWORD = 'Pa$$w0rd';


CREATE CERTIFICATE TestDbCert WITH SUBJECT = 'TestDbDatabase';


USE TestDb;
GO
CREATE DATABASE ENCRYPTION KEY
WITH ALGORITHM = AES_256
ENCRYPTION BY SERVER CERTIFICATE TestDbCert;

SELECT * FROM sys.certificates WHERE name = 'MyDatabaseCert';


DROP CERTIFICATE MyDatabaseCert;
GO

DROP MASTER KEY;
GO


ALTER DATABASE TestDb SET ENCRYPTION ON;
GO

SELECT name, is_encrypted
FROM sys.databases
WHERE name = 'TestDb';

SELECT 
    db_name(database_id) AS DatabaseName,
    encryption_state,
    encryptor_thumbprint,
    key_algorithm,
    key_length
FROM sys.dm_database_encryption_keys;

