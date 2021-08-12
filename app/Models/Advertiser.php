<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BlackList;

class Advertiser extends Model
{
    use HasFactory;

    public function blacklists() {
        return $this->hasMany(BlackList::class);
    }
}
