<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PillIdentification extends Model
{
    protected $fillable = ['id_medicine', 'shape', 'color', 'imprint', 'size', 'image_url'];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'id_medicine', 'id_medicine');
    }
}
