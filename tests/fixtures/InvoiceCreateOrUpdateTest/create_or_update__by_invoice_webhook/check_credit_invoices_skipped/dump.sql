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
(1103, '
        {
            "ID": "invoice.created",
            "reference": {
                "companyID": 0,
                "invoiceID": 1235
            }
        }
        ', 'new', null, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(1104, '
        {
            "ID": "invoice.created",
            "reference": {
                "companyID": 0,
                "invoiceID": 1236
            }
        }
        ', 'new', null, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(1105, '
        {
            "ID": "invoice.created",
            "reference": {
                "companyID": 0,
                "invoiceID": 1237
            }
        }
        ', 'new', null, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(1106, '
        {
            "ID": "invoice.created",
            "reference": {
                "companyID": 0,
                "invoiceID": 1238
            }
        }
        ', 'new', null, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(1107, '{"ID": "job.created"}', 'error', null, '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO jobs (id, job_id, simpro_customer_id, simpro_site_id, created_at, updated_at) VALUES
(4102, 2345, 2102, 3102, '2018-10-10 10:10:10', '2018-10-10 10:10:10');