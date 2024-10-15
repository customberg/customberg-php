<?php

namespace Customberg\PHP;

use Spatie\Macroable\Macroable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;

class CustombergInstance
{
    use Macroable;

    protected static $instance;
    protected $config;

    public function __construct()
    {
        return $this;
    }

    public function setConfig($config = null)
    {
        $this->config = $config;
        return $this;
    }
    public function getConfig()
    {
        return $this->config;
    }

    public function getBlocks()
    {
        $blocks = [];
        foreach (glob(app_path('Blocks/*.php')) as $filename) {
            $className = pathinfo($filename, PATHINFO_FILENAME);
            if (!$className) {
                continue;
            }
            $blocks[] = call_user_func(["App\\Blocks\\$className", 'render']);
        }
        return $blocks;
    }

    public function loadBlocks()
    {
        $init = '';
        $config = Customberg::getConfig();
        $langs = array_keys(config('customberg.languages'));
        $default_allowed_types = optional($config['upload'])['default_allowed_types'] ?: [];
        foreach ($this->getBlocks() as $block) {
            $blockDotMap = Arr::dot($block);
            $block['multilanguage'] = false;
            $calculateAttributes = function ($fields, $calculateAttributes) use ($langs, &$block) {
                $attributes = [];
                foreach ($fields as $field) {
                    $definition = [];
                    if ($field['type'] == 'repeatable') {
                        $definition = [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => $calculateAttributes($field['fields'], $calculateAttributes),
                            ],
                        ];
                    } else {
                        $definition = [
                            'type' => 'string',
                        ];
                        if (isset($field['multilanguage']) && $field['multilanguage']) {
                            $block['multilanguage'] = true;
                            $props = [];
                            foreach ($langs as $lang) {
                                $props[$lang] = $definition;
                            }
                            $definition = [
                                'type' => 'object',
                                'properties' => $props,
                            ];
                        }
                    }
                    $attributes[$field['name']] = $definition;
                }
                return $attributes;
            };
            $attributesJson = json_encode($calculateAttributes($block['fields'], $calculateAttributes));
            // add self_path to some fields for validation purposes
            foreach ($blockDotMap as $key => $value) {
                if (in_array($value, ['upload_image', 'upload_file'], true)) {
                    $base_path = str_replace('.type', '', $key);
                    data_set($block, "$base_path.self_path", $base_path);
                    if (!data_get($block, "$base_path.allowed_types", null)) {
                        data_set($block, "$base_path.allowed_types", $default_allowed_types);
                    }
                }
            }
            $blockJson = json_encode($block);
            $init .= "
                try {
                    window.Laraberg.registerBlockType('cb/{$block['slug']}', {
                        title: '{$block['name']}',
                        icon: '{$block['icon']}',
                        category: 'customberg',
                        attributes: $attributesJson,
                        edit: window.CustombergEditFields($blockJson),
                        save: function (props) { return null; },
                    });
                } catch (e) {
                    console.error('Error registering block: cb/{$block['slug']}', e.message, { e });
                }
            ";
        }
        return "
            if (typeof window.CustombergEditFields != 'undefined') {
                $init
            }
        ";
    }

    public function render($html, $lang = null)
    {
        $config = $this->getConfig();
        $activeLang = $lang ?: (app()->getLocale() ?: $config['default_language']);
        $html = str_replace('-->', "-->\n", $html);

        $block_prefix = 'cb/';
        preg_match_all('/\<\!\-\-\s*wp\:([^\ ]+)\s*(.*)\s+\/?\-\-\>/i', $html, $blocks_found, PREG_SET_ORDER);

        // get multilanguage fields
        $allBocks = $this->getBlocks();
        $multilanguageFields = [];
        $repeatableFields = [];
        foreach ($allBocks as $block_data) {
            $multilanguageFields[$block_data['slug']] = [];
            $repeatableFields[$block_data['slug']] = [];
            foreach ($block_data['fields'] as $field) {
                if (isset($field['multilanguage']) && $field['multilanguage']) {
                    $multilanguageFields[$block_data['slug']][$field['name']] = true;
                }
                if ($field['type'] == 'repeatable') {
                    $repeatableFields[$block_data['slug']][$field['name']] = true;
                    foreach ($field['fields'] as $subField) {
                        if (isset($subField['multilanguage']) && $subField['multilanguage']) {
                            $multilanguageFields[$block_data['slug']]["{$field['name']}-{$subField['name']}"] = true;
                        }
                        if ($subField['type'] == 'repeatable') {
                            $repeatableFields[$block_data['slug']]["{$field['name']}-{$subField['name']}"] = true;
                            foreach ($subField['fields'] as $subsubField) {
                                if (isset($subsubField['multilanguage']) && $subsubField['multilanguage']) {
                                    $multilanguageFields[$block_data['slug']]["{$field['name']}-{$subField['name']}-{$subsubField['name']}"] = true;
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($blocks_found) {
            foreach ($blocks_found as $matched_block) {
                if (!$matched_block[2]) {
                    $matched_block[2] = '[]';
                }
                $attributes = json_decode($matched_block[2], true);
                if (isset($matched_block[1]) && strpos($matched_block[1], $block_prefix) === 0) {
                    $blockSlug = substr($matched_block[1], strlen($block_prefix));
                    if (View::exists('blocks.' . $blockSlug)) {
                        foreach ($attributes as $attrName => &$value) {
                            if (isset($multilanguageFields[$blockSlug]) && isset($multilanguageFields[$blockSlug][$attrName])) {
                                if (gettype($value) == 'string') {
                                    $value = [$activeLang => $value];
                                }
                                $value = isset($value[$activeLang])
                                    ? $value[$activeLang]
                                    : (isset($value[$config['default_language']])
                                        ? $value[$config['default_language']]
                                        : '');
                            }
                            if (isset($repeatableFields[$blockSlug][$attrName])) {
                                // is repetable. do foreach
                                if (gettype($value) == 'array') {
                                    foreach ($value as &$item) {
                                        foreach ($item as $subKey => &$subValue) {
                                            if (isset($repeatableFields[$blockSlug]["$attrName-$subKey"])) {
                                                if (gettype($subValue) == 'array') {
                                                    foreach ($subValue as &$subItem) {
                                                        foreach ($subItem as $subsubKey => &$subsubValue) {
                                                            if (isset($multilanguageFields[$blockSlug]["$attrName-$subKey-$subsubKey"])) {
                                                                if (gettype($subsubValue) == 'string') {
                                                                    $subsubValue = [$activeLang => $subsubValue];
                                                                }
                                                                $subsubValue = isset($subsubValue[$activeLang])
                                                                    ? $subsubValue[$activeLang]
                                                                    : (isset($subsubValue[$config['default_language']])
                                                                        ? $subsubValue[$config['default_language']]
                                                                        : '');
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    $subValue = [];
                                                }
                                                continue;
                                            }
                                            if (isset($multilanguageFields[$blockSlug]["$attrName-$subKey"])) {
                                                if (gettype($subValue) == 'string') {
                                                    $subValue = [$activeLang => $subValue];
                                                }
                                                $subValue = isset($subValue[$activeLang])
                                                    ? $subValue[$activeLang]
                                                    : (isset($subValue[$config['default_language']])
                                                        ? $subValue[$config['default_language']]
                                                        : '');
                                            }
                                        }
                                    }
                                } else {
                                    $subValue = [];
                                }
                            }
                        }
                        $content = view('blocks.' . $blockSlug, $attributes);
                        $html = str_replace_limit($matched_block[0], $content, $html, 1);
                    }
                }
            }
        }

        return $html;
    }
}

function str_replace_limit($find, $replacement, $subject, $limit = 0) {
    // If no limit is set, use regular str_replace
    if ($limit == 0) {
        return str_replace($find, $replacement, $subject);
    }
    $pos = 0; // Position to start searching
    $count = 0; // Keep track of how many replacements we've made
    while (($pos = strpos($subject, $find, $pos)) !== false) {
        // Replace only up to the limit
        if (++$count > $limit) {
            break;
        }
        // Perform the replacement
        $subject = substr_replace($subject, $replacement, $pos, strlen($find));
        // Move the position forward by the length of the replacement
        $pos += strlen($replacement);
    }
    return $subject;
}
