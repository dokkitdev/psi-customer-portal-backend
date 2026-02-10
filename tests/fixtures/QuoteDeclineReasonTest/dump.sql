INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, remember_token, set_password_hash, role_id, created_at, updated_at) VALUES
  (1, 'Gerhard Feest', 'fidel.kutch@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', null, null, 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'Alien West', 'alien.west@example.com', 'old_password', null, 'restore_token', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO quote_decline_reasons(id, title, created_at, updated_at) VALUES
  (1, 'Reason 1', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'Reason 2', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 'Reason 3', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (4, 'Reason 4', '2016-10-20 11:05:00', '2016-10-20 11:05:00');