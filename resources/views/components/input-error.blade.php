@props(['messages'])

@if ($messages)
    {{-- <ul {{ $attributes->merge(['class' => 'text-sm text-danger dark:text-red-400 space-y-1' ]) }}> --}}
    @foreach ((array) $messages as $message)
        <div class="text-sm text-danger space-y-1">{{ $message }}</div>
    @endforeach
    {{-- </ul> --}}
@endif
