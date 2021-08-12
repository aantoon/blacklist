<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Advertiser, BlackList};
use App\Http\Requests\BlackListRequest;

class BlackListController extends Controller
{
    public function addingForm() {
        $advertisers = Advertiser::select('id')->get();

        return view('blacklist.form', ['advertisers' => $advertisers]);
    }

    public function store(BlackListRequest $request) {
        $blacklist = new Blacklist();
        $blacklist->saveFromString($request->blacklist, $request->advertiser_id);

        return back()->with(['success' => true]);
    }
}
