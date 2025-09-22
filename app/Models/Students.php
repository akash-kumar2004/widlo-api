<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Students extends Model
{
    use HasApiTokens;
    protected $table = "students";
}
