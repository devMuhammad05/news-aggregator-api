<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
