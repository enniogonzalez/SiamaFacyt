
INSERT INTO Usuarios(Username,Nombre,Clave,Cargo)
VALUES ('admin','Administrador','e11170b8cbd2d74102651cb967fa28e5','');

INSERT INTO permisosusuarios ("usu_id", "menu") VALUES ('1', 'Sistema');
INSERT INTO permisosusuarios ("usu_id", "menu") VALUES ('1', 'Localizacion');
INSERT INTO permisosusuarios ("usu_id", "menu") VALUES ('1', 'Mantenimiento');
INSERT INTO permisosusuarios ("usu_id", "menu") VALUES ('1', 'Marcas');
INSERT INTO permisosusuarios ("usu_id", "menu") VALUES ('1', 'Partidas');
INSERT INTO permisosusuarios ("usu_id", "menu") VALUES ('1', 'Patrimonio');
INSERT INTO permisosusuarios ("usu_id", "menu") VALUES ('1', 'Proveedores');
INSERT INTO permisosusuarios ("usu_id", "menu") VALUES ('1', 'Reportes');


INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
VALUES(
    'COB-LISDES',
    'Campos para Ordenar Listas Desplegables Busqueda',
    'Lista creada con los nombres de los campos disponibles para hacer un ordenamiento de la tabla Listas Desplegables al momento de hacer una busqueda de los registros de dicha tabla',
    '[{"Valor":"codigo","Opcion":"Codigo","Descripcion":"Campo codigo de la tabla listas desplegables"},{"Valor":"nombre","Opcion":"Nombre","Descripcion":"Campo nombre de la tabla listas desplegables"},{"Valor":"descripcion","Opcion":"Descripcion","Descripcion":"Campo descripcion de la tabla listas desplegables"}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
VALUES(
    'COB-LOCALI',
    'Campos para Ordenar Busqueda Localizaciones',
    'Lista creada con los nombres de los campos disponibles para hacer un ordenamiento de la tabla Localizaciones al momento de hacer una busqueda de los registros de dicha tabla',
    '[{"Valor":"nombre","Opcion":"Nombre","Descripcion":"Campo nombre de la tabla localizaciones"},{"Valor":"ubicacion","Opcion":"Ubicacion","Descripcion":"Campo ubicacion de la tabla localizaciones"},{"Valor":"tipo","Opcion":"Tipo","Descripcion":"Campo Tipo de la tabla localizaciones"}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
VALUES(
    'LOC-TIPO',
    'Tipos de Localizaciones',
    'Tipos de localizaciones',
    '[{"Valor":"Facultad","Opcion":"Facultad","Descripcion":""},{"Valor":"Departamento","Opcion":"Departamento","Descripcion":""},{"Valor":"Dependencia","Opcion":"Dependencia","Descripcion":""},{"Valor":"Laboratorio","Opcion":"Laboratorio","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
VALUES(
    'COB-PARTID',
    'Campos para Ordenar Busqueda Partidas',
    'Lista creada con los nombres de los campos disponibles para hacer un ordenamiento de la tabla Partidas al momento de hacer una busqueda de los registros de dicha tabla',
    '[{"Valor":"codigo","Opcion":"Codigo","Descripcion":"Campo codigo de la tabla partidas"},{"Valor":"nombre","Opcion":"Nombre","Descripcion":"Campo nombre de la tabla partidas"},{"Valor":"observaciones","Opcion":"Observaciones","Descripcion":"Campo observaciones de la tabla partidas"}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
VALUES(
    'COB-PROVEE',
    'Campos para Ordenar Busqueda Proveedores',
    'Lista creada con los nombres de los campos disponibles para hacer un ordenamiento de la tabla Partidas al momento de hacer una busqueda de los registros de dicha tabla',
    '[{"Valor":"rif","Opcion":"Rif","Descripcion":"Campo Rif de la tabla Proveedores"},{"Valor":"raz_soc","Opcion":"Razon Social","Descripcion":"Campo raz_soc de la tabla Proveedores"},{"Valor":"direccion","Opcion":"Direccion","Descripcion":"Campo Direccion de la tabla partidas"}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'UNI-VOLTAJ',
    'Unidades de Medida de Voltaje',
    null,
    '[{"Valor":"Voltio","Opcion":"Voltio","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'UNI-AMPERA',
    'Unidades de Medida de Amperaje',
    null,
    '[{"Valor":"Amperio","Opcion":"Amperio","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'UNI-POTENC',
    'Unidades de Medida de Potencia',
    null,
    '[{"Valor":"Vatios","Opcion":"Vatios","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'UNI-FRECUE',
    'Unidades de Medida de Frecuencia',
    null,
    '[{"Valor":"Hertz","Opcion":"Hertz","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'UNI-CAPACI',
    'Unidades de Medida de Capacidad',
    null,
    '[{"Valor":"Litro","Opcion":"Litro","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'UNI-PRESIO',
    'Unidades de Medida de Presion',
    null,
    '[{"Valor":"Pascal","Opcion":"Pascal","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'UNI-FLUJO',
    'Unidades de Medida de Flujo',
    null,
    '[{"Valor":"Caudal","Opcion":"Caudal","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'UNI-TEMPER',
    'Unidades de Medida de Temperatura',
    null,
    '[{"Valor":"Celsius","Opcion":"Celsius","Descripcion":""},{"Valor":"Fahrenheit","Opcion":"Fahrenheit","Descripcion":""},{"Valor":"Kelvin","Opcion":"Kelvin","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'UNI-PESO',
    'Unidades de Medida de Peso',
    null,
    '[{"Valor":"Gramo","Opcion":"Gramo","Descripcion":""},{"Valor":"Kilogramo","Opcion":"Kilogramo","Descripcion":""},{"Valor":"Tonelada","Opcion":"Tonelada","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'UNI-VELOCI',
    'Unidades de Medida de Velocidad',
    null,
    '[{"Valor":"m\/s","Opcion":"m\/s","Descripcion":""},{"Valor":"km\/h","Opcion":"km\/h","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'BIE-ADQUIS',
    'Tipo de Adquisicion de Bienes',
    null,
    '[{"Valor":"Compra","Opcion":"Compra","Descripcion":""},{"Valor":"Donacion","Opcion":"Donacion","Descripcion":""},{"Valor":"Otro","Opcion":"Otro","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'BIE-ALIMEN',
    'Fuentes de Alimentacion de los Bienes',
    null,
    '[{"Valor":"Agua","Opcion":"Agua","Descripcion":""},{"Valor":"Aire","Opcion":"Aire","Descripcion":""},{"Valor":"Electricidad","Opcion":"Electricidad","Descripcion":""},{"Valor":"Vapor","Opcion":"Vapor","Descripcion":""},{"Valor":"Otros","Opcion":"Otros","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables(Codigo,Nombre,Descripcion,Opciones) 
Values(
    'BIE-USO',
    'Clasificacion de Uso de los Bienes',
    null,
    '[{"Valor":"Apoyo","Opcion":"Apoyo","Descripcion":""},{"Valor":"Basico","Opcion":"Basico","Descripcion":""},{"Valor":"Medico","Opcion":"Medico","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'BIE-TIPO',
    'Tipo de Bien',
    null,
    '[{"Valor":"Fijo","Opcion":"Fijo","Descripcion":""},{"Valor":"Movil","Opcion":"Movil","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'BIE-TECNOL',
    'Tecnologia Predominante del Bien',
    null,
    '[{"Valor":"Electrico","Opcion":"Electrico","Descripcion":""},{"Valor":"Electronico","Opcion":"Electronico","Descripcion":""},{"Valor":"Hidraulico","Opcion":"Hidraulico","Descripcion":""},{"Valor":"Mecanico","Opcion":"Mecanico","Descripcion":""},{"Valor":"Neumatico","Opcion":"Neumatico","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'BIE-RIESGO',
    'Tipos de Riesgo de los Bienes',
    null,
    '[{"Valor":"Bajo","Opcion":"Bajo","Descripcion":""},{"Valor":"Moderado","Opcion":"Moderado","Descripcion":""},{"Valor":"Alto","Opcion":"Alto","Descripcion":""},{"Valor":"Muy Alto","Opcion":"Muy Alto","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'BIE-ESTATU',
    'Estatus de los bienes',
    null,
    '[{"Valor":"Activo","Opcion":"Activo","Descripcion":""},{"Valor":"Reparacion","Opcion":"En reparacion","Descripcion":""},{"Valor":"Inactivo","Opcion":"Inactivo","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'USU-CARGO',
    'Lista de Cargo de los Usuarios',
    null,
    '[{"Valor":"Administrador","Opcion":"Administrador","Descripcion":""},{"Valor":"Decano","Opcion":"Decano","Descripcion":""},{"Valor":"Jefe Mantenimiento","Opcion":"Jefe de Mantenimiento","Descripcion":""},{"Valor":"Jefe Departamento","Opcion":"Jefe de Departamento","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'COB-USUARI',
    'Campos para Ordenar Busqueda Usuarios',
    null,
    '[{"Valor":"username","Opcion":"Usuario","Descripcion":""},{"Valor":"Nombre","Opcion":"Nombre","Descripcion":""},{"Valor":"Cargo","Opcion":"Cargo","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'COB-MARCAS',
    'Campos para Ordenar Busqueda Marcas',
    null,
    '[{"Valor":"Nombre","Opcion":"Nombre","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'COB-BIENES',
    'Campos para Ordenar Busqueda Bienes',
    null,
    '[{"Valor":"B.Nombre","Opcion":"Nombre","Descripcion":""},{"Valor":"B.Inv_UC","Opcion":"Inventario UC","Descripcion":""},{"Valor":"L.Nombre","Opcion":"Localizacion","Descripcion":""},{"Valor":"M.Nombre","Opcion":"Marca","Descripcion":""},{"Valor":"B.estatus","Opcion":"Estatus","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'COB-PIEZAS',
    'Campos para Ordenar Busqueda Piezas',
    null,
    '[{"Valor":"B.Inv_UC","Opcion":"Inventario UC","Descripcion":""},{"Valor":"P.Nombre","Opcion":"Nombre","Descripcion":""},{"Valor":"M.Nombre","Opcion":"Marca","Descripcion":""},{"Valor":"B.nombre","Opcion":"Bien","Descripcion":""},{"Valor":"P.estatus","Opcion":"Estatus","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'COB-CORREC',
    'Campos para Ordenar Busqueda Mantenimiento Correctivo',
    null,
    '[{"Valor":"MCO.Documento","Opcion":"Documento","Descripcion":""},{"Valor":"B.Nombre","Opcion":"Bien","Descripcion":""},{"Valor":"MCO.Estatus","Opcion":"Estatus","Descripcion":""},{"Valor":"MCO.fec_ini","Opcion":"Inicio","Descripcion":""},{"Valor":"MCO.fec_fin","Opcion":"Fin","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'COB-PLANTI',
    'Campos para Ordenar Busqueda Plantillas de Mantenimiento',
    null,
    '[{"Valor":"PLM.documento","Opcion":"Documento","Descripcion":""},{"Valor":"B.nombre","Opcion":"Bien","Descripcion":""},{"Valor":"PLM.estatus","Opcion":"Estatus","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'COB-PREVEN',
    'Campos para Ordenar Busqueda Mantenimientos Preventivos',
    null,
    '[{"Valor":"MAN.Documento","Opcion":"Documento","Descripcion":""},{"Valor":"B.Nombre","Opcion":"Bien","Descripcion":""},{"Valor":"MAN.Estatus","Opcion":"Estatus","Descripcion":""},{"Valor":"MAN.fec_ini","Opcion":"Inicio","Descripcion":""},{"Valor":"MAN.fec_fin","Opcion":"Fin","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'COB-AJUSTE',
    'Campos para Ordenar Busqueda Ajustes',
    null,
    '[{"Valor":"AJU.Documento","Opcion":"Documento","Descripcion":""},{"Valor":"B.nombre","Opcion":"Bien","Descripcion":""},{"Valor":"AJU.Estatus","Opcion":"Estatus","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'COB-CAMBIO',
    'Campos para Ordenar Busqueda Cambio de Estatus',
    null,
    '[{"Valor":"CAM.Documento","Opcion":"Documento","Descripcion":""},{"Valor":"Cam.doc_estatus","Opcion":"Estatus Documento","Descripcion":""},{"Valor":"B.nombre","Opcion":"Bien","Descripcion":""},{"Valor":"Cam.bie_estatus","Opcion":"Estatus Bien","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'COB-FALLAS',
    'Campos para Ordenar Busqueda Fallas',
    null,
    '[{"Valor":"Nombre","Opcion":"Nombre","Descripcion":""},{"Valor":"Tipo","Opcion":"Tipo","Descripcion":""},{"Valor":"Observacion","Opcion":"Observacion","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'COB-TIPOPI',
    'Campos para Ordenar Busqueda Tipo de Marca',
    null,
    '[{"Valor":"Nombre","Opcion":"Nombre","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'COB-HERRAM',
    'Campos para Ordenar Busqueda Herramientas',
    null,
    '[{"Valor":"Nombre","Opcion":"Nombre","Descripcion":""}]'
);

INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    'COB-OBRERO',
    'Campos para Ordenar Busqueda Obreros',
    null,
    '[{"Valor":"cedula","Opcion":"Cedula","Descripcion":""},{"Valor":"nombre","Opcion":"Nombre","Descripcion":""}]'
);

/*
INSERT INTO Listas_Desplegables( Codigo,Nombre,Descripcion,Opciones) 
Values(
    '',
    '',
    null,
    ''
);
*/