<?php

namespace Customberg\PHP\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustombergController extends Controller
{
    public function preview(Request $request)
    {
        $activeLang = 'ro'; //app()->getLocale();
        $view = preg_replace('/[^a-zA-Z0-9\-\.]*/', '', $request->slug);

        // get multilanguage fields
        $allBocks = get_tm_blocks();
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
        return view("blocks.$view", $attributes);
    }
}
