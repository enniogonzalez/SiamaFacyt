CREATE DATABASE siamafacyt WITH 
ENCODING 'UTF8' owner "adminsiama";

\c siamafacyt

/*Tablas de Primer Nivel*/
CREATE TABLE Partidas(
	PAR_ID			SERIAL PRIMARY KEY,
	Codigo			VARCHAR(10) NOT NULL UNIQUE,
	Nombre			VARCHAR(100) NOT NULL,
	Observaciones	TEXT
);

CREATE TABLE Proveedores(
	PRO_ID			SERIAL			PRIMARY KEY,
	Rif				VARCHAR(20)		NOT NULL UNIQUE,
	Raz_Soc			VARCHAR(100)	NOT NULL,
	Reg_Nac_Con		VARCHAR(50)		NOT NULL,
	Direccion		TEXT			NOT NULL,
	Telefonos		VARCHAR(100),
	Correo			VARCHAR(100),
	Observaciones	TEXT
);

CREATE TABLE Marcas(
	MAR_ID			SERIAL			PRIMARY KEY,
	Nombre			VARCHAR(100)	NOT NULL UNIQUE,
	Observaciones	TEXT
);

CREATE TABLE Usuarios(
	USU_ID			SERIAL				PRIMARY KEY,
	Username		VARCHAR(25)			NOT NULL UNIQUE,
	Nombre			VARCHAR(100)		NOT NULL,
	Clave			VARCHAR(100)		NOT NULL,
	Cargo			VARCHAR(100)		NOT NULL,
	Correo			VARCHAR(100),
	Usu_Cre			INT, --Usuario Creador
	Fec_Cre			TIMESTAMP			NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT, --Usuario Modificador
	Fec_Mod			TIMESTAMP			NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Observaciones	TEXT,
	
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

CREATE TABLE PermisosUsuarios(
	USU_ID	INT				NOT NULL,
	Menu	VARCHAR(100)	NOT NULL,
	PRIMARY KEY (usu_id, Menu),
	
	FOREIGN KEY (USU_ID) References Usuarios ON DELETE CASCADE
);

CREATE TABLE Listas_Desplegables(
	LD_ID 		SERIAL			PRIMARY KEY,
	Codigo 		VARCHAR(10)		NOT NULL UNIQUE,
	Nombre 		VARCHAR(100)	NOT NULL,
	Descripcion TEXT			NULL,
	Opciones	TEXT			NOT NULL
);

CREATE TABLE Localizaciones(
	LOC_ID			SERIAL				PRIMARY KEY,
	Nombre			VARCHAR(100)		NOT NULL,
	Ubicacion		TEXT				NOT NULL,
	Tipo			VARCHAR(100)		NOT NULL,
	Secuencia		TEXT				NOT NULL DEFAULT '',
	Cap_Amp			DECIMAL(10,4)		NOT NULL,
	Usu_Cre			INT					NOT NULL,--Usuario Creador
	Fec_Cre			TIMESTAMP			NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT					NULL,--Usuario Creador
	Fec_Mod			TIMESTAMP			NOT NULL DEFAULT(NOW()),
	Observaciones	TEXT,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

/*Tablas de Segundo Nivel*/
CREATE TABLE Pertenece(
	LOH_ID	INT PRIMARY KEY, --Locacion hijo
	LOP_ID	INT NOT NULL,	--Locacion Padre
	FOREIGN KEY (LOH_ID) REFERENCES  Localizaciones ON DELETE CASCADE,
	FOREIGN KEY (LOP_ID) REFERENCES Localizaciones
);

CREATE TABLE EsSubpartida(
	PAH_ID	INT PRIMARY KEY, --partida hijo (subpartida)
	PAP_ID	INT NOT NULL, --Partida padre
	FOREIGN KEY (PAH_ID) References Partidas  ON DELETE CASCADE,
	FOREIGN KEY (PAP_ID) References Partidas
);

CREATE TABLE Fallas(
	FAL_ID			SERIAL			PRIMARY KEY,
	Nombre			VARCHAR(255)	NOT NULL,
	Tipo			VARCHAR(100)	NOT NULL,
	Usu_Cre			INT				NOT NULL,--Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NULL,--Usuario Creador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()),
	Observaciones	TEXT			NULL,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

CREATE TABLE Bienes(
	BIE_ID			SERIAL				PRIMARY KEY,
	Nombre			VARCHAR(100)		NOT NULL,
	Modelo			VARCHAR(100)		NOT NULL,
	BIE_SER			VARCHAR(100)		NOT NULL,			--Serial
	Inv_UC			VARCHAR(100)		NULL,	--Inventario UC

	MAR_ID			INT					NOT NULL,			--Marca
	PRO_ID			INT					NOT NULL,			--Proveedor
	LOC_ID			INT					NOT NULL,			--Locacion
	PAR_ID			INT					NOT NULL,			--Partida
	Custodio		INT					NOT NULL,			--Custodio

	Fec_Fab			DATE				NOT NULL,			--Fecha de fabricacion
	Fec_adq			DATE				NOT NULL,			--fecha de adquisicion
	Fec_ins			DATE				NOT NULL,			--Fecha de instalacion
	Tip_Adq			VARCHAR(100)		NOT NULL,			--Tipo de adquisicion (mover a tabla)
	Fue_Ali			VARCHAR(100)		NOT NULL,			--Fuentes de Alimentacion
	Cla_Uso			VARCHAR(100)		NOT NULL,			--Clasificacion por uso
	Tipo			VARCHAR(100)		NOT NULL,			--Tipo del bien (mover a tabla)
	med_vol			VARCHAR(100)		NOT NULL,			--medida de voltaje
	uni_vol			VARCHAR(100)		NOT NULL,			--unidad de voltaje
	med_amp			VARCHAR(100)		NOT NULL,			--medida de amperaje
	uni_amp			VARCHAR(100)		NOT NULL,			--unidad de amperaje
	med_pot			VARCHAR(100)		NOT NULL,			--medida de potencia
	uni_pot			VARCHAR(100)		NOT NULL,			--unidad de potencia
	med_fre			VARCHAR(100)		NOT NULL,			--medida de frecuencia
	uni_fre			VARCHAR(100)		NOT NULL,			--unidad de frecuencia
	med_cap			VARCHAR(100)		NOT NULL,			--medida de capacidad
	uni_cap			VARCHAR(100)		NOT NULL,			--unidad de capacidad
	med_pre			VARCHAR(100)		NOT NULL,			--medida de presion
	uni_pre			VARCHAR(100)		NOT NULL,			--unidad de presion
	med_flu			VARCHAR(100)		NOT NULL,			--medida de flujo
	uni_flu			VARCHAR(100)		NOT NULL,			--unidad de flujo
	med_tem			VARCHAR(100)		NOT NULL,			--medida de temperatura
	uni_tem			VARCHAR(100)		NOT NULL,			--unidad de temperatura
	med_pes			VARCHAR(100)		NOT NULL,			--medida de peso
	uni_pes			VARCHAR(100)		NOT NULL,			--unidad de peso
	med_vel			VARCHAR(100)		NOT NULL,			--medida de velocidad
	uni_vel			VARCHAR(100)		NOT NULL,			--unidad de velocidad
	Tec_Pre			VARCHAR(100)		NOT NULL,			--Tecnolog�a predominante
	Riesgo			VARCHAR(100)		NOT NULL,		
	Rec_Fab			TEXT				,					--Recomendaciones del Fabricante,
	Estatus			VARCHAR(100)		NOT NULL,
	Usu_Cre			INT					NOT NULL,			--Usuario Creador
	Fec_Cre			TIMESTAMP			NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT					NOT NULL, 			--Usuario Creador
	Fec_Mod			TIMESTAMP			NOT NULL DEFAULT(NOW()),
	Observaciones	TEXT,

	FOREIGN KEY (PRO_ID) References Proveedores,
	FOREIGN KEY (PAR_ID) References Partidas,
	FOREIGN KEY (LOC_ID) References Localizaciones,
	FOREIGN KEY (MAR_ID) References Marcas,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios,
	FOREIGN KEY (Custodio) References Usuarios

);

CREATE TABLE Piezas(
	PIE_ID			SERIAL				PRIMARY KEY,
	BIE_ID			INT					NULL,
	Nombre			VARCHAR(100)		NOT NULL,--bien al que pertenece
	Inv_UC			VARCHAR(100)		NULL,
	Modelo			VARCHAR(100)		NOT NULL,
	Pie_ser			VARCHAR(100)		NOT NULL,--serial

	PRO_ID			INT					NOT NULL,--proveedor
	PAR_ID			INT					NOT NULL,--partida
	MAR_ID			INT					NOT NULL,--Marca

	Fec_Fab			DATE				NOT NULL,--fecha de fabricacion
	Fec_adq			DATE				NOT NULL,--fecha de adquisicion
	Fec_ins			DATE				NOT NULL,--fecha de instalacion
	Tip_Adq			VARCHAR(100)		NOT NULL,--tipo de adquisicion
	Estatus			VARCHAR(100)		NOT NULL,
	Usu_Cre			INT					NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP			NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT					NOT NULL, --Usuario Creador
	Fec_Mod			TIMESTAMP			NOT NULL DEFAULT(NOW()),
	Observaciones	TEXT,
	FOREIGN KEY (PAR_ID) References Partidas,
	FOREIGN KEY (BIE_ID) References Bienes,
	FOREIGN KEY (MAR_ID) References Marcas,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios,
	FOREIGN KEY (PRO_ID) References Proveedores
);

--Plantilla para ejecutar mantenimiento
CREATE TABLE PlantillaMantenimiento(
	PLM_ID			SERIAL			PRIMARY KEY,
	Documento		VARCHAR(10)		NOT NULL UNIQUE,
	BIE_ID			INT				NOT NULL,--Bien al que se le esta haciendo plantilla
	Estatus			VARCHAR(100)	NOT NULL,--Solicitado, Aprobado
	Frecuencia		INT				NOT NULL,--Frecuencia mantenimiento en meses
	Fec_Ult			DATE			NOT NULL, --fecha ultimo mantenimiento
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NOT NULL, --Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Usu_Apr			INT				NULL, --Usuario Aprobador
	Fec_Apr			TIMESTAMP		NULL, --fecha aprobacion
	Observaciones	TEXT,
	FOREIGN KEY (BIE_ID) References Bienes,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Apr) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

CREATE TABLE PlantillaMantenimientoTarea(
	PMT_ID			SERIAL			PRIMARY KEY,
	PLM_ID			INT				NOT NULL,--Plantilla de mantenimiento
	Titulo			VARCHAR(20)		NOT NULL,--Titulo tarea
	PIE_ID			INT				NOT NULL,--Pieza a la que se le est� haciendo mantenimiento
	Minutos			INT				NOT NULL,--Tiempo Estimado en el que se realizara el mantenimiento
	Descripcion		TEXT			NOT NULL,
	Herramientas	TEXT			NOT NULL,
	Usu_Cre			INT				NOT NULL,--Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()),--fecha Creacion
	Usu_Mod			INT				NOT NULL,--Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), 
	Observaciones	TEXT,
	FOREIGN KEY (PLM_ID) References PlantillaMantenimiento  ON DELETE CASCADE,
	FOREIGN KEY (PIE_ID) References Piezas,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

--Mantenimientos Correctivos
CREATE TABLE MantenimientoCorrectivo(
	MCO_ID			SERIAL	PRIMARY KEY,
	BIE_ID			INT				NOT NULL,
	Documento		VARCHAR(10)		NOT NULL UNIQUE,
	Estatus			VARCHAR(100)	NOT NULL, --Solicitado, Afectado, Aprobado, Realizado
	Fec_Ini			DATE			NOT NULL,
	Fec_Fin			DATE			NOT NULL,
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NOT NULL, --Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Usu_Apr			INT				NULL, --Usuario Aprobador
	Fec_Apr			TIMESTAMP		NULL, --fecha aprobacion
	Observaciones	TEXT,
	FOREIGN KEY (BIE_ID) References Bienes,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Apr) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios,
	CONSTRAINT CHK_Fechas_MCO CHECK (Fec_Ini <= Fec_Fin)
);

