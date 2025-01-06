CREATE TABLE Users (
    ID INT IDENTITY(1,1) PRIMARY KEY, -- Auto-incrementing primary key
    Username NVARCHAR(50) NOT NULL UNIQUE, -- Username field, unique constraint
    Password VARBINARY(64) NOT NULL,       -- Password field for storing SHA-256 hash
    CreatedAt DATETIME DEFAULT GETDATE(),
	Role NVARCHAR(20) NOT NULL DEFAULT 'sales'-- Timestamp of creation
);

INSERT INTO Users (Username, Password, Role)
VALUES ('admin1', HASHBYTES('SHA2_256', 'Admin1'),'admin');

INSERT INTO Users (Username, Password, Role)
VALUES ('sales1', HASHBYTES('SHA2_256', 'Sales1'),'sales');

SELECT AdminID, Username, CONVERT(VARCHAR(MAX), Password, 1) AS PasswordHash, CreatedAt
FROM Admin;

DBCC CHECKIDENT ('CustomersInfo', RESEED, 13);

BACKUP DATABASE [TestDb]
TO DISK = N'C:\Backups\TestDb.bak'
WITH NOFORMAT, NOINIT,
NAME = N'TestDb-Full Database Backup',
SKIP, NOREWIND, NOUNLOAD, STATS = 10;

EXEC sp_rename 'Admin', 'Users';

Select * from Users;

DELETE FROM Users
WHERE Username = 'admin1';

ALTER TABLE Users
ADD Role NVARCHAR(20) NOT NULL DEFAULT 'sales';  

EXEC sp_rename 'Users.AdminID', 'ID', 'COLUMN';

INSERT INTO Users (Username, Password,Role)
VALUES ('admin1', HASHBYTES('SHA2_256', 'Admin1'),'Admin');

