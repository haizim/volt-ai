# Import Bank Accounts

## Model

BankAccount

## Form

- File : file (file xlsx)

## Template

### Sheet : Import

#### Columns

- Kode Bank : {bank->code}
- Nama Bank : =VLOOKUP(A2,Bank!A:B,2,FALSE)
- Nomor Rekening : random_int(100000000, 999999999)
- Atas Nama : fake()->name

### Sheet : Bank

#### Model

Bank

#### Columns

- Kode Bank : string
- Nama Bank : string

## Import

### Rules

- kode_bank : required|string|exists:banks,code
- nama_bank : nullable|string
- nomor_rekening : required|numeric
- atas_nama : required|string|max:255