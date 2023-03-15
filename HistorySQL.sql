create database dismac;

SELECT 
    p.id, p.name, p.sku, b.name as brand, c.label as clacom,s.name as store, pss.status, SUM(pw.stock) as stockP,pr.price, pr.special_price 
FROM product p 
LEFT OUTER JOIN brand b ON p.id_brand=b.id 
LEFT OUTER JOIN clacom c on c.id=p.id_clacom 
LEFT OUTER JOIN product_store_status pss on p.id=pss.id_product 
LEFT OUTER JOIN store s on s.id=pss.id_store 
LEFT OUTER JOIN product_warehouse pw ON pw.id_product=p.id 
LEFT OUTER JOIN warehouses wh ON wh.id=pw.id_warehouse 
LEFT OUTER JOIN product_price_store pps ON pps.id_store=s.id AND pps.id_product=p.id 
LEFT OUTER JOIN prices pr ON pr.id=pps.id_price 
GROUP BY 
    p.id, p.name, p.sku, b.name, c.label,s.name, pss.status,pr.price, pr.special_price 
ORDER BY 
    p.id 
ASC;