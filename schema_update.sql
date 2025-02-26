CREATE INDEX idx_order_date ON orders(order_date);

ALTER TABLE orders ADD COLUMN order_year INT GENERATED ALWAYS AS (YEAR(order_date)) STORED;
ALTER TABLE orders ADD COLUMN order_month INT GENERATED ALWAYS AS (MONTH(order_date)) STORED;
CREATE INDEX idx_order_year_month ON orders(order_year, order_month);

CREATE INDEX idx_order_store ON orders(store_id);
CREATE INDEX idx_order_category ON orders(category_id);

CREATE INDEX idx_order_date_region ON orders(order_date, store_id);
ALTER TABLE orders ADD CONSTRAINT fk_store FOREIGN KEY (store_id) REFERENCES stores(id);

ALTER TABLE orders PARTITION BY RANGE (YEAR(order_date)) (
    PARTITION p1 VALUES LESS THAN (2023),
    PARTITION p2 VALUES LESS THAN (2024),
    PARTITION p3 VALUES LESS THAN (2025)
);

CREATE INDEX idx_order_date_sales ON orders(order_date, unitPrice, quantity);