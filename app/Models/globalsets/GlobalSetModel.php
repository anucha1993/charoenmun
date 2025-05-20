<?php

namespace App\Models\globalsets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalSetModel extends Model
{
    use HasFactory;
     //
     protected $table = 'global_sets';
     protected $primaryKey = 'id';

     protected $fillable = [
        'name',
        'description',
    ];
    
public function values()
{
    return $this->hasMany(GlobalSetValueModel::class, 'global_set_id')->orderBy('sort_order');
}
}

