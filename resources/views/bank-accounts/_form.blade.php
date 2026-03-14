<?php
$queryBank = 'SELECT id, name from banks';
?>

{!! form()->dropdownDB('bank_id', $queryBank, 'id', 'name')
    ->label('Bank')
    ->placeholder('-- Select Bank --') !!}

{!! form()->text('account_number')->label('Account Number')->placeholder('Enter 9-digit account number') !!}

{!! form()->text('by_name')->label('Account Name')->placeholder('Enter account holder name') !!}

<x-volt-link-button class="basic" url="{!! url()->previous() !!}" icon="arrow left">
    Kembali
</x-volt-link-button>
<x-volt-button>
    Simpan
</x-volt-button>