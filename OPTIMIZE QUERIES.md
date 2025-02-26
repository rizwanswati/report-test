# Why Current Queries Can Be Slow And Ways To Optimizing Slow Queries

## Why These Queries Might Be Slow?
Your **reporting queries** (`monthly-sales-by-region` and `top-categories-by-store`) **aggregate large data sets** over potentially millions of records. Here’s why they may be slow and how to optimize them.

---

## **Grouping & Aggregation Complexity**
### **Issue**
- Queries use `GROUP BY` on multiple fields (`YEAR(order_date)`, `MONTH(order_date)`, `region_id`).
- `SUM()` and `COUNT()` require scanning and aggregating a large number of rows.
- **Indexes are often ignored** when functions like `YEAR(order_date)` are applied.

### **Optimizations**
**Create an index on `order_date` without functions**
```sql
CREATE INDEX idx_order_date ON orders(order_date);
```
**Precompute year/month fields for fast lookup**
- Add `YEAR(order_date)` and `MONTH(order_date)` as **stored/generated columns**.
```sql
ALTER TABLE orders ADD COLUMN order_year INT GENERATED ALWAYS AS (YEAR(order_date)) STORED;
ALTER TABLE orders ADD COLUMN order_month INT GENERATED ALWAYS AS (MONTH(order_date)) STORED;
CREATE INDEX idx_order_year_month ON orders(order_year, order_month);
```
- Now, **Cycle ORM can query directly**:
  ```php
  ->groupBy('o.order_year', 'o.order_month', 'store.regionId')
  ```

---

## **Lack of Proper Indexing**
### **Issue**
- `JOIN` operations (like `orders JOIN stores`) can cause **full table scans** if indexes are missing.
- Sorting by `YEAR(order_date) DESC, MONTH(order_date) DESC` forces **filesort**.

### **Optimizations**
**Index foreign keys (`store_id`, `category_id`)**  
```sql
CREATE INDEX idx_order_store ON orders(store_id);
CREATE INDEX idx_order_category ON orders(category_id);
```
**Create composite indexes for filtering and sorting**  
```sql
CREATE INDEX idx_order_date_region ON orders(order_date, store_id);
```
**Ensure foreign key constraints exist**
```sql
ALTER TABLE orders ADD CONSTRAINT fk_store FOREIGN KEY (store_id) REFERENCES stores(id);
```

---

## **Sorting with ORDER BY is Expensive**
### **Issue**
- `ORDER BY YEAR(order_date) DESC, MONTH(order_date) DESC` requires sorting huge datasets.
- If no proper index exists, MySQL uses a **temporary table and filesort** (slow).

### **Optimizations**
**Sort using indexed columns**
```php
->orderBy('o.order_year', 'DESC')
->orderBy('o.order_month', 'DESC');
```

---

## **Large Table Scans**
### **Issue**
- When filtering a 3-month range (`BETWEEN start_date AND end_date`), MySQL **scans the entire table**.

### **Optimizations**
**Use partitioning for large tables**  
```sql
ALTER TABLE orders PARTITION BY RANGE (YEAR(order_date)) (
    PARTITION p1 VALUES LESS THAN (2023),
    PARTITION p2 VALUES LESS THAN (2024),
    PARTITION p3 VALUES LESS THAN (2025)
);
```
**Use covering indexes for queries filtering by date**
```sql
CREATE INDEX idx_order_date_sales ON orders(order_date, unitPrice, quantity);
```
**Limit query scope to the necessary time period**
```php
$where = [
    'o.order_date >=' => $startDate,
    'o.order_date <=' => $endDate
];
```

---