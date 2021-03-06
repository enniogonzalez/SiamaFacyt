CREATE DATABASE sigmafacyt WITH 
ENCODING 'UTF8' owner "adminsigma";

\c sigmafacyt

CREATE TABLE Roles(
	ROL_ID			SERIAL			PRIMARY KEY,
	Nombre			VARCHAR(100)	NOT NULL UNIQUE,
	Observaciones	TEXT
);

CREATE TABLE Permisos(
	Per_ID			SERIAL			PRIMARY KEY,
	Opcion			VARCHAR(100)	NOT NULL UNIQUE,
	Observaciones	TEXT
);

CREATE TABLE RolPermisos(
	ROL_ID 	INT NOT NULL,
	PER_ID	INT NOT NULL,
	PRIMARY KEY(ROL_ID,PER_ID),
	FOREIGN KEY (ROL_ID) References Roles ON DELETE CASCADE,
	FOREIGN KEY (PER_ID) References Permisos ON DELETE CASCADE
);

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
	ROL_ID			INT					NOT NULL,
	Nombre			VARCHAR(100)		NOT NULL,
	LOC_ID			INT					NULL,
	Clave			VARCHAR(100)		NOT NULL,
	Correo			VARCHAR(100),
	Usu_Cre			INT, --Usuario Creador
	Fec_Cre			TIMESTAMP			NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT, --Usuario Modificador
	Fec_Mod			TIMESTAMP			NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Observaciones	TEXT,
	
	FOREIGN KEY (ROL_ID) References roles,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

CREATE TABLE TipoPieza(
	TPI_ID			SERIAL			PRIMARY KEY,
	Nombre			VARCHAR(255)	NOT NULL UNIQUE,
	Usu_Cre			INT				NOT NULL,--Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NULL,--Usuario Creador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()),
	Observaciones	TEXT			NULL,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
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
	Nombre			VARCHAR(100)		NOT NULL UNIQUE,
	Ubicacion		TEXT				NOT NULL,
	Tipo			VARCHAR(100)		NOT NULL,
	Secuencia		TEXT				NOT NULL DEFAULT '',
	Usu_Cre			INT					NOT NULL,--Usuario Creador
	Fec_Cre			TIMESTAMP			NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT					NULL,--Usuario Creador
	Fec_Mod			TIMESTAMP			NOT NULL DEFAULT(NOW()),
	Observaciones	TEXT,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

ALTER TABLE Usuarios ADD FOREIGN KEY (LOC_ID) REFERENCES Localizaciones(loc_id);
/*Tablas de Segundo Nivel*/

CREATE TABLE Obreros(
	OBR_ID			SERIAL			PRIMARY KEY,
	Cedula			VARCHAR(25)		NOT NULL UNIQUE,
	Nombre			VARCHAR(100)	NOT NULL,
	Telefonos		VARCHAR(100),
	Correo			VARCHAR(100),
	Usu_Cre			INT				NOT NULL,--Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NULL,--Usuario Creador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()),
	Observaciones	TEXT,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

CREATE TABLE Herramientas(
	HER_ID			SERIAL			PRIMARY KEY,
	Nombre			VARCHAR(255)	NOT NULL UNIQUE,
	Usu_Cre			INT				NOT NULL,--Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NULL,--Usuario Creador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()),
	Observaciones	TEXT			NULL,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

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
	Nombre			VARCHAR(255)	NOT NULL	UNIQUE,
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
	PAR_ID			INT					NULL,--partida
	MAR_ID			INT					NOT NULL,--Marca
	TPI_ID			INT					NOT NULL,--Tipo de pieza

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
	FOREIGN KEY (TPI_ID) References TipoPieza,
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
	Titulo			VARCHAR(255)	NOT NULL,--Titulo tarea
	TPI_ID			INT				NOT NULL,--Tipo de Pieza a la que se le hara haciendo mantenimiento
	hor_hom			Decimal(10,2)	NOT NULL,--Tiempo Estimado en el que se realizara el mantenimiento
	Descripcion		TEXT			NOT NULL,
	Usu_Cre			INT				NOT NULL,--Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()),--fecha Creacion
	Usu_Mod			INT				NOT NULL,--Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), 
	Observaciones	TEXT,
	UNIQUE(PLM_ID,Titulo,TPI_ID),
	FOREIGN KEY (PLM_ID) References PlantillaMantenimiento  ON DELETE CASCADE,
	FOREIGN KEY (TPI_ID) References TipoPieza,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

