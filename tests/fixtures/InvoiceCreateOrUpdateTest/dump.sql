INSERT INTO simpro_jobs (id, data, handle_status, handle_result, created_at, updated_at) VALUES
(1001, '{}', 'error', null, '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO simpro_customers (id, customer_id, name, type, created_at, updated_at) VALUES
(2001, 9999, 'Not Used Customer', 'companies', '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO simpro_sites (id, site_id, name, created_at, updated_at) VALUES
(3001, 9999, 'Not Used Site', '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO jobs (id, job_id, simpro_customer_id, simpro_site_id, created_at, updated_at) VALUES
(4001, 9999, 2001, 3001, '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO invoices (id, job_id, invoice_id, is_paid, created_at, updated_at) VALUES
(5001, 4001, 9999, false, '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO job_attachments (id, job_id, attachment_id, name) VALUES
(6001, 4001, 'attachment_9999', 'Not Used Attachment');