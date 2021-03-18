INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, remember_token, set_password_hash, role_id, created_at, updated_at) VALUES
  (1, 'Gerhard Feest', 'fidel.kutch@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', null, null, 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'Alien West', 'alien.west@example.com', 'old_password', null, 'restore_token', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO settings(name, value) VALUES
  ('default_tag', '{"ID": 55, "Name": "Alex Grant-Browning"}');

INSERT INTO simpro_customers(id, customer_id, name, type, created_at, updated_at) VALUES
  (1, 5, '18 Hyde Park Gardens Freehold Ltd', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 6, 'Old name', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 100, 'For delete', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO simpro_sites(id, site_id, name, postal_code, created_at, updated_at) VALUES
  (1, 1, 'Sitename 1', 'SL5 7HY', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 2, 'Sitename 2', 'EC2M 3YD', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 3, 'Sitename 3', 'W13 9BE', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (4, 4, 'Sitename 4', 'W1J 8LL', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

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
  (9, 108, 4, 2, '2016-10-20', 'Progress', null, 1);                                                                                                        ;

INSERT INTO schedules(id, job_id, schedule_id, name, date, start_time, end_time) VALUES
  (1, 1, 100, 'Name', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO job_catalogs(id, job_id, section_id, cost_center_id, catalog_id, original_catalog_id, name, part_no, qty) VALUES
  (1, 1, 0, 0, 0, 0, 'Test', 'Test', 1);

INSERT INTO job_attachments(id, job_id, attachment_id, name) VALUES
  (1, 1, 'Test', 'Test');

INSERT INTO job_work_orders(id, job_id, section_id, cost_center_id, work_order_id, name, description, date) VALUES
(1, 1, 0, 0, 0, 'Test', 'Test', '2020-10-06');
