# Import Products

## Model

Product

## Form

- File : file (file xlsx)

## Template

### Sheet : Import

#### Columns

- Nama Produk : string
- Tipe : string
- Harga : numeric
- Berat : numeric

## Import

### Rules

- nama_produk : required|string|max:255
- tipe : required|string|max:255
- harga : required|numeric
- berat : required|numeric

### Data Map

- nama_produk : name
- tipe : type
- harga : price
- berat : weight