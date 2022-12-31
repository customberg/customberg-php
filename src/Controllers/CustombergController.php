<?php

namespace Customberg\PHP\Controllers;

use App\Http\Controllers\Controller;
use Customberg\PHP\Customberg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustombergController extends Controller
{
    public function preview(Request $request)
    {
        $activeLang = 'ro'; //app()->getLocale();
        $view = preg_replace('/[^a-zA-Z0-9\-\.]*/', '', $request->slug);

        // get multilanguage fields
        $allBocks = Customberg::getBlocks();
        $multilanguageFields = [];
        $repeatableFields = [];
        foreach ($allBocks as $block_data) {
            if ($block_data['slug'] != $view) {
                continue;
            }
            foreach ($block_data['fields'] as $field) {
                if (isset($field['multilanguage']) && $field['multilanguage']) {
                    $multilanguageFields[$field['name']] = true;
                }
                if ($field['type'] == 'repeatable') {
                    $repeatableFields[$field['name']] = true;
                    foreach ($field['fields'] as $subField) {
                        if (isset($subField['multilanguage']) && $subField['multilanguage']) {
                            $multilanguageFields["{$field['name']}-{$subField['name']}"] = true;
                        }
                    }
                }
            }
        }

        $attributes = $request->get('attributes');
        if (!$attributes) {
            $attributes = [];
        }

        foreach ($attributes as $attrName => &$value) {
            if (isset($multilanguageFields[$attrName])) {
                if (gettype($value) == 'string') {
                    $value = ['ro' => $value];
                }
                $value = isset($value[$activeLang]) ? $value[$activeLang] : $value;
            }
            if (isset($repeatableFields[$attrName])) {
                // is repetable. do foreach
                if (gettype($value) == 'array') {
                    foreach ($value as &$item) {
                        foreach ($item as $subKey => &$subValue) {
                            if (isset($multilanguageFields["$attrName-$subKey"])) {
                                if (gettype($subValue) == 'string') {
                                    $subValue = ['ro' => $subValue];
                                }
                                $subValue = isset($subValue[$activeLang]) ? $subValue[$activeLang] : $subValue;
                            }
                        }
                    }
                } else {
                    $subValue = [];
                }
            }
        }
        return view('customberg::preview', [
            'file' => "blocks.$view",
            'data' => $attributes,
        ]);
    }

    public function file_upload(Request $request)
    {
        $config = Customberg::getConfig();
        $disk = $config['upload']['disk'] ?? 'public';
        $path = $config['upload']['path'] ?? '';

        $now = now();
        $pathVars = [
            '{Y}' => $now->format('Y'),
            '{m}' => $now->format('m'),
            '{d}' => $now->format('d'),
        ];
        $path = str_replace(array_keys($pathVars), array_values($pathVars), $path);

        $urls = [];
        if ($request->file('files')) {
            foreach ($request->file('files') as $file) {
                $ext = '.' . $file->getClientOriginalExtension();
                $originalFileName = $fileName = Str::slug(str_replace($ext, '', $file->getClientOriginalName()));
                $uniqueCount = 1;
                while (Storage::disk($disk)->exists($path . '/' . $fileName . $ext)) {
                    $fileName = $originalFileName . '-' . ++$uniqueCount;
                }
                $file->storeAs($path, $fileName . $ext, $disk);
                array_push($urls, "/storage/$path/$fileName$ext");
            }
        }
        return $urls;
    }
}
