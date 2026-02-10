INSERT INTO simpro_jobs(id, handle_status, data) VALUES
(10, 'new', '{
    "ID": "job.attachment.deleted",
    "description": "Attachment #existed_attachment_simpro_id has been deleted.",
    "reference": {
        "ID": 1010,
        "attachmentID": "existed_attachment_simpro_id",
        "companyID": 0
    }
}');

INSERT INTO jobs(id, job_id, simpro_site_id, simpro_customer_id) VALUES
(10, 1009, 1, 1),
(11, 1010, 1, 1),
(12, 1011, 1, 1);

INSERT INTO job_attachments(id, job_id, attachment_id, name, created_at, updated_at) VALUES
(10, 11, 'simpro_id_10', '10.pdf', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(11, 12, 'existed_attachment_simpro_id', '11.pdf', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(12, 11, 'existed_attachment_simpro_id', '12.pdf', '2018-10-10 10:10:10', '2018-10-10 10:10:10'),
(13, 11, 'simpro_id_12', '13.pdf', '2018-10-10 10:10:10', '2018-10-10 10:10:10');