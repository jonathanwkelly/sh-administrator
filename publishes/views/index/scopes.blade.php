@if (! empty($scopes = $filter->scopes()))
    <div class="pull-left">
        @foreach($scopes as $slug => $scope)
            {!! link_to($filter->makeScopedUrl($slug), $scope['name'], [
                'class' => 'btn btn-link',
                'style' => ($filter->scope() == $slug ? 'color: black' : '')
            ]) !!}
        @endforeach
    </div>
@endif
