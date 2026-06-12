CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password TEXT NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE fields (
    id SERIAL PRIMARY KEY,
    number VARCHAR(10) NOT NULL,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE exercises (
    id SERIAL PRIMARY KEY,
    field_id INT REFERENCES fields(id) ON DELETE CASCADE,
    image_url TEXT,
    type VARCHAR(4) NOT NULL CHECK (type IN ('ABCD', 'PF')),
    right_answer TEXT NOT NULL,
    CONSTRAINT check_right_answer_format CHECK (
        (type = 'ABCD' AND right_answer ~ '^[A-D]$') OR
        (type = 'PF' AND right_answer ~ '^[PF]{2}$')
    )
);

CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE user_roles (
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    role_id INT REFERENCES roles(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, role_id)
);

CREATE TABLE user_progress (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    field_id INT REFERENCES fields(id) ON DELETE CASCADE,
    score INT NOT NULL,
    total INT NOT NULL,
    solved_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- JEDYNA ROLA W SYSTEMIE - DLA ADMINISTRATORA
INSERT INTO roles (name) VALUES 
('ADMIN');

-- WPISYWANIE DZIAŁÓW MATEMATYCZNYCH
INSERT INTO fields (number, name) VALUES
('0', 'Mieszane zadania'),
('I', 'Liczby rzeczywiste'),
('II', 'Wyrażenia algebraiczne'),
('III', 'Równania i nierówności'),
('IV', 'Układy równań'),
('V', 'Funkcje'),
('VI', 'Ciągi'),
('VII', 'Trygonometria'),
('VIII', 'Planimetria'),
('IX', 'Geometria analityczna'),
('X', 'Stereometria'),
('XI', 'Kombinatoryka'),
('XII', 'Prawdopodobieństwo'),
('XIII', 'Statystyka'),
('XIV', 'Optymalizacja');

-- BAZA ZADAŃ MATURALNYCH
INSERT INTO exercises (field_id, image_url, type, right_answer) VALUES
(2, 'public/images/exercises/I/1.png', 'ABCD', 'C'),
(2, 'public/images/exercises/I/2.png', 'ABCD', 'C'),
(2, 'public/images/exercises/I/3.png', 'ABCD', 'B'),
(2, 'public/images/exercises/I/4.png', 'ABCD', 'B'),
(2, 'public/images/exercises/I/5.png', 'ABCD', 'B'),
(2, 'public/images/exercises/I/6.png', 'ABCD', 'C'),
(2, 'public/images/exercises/I/7.png', 'ABCD', 'B'),
(2, 'public/images/exercises/I/8.png', 'PF', 'PP'),
(3, 'public/images/exercises/II/1.png', 'ABCD', 'A'),
(3, 'public/images/exercises/II/2.png', 'ABCD', 'A'),
(3, 'public/images/exercises/II/3.png', 'ABCD', 'A'),
(4, 'public/images/exercises/III/1.png', 'ABCD', 'B'),
(4, 'public/images/exercises/III/2.png', 'ABCD', 'C'),
(4, 'public/images/exercises/III/3.png', 'ABCD', 'D'),
(5, 'public/images/exercises/IV/1.png', 'ABCD', 'D'),
(6, 'public/images/exercises/V/1.png', 'PF', 'PF'),
(6, 'public/images/exercises/V/2.png', 'ABCD', 'C'),
(6, 'public/images/exercises/V/3.png', 'ABCD', 'A'),
(6, 'public/images/exercises/V/4.png', 'ABCD', 'D'),
(6, 'public/images/exercises/V/5.png', 'PF', 'FF'),
(6, 'public/images/exercises/V/6.png', 'ABCD', 'A'),
(6, 'public/images/exercises/V/7.png', 'ABCD', 'D'),
(7, 'public/images/exercises/VI/1.png', 'ABCD', 'C'),
(7, 'public/images/exercises/VI/2.png', 'PF', 'PP'),
(7, 'public/images/exercises/VI/3.png', 'ABCD', 'C'),
(7, 'public/images/exercises/VI/4.png', 'ABCD', 'B'),
(8, 'public/images/exercises/VII/1.png', 'ABCD', 'B'),
(8, 'public/images/exercises/VII/2.png', 'ABCD', 'A'),
(8, 'public/images/exercises/VII/3.png', 'ABCD', 'C'),
(8, 'public/images/exercises/VII/4.png', 'ABCD', 'D'),
(9, 'public/images/exercises/VIII/1.png', 'ABCD', 'C'),
(9, 'public/images/exercises/VIII/2.png', 'ABCD', 'B'),
(9, 'public/images/exercises/VIII/3.png', 'ABCD', 'C'),
(9, 'public/images/exercises/VIII/4.png', 'ABCD', 'B'),
(10, 'public/images/exercises/IX/1.png', 'ABCD', 'B'),
(10, 'public/images/exercises/IX/2.png', 'ABCD', 'D'),
(10, 'public/images/exercises/IX/3.png', 'ABCD', 'B'),
(10, 'public/images/exercises/IX/4.png', 'ABCD', 'D'),
(10, 'public/images/exercises/IX/5.png', 'PF', 'PF'),
(11, 'public/images/exercises/X/1.png', 'ABCD', 'D'),
(11, 'public/images/exercises/X/2.png', 'PF', 'PP'),
(12, 'public/images/exercises/XI/1.png', 'ABCD', 'A'),
(12, 'public/images/exercises/XI/2.png', 'ABCD', 'A'),
(14, 'public/images/exercises/XIII/1.png', 'ABCD', 'A'),
(14, 'public/images/exercises/XIII/2.png', 'ABCD', 'C'),
(14, 'public/images/exercises/XIII/3.png', 'PF', 'PP'),
(14, 'public/images/exercises/XIII/4.png', 'ABCD', 'C'),
(15, 'public/images/exercises/XIV/1.png', 'ABCD', 'D'),
(15, 'public/images/exercises/XIV/2.png', 'ABCD', 'A');

-- KILKUNASTU PRZYKŁADOWYCH UŻYTKOWNIKÓW (Czysty tekst, brak hashowania)
INSERT INTO users (username, email, password) VALUES 
('MatmaMistrz', 'mistrz@example.com', '$2y$10$AQorn7STxT67uCWbD55O7OQYPMcWPJt8CXOzhpXVYV7pZWJ0TgHXm'),
('AnkaSkakanka', 'anna.nowak@example.com', '$2y$10$AQorn7STxT67uCWbD55O7OQYPMcWPJt8CXOzhpXVYV7pZWJ0TgHXm'),
('Kowal99', 'kowal99@example.com', '$2y$10$AQorn7STxT67uCWbD55O7OQYPMcWPJt8CXOzhpXVYV7pZWJ0TgHXm'),
('KrólowaNauk', 'krolowa@example.com', '$2y$10$AQorn7STxT67uCWbD55O7OQYPMcWPJt8CXOzhpXVYV7pZWJ0TgHXm'),
('PiotrekPolujeNa100', 'piotr.mat@example.com', '$2y$10$AQorn7STxT67uCWbD55O7OQYPMcWPJt8CXOzhpXVYV7pZWJ0TgHXm'),
('ZdysiekX', 'zdysiek@example.com', '$2y$10$AQorn7STxT67uCWbD55O7OQYPMcWPJt8CXOzhpXVYV7pZWJ0TgHXm'),
('SzybkiZmienna', 'x.y.z@example.com', '$2y$10$AQorn7STxT67uCWbD55O7OQYPMcWPJt8CXOzhpXVYV7pZWJ0TgHXm'),
('DeltaUjemna', 'brak_rozwiazan@example.com', '$2y$10$AQorn7STxT67uCWbD55O7OQYPMcWPJt8CXOzhpXVYV7pZWJ0TgHXm'),
('Calka_z_Kawy', 'student2026@example.com', '$2y$10$AQorn7STxT67uCWbD55O7OQYPMcWPJt8CXOzhpXVYV7pZWJ0TgHXm'),
('PlanimetriaFan', 'trojkaty@example.com', '$2y$10$AQorn7STxT67uCWbD55O7OQYPMcWPJt8CXOzhpXVYV7pZWJ0TgHXm'),
('AsKombinatoryki', 'prawdopodobienstwo@example.com', '$2y$10$AQorn7STxT67uCWbD55O7OQYPMcWPJt8CXOzhpXVYV7pZWJ0TgHXm'),
('MaturzystaNaKrawedzi', 'ostatnia.szansa@example.com', '$2y$10$AQorn7STxT67uCWbD55O7OQYPMcWPJt8CXOzhpXVYV7pZWJ0TgHXm');

-- NOWY ELEMENT: DODANIE ADMINISTRATORA (Z podanym przez Ciebie hashem)
INSERT INTO users (username, email, password) VALUES 
('admin', 'admin@example.com', '$2y$10$Iuzxt9Zlcj3.5oC9tGmpYO/tCh5Yv/Ykqnc6KIyKTD0YFM73jI8o.');

-- POWIĄZANIE ADMINISTRATORA Z ROLĄ 'ADMIN'
INSERT INTO user_roles (user_id, role_id) VALUES (
    (SELECT id FROM users WHERE username = 'admin'),
    (SELECT id FROM roles WHERE name = 'ADMIN')
);

-- ZMYŚLONY PROGRESS DLA UŻYTKOWNIKÓW
INSERT INTO user_progress (user_id, field_id, score, total) VALUES
-- MatmaMistrz
((SELECT id FROM users WHERE username = 'MatmaMistrz'), 2, 8, 8),
((SELECT id FROM users WHERE username = 'MatmaMistrz'), 3, 3, 3),
((SELECT id FROM users WHERE username = 'MatmaMistrz'), 1, 12, 15),

-- AnkaSkakanka
((SELECT id FROM users WHERE username = 'AnkaSkakanka'), 2, 5, 8),
((SELECT id FROM users WHERE username = 'AnkaSkakanka'), 6, 4, 7),

-- Kowal99
((SELECT id FROM users WHERE username = 'Kowal99'), 4, 1, 3),
((SELECT id FROM users WHERE username = 'Kowal99'), 5, 0, 1),

-- KrólowaNauk
((SELECT id FROM users WHERE username = 'KrólowaNauk'), 2, 7, 8),
((SELECT id FROM users WHERE username = 'KrólowaNauk'), 7, 3, 4),
((SELECT id FROM users WHERE username = 'KrólowaNauk'), 1, 14, 15),

-- PiotrekPolujeNa100
((SELECT id FROM users WHERE username = 'PiotrekPolujeNa100'), 2, 8, 8),
((SELECT id FROM users WHERE username = 'PiotrekPolujeNa100'), 3, 3, 3),
((SELECT id FROM users WHERE username = 'PiotrekPolujeNa100'), 4, 3, 3),

-- DeltaUjemna
((SELECT id FROM users WHERE username = 'DeltaUjemna'), 2, 2, 8),
((SELECT id FROM users WHERE username = 'DeltaUjemna'), 6, 1, 7),
((SELECT id FROM users WHERE username = 'DeltaUjemna'), 1, 4, 15),

-- SzybkiZmienna
((SELECT id FROM users WHERE username = 'SzybkiZmienna'), 6, 6, 7),

-- PlanimetriaFan
((SELECT id FROM users WHERE username = 'PlanimetriaFan'), 9, 4, 4),

-- MaturzystaNaKrawedzi
((SELECT id FROM users WHERE username = 'MaturzystaNaKrawedzi'), 2, 4, 8),
((SELECT id FROM users WHERE username = 'MaturzystaNaKrawedzi'), 1, 7, 15);
