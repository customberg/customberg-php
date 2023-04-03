<?php

namespace App\Blocks;

class Features
{
    public static function render()
    {
        return [
            'name' => 'Features',
            'slug' => 'cb-features',
            'icon' => 'editor-ol',
            'fields' => [
                // supported types are:
                // 'text', 'rich_text', 'color',
                // 'upload_image' or 'upload_file' (accepts 'allowed_types' as array with file extensions, only file supports multiple=true)
                // 'repeatable' ( 'fields' => [  ] )
                // 'select' (
                //     'placeholder' => 'string',
                //     'multiple' => true/false,
                //     'disabled' => true/false,
                //     'default' => string or array,
                //     'options' => array with key => value,
                // )

                [
                    'name' => 'title',
                    'label' => 'Title',
                    'type' => 'text',
                    'multilanguage' => true,
                ],
                [
                    'name' => 'features',
                    'label' => 'List of features',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'image',
                            'label' => 'Feature image',
                            'type' => 'upload_image',
                            'allowed_types' => ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'],
                        ],
                        [
                            'name' => 'name',
                            'label' => 'Feature name',
                            'type' => 'text',
                            'multilanguage' => true,
                        ],
                        [
                            'name' => 'description',
                            'label' => 'Description',
                            'type' => 'rich_text',
                            'multilanguage' => true,
                        ],
                        [
                            'name' => 'info',
                            'label' => 'Information PDF file',
                            'type' => 'upload_file',
                            'allowed_types' => ['pdf', 'txt', 'doc', 'docx', 'csv'],
                            'multiple' => true,
                        ],
                    ],
                ],
            ],
        ];
    }
}
