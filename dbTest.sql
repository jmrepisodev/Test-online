-- Ejemplo de script de implementación de BBDD (por ejemplo, 'bbdd.sql')
-- Creamos y empezamos a usar la BBDD

DROP DATABASE IF EXISTS bbdd_test;
CREATE DATABASE IF NOT EXISTS bbdd_test;
USE bbdd_test;

DROP TABLE IF EXISTS detalle_resultados;
DROP TABLE IF EXISTS resultados;
DROP TABLE IF EXISTS respuestas;
DROP TABLE IF EXISTS preguntas;
DROP TABLE IF EXISTS test;
DROP TABLE IF EXISTS usuarios;


-- Implementación en SQL del modelo de base de datos


CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
	email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
	rol VARCHAR(50)

);

CREATE TABLE test (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    testname VARCHAR(50),
	descripcion VARCHAR(255)

);

CREATE TABLE preguntas (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    question TEXT,
    id_test INT,
	answer_explained TEXT,
    CONSTRAINT id_test_fk FOREIGN KEY (id_test) REFERENCES test (id) ON DELETE CASCADE
);

CREATE TABLE respuestas (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    answer TEXT,
    id_question INT,
	es_correcta BOOLEAN,
    CONSTRAINT id_preg_fk FOREIGN KEY (id_question) REFERENCES preguntas (id) ON DELETE CASCADE
);

CREATE TABLE resultados (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	id_usuario INT,
    id_test INT,
	nota INT,
    CONSTRAINT id_user_fk FOREIGN KEY (id_usuario) REFERENCES usuarios (id) ON DELETE CASCADE,
    CONSTRAINT id_test2_fk FOREIGN KEY (id_test) REFERENCES test (id) ON DELETE CASCADE
);

CREATE TABLE detalle_resultados (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	id_resultado INT,
    id_pregunta INT,
	id_respuesta INT,
    es_correcta BOOLEAN,
	CONSTRAINT id_res_fk FOREIGN KEY (id_resultado) REFERENCES resultados (id) ON DELETE CASCADE,
    CONSTRAINT id_preg2_fk FOREIGN KEY (id_pregunta) REFERENCES preguntas (id) ON DELETE CASCADE,
	CONSTRAINT id_resp_fk FOREIGN KEY (id_respuesta) REFERENCES respuestas (id) ON DELETE CASCADE
);


-- Datos de ejemplo

INSERT INTO usuarios (username, password, email, rol) VALUES
('admin', '$2y$10$J0p3zn8xMQ/Hhb4lkuhr9e58/3Jl8YvqC.q6GlWJQ8/rF4qRcbop6','admin@gmail.com','admin'),
('maria', '$2y$10$J0p3zn8xMQ/Hhb4lkuhr9e58/3Jl8YvqC.q6GlWJQ8/rF4qRcbop6','maria@gmail.com','user'),
('juan', '$2y$10$J0p3zn8xMQ/Hhb4lkuhr9e58/3Jl8YvqC.q6GlWJQ8/rF4qRcbop6','juan@gmail.com','user'),
('pepe', '$2y$10$J0p3zn8xMQ/Hhb4lkuhr9e58/3Jl8YvqC.q6GlWJQ8/rF4qRcbop6','pepe@gmail.com','user');

INSERT INTO test VALUES
(1, 'PHP' ,'Cuestionario de preguntas de elección múltiple sobre PHP'),
(2, 'JavaScript','Cuestionario de preguntas de elección múltiple sobre JavaScript'),
(3, 'Java','Cuestionario de preguntas de elección múltiple sobre Java'),
(4, 'Python','Cuestionario de preguntas de elección múltiple sobre Python');


INSERT INTO preguntas (question, id_test, answer_explained) VALUES
('¿Qué significa PHP?', 1, ' Acrónimo recursivo PHP: Hypertext Preprocessor'),
(' ¿Con qué símbolo se debe empezar el nombre de una variable en PHP?', 1, 'El símbolo $'),
('¿Con qué símbolo se debe envolver un valor numérico en una variable?', 1, 'Ninguno. Solo se escribe el número'),
('¿Cuál de las siguientes variables está declarada de forma correcta?', 1, '$edad = 25;'),
('¿Cuáles son operadores relacionales?', 1, '<, >, <=, >=, ==, !='),
('En una variable de cadena o string el valor va rodeado entre comillas:', 1, 'Verdadero'),
('¿Qué significa la expresión $promedio==17;?', 1, 'Comparación: igual a 17'),
('¿Qué tipo de valor se obtiene al evaluar una expresión con operadores relacionales?', 1, 'Valor booleano: true o false'),
('Función para escribir texto con formato:', 1, 'Printf()'),
('Comando para escribir texto o código:', 1, 'Echo');


INSERT INTO respuestas (answer, id_question, es_correcta) VALUES
('PHP: Hypertext Preprocessor', 1, true),
('Private Home Page', 1, false),
('Personal Hypertext Preprocessor', 1 , false),

('=', 2, false),
('==', 2, false),
('//', 2, false),
('$', 2, true),

('\\', 3, false),
('==', 3, false),
('/**/', 3, false),
('Ninguno, solo se escribe el número', 3, true),

('edad = 25;', 4, false),
('edad = "25;"', 4, false),
('$edad == 25;', 4, false),
('$edad = 25;', 4, true),

(' +, -, *, /, %, ++, --', 5, false),
(' <, >, <=, >=, ==, !=', 5, true),
('&&, ||, and, or, !', 5, false),
('$, &, //, /* */, { }', 5, false),

('Verdadero', 6, true),
('Falso', 6, false),

('El valor de la variable $promedio es igual a 17', 7, true),
('La variable $promedio vale 17', 7, false),
('La variable $promedio no es igual a 1', 7, false),
('La variable $promedio es mayor a 17', 7, false),

('Valor numérico', 8, false),
('Cadena de texto', 8, false),
('Valor booleano', 8, true),
('Ninguno de los anteriores', 8, false),

('write()', 9, false),
('printf()', 9, true),
('echo', 9, false),
('trace()', 9, false),

('write()', 10, false),
('printf()', 10, false),
('echo', 10, true),
('trace()', 10, false);


INSERT INTO `resultados` (id_usuario, id_test, nota) VALUES
(2, 1, 6),
(3, 1, 7),
(4, 1, 6);



INSERT INTO `detalle_resultados` ( id_resultado, id_pregunta, id_respuesta, es_correcta) VALUES
(1, 1, 1, 1),
(1, 2, 6, 0),
(1, 3, 11, 1),
(1, 4, 15, 1),
(1, 5, 17, 1),
(1, 6, 20, 1),
(1, 7, 22, 1),
(1, 8, 27, 0),
(1, 9, 32, 0),
(1, 10, 34, 0),

(2, 1, 1, 1),
(2, 2, 7, 1),
(2, 3, 11, 1),
(2, 4, 15, 1),
(2, 5, 19, 0),
(2, 6, 20, 1),
(2, 7, 24, 0),
(2, 8, 27, 0),
(2, 9, 31, 1),
(2, 10, 36, 1),

(3, 1, 1, 1),
(3, 2, 5, 0),
(3, 3, 9, 0),
(3, 4, 13, 0),
(3, 5, 17, 1),
(3, 6, 20, 1),
(3, 7, 22, 1),
(3, 8, 28, 1),
(3, 9, 31, 1),
(3, 10, 34, 0);

