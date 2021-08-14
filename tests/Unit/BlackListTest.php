<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use App\Models\{BlackList, Site, Publisher, Advertiser};

class BlackListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Проверка парсинга из строки, сохранения в бд и обратного получения и конвертирования из бд в строку
     */
    public function testSaveBlackListAndGetBack()
    {
        Site::factory(10)->create();
        Publisher::factory(10)->create();
        $advert = Advertiser::factory()->create();

        // генерируем рандомный список для блэклиста
        $items = Publisher::inRandomOrder()->limit(5)->get()
              ->merge(Site::inRandomOrder()->limit(5)->get());

        $bl_list = [];

        foreach($items as $key => $item) {
            switch(get_class($item)) {
                case 'App\Models\Site':
                    $bl_list[] = "s{$item->id}";
                    break;
                case 'App\Models\Publisher':
                    $bl_list[] = "p{$item->id}";
            }
        }

        // полная массив для конечной проверки
        $bl_in = $bl_list;
        // один элемент, будет добавлен отдельно
        $bl_one_el = array_shift($bl_list);
        // строка без элемента
        $bl_list = implode(', ', $bl_list);

        // пробуем добавить один элемент отдельно
        $this->assertTrue(BlackList::saveFromString($bl_one_el, $advert->id));
        // пробуем добавить список из нескольких элементов
        $this->assertTrue(BlackList::saveFromString($bl_list, $advert->id));
        // получаем полную строку блэк листа из бд
        $bl_out = BlackList::getForAdvertiser($advert->id);
        // разбиваем на массив
        $bl_out = explode(', ', $bl_out);
        // сортируем оба массива, - стартовый и итоговый
        sort($bl_in);
        sort($bl_out);
        $this->assertEquals($bl_in, $bl_out);
    }

    /**
     * Проверка на исключение, если передали строку не по формату
     */
    public function testNotValidBlackListString()
    {
        $site = Site::factory()->create();
        $advert = Advertiser::factory()->create();

        $this->expectExceptionMessage('Blacklist is not valid');
        BlackList::saveFromString("s{$site->id}, d33", $advert->id);
    }

    /**
     * Проверка на исключение, если в блэклист передали айди несуществующего элемента
     */
    public function testPublisherNotExist()
    {
        $advert = Advertiser::factory()->create();

        $this->expectExceptionMessage('Publisher not found');
        BlackList::saveFromString('p66666666666', $advert->id);
    }


    /**
     * Проверка на исключение, если в функцию парсинга передали айди несуществующего адверта
     */
    public function testAdvertiserNotExist()
    {
        $publisher = Publisher::factory()->create();

        $this->expectExceptionMessage('Advertiser not found');
        BlackList::saveFromString("p{$publisher->id}", 66666666666);
    }
}
