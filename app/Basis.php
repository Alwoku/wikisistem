<?php

namespace App;

use App\Http\Controllers\Finder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Schema\Blueprint;

class Basis
{

    /**
     * Получение массива для select элемента
     * @param Collection $data Класс модели для получения всех
     * значений или модель с выставленными условиями
     * @param string $key Ключ для имени
     * @param bool $null Добавлять ли null вариант
     * @param string $idKey
     * @param string $noText
     * @return mixed
     */
    public static function select(Collection $collection, $key = "name", $null = false, $idKey = "id", $noText = "Не задано")
    {
        $result = $collection->pluck($key, $idKey)->toArray();

        if ($null) {
            $result = ["" => $noText] + $result;
        }

        return $result;
    }

    /**
     * Формирование select для Vue Select
     */
    public static function vueSelect($collection,  $key = "name", $null = false, $idKey = "id", $noText = "Не задано")
    {
        $result = static::select($collection, $key, $null, $idKey, $noText);

        $select = [];
        foreach ($result as $key => $item) {
            
            $select[] = [
                "label" => $item,
                "id" => $key,
            ];
        }

        return $select;
    }


}
