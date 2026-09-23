<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_laporan', 'photo_path', 'photo_data', 'urutan'])]
class ReportPhoto extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id_foto';

    public $incrementing = false;

    protected $keyType = 'string';

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'id_laporan');
    }
}