<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Blogs;

class Images extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'item_id', 'title', 'alt_tag'];

    public function blog(){
        return $this->belongsTo(Blogs::class, 'item_id');
    }
}
