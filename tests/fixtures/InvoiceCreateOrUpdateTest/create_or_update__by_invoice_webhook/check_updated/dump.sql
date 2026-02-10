INSERT INTO simpro_jobs (id, data, handle_status, handle_result, created_at, updated_at) VALUES
(1101, '{"ID": "job.created"}', 'error', null, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(1102, '
        {
            "ID": "invoice.created",
            "reference": {
                "companyID": 0,
                "invoiceID": 1234
            }
        }
        ', 'new', null, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(1107, '{"ID": "job.created"}', 'error', null, '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO jobs (id, job_id, simpro_customer_id, simpro_site_id, created_at, updated_at) VALUES
(4101, 2344, 2102, 3102, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(4102, 2345, 2102, 3102, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(4103, 2346, 2102, 3102, '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO invoices (id, job_id, invoice_id, is_paid, created_at, updated_at) VALUES
(5101, 4102, 1233, false, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(5102, 4102, 1234, false, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(5103, 4102, 1235, false, '2018-10-10 10:10:10', '2018-10-10 10:10:10');