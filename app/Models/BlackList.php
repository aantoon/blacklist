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
            'column' => 'site_id',
            'class' => Site::class
        ],
        'p' => [
            'name' => 'Publisher',
            'column' => 'publisher_id',
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

        $allowed_prefixes = array_keys(self::$elements);
        // массив для проверенных элементов блэклиста
        $bl_valid = array_fill_keys($allowed_prefixes, []);
        // массив для инсерта
        $bl_insert = [];
        $bl_insert_template = array_merge(['advertiser_id' => $advertiser_id],
                                           array_fill_keys(array_column(self::$elements, 'column'), null));
        // список префиксов для регулярки
        $list_of_prefixes = implode('|', $allowed_prefixes);

        // проверяем соответствие формату
        foreach($blacklist_items as $item) {
            if(preg_match("#^({$list_of_prefixes})(\d+)$#", $item, $matches)) {
                $prefix = $matches[1];
                $id = $matches[2];
                // проверка на случай если в переданном блэк листе есть повторяющиеся айди
                if(!in_array($id, $bl_valid[$prefix]))
                    $bl_valid[$prefix][] = $id;
            }
            else
                throw new \Exception('Blacklist is not valid');
        }

        // проверяем на существование записей в бд и формируем массив для инсерта
        foreach(self::$elements as $prefix => $el_params) {
            if(isset($bl_valid[$prefix])) {
                // проверяем существуют ли записи в бд
                $count_rows_exist = $el_params['class']::whereIn('id', $bl_valid[$prefix])->count();
                if($count_rows_exist === count($bl_valid[$prefix])) {
                    // всё ок, добавляем запись в массив для инсерта
                    foreach($bl_valid[$prefix] as $id) {
                        $temp_array = $bl_insert_template;
                        $temp_array[$el_params['column']] = $id;

                        $blacklist_insert[] = $temp_array;
                    }
                }
                else
                    throw new \Exception("{$el_params['name']} not found");
            }
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
