<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Models\OrderItem;
use Carbon\Carbon;
use App\Models\Order;
use Filament\Pages\Page;
use Livewire\WithPagination;
use App\Filament\Resources\AdminResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\DB;
class SalesReport extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.resources.admin-resource.pages.sales-report';
    protected static ?string $navigationLabel = 'Sales Report';
    protected static ?string $title = 'Sales Report';
    protected static bool $shouldRegisterNavigation = true;

    use WithPagination;

     use InteractsWithForms;

     public $fromDate;
    public $toDate;
    public $sales = [];
    public $totalQuantity = 0;
    public $totalAmount = 0;



    public function mount()
    {
        // Optionally set default dates
        $this->fromDate = now()->startOfMonth()->toDateString();
        $this->toDate = now()->endOfMonth()->toDateString();

        $this->loadSales();
    }

    public function loadSales()
    {
        $query = OrderItem::query()
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total) as total_sales'))
            ->when($this->fromDate && $this->toDate, function ($q) {
                $q->whereHas('order', function ($q2) {
                    $q2->whereBetween('created_at', [
                        Carbon::parse($this->fromDate)->startOfDay(),
                        Carbon::parse($this->toDate)->endOfDay(),
                    ]);
                });
            })
            ->groupBy('product_id')
            ->with('product')
            ->get();

        $this->sales = $query;
        $this->totalQuantity = $this->sales->sum('total_quantity');
        $this->totalAmount = $this->sales->sum('total_sales');
    }

    public function updatedFromDate()
    {
        $this->loadSales();
    }

    public function updatedToDate()
    {
        $this->loadSales();
    }
}
