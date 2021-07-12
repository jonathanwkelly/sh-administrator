@if (\admin\helpers\exportable($module))
    <div class="export-collection" style="padding: 10px 0;">
        {{ trans('administrator::buttons.download') }}: {!! join(" ", array_map(function($format) use ($module) {
            return link_to($module->makeExportableUrl($format), mb_strtoupper($format));
        }, $module->formats())) !!}
    </div>
@endif
