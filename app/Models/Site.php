<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Advertiser, BlackList};

class Site extends Model
{
    use HasFactory;

    /**
     * Возвращает всех адвертов, кто добавил сайт в блэклист
     */
    public function advisersWhoAddedToBlackList() {
        return $this->belongsToMany(Advertiser::class, 'black_lists', 'site_id', 'advertiser_id');
    }
}
