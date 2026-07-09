@php
use App\OrderStatus;

$order = $getRecord();
$histories = $order->statusHistories
    ->keyBy('status');
$currentStatus = $order->status;

$isCanceled = $currentStatus === OrderStatus::Canceled;

if ($isCanceled) {

    $lastHistoryBeforeCancel = $order->statusHistories
        ->filter(function ($history) {
            $statusVal = $history->status instanceof OrderStatus ? $history->status->value : $history->status;
            return $statusVal != OrderStatus::Canceled->value;
        })
        ->last();

    if ($lastHistoryBeforeCancel) {
        $activeStatus = $lastHistoryBeforeCancel->status instanceof OrderStatus 
            ? $lastHistoryBeforeCancel->status 
            : OrderStatus::tryFrom($lastHistoryBeforeCancel->status);
    }
    
    $activeStatus = $activeStatus ?? OrderStatus::Pending;
} else {
    $activeStatus = $currentStatus;
}

$activeIndex = array_search(
    $activeStatus->value,
    array_column(OrderStatus::cases(), 'value')
);
@endphp

<div class="flex items-center justify-center">

    <ul class="relative flex flex-row flex-1 gap-x-2 mt-3">
        @foreach(OrderStatus::cases() as $index => $status)
            @continue($status === OrderStatus::Canceled)

            @php
                $history = $histories->get($status->value);

                $date = match ($status) {
                    OrderStatus::Pending => $order->created_at,
                    default => $history?->created_at,
                };
            @endphp

            <li class="shrink basis-0 flex-1 group">
                <div class="min-w-7 min-h-7 w-full inline-flex items-center text-xs align-middle">
                    <span class="size-7 flex basis-16 justify-center items-center shrink-0 bg-surface font-medium text-surface-foreground rounded-full">
                        @if ($index === $activeIndex)
                            @if ($isCanceled)
                                <x-filament::icon-button
                                    icon="heroicon-m-x-circle"
                                    color="danger"
                                    disabled="true"
                                />
                            @else
                                <x-filament::icon-button
                                    icon="heroicon-m-clock"
                                    color="warning"
                                    disabled="true"
                                />
                            @endif
                            
                        @elseif ($index < $activeIndex)
                            <x-filament::icon-button
                                icon="heroicon-m-check-circle"
                                color="success"
                                disabled="true"
                            />
                        @else
                            &#x2022;
                        @endif
                    </span>
                    <div class="ms-2 w-full h-px flex-1 bg-surface-1 border group-last:hidden"></div>
                </div>
                <div class="mt-3 min-w-7 min-h-7 w-full inline-flex items-center text-xs align-middle">
                    <div class="flex flex-col basis-16 justify-center items-center min-h-16">
                        <span class="block text-sm font-medium text-foreground">
                            {{ $status->getLabel() }}
                        </span>
                        <span class='text-center text-xs min-h-5 mt-1'>
                            @if ($date)
                                {{ $date->format('d M Y H:i') }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="ms-2 w-full h-px flex-1 bg-surface-1 group-last:hidden"></div>
                </div>
            </li>
            
        @endforeach
    </ul>

</div>