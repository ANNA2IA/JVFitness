drop database if exists JV;
create database JV ;
use JV;

CREATE TABLE Administradores (
    codigoA VARCHAR(5) ,
    Nombres VARCHAR(100),
    Apellidos VARCHAR(100),
    Usuario VARCHAR(100) PRIMARY KEY,
    Contrasenya VARCHAR(20)
);

CREATE TABLE Clientes (
    codigoC VARCHAR(5) PRIMARY KEY,
    Nombres VARCHAR(100),
    Apellidos VARCHAR(100),
    Correo VARCHAR(20),
    Telefono VARCHAR(100),
    Registro VARCHAR(100)
);

ALTER TABLE Clientes ADD COLUMN membresia VARCHAR(50);
ALTER TABLE Clientes ADD COLUMN promocion VARCHAR(5);


CREATE TABLE Promociones (
    codigoP VARCHAR(5) PRIMARY KEY,
    Nombres VARCHAR(100),
    Precio VARCHAR(100),
    Fecha_Ini VARCHAR(100),
    Fecha_Fin VARCHAR(20),
    Descripcion VARCHAR(800)
);


CREATE TABLE Membresias (
    codigo VARCHAR(5) PRIMARY KEY,
    nom VARCHAR(50),
    Precio VARCHAR(800)
);
ALTER TABLE Membresias ADD COLUMN Fecha_Fin DATE;



insert into Administradores values ('A0001','Shantall', 'Alegria','Shanty','info3ia');
select * from Administradores;
select * from Clientes;
DESCRIBE Membresias;
