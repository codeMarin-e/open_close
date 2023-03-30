<?php
namespace App\Http\Requests\Admin;

class OpenCloseRequest {

    public static function validation_rules($lang_prefix = null, $openClose_bag = false, $openCloseSpecial_bag = false) {
        $translations = trans('admin/open_close/validation');
        $langs = isset($lang_prefix)?
            transOrOther($lang_prefix, 'admin/open_close/validation', array_keys($translations)) : $translations;
        $openClose_bag = $openClose_bag? $openClose_bag : 'open_close';
        $openCloseSpecial_bag = $openCloseSpecial_bag? $openCloseSpecial_bag : 'open_close_special';
        return [
            $openClose_bag.'.*.open_at.hour' => ['numeric', function($attribute, $value, $fail) use($langs){
                if($value < 0 || $value > 23)
                    return $fail($langs['open_hour_between']);
            }],
            $openClose_bag.'.*.open_at.minute' => ['numeric', function($attribute, $value, $fail) use($langs) {
                if($value < 0 || $value > 59)
                    return $fail($langs['open_minute_between']);
            }],
            $openClose_bag.'.*.close_at.hour' => ['numeric', function($attribute, $value, $fail) use($langs) {
                if($value < 0 || $value > 23)
                    return $fail($langs['close_hour_between']);
            }],
            $openClose_bag.'.*.close_at.minute' => ['numeric', function($attribute, $value, $fail) use($langs) {
                if($value < 0 || $value > 59)
                    return $fail($langs['close_minute_between']);
            }],
            $openClose_bag.'.*.close_next_day' => 'boolean',
            $openCloseSpecial_bag => 'nullable',
            $openCloseSpecial_bag.'.*.date' => [
                function($attribute, $value, $fail) use($langs){
                    if(!($dt = \DateTime::createFromFormat("d.m.Y H:i:s", "{$value} 00:00:00" )))
                        return $fail($langs['open_hour_date']);
                }
            ],
            $openCloseSpecial_bag.'.*.open_at.hour' => ['numeric', function($attribute, $value, $fail) use($langs){
                if($value < 0 || $value > 23)
                    return $fail($langs['open_hour_between']);
            }],
            $openCloseSpecial_bag.'.*.open_at.minute' => ['numeric', function($attribute, $value, $fail) use($langs) {
                if($value < 0 || $value > 59)
                    return $fail($langs['open_minute_between']);
            }],
            $openCloseSpecial_bag.'.*.close_at.hour' => ['numeric', function($attribute, $value, $fail) use($langs) {
                if($value < 0 || $value > 23)
                    return $fail($langs['close_hour_between']);
            }],
            $openCloseSpecial_bag.'.*.close_at.minute' => ['numeric', function($attribute, $value, $fail) use($langs) {
                if($value < 0 || $value > 59)
                    return $fail($langs['close_minute_between']);
            }],
            $openCloseSpecial_bag.'.*.close_next_day' => 'boolean',
        ];
    }

