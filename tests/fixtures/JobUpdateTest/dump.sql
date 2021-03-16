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

INSERT INTO simpro_sites(id, site_id, name, postal_code, created_at, updated_at) VALUES
  (1, 1, 'Sitename 1', 'SL5 7HY', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO jobs(id, job_id, simpro_site_id, simpro_customer_id, requested, stage, recent_schedule_id) VALUES
  (1, 200563, 1, 1, '2016-10-20', 'Progress', 1),
  (2, 209402, 1, 1, '2016-10-20', 'Progress', null);                                                                                                        ;

INSERT INTO schedules(id, job_id, schedule_id, name, date, start_time, end_time) VALUES
  (1, 1, 100, 'Name', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO job_work_orders(id, job_id, section_id, cost_center_id, work_order_id, name, description, date) VALUES
  (1, 1, 0, 0, 0, 'Test', 'Test', '2020-10-06');