CREATE TABLE CambioCorrectivo(
	CCO_ID			SERIAL	PRIMARY KEY,
	MCO_ID			INT				NOT NULL,--Mantenimiento Correctivo
	PDA_ID			INT				NOT NULL,--Pieza da�ada
	USU_ID			INT				NULL,
	PRO_ID			INT				NULL,
	BIE_ID			INT				NULL,--Bien del cual proviene la pieza cambiada
	PCA_ID			INT				NOT NULL,--Pieza cambiada
	ESTATUS			VARCHAR(100)	NOT NULL,--Solicitado, Aprobado, Realizado
	Fec_Ini			DATE			NOT NULL,
	Fec_Fin			DATE			NOT NULL,
	fal_id			INT				NOT NULL,
	Usu_Cre			INT				NOT NULL,--Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()),--fecha Creacion
	Usu_Mod			INT				NOT NULL,--Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), 
	Observaciones	TEXT,

	FOREIGN KEY (PDA_ID) References Piezas,
	FOREIGN KEY (PCA_ID) References Piezas,
	FOREIGN KEY (fal_id) References Fallas,
	FOREIGN KEY (USU_ID) References Usuarios,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios,
	FOREIGN KEY (BIE_ID) References Bienes,
	FOREIGN KEY (MCO_ID) References MantenimientoCorrectivo ON DELETE CASCADE,
	CONSTRAINT CHK_EJECUTOR_CCO CHECK ((USU_ID is null and PRO_ID is not null) OR 
									(USU_ID is not null and PRO_ID is null) ),
	CONSTRAINT CHK_Piezas_Diferentes CHECK (PDA_ID <> PCA_ID),
	CONSTRAINT CHK_Fechas_CCO CHECK (Fec_Ini <= Fec_Fin),
	CONSTRAINT UQ_CCO_MCO_PDA UNIQUE (MCO_ID,PDA_ID),
	CONSTRAINT UQ_CCO_MCO_PCA UNIQUE (MCO_ID,PCA_ID)
);