    public static function validation_prework(&$inputs, $inputBag, $openClose_bag = false, $openCloseSpecial_bag = false) {
        $openClose_bag = $openClose_bag? $openClose_bag : 'open_close';
        $openCloseSpecial_bag = $openCloseSpecial_bag? $openCloseSpecial_bag : 'open_close_special';
        for($i=1; $i<8; $i++) {
            $inputs[$inputBag][$openClose_bag][$i]['open_at']['hour'] = isset($inputs[$inputBag][$openClose_bag][$i]['open_at']['hour']) ?
                (int)$inputs[$inputBag][$openClose_bag][$i]['open_at']['hour'] : 0;
            $inputs[$inputBag][$openClose_bag][$i]['open_at']['minute'] = isset($inputs[$inputBag][$openClose_bag][$i]['open_at']['minute']) ?
                (int)$inputs[$inputBag][$openClose_bag][$i]['open_at']['minute'] : 0;
            $inputs[$inputBag][$openClose_bag][$i]['close_at']['hour'] = isset($inputs[$inputBag][$openClose_bag][$i]['close_at']['hour']) ?
                (int)$inputs[$inputBag][$openClose_bag][$i]['close_at']['hour'] : 0;
            $inputs[$inputBag][$openClose_bag][$i]['close_at']['minute'] = isset($inputs[$inputBag][$openClose_bag][$i]['close_at']['minute']) ?
                (int)$inputs[$inputBag][$openClose_bag][$i]['close_at']['minute'] : 0;
            $inputs[$inputBag][$openClose_bag][$i]['close_next_day'] = isset($inputs[$inputBag][$openClose_bag][$i]['close_next_day']);
        }
        $inputs[$inputBag][$openCloseSpecial_bag] = $inputs[$inputBag][$openCloseSpecial_bag] ?? [];
        foreach((array)$inputs[$inputBag][$openCloseSpecial_bag] as $i => $data) {
            $inputs[$inputBag][$openCloseSpecial_bag][$i]['open_at']['hour'] = isset($inputs[$inputBag][$openCloseSpecial_bag][$i]['open_at']['hour']) ?
                (int)$inputs[$inputBag][$openCloseSpecial_bag][$i]['open_at']['hour'] : 0;
            $inputs[$inputBag][$openCloseSpecial_bag][$i]['open_at']['minute'] = isset($inputs[$inputBag][$openCloseSpecial_bag][$i]['open_at']['minute']) ?
                (int)$inputs[$inputBag][$openCloseSpecial_bag][$i]['open_at']['minute'] : 0;
            $inputs[$inputBag][$openCloseSpecial_bag][$i]['close_at']['hour'] = isset($inputs[$inputBag][$openCloseSpecial_bag][$i]['close_at']['hour']) ?
                (int)$inputs[$inputBag][$openCloseSpecial_bag][$i]['close_at']['hour'] : 0;
            $inputs[$inputBag][$openCloseSpecial_bag][$i]['close_at']['minute'] = isset($inputs[$inputBag][$openCloseSpecial_bag][$i]['close_at']['minute']) ?
                (int)$inputs[$inputBag][$openCloseSpecial_bag][$i]['close_at']['minute'] : 0;
            $inputs[$inputBag][$openCloseSpecial_bag][$i]['close_next_day'] = isset($inputs[$inputBag][$openCloseSpecial_bag][$i]['close_next_day']);
        }
        return $inputs;
    }

