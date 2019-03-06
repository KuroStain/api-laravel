<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    // Se espesifica que tabla es la que se usara.
    protected $table = cars;

    // Relacion uno a muchos
    // De esta forma el objeto "user" tendra relacionado todos los atributos de la tabla cars
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
