-- Script para corrigir o erro de digitação no nome da coluna
-- cidade_usario -> cidade_usuario

USE TechFit;

-- Verifica se a coluna com erro existe
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'TechFit' 
  AND TABLE_NAME = 'Usuarios' 
  AND COLUMN_NAME = 'cidade_usario';

-- Renomeia a coluna se existir
ALTER TABLE Usuarios 
CHANGE COLUMN cidade_usario cidade_usuario VARCHAR(255);

-- Verifica se a correção foi aplicada
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'TechFit' 
  AND TABLE_NAME = 'Usuarios' 
  AND COLUMN_NAME = 'cidade_usuario';

