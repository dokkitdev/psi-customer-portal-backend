INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, remember_token, set_password_hash, role_id, created_at, updated_at) VALUES
  (1, 'Gerhard Feest', 'fidel.kutch@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', null, null, 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'Alien West', 'alien.west@example.com', 'old_password', null, 'restore_token', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO quote_status_codes(id, simpro_code_id, name, status, created_at, updated_at) VALUES
  (1, 33, 'Quote : Approved to be Sent', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 32, 'Quote : Awaiting Approval', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 87, 'Quote : Awaiting Information', 'New', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (4, 86, 'Quote : Awaiting Price', 'New', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (5, 85, 'Quote : Awaiting Procurement', 'New', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (6, 92, 'Quote : Declined', 'Pending', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (7, 169, 'Quote : Enquiry to be Assigned', 'Pending', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (8, 170, 'Quote : On Hold', 'Pending', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (9, 31, 'Quote : Overdue', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (10, 101, 'Quote : Re-requested', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00');