CREATE TABLE PlantillaTareaHerramienta(
	PMT_ID	INT	NOT NULL,
	HER_ID	INT	NOT NULL,
	PRIMARY KEY (PMT_ID,HER_ID),
	FOREIGN KEY (PMT_ID) References PlantillaMantenimientoTarea  ON DELETE CASCADE,
	FOREIGN KEY (HER_ID) References Herramientas
);

--Mantenimientos Correctivos
CREATE TABLE MantenimientoCorrectivo(
	MCO_ID			SERIAL			PRIMARY KEY,
	BIE_ID			INT				NULL,
	CPL_ID			INT				NULL,
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
	CCO_ID			SERIAL			PRIMARY KEY,
	MCO_ID			INT				NOT NULL,--Mantenimiento Correctivo
	PDA_ID			INT				NOT NULL,--Pieza da�ada
	OBR_ID			INT				NULL,
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
	FOREIGN KEY (OBR_ID) References Obreros,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios,
	FOREIGN KEY (BIE_ID) References Bienes,
	FOREIGN KEY (MCO_ID) References MantenimientoCorrectivo ON DELETE CASCADE,
	CONSTRAINT CHK_EJECUTOR_CCO CHECK ((OBR_ID is null and PRO_ID is not null) OR 
									(OBR_ID is not null and PRO_ID is null) ),
	CONSTRAINT CHK_Piezas_Diferentes CHECK (PDA_ID <> PCA_ID),
	CONSTRAINT CHK_Fechas_CCO CHECK (Fec_Ini <= Fec_Fin),
	CONSTRAINT UQ_CCO_MCO_PDA UNIQUE (MCO_ID,PDA_ID),
	CONSTRAINT UQ_CCO_MCO_PCA UNIQUE (MCO_ID,PCA_ID)
);

CREATE TABLE ReparacionCorrectiva(
	RCO_ID			SERIAL			PRIMARY KEY,
	MCO_ID			INT				NOT NULL,
	PIE_ID			INT				NOT NULL,--Pieza da�ada
	OBR_ID			INT				NULL,--Obrero
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
	FOREIGN KEY (OBR_ID) References Obreros,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios,
	FOREIGN KEY (PRO_ID) References Proveedores,
	CONSTRAINT CHK_EJECUTOR_RCO CHECK ((OBR_ID is null and PRO_ID is not null) OR 
									(OBR_ID is not null and PRO_ID is null)),
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
	MTA_ID			SERIAL			PRIMARY KEY,
	MAN_ID			INT				NOT NULL,
	PIE_ID			INT				NOT NULL,
	Titulo			VARCHAR(250)	NOT NULL,--Titulo tarea
	ESTATUS			VARCHAR(100)	NOT NULL,--Solicitado, Aprobado, Realizado,Eliminado
	OBR_ID			INT				NULL,
	PRO_ID			INT				NULL,
	Hor_Asi			DECIMAL(10,2)	NOT NULL,--Minutos Asignados
	Hor_Eje			DECIMAL(10,2)	NOT NULL,--Minutos Ejecutados
	Fec_Ini			DATE			NOT NULL,
	Fec_Fin			DATE			NOT NULL,
	Descripcion		TEXT			NOT NULL, 
	Herramientas	TEXT			NOT NULL, 
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NOT NULL, --Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Observaciones	TEXT,
	UNIQUE(MAN_ID,PIE_ID,Titulo),
	CONSTRAINT CHK_EJECUTOR_MTA CHECK ((OBR_ID is null and PRO_ID is not null) OR 
									(OBR_ID is not null and PRO_ID is null) OR 
									(OBR_ID is null and PRO_ID is null)
									),
	FOREIGN KEY (MAN_ID) References Mantenimiento ON DELETE CASCADE,
	FOREIGN KEY (PIE_ID) References Piezas,
	FOREIGN KEY (OBR_ID) References Obreros,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios,
	FOREIGN KEY (PRO_ID) References Proveedores
);

--Mantenimiento Correctivo Planificado
CREATE TABLE CorrectivoPlanificado(
	CPL_ID			SERIAL			PRIMARY KEY,
	Documento		VARCHAR(10)		NOT NULL UNIQUE,
	Estatus			VARCHAR(100)	NOT NULL,--Solicitado, Aprobado
	MAN_ID			INT 			NULL,
	MCO_ID			INT 			NULL,
	FEC_EJE			DATE			NOT NULL,
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NOT NULL, --Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Usu_Apr			INT				NULL, --Usuario Aprobador
	Fec_Apr			TIMESTAMP		NULL, --fecha aprobacion
	Observaciones	TEXT,
	CONSTRAINT CHK_ORIGEN_CPL CHECK ((MAN_ID is null and MCO_ID is not null) OR 
									(MAN_ID is not null and MCO_ID is null) ),
	FOREIGN KEY (MAN_ID) References Mantenimiento,
	FOREIGN KEY (MCO_ID) References MantenimientoCorrectivo,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Apr) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

