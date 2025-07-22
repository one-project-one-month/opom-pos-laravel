<x-filament::page>
    <div class="flex gap-4 mb-6">
        <x-filament::input label="From Date" type="date" wire:model.lazy="fromDate" />
        <x-filament::input label="To Date" type="date" wire:model.lazy="toDate" />
    </div>

    <table class="filament-tables-table w-full text-sm">
        <thead>
            <tr class="filament-tables-table-header-row">
                <th class="filament-tables-table-header-cell text-left">Product</th>
                <th class="filament-tables-table-header-cell text-left">Total Quantity Sold</th>
                <th class="filament-tables-table-header-cell text-left">Total Sales Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $item)
                <tr class="filament-tables-table-row" style="transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#ffe4b5'" onmouseout="this.style.backgroundColor=''">
                <td class="filament-tables-table-cell">{{ $item->product->name ?? 'N/A' }}</td>
                    <td class="filament-tables-table-cell">{{ $item->total_quantity }}</td>
                    <td class="filament-tables-table-cell">{{ number_format($item->total_sales, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <div class="mt-4 font-bold text-right">
        Total Quantity: {{ $totalQuantity }} <br />
        Total Sales: {{ number_format($totalAmount, 2) }}
    </div>
</x-filament::page>
