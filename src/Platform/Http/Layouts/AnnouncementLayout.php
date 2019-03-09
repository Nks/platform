<?php

declare(strict_types=1);

namespace Orchid\Platform\Http\Layouts;

use Orchid\Screen\Fields\SimpleMDE;
use Orchid\Screen\Layouts\Rows;

class AnnouncementLayout extends Rows
{
    /**
     * @throws \Throwable
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            SimpleMDE::make('announcement.content')
                ->type('text'),
        ];
    }
}
