<?php

namespace App\Http\Controllers;

use App\Models\ExpansionFile;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Files extends Controller
{    
    /**
     * отображение файла 
     *
     * @param  File $file
     */
    public function viewFile(File $file){
        
        // проверяем наличие файла на сервере
        if(!file_exists(storage_path($file->path))){
            return;
        }
        
        return response()->file(storage_path($file->path));
    }
       
    /**
     * Возвращает массив с расширениями и 
     * ассоциативный массив код => имя с их типами
     * @return array
     */
    public function expansionFiles(){
        
        $user = Auth::user();

        if (!$user->is_admin) {
            abort(403);
        }

        $expansions = ExpansionFile::all();

        $labels = trans("titles.fileTypeName");


        return [
            "expansions" => $expansions,
            "labels" => $labels,
            "types" => config("basis.vueTypeFiles"),
        ];
    }
    
    /**
     * сохранение изменений 
     * создание новых расширений
     *
     * @return array
     */
    public function saveExpansion(){
        
        // проверяем является ли пользователь админом
        $user = Auth::user();

        if (!$user->is_admin) {
            abort(403);
        }

        // забираем измененнные расширения
        $change = request("change");
        
        // забираем новые расширения
        $new = request("new");

        // обходим и сохраняем измения 
        $resultChange = $this->updateExpansions($change);

        // обходим и сохраняем новые расширения
        $resultNew =  $this->createExpansions($new);
        

        // проверяем наличие ошибок при обходе
        if (isset($resultChange['errors']) ||
            isset($resultNew['errors'])    
        ) {
            return isset($resultChange['errors']) ? $resultChange : $resultNew;
        }
       
        return[
            "new" => $resultNew,
            "change"  => $resultChange
        ];
    }
    
    /**
     * создание новых расширений
     *
     * @param  mixed $newExpansions
     * @return array
     */
    public function createExpansions($newExpansions){
        
        // 
        $result = [];



        // забираем типы расширений
        $types = array_keys(config("basis.typeFiles"));

        foreach ($newExpansions as $key => $expansion) {
            
            $validator = Validator::make($expansion, [
                "expansion" =>'required|max:255',
                "upload" => 'required|boolean',
                "type" => [
                    'required',
                    Rule::in($types)
                ],
            ]);

            if ($validator->fails()) {
                return [
                    "new" => $result,
                    "errors" => $validator->messages()
                ];
            }

            // проверяем на дублирование 
            if (ExpansionFile::where("type", $expansion['type'])
                ->Where("expansion", $expansion['expansion'])
                ->exists()) {

                return [
                    "new" => $result,
                    "errors" =>[ "Расширение New#".$key." ".$expansion['expansion']." уже существует"]
                ];
            } 
            
            // создание расширения
            $new = ExpansionFile::create([
                "expansion" => $expansion["expansion"],
                "type" => $expansion["type"],
                "upload" => $expansion["upload"],
            ]);

            $new->temporaryId = $expansion['temporaryId'];

            // добавляем в результирующий массив
            $result[] = $new;

        }

        return $result;
    }

    /**
     * Обход и сохранение изменений у расширений
     *
     * @param  mixed $arrayExpansions
     * @return array
     */
    public function updateExpansions($arrayExpansions){

        // забираем типы расширений
        $types = array_keys(config("basis.typeFiles"));
        $result = collect();

        foreach ($arrayExpansions as $value) {
            
            $validator = Validator::make($value, [
                "upload" => 'boolean',
                "type" => [
                    'required',
                    Rule::in($types)
                ]
            ]);

            if ($validator->fails()) {
                return [
                    "change" => $result,
                    "errors" => $validator->messages()
                ];
            }


            if (array_key_exists('expansion',$value) )  {


                $validator = Validator::make($value, [
                    "expansion" =>'required|max:255',]
                );
                if ($validator->fails()) {
                    return [
                        "change" => $result,
                        "errors" => $validator->messages()
                    ];
                }
    
                // проверяем на дублирование 
                if (ExpansionFile::where("type", $value['type'])
                    ->where('id', '!=', $value['id'])
                    ->Where("expansion", $value['expansion'])
                    ->exists()) {

                    return [

                        "change" => $result,
                        "errors" => ["Расширение ID:".$value['id']." ".$value['expansion']." уже существует"]
                    ];
                }   

                // проверяем если пользователь посттавил точку впереди
                $array = explode(".", $value['expansion']);

                if ($array[0] === "") {
                    $value['expansion'] = implode(".", array_splice($array, 1));
                }

            }

            // создаем новое расширение либо забираем существующее
            $expansion = ExpansionFile::firstOrNew([
                "id" => $value['id'],
            ]);

            $expansion->fill($value);


            $expansion->save();

            // добавляем в результирующий массив
            $result[] = $expansion;

        }

        return $result;
    }
    
    /**
     * Удаление расширений
     *
     * @return array
     */
    public function deleteExpansion(Request $request){

        $user = Auth::user();

        if (!$user->is_admin) {
            abort(403);
        }

        $errors = [];

        foreach (request()->all() as $element) {
            
            if (!isset($element['id'])) {
                array_push($errors, "Невозможно удалить. ".$element['expansion']." не существует.");
                continue;
            }

            $expansion = ExpansionFile::whereKey($element['id'])->first();
        
            if (!$expansion) {
                array_push($errors, "Невозможно удалить. #".$element['id'].": ".$element['expansion']." не существует.");
                continue;
            }

            $expansion->delete();
        }

        if (count($errors) === 0) {
            return [
                "message" => "Удалено"
            ];
        }

        return [
            "errors" => $errors
        ];
    }
    
}
