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
    right_answer TEXT NOT NULL
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
('USER');

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