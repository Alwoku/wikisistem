<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait SyncMany
{

    /**
     * Синхронизация hasMany
     * @param $name
     * @param null $data
     * @param bool $delete
     * @return bool
     */
    public function syncMany($name, $data = null, $delete = true)
    {
        /**
         * Изменился ли состав записей
         */
        $changed = false;

        $key = "id";

        // коллекция отношения
        $relation = $this->$name;
        
        // на случай null
        $data = $data ?: [];


        $posted = Arr::pluck($data, $key);  

        // везде используем свежий Builder отношения, иначе повторные поиски
        // по первичному ключу не будут давать результата из-за противоречащих ограничений

        if ($delete) {
            // находим не переданные id
            $diff = array_diff($relation->pluck($key)->toArray(), $posted);

            // и удаляем их
            foreach ($diff as $toDelete) {
                $changed = true;
                $this->$name()->findOrNew($toDelete)->delete();
            }
        }

        foreach ($data as $relationData) {
            // находим запись или создаём новую

            $id = data_get($relationData, $key);

            /** @var Model $model */
            $model = $this->$name()->findOrNew($id);

            // или поиск через коллекцию, если модели нужны измененные
            // $model = $relation->find($relationData["id"], $this->$name()->getRelated());

            // получаем все заполняемые поля
            $results = Arr::only($relationData, $model->getFillable());

            $model->fill($results);

            // если модель изменена (новая будет изменена в любом случае)
            if ($model->isDirty()) {
                $changed = true;
            }

            $this->$name()->save($model);
        }

        return $changed;
    }
}