CREATE TABLE ReparacionCorrectiva(
	RCO_ID			SERIAL	PRIMARY KEY,
	MCO_ID			INT				NOT NULL,
	PIE_ID			INT				NOT NULL,--Pieza da�ada
	USU_ID			INT				NULL,--Usuario
	PRO_ID			INT				NULL,--Proveedor
	ESTATUS			VARCHAR(100)	NOT NULL,--Solicitado, Aprobado, Realizado
	Fec_Ini			DATE			NOT NULL,
	Fec_Fin			DATE			NOT NULL,
	fal_id			INT				NOT NULL,
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NOT NULL, --Usuario Creador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()),
	Observaciones	TEXT,
	FOREIGN KEY (PIE_ID) References Piezas,
	FOREIGN KEY (fal_id) References Fallas,
	FOREIGN KEY (MCO_ID) References MantenimientoCorrectivo ON DELETE CASCADE,
	FOREIGN KEY (USU_ID) References Usuarios,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios,
	FOREIGN KEY (PRO_ID) References Proveedores,
	CONSTRAINT CHK_EJECUTOR_RCO CHECK ((USU_ID is null and PRO_ID is not null) OR 
									(USU_ID is not null and PRO_ID is null)),
	CONSTRAINT CHK_Fechas_RCO CHECK (Fec_Ini <= Fec_Fin),
	CONSTRAINT UQ_RCO_MCO_PIE UNIQUE (MCO_ID,PIE_ID)
);

