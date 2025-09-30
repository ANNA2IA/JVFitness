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
    Correo VARCHAR(100),
    Telefono VARCHAR(100), 
    Registro DATE,
     Fecha_Fin DATE
);

ALTER TABLE Clientes ADD COLUMN membresia VARCHAR(50);
ALTER TABLE Clientes ADD COLUMN promocion VARCHAR(5);
ALTER TABLE Clientes ADD COLUMN Fecha_Fin DATE NULL;



CREATE TABLE Promociones (
    codigoP VARCHAR(5) PRIMARY KEY,
    Nombres VARCHAR(100),
    Precio VARCHAR(100),
    Fecha_Ini DATE,
    Fecha_Fin DATE,
    Descripcion VARCHAR(800)
);


CREATE TABLE Membresias (
    codigo VARCHAR(5) PRIMARY KEY,
    nom VARCHAR(50),
    Precio VARCHAR(800)
);




insert into Administradores values ('A0001','Shantall', 'Alegria','Shanty','info3ia');
select * from Administradores;
select * from Clientes;
select * from Membresias;
DESCRIBE Membresias;
