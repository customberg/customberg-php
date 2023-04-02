<?php

namespace Customberg\PHP\Controllers;

use App\Http\Controllers\Controller;
use Customberg\PHP\Customberg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustombergController extends Controller
{
    public function preview(Request $request)
    {
        $config = Customberg::getConfig();
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
        $activeLang = isset($attributes['activeLang'])
            ? $attributes['activeLang']
            : ($config['default_language'] ?:
            app()->getLocale());

        foreach ($attributes as $attrName => &$value) {
            if (isset($multilanguageFields[$attrName])) {
                if (gettype($value) == 'string') {
                    $value = [$activeLang => $value];
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
                                    $subValue = [$activeLang => $subValue];
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
        $disk = $config['upload']['disk'] ?: 'public';
        $path = $config['upload']['path'] ?: '';

        $now = now();
        $pathVars = [
            '{Y}' => $now->format('Y'),
            '{m}' => $now->format('m'),
            '{d}' => $now->format('d'),
        ];
        $path = str_replace(array_keys($pathVars), array_values($pathVars), $path);

        $block_slug = $request->get('block_slug', null);
        $self_path = $request->get('self_path', null);
        $block = null;
        $field = null;
        if ($block_slug) {
            $allBocks = Customberg::getBlocks();
            foreach ($allBocks as $item) {
                if ($item['slug'] == $block_slug) {
                    $block = $item;
                    $field = $self_path ? data_get($block, $self_path, null) : null;
                }
            }
        }
        // prettier-ignore
        $allowed_types = $field && isset($field['allowed_types'])
            ? $field['allowed_types']
            : (optional($config['upload'])['default_allowed_types'] ?: []);

        $urls = [];
        $errors = [];
        if ($request->file('files')) {
            foreach ($request->file('files') as $file) {
                $validator = Validator::make(
                    ['file' => $file],
                    ['file' => ['file', 'mimes:' . implode(',', $allowed_types)]]
                );
                if ($validator->fails()) {
                    $errors[] = $validator->errors()->first('file');
                    continue;
                }
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
        return [
            'success' => true,
            'files' => $urls,
            'errors' => $errors,
        ];
    }
}