    public static function validateData(&$validatedData, $openClose_bag = false, $openCloseSpecial_bag = false) {
        $openClose_bag = $openClose_bag? $openClose_bag : 'open_close';
        $openCloseSpecial_bag = $openCloseSpecial_bag? $openCloseSpecial_bag : 'open_close_special';
        for($i=1; $i<8; $i++) {
            if(!$validatedData[$openClose_bag][$i]['close_next_day']) {
                if($validatedData[$openClose_bag][$i]['open_at']['hour'] > $validatedData[$openClose_bag][$i]['close_at']['hour'] ) {
                    $buf = $validatedData[$openClose_bag][$i]['open_at'];
                    $validatedData[$openClose_bag][$i]['open_at'] = $validatedData[$openClose_bag][$i]['close_at'];
                    $validatedData[$openClose_bag][$i]['close_at'] = $buf;
                } elseif($validatedData[$openClose_bag][$i]['open_at']['hour'] == $validatedData[$openClose_bag][$i]['close_at']['hour'] ) {
                    if($validatedData[$openClose_bag][$i]['open_at']['minute'] > $validatedData[$openClose_bag][$i]['close_at']['minute']) {
                        $buf = $validatedData[$openClose_bag][$i]['open_at'];
                        $validatedData[$openClose_bag][$i]['open_at'] = $validatedData[$openClose_bag][$i]['close_at'];
                        $validatedData[$openClose_bag][$i]['close_at'] = $buf;
                    }
                }
            }
            $validatedData[$openClose_bag][$i]['open_at'] = "PT{$validatedData[$openClose_bag][$i]['open_at']['hour']}H{$validatedData[$openClose_bag][$i]['open_at']['minute']}M";
            $validatedData[$openClose_bag][$i]['close_at'] = "PT{$validatedData[$openClose_bag][$i]['close_at']['hour']}H{$validatedData[$openClose_bag][$i]['close_at']['minute']}M";

        }
        foreach((array)$validatedData[$openCloseSpecial_bag] as $i => $data) {
            if(!$validatedData[$openCloseSpecial_bag][$i]['close_next_day']) {
                if($validatedData[$openCloseSpecial_bag][$i]['open_at']['hour'] > $validatedData[$openCloseSpecial_bag][$i]['close_at']['hour'] ) {
                    $buf = $validatedData[$openCloseSpecial_bag][$i]['open_at'];
                    $validatedData[$openCloseSpecial_bag][$i]['open_at'] = $validatedData[$openCloseSpecial_bag][$i]['close_at'];
                    $validatedData[$openCloseSpecial_bag][$i]['close_at'] = $buf;
                } elseif($validatedData[$openCloseSpecial_bag][$i]['open_at']['hour'] == $validatedData[$openCloseSpecial_bag][$i]['close_at']['hour'] ) {
                    if($validatedData[$openCloseSpecial_bag][$i]['open_at']['minute'] > $validatedData[$openCloseSpecial_bag][$i]['close_at']['minute']) {
                        $buf = $validatedData[$openCloseSpecial_bag][$i]['open_at'];
                        $validatedData[$openCloseSpecial_bag][$i]['open_at'] = $validatedData[$openCloseSpecial_bag][$i]['close_at'];
                        $validatedData[$openCloseSpecial_bag][$i]['close_at'] = $buf;
                    }
                }
            }
            $validatedData[$openCloseSpecial_bag][$i]['open_at'] = "PT{$validatedData[$openCloseSpecial_bag][$i]['open_at']['hour']}H{$validatedData[$openCloseSpecial_bag][$i]['open_at']['minute']}M";
            $validatedData[$openCloseSpecial_bag][$i]['close_at'] = "PT{$validatedData[$openCloseSpecial_bag][$i]['close_at']['hour']}H{$validatedData[$openCloseSpecial_bag][$i]['close_at']['minute']}M";
        }
    }

    public static function submit(&$model, $validatedData, $openClose_bag=null, $openCloseSpecial_bag=null) {
        $openClose_bag = $openClose_bag?? 'open_close';
        $openCloseSpecial_bag = $openCloseSpecial_bag?? 'open_close_special';
        for ($i = 1; $i < 8; $i++) {
            $model->open_closes()->updateOrCreate([
                'type' => 'week',
                'type_value' => $i
            ], [
//                'site_id' => $model->site_id,
                'open_at' => $validatedData[$openClose_bag][$i]['open_at'],
                'close_at' => $validatedData[$openClose_bag][$i]['close_at'],
                'close_next_day' => $validatedData[$openClose_bag][$i]['close_next_day'],
            ]);
        }
        $model->open_closes_special()->delete();
        foreach ((array)$validatedData[$openCloseSpecial_bag] as $i => $data) {
            $typeValueDT = \DateTime::createFromFormat("d.m.Y H:i:s", "{$data['date']} 00:00:00" );
            $model->open_closes_special()->create([
                'type' => 'special',
                'type_value' => $typeValueDT->getTimestamp(),
//                'site_id' => $model->site_id,
                'open_at' => $data['open_at'],
                'close_at' => $data['close_at'],
                'close_next_day' => $data['close_next_day'],
            ]);
        }
        $model->open_closes()->where([
            'open_at' => 'PT0H0M',
            'close_at' => 'PT0H0M',
            'close_next_day' => false,
        ])->delete();
    }
}
