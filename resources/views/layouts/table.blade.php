<div class="row">
    <table class="table">
        <thead>
        <tr>
            @foreach($columns as $th)
                <th width="{{$th->width}}" class="text-{{$th->align}}">
                    <div>
                        @if($th->sort)
                            <a href="?sort={{revert_sort($th->column)}}"
                               class="@if(!is_sort($th->column)) text-muted @endif">
                                {{$th->title}}

                                @if(is_sort($th->column))
                                    <i class="icon-sort-amount-{{get_sort($th->column)}}"></i>
                                @endif
                            </a>
                        @else
                            {{$th->title}}
                        @endif


                        @includeWhen(!is_null($th->filter), "platform::partials.filters.{$th->filter}", ['th' => $th])
                    </div>

                    @if($filter = get_filter_string($th->column))
                        <div data-controller="screen--filter">
                            <a href="#"
                               data-action="screen--filter#clearFilter"
                               data-filter="{{$th->column}}"
                               class="badge badge-pill badge-light">
                                {{ $filter }}
                            </a>
                        </div>
                    @endif
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody>

        @foreach($rows as $key => $datum)
            <tr>
                @foreach($columns as $td)
                    <td class="text-{{$td->align}}">

                        @isset($td->render)
                            {!! $td->handler($datum) !!}
                        @else
                            {{ $datum->getContent($td->name) }}
                        @endisset
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>

    @if(
    (method_exists($rows,'total') && optional($rows)->total() === 0) ||
    (method_exists($rows,'count') && optional($rows)->count() === 0) ||
    (is_array($rows) && count($rows) === 0)
    )

        <div class="text-center bg-white pt-5 pb-5 w-full">
            <h3 class="font-thin">
                <i class="{{ $iconNotFound }} block m-b"></i>
                {!!  $textNotFound !!}
            </h3>

            {!! $subNotFound !!}
        </div>

    @endif


    @includeWhen($rows instanceof \Illuminate\Contracts\Pagination\Paginator && $rows->total() > 0,
        'platform::layouts.pagination',
        ['paginator' => $rows]
      )

</div>


