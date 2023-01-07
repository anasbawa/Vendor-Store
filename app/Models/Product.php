<?php

namespace App\Models;

use App\Models\Scopes\StoreScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'category_id', 'store_id',
        'price', 'compare_price', 'status', 'quantity',
    ];

    protected $hidden = [
        'image',
        'created_at', 'updated_at', 'deleted_at',
    ];

    protected $appends = [ // For append attributes to json response
        'image_url', // name of accessor attribute
    ];

    // Global Scopes ////////////////////////////////////////
    protected static function booted()
    {
        static::addGlobalScope('store', new StoreScope);
        static::creating(function(Product $product) {
            $product->slug = Str::slug($product->name);
        });
    }

    // Scopes ////////////////////////////////////////
    public function scopeActive(Builder $builder)
    {
        $builder->where('status', 'active');
    }

    public function scopeFilter(Builder $builder, $filters)
    {
        $options = array_merge([
            'store_id' => null,
            'category_id' => null,
            'tag_id' => null,
            'status' => 'active',
        ], $filters);

        $builder->when($options['status'], function ($query, $status) {
            return $query->where('status', $status);
        });

        $builder->when($options['store_id'], function($builder, $value) {
            $builder->where('store_id', $value);
        });
        $builder->when($options['category_id'], function($builder, $value) {
            $builder->where('category_id', $value);
        });

        $builder->when($options['tag_id'], function($builder, $value) {

            // $builder->whereRaw('EXISTS (SELECT 1 FROM product_tag WHERE tag_id = ? AND product_id = products.id)', [$value]);
            $builder->whereExists(function($query) use ($value) {
                $query->select(1)
                    ->from('product_tag')
                    ->whereRaw('product_id = products.id')
                    ->where('tag_id', $value);
            });
            // $builder->whereRaw('id IN (SELECT product_id FROM product_tag WHERE tag_id = ?)', [$value]);

            // $builder->whereHas('tags', function($builder) use ($value) {
            //     $builder->where('id', $value);
            // });
        });
    }


    // Relations /////////////////////////////////////
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class, // related model
            'product_tag', // pivot table
            'product_id', // FK in pivot table for the current model
            'tag_id', // FK, related model
            'id', // PK current model
            'id',
        );
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return 'https://www.arraymedical.com/wp-content/uploads/2018/12/product-image-placeholder-300x300.jpg';
        }

        if (Str::startsWith($this->image, ['https://'])) {
            return $this->image;
        }

        return asset('storage/' . $this->image);

    }

    public function getSalePercentAttribute()
    {
        if (!$this->compare_price) {
            return 0;
        }
        return round(100 - (100 * $this->price / $this->compare_price), 0);
    }


}
