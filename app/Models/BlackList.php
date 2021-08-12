<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Advertiser, Publisher, Site};

class BlackList extends Model
{
    use HasFactory;
    private static $prefixes = [
        's' => 'Site',
        'p' => 'Publisher'
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
        $blacklist_insert = array();

        foreach($blacklist_items as $key => $item) {
            // соответствие формату
            if(preg_match('#(p|s)(\d+)#', $item, $matches)) {
                $blacklist_insert[$key] = [
                    'advertiser_id' => $advertiser_id,
                    'publisher_id' => null,
                    'site_id' => null
                ];

                $name = self::getNameByPrefix($matches[1]);
                $id = $matches[2];

                // у каждой сущности должен быть класс
                if(!class_exists("App\Models\\{$name}"))
                    throw new \Exception("Class {$name} is not defined");

                // ищем запись по айдишнику
                if(!$name::find($id))
                    throw new \Exception("{$name} not found");

                $blacklist_insert[$key][self::getColumnByName($name)] = $id;
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
                foreach(self::$prefixes as $prefix => $name) {
                    // ищем к какой сущности относится запись
                    if($id = ($blacklist[self::getColumnByName($name)])) {
                        // добавляем в массив
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

    /**
     * Ищет полное название сущности по префиксу
     * @param string $prefix
     * @return string
     */
    private static function getNameByPrefix(string $prefix) {
        if(isset(self::$prefixes[$prefix]))
            return self::$prefixes[$prefix];
        else
            throw new \Exception("Can't find name by prefix {$prefix}");
    }

    private static function getColumnByName(string $name) {
        return strtolower($name) . '_id';
    }
}
