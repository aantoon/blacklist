<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Advertiser, BlackList};

class Publisher extends Model
{
    use HasFactory;

    /**
     * Возвращает всех адвертов, кто добавил публишер в блэклист
     */
    public function advisersWhoAddedToBlackList() {
        return $this->belongsToMany(Advertiser::class, 'black_lists', 'publisher_id', 'advertiser_id');
    }
}
