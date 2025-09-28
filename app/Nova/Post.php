<?php

declare(strict_types=1);

namespace App\Nova;

use App\Enums\PostStatus;
use App\Nova\Actions\ArchivePost;
use App\Nova\Actions\PublishPost;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\ActionRequest;
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
        return [
            /*
             * canSee allows to completely hide the action from the UI. Does not have model instance available.
             * Workaround is to use $this->resource inside the closure, but it does not have the model instance
             * when on Action request, thus requires an additional check for ActionRequest. Otherwise, the action
             * will fail with 403 Forbidden when trying to run it.
            */
            ArchivePost::make()
                ->canSee(function ($request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }

                    return $request->user()->can('update-post', $this->resource);  // update-post is Gate ability, which checks if the user owns the post
                })
                ->canRun(function (NovaRequest $request, \App\Models\Post $post) {
                    return $post->status !== PostStatus::Archived  && $request->user()->can('update-post', $post);
                }),


            /*
             * action cannot be run, but is still visible in the UI. canRun
             * as model instance available on both initial and ActionRequest
            */
            PublishPost::make()
                ->canRun(function (NovaRequest $request, \App\Models\Post $post) {
                    return $post->status !== PostStatus::Published && $request->user()->can('update-post', $post);
                }),
        ];
    }
}
