<?php

declare(strict_types=1);

namespace Orchid\Press\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Platform\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\DateRange;

class CreatedFilter extends Filter
{
    /**
     * @var array
     */
    public $parameters = [
        'start_created_at',
        'end_created_at',
    ];

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        return $builder->where('created_at', '>', $this->request->get('start_created_at'))
            ->where('created_at', '<', $this->request->get('end_created_at'));
    }

    /**
     * @return Field
     */
    public function display() : Field
    {
        return DateRange::make('created_at')
            ->title(__('Date of creation'))
            ->value([
                'start' => $this->request->get('start_created_at'),
                'end'   => $this->request->get('end_created_at'),
            ]);
    }
}
