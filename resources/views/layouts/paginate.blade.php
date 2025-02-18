

<div class="flex justify-center text-xs mt-2 ">
    @if(count($documents) < 10)
        <p class="text-center">
            Total <span class="text-red-600 px-2">{{ count($documents) }}</span> Record
        </p>
    @endif
    <div class="mb-2 mt-2 ">
        {{-- {!! $documents->render() !!} --}}
        {{ $documents->appends(request()->query())->links() }}
    </div>
</div>

