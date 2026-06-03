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

INSERT INTO roles (name) VALUES 
('ADMIN'),

INSERT INTO fields (number, name) VALUES
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

INSERT INTO exercises (field_id, image_url, type, right_answer) VALUES
(1, 'public/images/1/1.png', 'ABCD', 'C'),
(1, 'public/images/1/2.png', 'ABCD', 'C'),
(1, 'public/images/1/3.png', 'ABCD', 'B'),
(1, 'public/images/1/4.png', 'ABCD', 'B'),
(1, 'public/images/1/5.png', 'ABCD', 'B'),
(1, 'public/images/1/6.png', 'ABCD', 'C'),
(1, 'public/images/1/7.png', 'ABCD', 'B'),
(1, 'public/images/1/8.png', 'PF', 'PP'),
(2, 'public/images/2/1.png', 'ABCD', 'A'),
(2, 'public/images/2/2.png', 'ABCD', 'A'),
(2, 'public/images/2/3.png', 'ABCD', 'A'),
(3, 'public/images/3/1.png', 'ABCD', 'B'),
(3, 'public/images/3/2.png', 'ABCD', 'C'),
(3, 'public/images/3/3.png', 'ABCD', 'D'),
(4, 'public/images/4/1.png', 'ABCD', 'D'),
(5, 'public/images/5/1.png', 'PF', 'PF'),
(5, 'public/images/5/2.png', 'ABCD', 'C'),
(5, 'public/images/5/3.png', 'ABCD', 'A'),
(5, 'public/images/5/4.png', 'ABCD', 'D'),
(5, 'public/images/5/5.png', 'PF', 'FF'),
(5, 'public/images/5/6.png', 'ABCD', 'A'),
(5, 'public/images/5/7.png', 'ABCD', 'D'),
(6, 'public/images/6/1.png', 'ABCD', 'C'),
(6, 'public/images/6/2.png', 'PF', 'PP'),
(6, 'public/images/6/3.png', 'ABCD', 'C'),
(6, 'public/images/6/4.png', 'ABCD', 'B'),
(7, 'public/images/7/1.png', 'ABCD', 'B'),
(7, 'public/images/7/2.png', 'ABCD', 'A'),
(7, 'public/images/7/3.png', 'ABCD', 'C'),
(7, 'public/images/7/4.png', 'ABCD', 'D'),
(8, 'public/images/8/1.png', 'ABCD', 'C'),
(8, 'public/images/8/2.png', 'ABCD', 'B'),
(8, 'public/images/8/3.png', 'ABCD', 'C'),
(8, 'public/images/8/4.png', 'ABCD', 'B'),
(9, 'public/images/9/1.png', 'ABCD', 'B'),
(9, 'public/images/9/2.png', 'ABCD', 'D'),
(9, 'public/images/9/3.png', 'ABCD', 'B'),
(9, 'public/images/9/4.png', 'ABCD', 'D'),
(9, 'public/images/9/5.png', 'PF', 'PF'),
(10, 'public/images/10/1.png', 'ABCD', 'D'),
(10, 'public/images/10/2.png', 'PF', 'PP'),
(11, 'public/images/11/1.png', 'ABCD', 'A'),
(11, 'public/images/11/2.png', 'ABCD', 'A'),
(13, 'public/images/13/1.png', 'ABCD', 'A'),
(13, 'public/images/13/2.png', 'ABCD', 'C'),
(13, 'public/images/13/3.png', 'PF', 'PP'),
(13, 'public/images/13/4.png', 'ABCD', 'C'),
(14, 'public/images/14/1.png', 'ABCD', 'D'),
(14, 'public/images/14/2.png', 'ABCD', 'A');

INSERT INTO users (username, email, password) VALUES 
('admin', 'admin@example.com', 'admin');

INSERT INTO user_roles (user_id, role_id) VALUES (
    (SELECT id FROM users WHERE username = 'admin'),
    (SELECT id FROM roles WHERE name = 'ADMIN')
);