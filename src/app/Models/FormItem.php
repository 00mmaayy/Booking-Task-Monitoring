<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormItem extends Model
{
    use HasFactory;

    protected $table = 'forms';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'form_name',
    ];
}
