CREATE TABLE CustomersInfo (
    Id INT PRIMARY KEY IDENTITY(1,1),
    Name NVARCHAR(100) NOT NULL,
    Phone NVARCHAR(15),
    Email NVARCHAR(255),
    Address NVARCHAR(255),
    Gender CHAR(1) CHECK (Gender IN ('F', 'M')),
    MemberPoints INT DEFAULT 0,
    Tiers NVARCHAR(50),
    Status NVARCHAR(10) CHECK (Status IN ('Active', 'Inactive')),
    Birthday DATE
);

INSERT INTO CustomersInfo (Name, Phone, Email, Address, Gender, MemberPoints, Tiers, Status, Birthday)
VALUES 
('Alice Smith', '1234567890', 'alice@example.com', '123 Maple St', 'F', 120, 'Gold', 'Active', '1985-04-15'),
('Bob Johnson', '2345678901', 'bob@example.com', '456 Oak St', 'M', 200, 'Platinum', 'Active', '1978-06-20'),
('Charlie Brown', '3456789012', 'charlie@example.com', '789 Pine St', 'M', 80, 'Silver', 'Inactive', '1992-08-30'),
('Diana Prince', '4567890123', 'diana@example.com', '101 Elm St', 'F', 50, 'Silver', 'Active', '1990-02-10'),
('Ethan Hunt', '5678901234', 'ethan@example.com', '202 Spruce St', 'M', 300, 'Diamond', 'Active', '1982-11-12'),
('Fiona Apple', '6789012345', 'fiona@example.com', '303 Cedar St', 'F', 95, 'Silver', 'Inactive', '1995-03-25'),
('George Clooney', '7890123456', 'george@example.com', '404 Birch St', 'M', 400, 'Diamond', 'Active', '1961-05-06'),
('Hannah Montana', '8901234567', 'hannah@example.com', '505 Cherry St', 'F', 150, 'Gold', 'Active', '1999-07-18'),
('Ian Somerhalder', '9012345678', 'ian@example.com', '606 Willow St', 'M', 120, 'Gold', 'Inactive', '1983-12-08'),
('Jane Austen', '1234509876', 'jane@example.com', '707 Ash St', 'F', 80, 'Silver', 'Active', '1976-10-23'),