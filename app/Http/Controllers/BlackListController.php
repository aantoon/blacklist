<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advertiser;

class BlackListController extends Controller
{
    public function formForAddingToBlackList() {
        $advertisers = Advertiser::select('id')->get();

        return view('blacklist.form', ['advertisers' => $advertisers]);
    }
}
