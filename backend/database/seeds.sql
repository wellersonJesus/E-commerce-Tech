-- Seed data for LandPage (moved to backend)

INSERT INTO services (title, description, active) VALUES
('Desenvolvimento Web', 'Sites e aplicações responsivas', 1),
('Sistemas PHP', 'Soluções backend em PHP', 1),
('APIs REST', 'Desenvolvimento de APIs seguras', 1);

INSERT INTO users (name, email, password, role) VALUES
('User', 'user@example.com', '123456', 'user');
