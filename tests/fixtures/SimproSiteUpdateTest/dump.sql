INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, remember_token, set_password_hash, role_id, created_at, updated_at) VALUES
  (1, 'Gerhard Feest', 'fidel.kutch@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', null, null, 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'Alien West', 'alien.west@example.com', 'old_password', null, 'restore_token', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO settings(name, value) VALUES
  ('default_tag', '{"ID": 55, "Name": "Alex Grant-Browning"}');

INSERT INTO simpro_customers(id, customer_id, name, type, created_at, updated_at) VALUES
  (1, 5, '18 Hyde Park Gardens Freehold Ltd', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO groups(id, simpro_customer_id, title, created_at, updated_at) VALUES
  (1, 1, 'Group 1', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 1, 'Group 2', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO group_user(id, group_id, user_id) VALUES
  (1, 1, 2),
  (2, 2, 2);

INSERT INTO simpro_sites(id, site_id, name, postal_code, created_at, updated_at) VALUES
  (1, 1717, 'Sitename 1', 'SL5 7HY', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 1999, 'Sitename 2', 'SL5 7HY', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO site_custom_fields(id, simpro_site_id, custom_field_id, value) VALUES
  (1, 1, 22, '100'),
  (2, 2, 22, '100');

INSERT INTO site_contacts(id, simpro_site_id, contact_id, title, is_primary) VALUES
  (1, 1, 1, 'Title', true),
  (2, 2, 2, 'Title', true);

INSERT INTO group_simpro_site(id, group_id, simpro_site_id) VALUES
  (1, 1, 1),
  (2, 2, 2);
