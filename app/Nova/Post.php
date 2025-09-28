<?php

declare(strict_types=1);

namespace App\Nova;

use App\Enums\PostStatus;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Post extends Resource
{
    public static $model = \App\Models\Post::class;

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Title')
                ->rules(['required', 'max:255']),

            Textarea::make('Body')
                ->rules(['required', 'max:65535'])
                ->alwaysShow(),

            Select::make('Status')
                ->options(PostStatus::class)
                ->rules(['required']),

            BelongsTo::make('User'),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [];
    }
}
