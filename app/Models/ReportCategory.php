<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Skeleton model — struktur dasar saja.
 */
#[Fillable(['category_name', 'is_active'])]
class ReportCategory extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id_kategori';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'id_kategori');
    }
}