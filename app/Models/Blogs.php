<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Categories;
use App\Models\Images;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blogs extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['category_id', 'title', 'slug', 'content'];

    public static function boot(){
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function category(){
        return $this->hasOne(Categories::class, 'id', 'category_id');
    }

    public function banner(){
        return $this->hasOne(Images::class, 'item_id')->where('type', 'blog_banner');
    }

    public static function generateSlug($name){
        $slug = Str::slug($name);
        $duplicate = static::withTrashed()->where('slug', 'like', '%' . $slug . '%')->count();
        return ($duplicate > 0) ? $slug . '-' . ($duplicate + 1) : $slug;
    }
}
