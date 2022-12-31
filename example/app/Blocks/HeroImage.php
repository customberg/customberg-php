<?php

namespace App\Blocks;

use App\Models\Page;

class HeroImage
{
    public static function render()
    {
        $pages = Page::pluck('title', 'id');
        return [
            'name' => 'Hero Image',
            'slug' => 'cb-hero-image',
            'icon' => 'block-default',
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
                    'label' => 'Titlu',
                    'type' => 'text',
                ],
                [
                    'name' => 'description',
                    'label' => 'Description',
                    'type' => 'rich_text',
                ],
                [
                    'name' => 'btn_color',
                    'label' => 'Button Color',
                    'type' => 'color',
                ],
                [
                    'name' => 'btn_page',
                    'label' => 'Button Page',
                    'type' => 'select',
                    'placeholder' => 'Select a page to link to',
                    'options' => $pages,
                ],
                [
                    'name' => 'image',
                    'label' => 'Hero image',
                    'type' => 'upload_image',
                ],
            ],
        ];
    }
}
