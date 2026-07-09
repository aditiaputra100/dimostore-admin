<?php
use Filament\Support\Enums\Size;
use App\OrderStatus;

$order = $getRecord();
$statuses = array_reverse(OrderStatus::cases());
$historyMap = $order->statusHistories->keyBy(function($history) {
    return is_string($history->status) ? $history->status : $history->status->value;
});

$orderStatusValues = array_column(OrderStatus::cases(), 'value');

$isCanceled = $order->status === OrderStatus::Canceled;
$canceledAtStatus = null;

if ($isCanceled) {
    $lastHistoryBeforeCancel = $order->statusHistories
        ->filter(function ($history) {
            $statusVal = $history->status instanceof OrderStatus ? $history->status->value : $history->status;
            return $statusVal != OrderStatus::Canceled->value;
        })
        ->last();

    if ($lastHistoryBeforeCancel) {
        $canceledAtStatus = $lastHistoryBeforeCancel->status instanceof OrderStatus 
            ? $lastHistoryBeforeCancel->status 
            : OrderStatus::tryFrom($lastHistoryBeforeCancel->status);
    }
    
    $canceledAtStatus = $canceledAtStatus ?? OrderStatus::Pending;
}
?>

<div class="p-6">
    <ol class="relative border-s border-default">
        @foreach ($statuses as $status)
            @php
                if ($status === OrderStatus::Canceled) {
                    continue;
                }

                $isActive = $order->status === $status;
                $isComplete = $order->isDone($status);

                $isCanceledPoint = $isCanceled && $status === $canceledAtStatus;

                $history = $historyMap->get($status->value);

                if ($isCanceled) {
                    $currentIdx = array_search($status->value, $orderStatusValues);
                    $canceledIdx = array_search($canceledAtStatus->value, $orderStatusValues);
                    
                    $shouldDisplay = $currentIdx <= $canceledIdx;
                } else {
                    $isActive = $order->status === $status;
                    $isComplete = $order->isDone($status);
                    $shouldDisplay = $isActive || $isComplete || $history;
                }   

                if (!$shouldDisplay) {
                    continue;
                }
                
                if ($isCanceledPoint) {
                    $cancelHistory = $historyMap->get(OrderStatus::Canceled->value);
                    $timestamp = $cancelHistory?->created_at ?? $history?->created_at ?? $order->updated_at;
                    $updatedBy = $cancelHistory?->createdBy 
                        ? "{$cancelHistory->createdBy->name} ({$cancelHistory->createdBy->email})" 
                        : ($history?->createdBy ? "{$history->createdBy->name} ({$history->createdBy->email})" : "-");
                    $note = $cancelHistory?->note ?? $history?->note ?? 'Order Canceled';
                } else if ($status === OrderStatus::Pending && !$history) {
                    $timestamp = $order->created_at;
                    $updatedBy = 'Order created';
                    $note = $order->notes ?? '-';
                } else {
                    $timestamp = $history?->created_at;
                    $updatedBy = $history?->createdBy ? "{$history->createdBy->name} ({$history->createdBy->email})" : "-";
                    $note = $history?->note ?? '-';
                }
            @endphp

            <li class="mb-10 ms-6">            
                <span class="absolute flex items-center justify-center w-6 h-6 bg-brand-softer rounded-full -inset-s-3">
                    @if ($isCanceled)
                        <x-filament::icon-button
                            icon="heroicon-m-x-circle"
                            color="danger"
                            :disabled="true"
                            :size="Size::Large"
                        />
                    @elseif ($isComplete)
                        <x-filament::icon-button
                            icon="heroicon-m-check-circle"
                            color="success"
                            :disabled="true"
                            :size="Size::Large"
                        />
                    @elseif ($isActive)
                        <x-filament::icon-button
                            icon="heroicon-m-arrow-right-circle"
                            :disabled="true"
                            :size="Size::Large"
                            class="animate-pulse"
                        />
                    @else
                        <x-filament::icon-button
                            icon="heroicon-m-clock"
                            color="gray"
                            :disabled="true"
                            :size="Size::Large"
                        />
                    @endif
                </span>
                <div class="flex justify-between text-base font-medium">
                    <span>{{ $status->getLabel() }}</span>
                    <time class="px-1.5 py-0.5">{{ $timestamp ? $timestamp->format('d M Y H:i') : '--:--' }}</time>
                </div>
                <h3 class="my-2">Updated by: {{ $updatedBy }}</h3>
                <p class="text-body">Notes: {{ $note }}</p>
            </li>
        @endforeach
    </ol>
</div>