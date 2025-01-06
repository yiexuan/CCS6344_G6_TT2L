Use TestDb
Go

CREATE TABLE AuditLog (
    LogID INT IDENTITY PRIMARY KEY,
    Action NVARCHAR(50),
    PerformedBy NVARCHAR(50),
    TableName NVARCHAR(50),
    Timestamp DATETIME DEFAULT GETDATE()
);



USE TestDb; -- Ensure you're in the correct database
GO

IF EXISTS (SELECT * FROM sysobjects WHERE name = 'trg_AuditChanges' AND xtype = 'TR')
BEGIN
    DROP TRIGGER trg_AuditChanges;
END
GO

-- Create Trigger for INSERT, UPDATE, DELETE operations
CREATE TRIGGER trg_AuditChanges
ON CustomersInfo
AFTER INSERT, UPDATE, DELETE
AS
BEGIN
    -- Declare variables
    DECLARE @Action NVARCHAR(50);
    DECLARE @PerformedBy NVARCHAR(50);
    DECLARE @Timestamp DATETIME;
    DECLARE @TableName NVARCHAR(50);

    -- Set constant values
    SET @TableName = 'CustomersInfo';
    SET @PerformedBy = SYSTEM_USER;  -- This retrieves the username of the current user.
    SET @Timestamp = GETDATE();

    -- Handling INSERT operation
    IF EXISTS (SELECT * FROM inserted) AND NOT EXISTS (SELECT * FROM deleted)
    BEGIN
        SET @Action = 'INSERT';
        INSERT INTO AuditLog (Action, PerformedBy, TableName, Timestamp)
        SELECT @Action, @PerformedBy, @TableName, @Timestamp;
    END

    -- Handling UPDATE operation
    IF EXISTS (SELECT * FROM inserted) AND EXISTS (SELECT * FROM deleted)
    BEGIN
        SET @Action = 'UPDATE';
        INSERT INTO AuditLog (Action, PerformedBy, TableName, Timestamp)
        SELECT @Action, @PerformedBy, @TableName, @Timestamp;
    END

    -- Handling DELETE operation
    IF EXISTS (SELECT * FROM deleted) AND NOT EXISTS (SELECT * FROM inserted)
    BEGIN
        SET @Action = 'DELETE';
        INSERT INTO AuditLog (Action, PerformedBy, TableName, Timestamp)
        SELECT @Action, @PerformedBy, @TableName, @Timestamp;
    END
END;
GO






Select * from AuditLog;




DELETE FROM AuditLog WHERE LogID = 1002;

DELETE FROM AuditLog;


DBCC CHECKIDENT ('AuditLog', RESEED, 0);
