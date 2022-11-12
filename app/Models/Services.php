<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Services extends Model
{
    use HasFactory, SoftDeletes, HasRecursiveRelationships;

    protected $fillable = ['title', 'slug', 'parent_id', 'icon_class', 'content', 'page_title', 'metadata', 'keywords'];

    public static function boot(){
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    /* public function getParentKeyName(){
        return 'parent_id';
    }

    public function getLocalKeyName(){
        return 'id';
    } */

    public static function generateSlug($name){
        $slug = Str::slug($name);
        $duplicate = static::withTrashed()->where('slug', 'like', '%' . $slug . '%')->count();
        return ($duplicate > 0) ? $slug . '-' . ($duplicate + 1) : $slug;
    }
}
