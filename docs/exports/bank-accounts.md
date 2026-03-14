# Export Bank Account

## File Name
bank_account-{Bank Name}-{Ymd-His}.xlsx

## Params
- bank_id (integer)

## Sheets

### Bank Account

#### Query

```sql
select 
	b."name" bank,
	ba.account_number ,
	ba.by_name ,
	ba.updated_at 
from bank_accounts ba 
	join banks b on b.id = ba.bank_id 
where
	b.id = {$bankId}
```

#### Columns

- Bank Name (bank) : String
- Account Number (account_number) : String
- Account Name (by_name) : String
- Last Update (updated_at) : DateTime