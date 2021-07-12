@section('scaffold.filter')

@if ($filter && count($elements = $filter->filters()))
<div class="box-body">
    <div class="row-fluid">
        <form action="" data-id="filter-form">
            <input type="hidden" name="sort_by"  value="{{ $sortable->element() }}" />
            <input type="hidden" name="sort_dir" value="{{ $sortable->direction() }}" />
            @if ($scope = $filter->scope())
                <input type="hidden" name="scoped_to" value="{{ $scope }}" />
            @endif

            @if ($magnet = app('scaffold.magnet'))
                @foreach ($magnet->toArray() as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                @endforeach
            @endif

            <div class="col-lg-10">
                @foreach($elements as $element)
                <div class="col-xs-3">
                    <div class="form-group">
                        {!! $element->html() !!}
                    </div>
                </div>
                @endforeach
            </div>

            <div class="col-lg-2">
                <button type="submit" class="btn btn-app btn-block">
                    <i class="fa fa-search"></i>
                    {{ trans('administrator::buttons.search') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scaffold.js')
    @include($template->scripts('listeners'))
@stop

@endif
