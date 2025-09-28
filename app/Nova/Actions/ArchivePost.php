<?php

declare(strict_types=1);

namespace App\Nova\Actions;

use App\Enums\PostStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class ArchivePost extends Action
{
    use InteractsWithQueue;
    use Queueable;

    public $showInline = true;

    public $sole = true;

    public function handle(ActionFields $fields, Collection $models): void
    {
        $models->each(function (Model $model) {
            $model->update(['status' => PostStatus::Archived]);
        });
    }
}