--Ejecucion de Mantenimientos
CREATE TABLE Mantenimiento(
	MAN_ID			SERIAL			PRIMARY KEY,
	PLM_ID			INT				NOT NULL, --Plantilla de mantenimiento
	Documento		VARCHAR(10)		NOT NULL UNIQUE,
	Estatus			VARCHAR(100)	NOT NULL,--Solicitado, Aprobado, Afectado, Realizado
	BIE_ID			INT				NOT NULL,
	Fec_Ini			DATE			NOT NULL,
	Fec_Fin			DATE			NOT NULL,
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NOT NULL, --Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Usu_Apr			INT				NULL, --Usuario Aprobador
	Fec_Apr			TIMESTAMP		NULL, --fecha aprobacion
	Observaciones	TEXT,
	FOREIGN KEY (BIE_ID) References Bienes,
	FOREIGN KEY (PLM_ID) References PlantillaMantenimiento,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Apr) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

CREATE TABLE MantenimientoTarea(
	MTA_ID			SERIAL	PRIMARY KEY,
	MAN_ID			INT				NOT NULL,
	PIE_ID			INT				NOT NULL,
	Titulo			VARCHAR(20)		NOT NULL,--Titulo tarea
	ESTATUS			VARCHAR(100)	NOT NULL,--Solicitado, Aprobado, Realizado
	USU_ID			INT				NULL,
	PRO_ID			INT				NULL,
	Min_Asi			INT				NOT NULL,--Minutos Asignados
	Min_Eje			INT				NOT NULL,--Minutos Ejecutados
	Fec_Ini			DATE			NOT NULL,
	Fec_Fin			DATE			NOT NULL,
	Descripcion		TEXT			NOT NULL, 
	Herramientas	TEXT			NOT NULL, 
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NOT NULL, --Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Observaciones	TEXT,
	CONSTRAINT CHK_EJECUTOR_MTA CHECK ((USU_ID is null and PRO_ID is not null) OR 
									(USU_ID is not null and PRO_ID is null) ),
	FOREIGN KEY (MAN_ID) References Mantenimiento ON DELETE CASCADE,
	FOREIGN KEY (PIE_ID) References Piezas,
	FOREIGN KEY (USU_ID) References Usuarios,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios,
	FOREIGN KEY (PRO_ID) References Proveedores
);

