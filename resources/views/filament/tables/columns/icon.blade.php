@php
    use App\Helpers\IconHelper;
@endphp

<div class="flex items-center gap-2">
    {!! IconHelper::get($getState()) !!}
</div>
