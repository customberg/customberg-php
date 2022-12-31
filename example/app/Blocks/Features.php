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
                // text, rich_text, color, upload_image
                // repeatable ( 'fields' => [  ] )
                // select (
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
                        ],
                        [
                            'name' => 'name',
                            'label' => 'Feature name',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'description',
                            'label' => 'Description',
                            'type' => 'rich_text',
                        ],
                    ],
                ],
            ],
        ];
    }
}
