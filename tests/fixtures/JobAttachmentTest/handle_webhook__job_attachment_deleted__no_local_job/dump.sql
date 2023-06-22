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
(11, 1011, 1, 1);
