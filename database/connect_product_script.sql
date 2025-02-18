-- View: public.stock_by_branch_local

-- DROP VIEW public.stock_by_branch_local;

CREATE OR REPLACE VIEW public.stock_by_branch_local
 AS
 SELECT cc.branch_code,
    cc.product_code,
    cc.barcode,
    cc.product_name,
    cc.stock_qty,
    cc.product_unit,
    cc.unit_rate
   FROM ( SELECT x.branch_code,
            x.product_code,
            x.barcode,
            x.product_name,
            x.stock_qty,
            x.product_unit,
            x.unit_rate
           FROM dblink('host=192.168.2.241 port=5432 user=postgres password=p@ssw0rd dbname=pro1'::text, 'select branch_code,product_code,barcode,product_name,stock_qty,product_unit,unit_rate  from (
		select brchcode as branch_code,productcode as product_code,barcode_code as barcode,product_name1 as product_name,sum((sum *  product_unit_rate))::numeric(19,3) as stock_qty,product_unit_name as product_unit,product_unit_rate as unit_rate
		from inventory.stock_poerp aa
			inner join (
					select aa.product_id,product_code,barcode_code,product_name1,main_product_unit_id,bb.product_unit_id,product_unit_name,product_unit_rate
					from master_data.master_product aa
						inner join master_data.master_product_barcode bb
						on aa.product_id=bb.product_id
						left join master_data.master_product_unit cc
						on bb.product_unit_id=cc.product_unit_id
					)bb on aa.productcode=bb.product_code 
			group by productcode,barcode_code,product_name1,product_unit_name,product_unit_rate,brchcode)  ab'::text) x(branch_code character varying(15), product_code character varying(25), barcode character varying(50), product_name character varying(255), stock_qty numeric(19,3), product_unit character varying(255), unit_rate numeric(18,4))) cc;

ALTER TABLE public.stock_by_branch_local
    OWNER TO postgres;