USE TestDb;
SELECT * FROM sys.dm_database_encryption_keys;

USE master;
SELECT * FROM sys.certificates WHERE name = 'TestDbCert';

USE TestDb;
GO
SELECT * FROM sys.dm_database_encryption_keys;
GO


EXEC sp_readerrorlog;


SHUTDOWN;
GO
-- Wait a few seconds
STARTUP;
GO
