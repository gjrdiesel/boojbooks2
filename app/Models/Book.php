<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rutorika\Sortable\SortableTrait;

class Book extends Model
{
    use HasFactory, SortableTrait;

    protected $guarded = [];
}
