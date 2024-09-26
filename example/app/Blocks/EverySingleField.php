<?php

namespace App\Blocks;

class EverySingleField
{
    public static function render()
    {
        return [
            'name' => 'EverySingleField',
            'slug' => 'cb-every-single-field',
            'icon' => 'block-default', // https://developer.wordpress.org/resource/dashicons/
            'fields' => [
                // supported types are:
                // 'text', 'number', 'email', 'url',
                // 'textarea', 'rich_text', 'color', 'checkbox',
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
                    'name' => 'demo_text',
                    'label' => 'Demo Text Field',
                    'type' => 'text',
                    'placeholder' => 'Type something',
                    'multilanguage' => true,
                ],
                [
                    'name' => 'demo_number',
                    'label' => 'Demo Number Field',
                    'type' => 'number',
                    'placeholder' => 'Type something',
                    'multilanguage' => true,
                ],
                [
                    'name' => 'demo_textarea',
                    'label' => 'Demo Textarea Field',
                    'placeholder' => 'Type something',
                    'type' => 'textarea',
                    'multilanguage' => true,
                ],
                [
                    'name' => 'demo_checkbox',
                    'label' => 'Demo Checkbox Field',
                    'type' => 'checkbox',
                    'multilanguage' => true,
                ],
                [
                    'name' => 'demo_richtext',
                    'label' => 'Demo Rich Text Field',
                    'type' => 'rich_text',
                    'multilanguage' => true,
                ],
                [
                    'name' => 'demo_color',
                    'label' => 'Demo Color Field',
                    'type' => 'color',
                ],
                [
                    'name' => 'demo_select',
                    'label' => 'Demo Select Field',
                    'type' => 'select',
                    'placeholder' => 'Select an option from the dropdown',
                    'options' => [
                        'value-1' => 'Option 1',
                        'value-2' => 'Option 2',
                        'value-3' => 'Option 3',
                    ],
                ],
                [
                    'name' => 'demo_image',
                    'label' => 'Demo Upload Image',
                    'type' => 'upload_image',
                    'allowed_types' => ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'],
                ],
                [
                    'name' => 'demo_file',
                    'label' => 'Demo Upload File - multiple',
                    'type' => 'upload_file',
                    'allowed_types' => ['pdf', 'txt', 'doc', 'docx', 'csv'],
                    'multiple' => true,
                ],
                [
                    'name' => 'demo_repeatable',
                    'label' => 'Demo Repeatable',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'demo_repeat_file',
                            'label' => 'Repeatable Upload File - single',
                            'type' => 'upload_file',
                            'allowed_types' => ['pdf', 'txt', 'doc', 'docx', 'csv'],
                        ],
                        [
                            'name' => 'demo_repeat_text',
                            'label' => 'Repeatable Text',
                            'type' => 'text',
                            'multilanguage' => true,
                        ],
                    ],
                ],
            ],
        ];
    }
}
