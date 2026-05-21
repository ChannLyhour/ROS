<!-- Key Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm h-100 bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 text-white">
                    <span class="fw-bold text-uppercase small">{{ __('Total Gross Income') }}</span>
                    <i data-lucide="dollar-sign" style="width: 24px;"></i>
                </div>
                <h2 class="fw-bold mb-1">${{ number_format($stats['total_income'], 2) }}</h2>
                <p class="mb-0 small opacity-75">{{ __('From') }} {{ $stats['total_transactions'] }} {{ __('transactions') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 text-muted">
                    <span class="fw-bold text-uppercase small">{{ __('Average Ticket') }}</span>
                    <i data-lucide="trending-up" class="text-success" style="width: 24px;"></i>
                </div>
                <h2 class="fw-bold mb-1 text-dark">${{ number_format($stats['avg_ticket'], 2) }}</h2>
                <p class="mb-0 small text-muted">{{ __('Average spend per order') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 text-muted">
                    <span class="fw-bold text-uppercase small">{{ __('Cash Ratio') }}</span>
                    <i data-lucide="banknote" class="text-warning" style="width: 24px;"></i>
                </div>
                @php
                $cash = $stats['by_method']['cash'] ?? 0;
                $ratio = $stats['total_income'] > 0 ? ($cash / $stats['total_income']) * 100 : 0;
                @endphp
                <h2 class="fw-bold mb-1 text-dark">{{ number_format($ratio, 1) }}%</h2>
                <div class="progress mt-2" style="height: 8px;">
                    <div class="progress-bar bg-warning" style="width: {{ $ratio }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visualization & Chart Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-0 p-4 pb-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-light rounded text-primary">
                        <i data-lucide="trending-up" style="width: 20px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">{{ __('Income Trend Analysis') }}</h5>
                        <p class="text-muted small mb-0">{{ __('Financial Performance Overview') }}</p>
                    </div>
                </div>
                <div class="d-flex bg-light p-1 rounded border">
                    <a href="{{ route('reports.income', ['period' => 'today']) }}" class="btn btn-sm px-3 fw-bold {{ request('period') == 'today' ? 'bg-primary text-white' : 'text-muted' }}">
                        {{ __('Today') }}
                    </a>
                    <a href="{{ route('reports.income', ['period' => 'weekly']) }}" class="btn btn-sm px-3 fw-bold {{ request('period') == 'weekly' ? 'bg-primary text-white' : 'text-muted' }}">
                        {{ __('Weekly') }}
                    </a>
                    <a href="{{ route('reports.income', ['period' => 'monthly']) }}" class="btn btn-sm px-3 fw-bold {{ request('period') == 'monthly' ? 'bg-primary text-white' : 'text-muted' }}">
                        {{ __('Month') }}
                    </a>
                    <a href="{{ route('reports.income') }}" class="btn btn-sm px-3 fw-bold {{ !request('period') ? 'bg-primary text-white' : 'text-muted' }}">
                        {{ __('Custom Range') }}
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <canvas id="incomeChart" height="280"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0">{{ __('Payment Distribution') }}</h5>
                <p class="text-muted small">{{ __('Market share by method') }}</p>
            </div>
            <div class="card-body p-4">
                <ul class="list-group list-group-flush">
                    @foreach($stats['by_method'] as $method => $amount)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <div class="d-flex align-items-center gap-2 text-capitalize">
                            <div class="p-2 rounded bg-light">
                                <i data-lucide="{{ $method == 'cash' ? 'banknote' : ($method == 'card' ? 'credit-card' : 'qr-code') }}" style="width: 16px;" class="text-secondary"></i>
                            </div>
                            <span class="fw-bold text-dark">{{ __(ucfirst($method)) }}</span>
                        </div>
                        <span class="fw-bold text-dark">${{ number_format($amount, 2) }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- 12-Month Performance Summary Row -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white border-0 p-4 pb-0">
        <h5 class="fw-bold mb-0">{{ __('12-Month Performance Summary') }}</h5>
        <p class="text-muted small">{{ __('Historical revenue ledger') }}</p>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            @foreach($monthlyTrend->reverse() as $month)
            <div class="col-md-3 col-sm-6">
                <div class="p-3 border rounded bg-light text-center">
                    <div class="small fw-bold text-muted text-uppercase mb-1">{{ $month->label }}</div>
                    <div class="h5 fw-bold text-primary mb-0">${{ number_format($month->total, 2) }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Transactions Table Row -->
<x-master-table
    title="{{ __('Transaction Ledger') }}"
    subtitle="{{ __('All filtered transactions') }}"
    searchPlaceholder="{{ __('Search ledgers...') }}"
    :headers="[
        ['text' => __('Date/Time'), 'align' => 'start'],
        ['text' => __('Order #'), 'align' => 'start'],
        ['text' => __('Customer'), 'align' => 'start'],
        ['text' => __('Method'), 'align' => 'start'],
        ['text' => __('Amount'), 'align' => 'end']
    ]"
    :items="$payments">
    @forelse($payments as $payment)
    <tr>
        <td>
            <div class="fw-bold text-dark">{{ $payment->paid_at->format('M d, Y') }}</div>
            <small class="text-muted">{{ $payment->paid_at->format('h:i A') }}</small>
        </td>
        <td><span class="badge bg-light text-dark border fw-bold">#{{ $payment->order->order_no }}</span></td>
        <td class="fw-medium">{{ $payment->order->customer->name ?? __('Guest') }}</td>
        <td>
            @php
            $methodClass = match($payment->payment_method) {
            'cash' => 'bg-success text-white',
            'card' => 'bg-primary text-white',
            'qr' => 'bg-info text-dark',
            default => 'bg-secondary text-white'
            };
            @endphp
            <span class="badge {{ $methodClass }} text-uppercase">
                <i data-lucide="{{ $payment->payment_method == 'cash' ? 'banknote' : ($payment->payment_method == 'card' ? 'credit-card' : 'qr-code') }}" class="me-1" style="width: 12px;"></i>
                {{ __(strtoupper($payment->payment_method)) }}
            </span>
        </td>
        <td class="text-end pe-4 fw-bold text-dark">${{ number_format($payment->total_amount, 2) }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="5" class="text-center py-5">
            <i data-lucide="inbox" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
            <p class="text-muted">{{ __('No transactions found.') }}</p>
        </td>
    </tr>
    @endforelse
</x-master-table>
