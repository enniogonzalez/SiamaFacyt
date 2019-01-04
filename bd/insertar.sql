INSERT INTO localizaciones(nombre,ubicacion,tipo,cap_amp,Secuencia,usu_cre)
VALUES  ('Facyt','Facyt','Facultad',0.00,'-1-',1),
        ('Computacion','Computacion','Departamento',0.00,'-2-',1),
        ('Biologia','Biologia','Departamento',0.00,'-3-',1),
        ('Fisica','Fisica','Departamento',0.00,'-4-',1),
        ('Matematica','Matematica','Departamento',0.00,'-5-',1),
        ('Quimica','Quimica','Departamento',0.00,'-6-',1);



INSERT INTO Partidas(codigo,nombre)
VALUES  ('01','01'),
        ('0101','0101'),
        ('02','02'),
        ('0201','0201');

INSERT INTO essubpartida(pah_id,pap_id)
VALUES  (2,1),(4,3);


INSERT INTO Proveedores (rif,raz_soc,reg_nac_con,direccion)
VALUES  ('24918319','Ennio Gonzalez','24918319','direccion'),
        ('5578595','Elizabeth Castillo','5578595','direccion'),
        ('2324846','Chevrolet Venezuela CA','AS23434AS','Caracas');


INSERT INTO "public"."marcas" VALUES ('1', 'kia', null);
INSERT INTO "public"."marcas" VALUES ('2', 'Chevrolet', null);
INSERT INTO "public"."marcas" VALUES ('3', 'HP', null);
INSERT INTO "public"."marcas" VALUES ('4', 'Intel', null);
INSERT INTO "public"."marcas" VALUES ('5', 'NVIDIA', null);
INSERT INTO "public"."marcas" VALUES ('6', 'Samsung', null);
INSERT INTO "public"."marcas" VALUES ('7', 'haier', null);

SELECT setval('marcas_mar_id_seq', 7, true);

INSERT INTO "public"."bienes" VALUES ('1', 'Aveo 2013 LT', 'LT', '12354A5L', '1', '2', '3', '1', '2', '1', '2018-09-14', '2018-09-13', '2018-09-06', 'Compra', 'Otros', 'Basico', 'Movil', '0.0000', 'Voltio', '0.0000', 'Amperio', '0.0000', 'Vatios', '0.0000', 'Hertz', '0.0000', 'Litro', '0.0000', 'Pascal', '0.0000', 'Caudal', '0.0000', 'Celsius', '0.0000', 'Gramo', '0.0000', 'm/s', 'Mecanico', 'Bajo', null, 'Activo', '1', '2018-09-06 21:19:07.31358', '1', '2018-11-25 17:31:31.105032', null);
INSERT INTO "public"."bienes" VALUES ('2', 'Aire Acondicionado Split', '1321-EE-K', '1599674', null, '7', '3', '1', '4', '1', '2018-11-15', '2018-11-19', '2018-11-13', 'Compra', 'Electricidad', 'Apoyo', 'Fijo', '2-3', 'Voltio', '02-3', 'Amperio', '2-3', 'Vatios', '2-3', 'Hertz', '2-3', 'Litro', '2-3', 'Pascal', '2-3', 'Caudal', '2-3', 'Celsius', '2-3', 'Gramo', '2-3', 'm/s', 'Electrico', 'Moderado', null, 'Inactivo', '1', '2018-11-25 17:36:31.87271', '1', '2018-11-25 17:36:31.87271', null);

SELECT setval('bienes_bie_id_seq', 2, true);

SELECT setval('piezas_pie_id_seq', 0, true);

INSERT INTO "public"."piezas" VALUES ('1', '1', 'Amortiguador Izquierdo', '2342SFS5554', 'Platino', 'SD54233421A', '3', '2', '2',1, '2018-09-06', '2018-09-13', '2018-09-21', 'Compra', 'Activo', '1', '2018-09-06 21:24:27.310642', '1', '2018-09-06 21:24:27.310642', null);
INSERT INTO "public"."piezas" VALUES ('2', null, 'Amortiguador Derecho', '2342SFS34323', 'Platino', 'SD54LSDF21A', '3', '2', '2',1, '2018-09-06', '2018-09-13', '2018-09-21', 'Compra', 'Activo', '1', '2018-09-07 01:43:00.882001', '1', '2018-11-25 18:02:17.024545', null);
INSERT INTO "public"."piezas" VALUES ('3', null, 'Amortiguador REPUESTO', '2342SFS34323SS', 'Platino', 'SD54LSDF21A', '3', '2', '2',1, '2018-09-06', '2018-09-13', '2018-09-21', 'Compra', 'Activo', '1', '2018-09-07 01:43:55.503738', '1', '2018-09-07 01:43:55.503738', null);
INSERT INTO "public"."piezas" VALUES ('4', '1', 'Aleternador', '2342SFSDF3445', 'CHIVERA', '2AASD32345', '3', '2', '2',1, '2018-09-06', '2018-09-13', '2018-09-21', 'Compra', 'Inactivo', '1', '2018-09-07 01:46:01.902304', '1', '2018-11-25 18:02:17.024545', null);
INSERT INTO "public"."piezas" VALUES ('5', null, 'Radiador', '32DFZAS113', 'TXdeluxe', 'TXKSJDELUX', '3', '2', '2',1, '2018-09-06', '2018-09-13', '2018-09-21', 'Compra', 'Activo', '1', '2018-09-07 01:48:14.268667', '1', '2018-09-07 01:49:54.961485', null);
INSERT INTO "public"."piezas" VALUES ('6', '2', 'Caucho delantero Izquierdo', 'ASDD345AA', 'AXDPIRELLI', 'TXKSJDELUX', '3', '2', '2',1, '2018-09-06', '2018-09-13', '2018-09-21', 'Compra', 'Activo', '1', '2018-09-07 01:50:55.594752', '1', '2018-11-25 18:08:46.340836', null);
INSERT INTO "public"."piezas" VALUES ('7', '1', 'Motor de Aire Acondicionador haire', '2342342', '2424jkl', '321321543265', '3', '3', '7',1, '2018-11-17', '2018-11-20', '2018-11-30', 'Compra', 'Activo', '1', '2018-11-25 17:39:03.525196', '1', '2018-11-25 18:52:44.873799', null);
INSERT INTO "public"."piezas" VALUES ('8', null, 'Condensador aire acondicionado', null, '2342342', 'KERIA3465432', '3', '4', '7',1, '2018-11-28', '2018-11-08', '2018-11-15', 'Donacion', 'Activo', '1', '2018-11-25 17:39:45.808732', '1', '2018-11-25 17:39:45.808732', null);


SELECT setval('piezas_pie_id_seq', 8, true);
