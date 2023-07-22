INSERT INTO simpro_jobs (id, data, handle_status, handle_result, created_at, updated_at) VALUES
(1101, '{"ID": "job.created"}', 'error', null, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(1102, '
    {
        "ID": "job.created",
        "reference": {
            "companyID": 0,
            "jobID": 1234
        }
    }
    ', 'new', null, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(1103, '{"ID": "job.created"}', 'error', null, '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO simpro_customers (id, customer_id, name, type, created_at, updated_at) VALUES
(2101, 2344, 'Not Used Customer', 'companies', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(2102, 2345, 'Job Customer', 'companies', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(2103, 2346, 'Not Used Customer', 'companies', '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO simpro_sites (id, site_id, name, created_at, updated_at) VALUES
(3101, 3455, 'Not Used Site', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(3102, 3456, 'Job Site', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(3103, 3457, 'Not Used Site', '2018-10-10 10:10:10', '2018-10-10 10:10:10');

INSERT INTO jobs (id, job_id, simpro_customer_id, simpro_site_id, created_at, updated_at) VALUES
(4101, 1233, 2102, 3102, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(4102, 1234, 2102, 3102, '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(4103, 1235, 2102, 3102, '2018-10-10 10:10:10', '2018-10-10 10:10:10');