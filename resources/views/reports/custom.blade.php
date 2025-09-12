<!-- resources/views/reports/custom.blade.php -->
@extends('layouts.app')

@section('title', 'Maxsus Hisobot')
@section('page-title', $title)

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4>{{ $title }}</h4>
            <div>
                <span class="text-muted">{{ $startDate }} - {{ $endDate }}</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chart-line"></i> {{ $title }}</h5>
    </div>
    <div class="card-body">
        @if($type === 'revenue')
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Sana</th>
                            <th>Daromad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d.m.Y') }}</td>
                            <td><strong>{{ number_format($item->revenue) }} so'm</strong></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">Ma'lumotlar topilmadi</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($data->count() > 0)
                    <tfoot>
                        <tr class="table-primary">
                            <th>Jami:</th>
                            <th>{{ number_format($data->sum('revenue')) }} so'm</th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        @elseif($type === 'orders')
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Sana</th>
                            <th>Buyurtmalar Soni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d.m.Y') }}</td>
                            <td><strong>{{ $item->orders_count }}</strong></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">Ma'lumotlar topilmadi</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($data->count() > 0)
                    <tfoot>
                        <tr class="table-primary">
                            <th>Jami:</th>
                            <th>{{ $data->sum('orders_count') }}</th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        @elseif($type === 'products')
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Mahsulot</th>
                            <th>Kategoriya</th>
                            <th>Sotilgan Miqdor</th>
                            <th>Daromad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $product)
                        <tr>
                            <td>{{ $product->name_uz }}</td>
                            <td>{{ $product->category->name_uz }}</td>
                            <td>{{ $product->total_quantity }} dona</td>
                            <td><strong>{{ number_format($product->total_revenue) }} so'm</strong></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Ma'lumotlar topilmadi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @elseif($type === 'customers')
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Sana</th>
                            <th>Yangi Mijozlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d.m.Y') }}</td>
                            <td><strong>{{ $item->new_customers }}</strong></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">Ma'lumotlar topilmadi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @elseif($type === 'rooms')
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Xona</th>
                            <th>Rezervatsiyalar</th>
                            <th>Daromad</th>
                            <th>Band bo'lish %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $room)
                        <tr>
                            <td>{{ $room->name_uz }}</td>
                            <td>{{ $room->total_bookings }}</td>
                            <td><strong>{{ number_format($room->total_revenue) }} so'm</strong></td>
                            <td>
                                @php
                                    $totalDays = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
                                    $occupancyRate = ($room->total_bookings / $totalDays) * 100;
                                @endphp
                                <span class="badge bg-{{ $occupancyRate > 70 ? 'success' : ($occupancyRate > 40 ? 'warning' : 'danger') }}">
                                    {{ number_format($occupancyRate, 1) }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Ma'lumotlar topilmadi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        @if($data->count() > 0)
        <div class="mt-3">
            <button class="btn btn-success" onclick="exportData()">
                <i class="fas fa-download"></i> Excel ga Eksport
            </button>
            <button class="btn btn-info" onclick="printReport()">
                <i class="fas fa-print"></i> Chop etish
            </button>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Hisobotlarga Qaytish
    </a>
</div>
@endsection

@section('scripts')
<script>
function exportData() {
    // Excel export functionality would go here
    alert('Excel eksport funksiyasi keyingi versiyada qo\'shiladi');
}

function printReport() {
    window.print();
}
</script>
@endsection