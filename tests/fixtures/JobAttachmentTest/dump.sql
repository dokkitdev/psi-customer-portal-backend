INSERT INTO roles(id, name, created_at, updated_at) VALUES
  (1, 'administrator', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'user', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO users(id, name, email, password, remember_token, set_password_hash, role_id, created_at, updated_at) VALUES
  (1, 'Gerhard Feest', 'fidel.kutch@example.com', '$2y$10$X4receiTrF24bXrEbAiChOZ8TMNPqoXuhuThgynvBdWIHZeu5HzsS', null, null, 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 'Alien West', 'alien.west@example.com', 'old_password', null, 'restore_token', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO simpro_customers(id, customer_id, name, type, created_at, updated_at) VALUES
  (1, 5, '18 Hyde Park Gardens Freehold Ltd', 'companies', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO simpro_sites(id, site_id, name, created_at, updated_at) VALUES
  (1, 1, 'Sitename', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO jobs(id, job_id, simpro_site_id, simpro_customer_id) VALUES
  (1, 209000, 1, 1);

INSERT INTO job_attachments(id, job_id, attachment_id, name) VALUES
  (1, 1, '8EgYd8urKKzzqcdDTKsKcpW9xWHxtwsKSoCscR3R7g4', 'Jobcard_For_Job_No_209000_24-02-2021_0942.pdf');