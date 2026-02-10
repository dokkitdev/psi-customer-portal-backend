INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, role_id, created_at, updated_at) VALUES
  (1, 'Mr Admin', 'admin@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'Another User', 'user@example.com', '$2y$10$ywtTizICfzWDTU2Cp3s.8.HIvJpGUsvi66Y.x6ByBib8O.D2fxbSK', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO settings(name, is_public, value, created_at, updated_at) VALUES
  ('admin_email', true, '{"email": "admin@test.com"}', '2016-10-20 11:05:00', '2016-10-20 11:06:00');

INSERT INTO simpro_customers(id, customer_id, name, type, created_at, updated_at) VALUES
  (1, 1667, 'Simpro Customer', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 1073, 'Simpro Customer', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 3, 'Simpro Customer', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO groups(id, simpro_customer_id, title, created_at, updated_at) VALUES
  (1, 1, 'Group 1', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 1, 'Group 2', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 1, 'Group 3', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (4, 1, 'Group 4', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO simpro_sites(id, site_id, name) VALUES
  (1, 1, 'Name 1'),
  (2, 2, 'Name 2'),
  (3, 3, 'Name 3'),
  (4, 4, 'Name 4');

INSERT INTO group_simpro_site(id, group_id, simpro_site_id) VALUES
  (1, 1, 1),
  (2, 1, 2),
  (3, 2, 1),
  (4, 3, 1);

INSERT INTO group_user(id, group_id, user_id) VALUES
  (1, 1, 1),
  (2, 2, 1),
  (3, 1, 2),
  (4, 2, 2);

INSERT INTO jobs(id, job_id, simpro_site_id, simpro_customer_id, requested, stage, priority, recent_schedule_id) VALUES
  (1, 100, 1, 3, '2016-10-20', 'Pending', 'Fire Alarm - Standard 8 Hours', 1),
  (2, 101, 1, 3, '2016-10-20', 'Pending', 'Fire Alarm - Standard', null),
  (3, 102, 1, 3, '2016-10-20', 'Progress', 'Intruder Alarm - Standard 4 Hours', null),
  (4, 103, 1, 3, '2016-10-20', 'Complete', null, null),
  (5, 104, 1, 3, null, 'Archived', null, null),
  (6, 105, 2, 1, null, 'Archived', null, null),
  (7, 106, 3, 1, null, 'Archived', null, null),
  (8, 107, 4, 3, '2016-10-20', 'Complete', null, null),
  (9, 108, 4, 2, '2016-10-20', 'Progress', null, 1),
  (10, 109, 4, 2, '2016-10-20', 'Progress', null, 1),
  (11, 110, 1, 3, '2016-10-20', 'Invoiced', 'Fire Alarm - Standard 8 Hours', 1);

INSERT INTO quotes(id, status_id, description, job_id, simpro_customer_id, simpro_site_id, quote_id, note, note_id, attachment_id, stage, status, cost_center_name, value, date_issued, date_expiry) VALUES
  (1, null, 'Description...', 1, 3, 1, 52648, 'Note...', 17256, '1n9nS2sI3NaTnu0kSDa_XpqOGMMm_VuQ_awG0Mn4E0g', 'Approved', 'Pending', 'Cost center', 25.50, '2021-04-05', '2021-05-05'),
  (2, null, null, 2, 3, 1, 52820, null, null, null, null, 'Declined', null, null, null, null),
  (3, null, null, 3, 3, 1, 3, null, null, null, null, 'Pending', null, null, null, null),
  (4, null, null, 4, 3, 1, 4, null, null, null, null, null, null, null, null, null),
  (5, null, null, 5, 3, 1, 5, null, null, null, null, 'Pending', null, null, null, null),
  (6, 66, null, 6, 1, 2, 6, null, null, null, null, 'New', null, null, null, null),
  (7, 77, null, 7, 1, 3, 7, null, null, null, null, 'Declined', null, null, null, null),
  (8, 88, null, 8, 3, 4, 8, null, null, null, null, null, null, null, null, null),
  (9, 99, null, 9, 2, 4, 9, null, null, null, null, null, null, null, null, null),
  (10, null, null, 9, 2, 4, 10, null, null, null, null, 'Pending', null, null, null, null);

INSERT INTO quote_status_codes(id, simpro_code_id, name, status, stage) VALUES
  (1, 66, '66', 'Pending', 'Sent'),
  (2, 77, '77', 'Pending', 'In Progress'),
  (3, 88, '88', 'Declined', 'Sent'),
  (4, 99, '99', 'Pending', 'Sent');

INSERT INTO invoices(id, job_id, invoice_id, date_issued, status, total, date_paid, is_paid) VALUES
  (1, 1, 1, '2021-01-18', 'Approved', 83.33, '2021-03-30', true),
  (2, 2, 2, null, null, null, null, false),
  (3, 3, 3, null, null, null, '2021-03-30', true),
  (4, 4, 4, null, null, null, null, false),
  (5, 5, 5, null, null, null, null, false),
  (6, 6, 6, null, null, null, '2021-03-30', false),
  (7, 7, 7, null, null, null, null, false),
  (8, 8, 8, null, null, null, null, false),
  (9, 9, 9, null, null, null, '2021-03-30', true),
  (10, 9, 10, null, null, null, null, false);