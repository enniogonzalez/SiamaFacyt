DELETE FROM ALERTAS 
WHERE Tabla = 'MantenimientoCorrectivo' AND TAB_ID = 1
    AND EXISTS (
        SELECT 1
        FROM MantenimientoCorrectivo
        where MCO = 1
            AND Estatus = 'Realizado'
    )

EXISTS(
    SELECT 
);

-- Titulo: Ajuste Solicitado 00000001
-- Descripcion: El día 02/01/2018 el usuario Ennio Gonzalez solicitó el ajuste 0000001 para el Bien Aveo LT ubicado en quimica.
