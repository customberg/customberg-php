<?php

namespace Customberg\PHP;

use Spatie\Macroable\Macroable;

class Customberg
{
    use Macroable;

    protected static $instance;
    protected $config;

    public function __construct()
    {
        return $this;
    }

    public static function getInstance(): Customberg
    {
        if (!isset(static::$instance)) {
            static::$instance = new Customberg();
        }
        return static::$instance;
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

    public static function __callStatic($method, $args = [])
    {
        if (substr($method, 0, 1) == '$') {
            $method = substr($method, 1);
        }
        if ($method == 'instance') {
            return static::$instance;
        }
        return call_user_func_array([static::getInstance(), $method], $args);
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
        $langs = array_keys(config('customberg.languages'));
        foreach ($this->getBlocks() as $block) {
            $block['multilanguage'] = false;
            $calculateAttributes = function ($fields, $calculateAttributes) use ($langs, &$block) {
                $attributes = [];
                foreach ($fields as $field) {
                    $definition = [];
                    if (in_array($field['type'], ['text', 'upload_image'])) {
                        $definition = [
                            'type' => 'string',
                        ];
                    }
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
                    if ($field['type'] == 'repeatable') {
                        $definition = [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => $calculateAttributes($field['fields'], $calculateAttributes),
                            ],
                        ];
                    }
                    $attributes[$field['name']] = $definition;
                }
                return $attributes;
            };
            $attributesJson = json_encode($calculateAttributes($block['fields'], $calculateAttributes));
            $blockJson = json_encode($block);
            $init .= "
                if (typeof window.CustombergEdit != 'undefined') {
                    window.Laraberg.registerBlock('cb/{$block['slug']}', {
                        title: '{$block['name']}',
                        icon: '{$block['icon']}',
                        category: 'custom-blocks',
                        attributes: $attributesJson,
                        edit: window.CustombergEdit($blockJson),
                        save: function (props) { return null; },
                    });
                }
            ";
        }
        return $init;
    }

    public function renderBlocks($html, $lang = null)
    {
        $activeLang = $lang ? $lang : app()->getLocale();

        function str_replace_limit($find, $replacement, $subject, $limit = 0)
        {
            if ($limit == 0) {
                return str_replace($find, $replacement, $subject);
            }
            return preg_replace('/' . preg_quote($find, '/') . '/', $replacement, $subject, $limit);
        }

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
                if (strpos($matched_block[1], $block_prefix) === 0) {
                    $blockSlug = substr($matched_block[1], strlen($block_prefix));
                    if (\View::exists('blocks.' . $blockSlug)) {
                        foreach ($attributes as $attrName => &$value) {
                            if (
                                isset($multilanguageFields[$blockSlug]) &&
                                isset($multilanguageFields[$blockSlug][$attrName])
                            ) {
                                if (gettype($value) == 'string') {
                                    $value = ['ro' => $value];
                                }
                                $value = isset($value[$activeLang])
                                    ? $value[$activeLang]
                                    : (isset($value['ro'])
                                        ? $value['ro']
                                        : '');
                            }
                            if (isset($repeatableFields[$blockSlug][$attrName])) {
                                // is repetable. do foreach
                                if (gettype($value) == 'array') {
                                    foreach ($value as &$item) {
                                        foreach ($item as $subKey => &$subValue) {
                                            if (isset($multilanguageFields[$blockSlug]["$attrName-$subKey"])) {
                                                if (gettype($subValue) == 'string') {
                                                    $subValue = ['ro' => $subValue];
                                                }
                                                $subValue = isset($subValue[$activeLang])
                                                    ? $subValue[$activeLang]
                                                    : (isset($subValue['ro'])
                                                        ? $subValue['ro']
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
