<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Advertiser, BlackList, Publisher, Site};
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

    public function example() {
        Advertiser::factory(3)->create();
        Publisher::factory(20)->create();
        Site::factory(20)->create();

        BlackList::saveFromString('p1, p3, p5, s2, s4, s6', 1);
        echo 'Парсим из строки "p1, p3, p5, s2, s4, s6" и сохраняем в бд адверту с айди 1';

        BlackList::saveFromString('p11, s12, p13, s13, s4, p3', 2);
        echo '<br>Парсим из строки "p11, s12, p13, s13, s4, p3" и сохраняем в бд адверту с айди 2';
        /*
        print_r([
            'BlackList::getForAdvertiser(1) Получаем блэклист из бд для адверта 1, и конвертим в строку' => BlackList::getForAdvertiser(1),
            'тоже самое для адверта 2' => BlackList::getForAdvertiser(2),
            'для адверта 3' =>  BlackList::getForAdvertiser(3),
            'адверты, которые добавили сайт с айди 4 в блэк лист' => Site::find(4)->advisersWhoAddedToBlackList,
            'адверты, которые добавили паблишер с айди 3 в блэк лист' => Publisher::find(3)->advisersWhoAddedToBlackList
        ]);*/

        return 's';
    }
}
