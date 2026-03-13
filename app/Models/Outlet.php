<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravolt\Suitable\AutoFilter;
use Laravolt\Suitable\AutoSearch;
use Laravolt\Suitable\AutoSort;

final class Outlet extends Model
{
    use AutoFilter, AutoSearch, AutoSort;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'city',
        'province',
        'postal_code',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
