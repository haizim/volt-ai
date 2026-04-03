# Export Products

## File Name
product-{Ymd-His}.xlsx

## Sheets

### Products

#### Query
```sql
select 
    *
from products
order by name asc
```

#### Columns

- Nama Produk (name) : String
- Tipe (type) : String
- Harga (price) : Number
- Berat (weight) : Number