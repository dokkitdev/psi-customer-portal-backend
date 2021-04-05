INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, remember_token, set_password_hash, role_id, created_at, updated_at) VALUES
  (1, 'Gerhard Feest', 'fidel.kutch@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', null, null, 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'Alien West', 'alien.west@example.com', 'old_password', null, 'restore_token', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO simpro_jobs(id, data, handle_status, handle_result) VALUES
  (1, '{"ID": "job.created", "build": "pfsgroup.simprosuite.com", "description": "Job #test has been crashed.", "name": "Job", "action": "created", "reference": {"companyID": 0, "jobID": 2406}, "date_triggered": "2019-12-18T11:52:29+00:00"}', 'error', '{}');

INSERT INTO simpro_customers(id, customer_id, name, type, created_at, updated_at) VALUES
  (1, 1667, 'Simpro Customer', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 1073, 'Simpro Customer', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 208, 'Simpro Customer', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO groups(id, simpro_customer_id, title, created_at, updated_at) VALUES
  (1, 1, 'Group 1', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 1, 'Group 2', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 1, 'Group 3', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (4, 1, 'Group 4', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (5, 3, 'Group 5', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (6, 3, 'Group 6', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO group_user(id, group_id, user_id) VALUES
  (1, 1, 2),
  (2, 4, 2);

INSERT INTO simpro_sites(id, site_id, name, simpro_customer_id, postal_code) VALUES
  (1, 3900, 'Name 1', 1, 'UB8 1JG'),
  (2, 2, 'Name 2', 1, null),
  (3, 3, 'Name 3', 1, null),
  (4, 3901, 'Name 4', 2, null),
  (5, 1700, 'Name 1700', 3, null);

INSERT INTO site_custom_fields(id, simpro_site_id, custom_field_id, value) VALUES
  (1, 1, 22, '100'),
  (2, 5, 22, '100'),
  (3, 1, 32, '100'),
  (4, 4, 32, '100');

INSERT INTO site_contacts(id, simpro_site_id, contact_id, title, name, is_primary) VALUES
  (1, 1, 11026, 'Title', 'Name', true),
  (2, 5, 10862, 'Title', 'Name', true),
  (3, 4, 11027, 'Title', 'Name', false),
  (4, 4, 3, 'Title', 'Name', true);

INSERT INTO group_simpro_site(id, group_id, simpro_site_id) VALUES
  (1, 1, 1),
  (2, 2, 2),
  (3, 3, 3),
  (4, 4, 4),
  (5, 4, 1),
  (6, 5, 5);

INSERT INTO jobs(id, job_id, simpro_site_id, simpro_customer_id, requested, stage, priority, recent_schedule_id) VALUES
  (1, 100, 1, 3, '2016-10-20', 'Pending', 'Fire Alarm - Standard 8 Hours', 1),
  (2, 101, 1, 3, '2016-10-20', 'Progress', 'Fire Alarm - Standard', null),
  (3, 102, 1, 3, '2016-10-20', 'Invoiced', 'Fire Alarm - Standard', null);