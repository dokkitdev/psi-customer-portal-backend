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
(1103, '{"ID": "job.created"}', 'error', null, '2018-10-10 10:10:10', '2018-10-10 10:10:10');