<?php

namespace App\Models;

use Illuminate\Container\Attributes\Tag;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'nombre',
        'precio',
        'category_id'
    ];

    // Accesor
    public function getNombreAttribute($valor)
    {
        return ucfirst($valor);
    }

    // Mutador
    public function setPrecioAttribute($valor)
    {
        $this->attributes['precio'] = number_format($valor, 2, '.', '');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
