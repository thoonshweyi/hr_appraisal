create extension dblink;

insert into branches (
        branch_id,
        branch_code,
        branch_name,
        branch_short_name,
        branch_address,
	    branch_phone_no,
	    branch_active
    )
select  branch_id,
        branch_code,
        branch_name,
        branch_short_name,
        branch_address1,
	    tel,
		branch_active
from dblink(
        'dbname=pro1 host = 192.168.2.241 port=5432 user=postgres password=p@ssw0rd',
        'select   distinct on (branch_id)branch_id, branch_code,branch_short_name,branch_name,branch_address1
		,tel,branch_active from master_data.master_branch'
    ) as temp(
        branch_id integer,
        branch_code  character varying(255),
        branch_name character varying(255),
        branch_short_name character varying(255),
        branch_address1 character varying(255),
		tel character varying(255),
		branch_active boolean
		
    );
    