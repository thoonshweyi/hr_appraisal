create extension dblink;

insert into suppliers (
        supplier_code,
        supplier_name
    )
select  supplier_code,
        supplier_name
from dblink(
        'dbname=pro1_rtn_exch host = 192.168.2.248 port=5432 user=postgres password=webdev',
        'select vendor_code, vendor_name from vendor'
    ) as temp(
        supplier_code  character varying(255),
        supplier_name character varying(255)
		
    )
    