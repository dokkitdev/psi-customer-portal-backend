INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, remember_token, set_password_hash, role_id, created_at, updated_at) VALUES
  (1, 'Gerhard Feest', 'fidel.kutch@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', null, null, 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'Alien West', 'alien.west@example.com', 'old_password', null, 'restore_token', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO settings(name, value) VALUES
  ('default_tag', '{"ID": 55, "Name": "Alex Grant-Browning"}'),
  ('quote_date_created', '{"ID": 12, "Name": "Quote Added"}');

INSERT INTO simpro_customers(id, customer_id, name, type, created_at, updated_at) VALUES
  (1, 1773, '18 Hyde Park Gardens Freehold Ltd', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 6, 'Old name', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 100, 'For delete', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO simpro_sites(id, site_id, simpro_customer_id, name, postal_code, created_at, updated_at) VALUES
  (1, 3421, 1, 'Sitename 1', 'SL5 7HY', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 2, 2, 'Sitename 2', 'EC2M 3YD', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 3, 3, 'Sitename 3', 'W13 9BE', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (4, 4, 3, 'Sitename 4', 'W1J 8LL', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO groups(id, simpro_customer_id, title, created_at, updated_at) VALUES
  (1, 1, 'Group 1', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 1, 'Group 2', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 1, 'Group 3', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (4, 3, 'Group 4', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (5, 2, 'Group 5', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO group_user(id, group_id, user_id) VALUES
  (1, 4, 2),
  (2, 1, 1),
  (3, 2, 2),
  (4, 5, 1);

INSERT INTO site_custom_fields(id, simpro_site_id, custom_field_id, value) VALUES
  (1, 1, 22, '100'),
  (2, 2, 22, '100'),
  (3, 1, 32, '100'),
  (4, 4, 32, '100');

INSERT INTO site_contacts(id, simpro_site_id, contact_id, title, is_primary) VALUES
  (1, 1, 11026, 'Title', true),
  (2, 2, 10862, 'Title', true),
  (3, 4, 11027, 'Title', false),
  (4, 4, 3, 'Title', true);

INSERT INTO group_simpro_site(id, group_id, simpro_site_id, is_enabled, created_at, updated_at) VALUES
  (1, 4, 1, true, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 1, 2, true, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 2, 3, false, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (4, 4, 4, true, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (5, 5, 4, true, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO simpro_jobs(id, data, handle_status, handle_result) VALUES
  (1, '{"ID": "job.created", "build": "pfsgroup.simprosuite.com", "description": "Job #test has been crashed.", "name": "Job", "action": "created", "reference": {"companyID": 0, "jobID": 2406}, "date_triggered": "2019-12-18T11:52:29+00:00"}', 'error', '{}');

INSERT INTO jobs(id, job_id, simpro_site_id, simpro_customer_id, requested, stage, priority, recent_schedule_id) VALUES
  (1, 100, 1, 3, '2016-10-20', 'Progress', 'Fire Alarm - Standard 8 Hours', 1),
  (2, 101, 1, 3, '2016-10-20', 'Progress', 'Fire Alarm - Standard', null),
  (3, 102, 1, 3, '2016-10-20', 'Progress', 'Intruder Alarm - Standard 4 Hours', null),
  (4, 103, 1, 3, '2016-10-20', 'Complete', null, null),
  (5, 104, 1, 3, null, 'Archived', null, null),
  (6, 105, 2, 1, null, 'Archived', null, null),
  (7, 106, 3, 1, null, 'Archived', null, null),
  (8, 107, 4, 3, '2016-10-20', 'Complete', null, null),
  (9, 108, 4, 2, '2016-10-20', 'Progress', null, 1);

INSERT INTO quotes(id, description, job_id, simpro_customer_id, simpro_site_id, quote_id, note, note_id, attachment_id, stage, status, cost_center_name, value, date_issued, date_expiry) VALUES
  (1, 'Description...', 1, 3, 1, 52648, 'Note...', 17256, '1n9nS2sI3NaTnu0kSDa_XpqOGMMm_VuQ_awG0Mn4E0g', 'Approved', null, 'Cost center', 25.50, '2021-04-05', '2021-05-05'),
  (2, null, 2, 3, 1, 52820, null, null, null, null, 'Declined', null, null, null, null),
  (3, null, 3, 3, 1, 3, null, null, null, null, 'Pending', null, null, null, null),
  (4, null, 4, 3, 1, 4, null, null, null, null, null, null, null, null, null),
  (5, null, 5, 3, 1, 5, null, null, null, null, null, null, null, null, null),
  (6, null, 6, 1, 2, 6, null, null, null, null, 'New', null, null, null, null),
  (7, null, 7, 1, 3, 7, null, null, null, null, 'Declined', null, null, null, null),
  (8, null, 8, 3, 4, 8, null, null, null, null, null, null, null, null, null),
  (9, null, 9, 2, 4, 9, null, null, null, null, null, null, null, null, null),
  (10, null, 9, 2, 4, 10, null, null, null, null, null, null, null, null, null);

