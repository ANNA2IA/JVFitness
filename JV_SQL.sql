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
    Fecha_Nac VARCHAR(100),
    Correo VARCHAR(20),
    Telefono VARCHAR(100),
    Registro VARCHAR(100)
);

CREATE TABLE Promociones (
    codigoP VARCHAR(5) PRIMARY KEY,
    Nombres VARCHAR(100),
    Precio VARCHAR(100),
    Fecha_Ini VARCHAR(100),
    Fecha_Fin VARCHAR(20),
    Descripcion VARCHAR(800)
);

CREATE TABLE Planes (
    codigoPL VARCHAR(5) PRIMARY KEY,
    Nombres VARCHAR(100),
    Duracion VARCHAR(100),
    Precio VARCHAR(100)
);

CREATE TABLE Membresias (
    codigo VARCHAR(5) PRIMARY KEY,
    codigoC VARCHAR(5),
   codigoP  VARCHAR(5),
   codigoPL VARCHAR(5),
   Fecha_Ini VARCHAR(20),
    Fecha_Fin VARCHAR(20),
    Precio VARCHAR(800),
    metodo VARCHAR(100),
    FOREIGN KEY (codigoC) REFERENCES Clientes(codigoC),
    FOREIGN KEY (codigoP) REFERENCES Promociones(codigoP),
    FOREIGN KEY (codigoPL) REFERENCES Planes(codigoPL)
);

insert into Administradores values ('A0001','Shantall', 'Alegria','Shanty','info3ia');
select * from Administradores;