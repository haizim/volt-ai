<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravolt\Suitable\AutoFilter;
use Laravolt\Suitable\AutoSearch;
use Laravolt\Suitable\AutoSort;

final class Product extends Model
{
    use AutoFilter, AutoSearch, AutoSort, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'price',
        'weight',
    ];

    protected $hidden = [
        'deleted_at',
    ];
}
