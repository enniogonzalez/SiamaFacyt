CREATE DATABASE SIAMAUC;

\c siamauc

CREATE TABLE Usuario(
    Nombre VARCHAR(20) PRIMARY KEY,
    clave  VARCHAR(20)
);

INSERT INTO Usuario VALUES ('ennio','fuji0918');