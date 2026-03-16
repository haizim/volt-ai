# Import Bank Accounts

## Model

Bank

## Form

- File : file (file xlsx)

## Template

### Sheet : Import

#### Columns

- Kode Bank : string
- Nama Bank : string

## Import

### Rules

- kode_bank : required|string|max:10
- nama_bank : required|string|max:255

### Data Map

- kode_bank : code
- nama_bank : name