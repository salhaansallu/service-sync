<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $casts = [
        'pro_images' => 'array',
    ];

    public function getImageList(): array
    {
        $images = $this->pro_images;

        if (is_array($images) && count($images) > 0) {
            return array_values(array_filter($images));
        }

        if (!empty($this->pro_image)) {
            return [$this->pro_image];
        }

        return ['placeholder.svg'];
    }
}