CREATE TABLE CorrectivoPlanificadoPieza(
	CPP_ID			SERIAL		PRIMARY KEY,
	CPL_ID			INT 		NOT NULL,
	PIE_ID			INT 		NOT NULL,
	FAL_ID			INT			NOT NULL,
	Usu_Cre			INT			NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP	NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT			NOT NULL, --Usuario Modificador
	Fec_Mod			TIMESTAMP	NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Observaciones	TEXT 		NOT NULL,
	UNIQUE(CPL_ID,PIE_ID),
	FOREIGN KEY (CPL_ID) References CorrectivoPlanificado ON DELETE CASCADE,
	FOREIGN KEY (fal_id) References Fallas,
	FOREIGN KEY (PIE_ID) References Piezas,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

ALTER TABLE MantenimientoCorrectivo ADD FOREIGN KEY (CPL_ID) REFERENCES CorrectivoPlanificado(CPL_ID);

-- ALTER TABLE CorrectivoPlanificado ADD FOREIGN KEY (MCO_ID)
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
	UNIQUE(AJU_ID,PIE_ID),
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
	bie_id			INT				NOT NULL,	
	TAB_ID			INT				NOT NULL,
	Usu_Cre			INT				NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Descripcion		TEXT			NOT NULL,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (bie_id) References Bienes
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

-- Cambios de Estatus Patrimonio
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
	FAL_ID			INT				NULL,
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NOT NULL, --Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Observaciones	TEXT			NOT NULL,
	UNIQUE(CAM_ID,PIE_ID),
	FOREIGN KEY (PIE_ID) References Piezas,
	FOREIGN KEY (FAL_ID) References Fallas,
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

CREATE TABLE AlertaCorreo(
	ACO_ID			SERIAL			PRIMARY KEY,
	Tabla			VARCHAR(50)		NOT NULL,
	TAB_ID			INT 			NOT NULL,
	Estatus			VARCHAR(100)	NOT NULL,
	Fecha			DATE			NOT NULL DEFAULT(NOW()),
	UNIQUE(Tabla,TAB_ID,Estatus,Fecha)
);

--Compatibilidad de los bienes
CREATE TABLE Compatibilidad(
	COM_ID			SERIAL			PRIMARY KEY,
	Documento		VARCHAR(10)		NOT NULL UNIQUE,
	BIE_ID			INT				NOT NULL,--Bien al que se le esta creando compatibilidad
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

CREATE TABLE CompatibilidadAccion(
	CAC_ID			SERIAL			PRIMARY KEY,
	COM_ID			INT				NOT NULL,
	TPI_ID			INT				NOT NULL,
	Tipo			VARCHAR(7)		NOT NULL,
	Usu_Cre			INT				NOT NULL, --Usuario Creador
	Fec_Cre			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Creacion
	Usu_Mod			INT				NOT NULL, --Usuario Modificador
	Fec_Mod			TIMESTAMP		NOT NULL DEFAULT(NOW()), --fecha Modificacion
	Observaciones	TEXT,
	UNIQUE (COM_ID,TPI_ID),
	CONSTRAINT CHK_CAC_TIPO CHECK (Tipo IN ('Agregar','Quitar')),
	FOREIGN KEY (TPI_ID) References TipoPieza,
	FOREIGN KEY (COM_ID) References Compatibilidad ON DELETE CASCADE,
	FOREIGN KEY (Usu_Cre) References Usuarios,
	FOREIGN KEY (Usu_Mod) References Usuarios
);

CREATE TABLE CompatibilidadBien(
	BIE_ID	INT NOT NULL,
	TPI_ID	INT	NOT NULL,
	PRIMARY KEY (BIE_ID,TPI_ID),
	FOREIGN KEY (TPI_ID) References TipoPieza,
	FOREIGN KEY (BIE_ID) References Bienes ON DELETE CASCADE
);


GRANT CONNECT ON DATABASE sigmafacyt TO userapp;

-- Grant usage the schema

GRANT USAGE ON SCHEMA public TO userapp ;

-- Grant all table for SELECT, INSERT, UPDATE, DELETE

GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO userapp;
GRANT USAGE, SELECT,  UPDATE ON ALL SEQUENCES IN SCHEMA public TO userapp;


\i DataReal.sql
\i insertar.sql