--Ajustes
CREATE TABLE Ajustes(
	AJU_ID			SERIAL			PRIMARY KEY,
	Documento		VARCHAR(10)		NOT NULL UNIQUE,
	BIE_ID			INT				NOT NULL,--Bien al que se le esta haciendo ajuste
	Estatus			VARCHAR(100)	NOT NULL,--Solicitado, Aprobado
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NOT NULL, --Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Usu_Apr			INT				NULL, --Usuario Aprobador
	Fec_Apr			TIMESTAMP		NULL, --fecha aprobacion
	Observaciones	TEXT,
	FOREIGN KEY (BIE_ID) References Bienes,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Apr) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

CREATE TABLE AjustesAccion(
	AAC_ID			SERIAL			PRIMARY KEY,
	AJU_ID			INT				NOT NULL,
	PIE_ID			INT				NOT NULL,
	Tipo			VARCHAR(7)		NOT NULL,
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NOT NULL, --Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Observaciones	TEXT,
	CONSTRAINT CHK_AAC_TIPO CHECK (Tipo IN ('Agregar','Quitar')),
	FOREIGN KEY (PIE_ID) References Piezas,
	FOREIGN KEY (AJU_ID) References Ajustes ON DELETE CASCADE,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

--Alertas
CREATE TABLE Alertas(
	ALE_ID			SERIAL			PRIMARY KEY,
	Titulo			VARCHAR(100)	NOT NULL UNIQUE,
	Menu			VARCHAR(30)		NOT NULL,
	Tabla			VARCHAR(30)		NOT NULL,
	TAB_ID			INT				NOT NULL,
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Descripcion		TEXT			NOT NULL,
	FOREIGN KEY (Usu_Cre) References Usuarios
);

CREATE TABLE LogContra(
	token 	VARCHAR(300) 	PRIMARY KEY,
	usu_id 	INT 			NOT NULL,
	fec_cre TIMESTAMP		NOT NULL DEFAULT(NOW()),
	fec_res TIMESTAMP		NULL,
	usado	BOOLEAN 		NOT NULL DEFAULT(FALSE),
	FOREIGN KEY (usu_id) References Usuarios ON DELETE CASCADE
);

CREATE TABLE LogCorreo(
	LOG_ID	SERIAL			PRIMARY KEY,
	correo 	VARCHAR(300) 	NOT NULL,
	asunto 	TEXT 			NOT NULL,
	Mensaje TEXT			NOT NULL,
	Fecha	TIMESTAMP		NOT NULL DEFAULT(NOW()),
	Tabla	VARCHAR(100)	NULL,
	Id		INT				NULL,
	Estatus VARCHAR(30)		NULL,
	Error	TEXT			NULL
);

CREATE TABLE HistoricoCustodios(
	usu_cus			INT				NOT NULL, --Usuario Custodio
	bie_id			INT				NOT NULL, --Usuario Custodio
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			DATE			NOT NULL DEFAULT(NOW()), --fecha Creacion
	PRIMARY KEY(usu_cus,bie_id,Fec_Cre),
	FOREIGN KEY (usu_cus) References Usuarios,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (BIE_ID) References Bienes ON DELETE CASCADE
);

CREATE TABLE CambiosEstatus(

	CAM_ID			SERIAL			PRIMARY KEY,
	Documento		VARCHAR(10)		NOT NULL UNIQUE,
	DOC_Estatus		VARCHAR(100)	NOT NULL,--Solicitado, Aprobado
	BIE_ID			INT				NOT NULL,--Bien al que se le esta haciendo ajuste
	BIE_Estatus		VARCHAR(100)	NOT NULL,--Activo, Inactivo
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NOT NULL, --Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Usu_Apr			INT				NULL, --Usuario Aprobador
	Fec_Apr			TIMESTAMP		NULL, --fecha aprobacion
	Observaciones	TEXT			NOT NULL,
	FOREIGN KEY (BIE_ID) References Bienes,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Apr) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios

);

