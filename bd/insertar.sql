
INSERT INTO localizaciones(nombre,ubicacion,tipo,cap_amp)
VALUES  ('Facyt','Facyt','Facultad',0.00),
        ('Computacion','Computacion','Departamento',0.00),
        ('Biologia','Biologia','Departamento',0.00),
        ('Fisica','Fisica','Departamento',0.00),
        ('Matematica','Matematica','Departamento',0.00),
        ('Quimica','Quimica','Departamento',0.00);

INSERT INTO Pertenece(loh_id,lop_id)
VALUES  (2,1),
        (4,1),
        (5,1),
        (6,1);


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

INSERT INTO Marcas(nombre)
VALUES ('kia'),('Chevrolet'),('HP'),('Intel'),('NVIDIA');


INSERT INTO Usuarios(Username,Nombre,Clave,Cargo)
VALUES ('angellopez','Angel Lopez','123456','');

INSERT INTO Bienes(     nombre, modelo, bie_ser, inv_uc, mar_id, pro_id, 
                        loc_id, par_id, custodio, fec_fab, fec_adq, fec_ins, 
                        tip_adq, fue_ali, cla_uso, tipo, med_vol, uni_vol, 
                        med_amp, uni_amp, med_pot, uni_pot, med_fre, uni_fre, 
                        med_cap, uni_cap, med_pre, uni_pre, med_flu, uni_flu, 
                        med_tem, uni_tem, med_pes, uni_pes, med_vel, uni_vel, 
                        tec_pre, riesgo, rec_fab, estatus, usu_cre, fec_cre, 
                        usu_mod, fec_mod, observaciones)

VALUES ('Aveo 2013 LT','LT','12354A5L','1','2','3','1','2','2',
        '2018-09-14','2018-09-13','2018-09-06','Compra','Otros',
        'Basico','Movil','0.0000','Voltio','0.0000','Amperio',
        '0.0000','Vatios','0.0000','Hertz','0.0000','Litro',
        '0.0000','Pascal','0.0000','Caudal','0.0000','Celsius',
        '0.0000','Gramo','0.0000','m/s','Mecanico','Bajo',null,
        'Activo','1','2018-09-06 21:19:07.31358','1','2018-09-06 21:19:07.31358',null);


INSERT INTO Piezas (bie_id, nombre, inv_uc, modelo, pie_ser, pro_id, 
                        par_id, mar_id, fec_fab, fec_adq, fec_ins, tip_adq, 
                        estatus, usu_cre, fec_cre, usu_mod, fec_mod, observaciones)
VALUES  ('1','Amortiguador Izquierdo','2342SFS5554','Platino','SD54233421A','3','2','2','2018-09-06','2018-09-13','2018-09-21','Compra','Activo','1','2018-09-06 21:24:27.310642','1','2018-09-06 21:24:27.310642',null),
        ('1','Amortiguador Derecho','2342SFS34323','Platino','SD54LSDF21A','3','2','2','2018-09-06','2018-09-13','2018-09-21','Compra','Activo','1','2018-09-07 01:43:00.882001','1','2018-09-07 01:43:00.882001',null),
        (null,'Amortiguador REPUESTO','2342SFS34323SS','Platino','SD54LSDF21A','3','2','2','2018-09-06','2018-09-13','2018-09-21','Compra','Activo','1','2018-09-07 01:43:55.503738','1','2018-09-07 01:43:55.503738',null),
        (null,'Aleternador','2342SFSDF3445','CHIVERA','2AASD32345','3','2','2','2018-09-06','2018-09-13','2018-09-21','Compra','Activo','1','2018-09-07 01:46:01.902304','1','2018-09-07 01:46:01.902304',null),
        ('1','Radiador','32DFZAS113','TXdeluxe','TXKSJDELUX','3','2','2','2018-09-06','2018-09-13','2018-09-21','Compra','Activo','1','2018-09-07 01:48:14.268667','1','2018-09-07 01:49:54.961485',null),
        ('1','Caucho delantero Izquierdo','ASDD345AA','AXDPIRELLI','TXKSJDELUX','3','2','2','2018-09-06','2018-09-13','2018-09-21','Compra','Activo','1','2018-09-07 01:50:55.594752','1','2018-09-07 01:52:46.806885',null);
