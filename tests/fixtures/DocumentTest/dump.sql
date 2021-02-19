INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, remember_token, set_password_hash, role_id, created_at, updated_at) VALUES
  (1, 'Gerhard Feest', 'fidel.kutch@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', null, null, 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'Alien West', 'alien.west@example.com', 'old_password', null, 'restore_token', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO media(id, name, owner_id, is_public, link, created_at, updated_at, deleted_at) VALUES
  (1, 'doc1', 1 , true, 'link1', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
  (2, 'doc2', 1, false, 'link2', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
  (3, 'doc3', 1 , true, 'link3', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
  (4, 'doc4', 1, false, 'link4', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
  (5, 'doc5', 1, false, 'link5', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null);

INSERT INTO documents(id, media_id, title, description, created_at, updated_at) VALUES
  (1, 1, 'Docname 1', 'Product main photo', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 2, 'Docname 2', 'Category Photo photo', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 3, 'Docname 3', 'Product main photo', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (4, 4, 'Docname 4', 'Category Photo photo', '2016-10-20 11:05:00', '2016-10-20 11:05:00');