CREATE TABLE CambioEstatusPieza(
	CEP_ID			SERIAL			PRIMARY KEY,
	CAM_ID			INT				NOT NULL,
	PIE_ID			INT				NOT NULL,
	Estatus			VARCHAR(100)	NOT NULL,--Activo, Inactivo
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NOT NULL, --Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Observaciones	TEXT			NOT NULL,
	FOREIGN KEY (PIE_ID) References Piezas,
	FOREIGN KEY (CAM_ID) References CambiosEstatus ON DELETE CASCADE,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

CREATE TABLE Auditorias(
	AUD_ID			SERIAL			PRIMARY KEY,
	Opcion			VARCHAR(100)	NOT NULL,
	Tabla			VARCHAR(50)		NOT NULL,
	TAB_ID			INT 			NOT NULL,
	Datos			TEXT			NOT NULL,
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	FOREIGN KEY (Usu_Cre) References Usuarios
);

GRANT CONNECT ON DATABASE siamafacyt TO userapp;

-- Grant usage the schema

GRANT USAGE ON SCHEMA public TO userapp ;

-- Grant all table for SELECT, INSERT, UPDATE, DELETE

GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO userapp;
GRANT USAGE, SELECT,  UPDATE ON ALL SEQUENCES IN SCHEMA public TO userapp;

\i DataReal.sql
\i insertar.sql