<?php

declare(strict_types=1);

use App\Models\Bank;
use Illuminate\Database\UniqueConstraintViolationException;

test('fillable attributes', function (): void {
    $bank = new Bank;

    expect($bank->getFillable())
        ->toBe([
            'name',
            'code',
        ]);
});

test('it uses soft deletes', function (): void {
    $bank = Bank::factory()->create();
    $bank->delete();

    $this->assertSoftDeleted($bank);
});

test('it has unique code constraint', function (): void {
    Bank::factory()->create(['code' => 'BCA']);

    $this->expectException(UniqueConstraintViolationException::class);

    Bank::factory()->create(['code' => 'BCA']);
});
