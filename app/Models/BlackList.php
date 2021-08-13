<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Advertiser, Publisher, Site};

class BlackList extends Model
{
    use HasFactory;
    private static $elements = [
        's' => [
            'name' => 'Site',
            'class' => Site::class,
        ],
        'p' => [
            'name' => 'Publisher',
            'class' => Publisher::class
        ]
    ];


    /**
     * Парсит блэклист из строки и сохраняет его конкретному адверту
     * @param string $blacklist
     * @param int $advertiser_id
     * @return bool
     */
    public static function saveFromString(string $blacklist, int $advertiser_id) {
        if(!Advertiser::find($advertiser_id))
            throw new \Exception('Advertiser not found');

        // разбиваем строку по формату
        $blacklist_items = explode(', ', $blacklist);

        // массив для будущего инсерта
        $blacklist_insert = [];

        // шаблон для записи в бд
        $blacklist_template = ['advertiser_id' => $advertiser_id];
        foreach(self::$elements as $el_params) {
            $blacklist_template[self::getColumnByName($el_params['name'])] = null;
        }

        // обходим каждый элемент из переданного блэклиста
        foreach($blacklist_items as $key => $item) {
            $list_of_prefixes = implode('|', array_keys(self::$elements));
            // проверяем соответствие формату
            if(preg_match("#^({$list_of_prefixes})(\d+)$#", $item, $matches)) {
                // прописываем шаблон записи в бд
                $blacklist_insert[$key] = $blacklist_template;

                $el_name = self::$elements[$matches[1]]['name'];
                $el_class = self::$elements[$matches[1]]['class'];
                $el_id = $matches[2];

                // ищем запись по айдишнику, она должна существовать
                if(!$el_class::find($el_id))
                    throw new \Exception("{$el_name} not found");

                $blacklist_insert[$key][self::getColumnByName($el_name)] = $el_id;
            }
            else
                throw new \Exception('Blacklist is not valid');
        }

        // сохраняем всё вместе
        return self::insert($blacklist_insert);
    }

    /**
     * Возвращает блеклист для конкретного адверта в текстовом формате
     * @param int $advertiser_id
     * @return string
     */
    public static function getForAdvertiser(int $advertiser_id) {
        if($advert = Advertiser::find($advertiser_id)) {
            $blacklist_array = [];

            // обходим каждую запись из блэклиста адверта
            foreach($advert->blacklists as $blacklist) {
                // обходим каждую доступную сущность
                foreach(self::$elements as $prefix => $el_params) {
                    // ищем к какой относится запись
                    if($id = ($blacklist[self::getColumnByName($el_params['name'])])) {
                        // нашли, добавляем в массив
                        $blacklist_array[] = "{$prefix}{$id}";
                    }
                }
            }

            $blacklist_string = implode(', ', $blacklist_array);

            return $blacklist_string;
        }
        else
            throw new \Exception('Advertiser not found');
    }

    private static function getColumnByName(string $name) {
        return strtolower($name) . '_id';
    }
}
