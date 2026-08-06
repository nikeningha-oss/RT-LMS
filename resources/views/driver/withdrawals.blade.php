@extends('layouts.dashboard')

@section('title', 'Withdrawal History - Tracklane')

@section('dashboard-content')
    <!-- ✅ Changed back to dashboard-content -->

    <style>
        .balance-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .balance-summary {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .balance-summary .summary-card {
            background: #FFFFFF;
            border: 0.5px solid #E2E8F0;
            border-radius: 10px;
            padding: 14px 16px;
            text-align: center;
        }
        .balance-summary .summary-card .label {
            font-size: 11px;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 500;
        }
        .balance-summary .summary-card .value {
            font-size: 20px;
            font-weight: 700;
            color: #0F172A;
            margin-top: 4px;
        }
        .balance-summary .summary-card .value.green { color: #10B981; }
        .balance-summary .summary-card .value.blue { color: #3B82F6; }
        .balance-summary .summary-card .value.orange { color: #F59E0B; }
        .balance-summary .summary-card .value.purple { color: #8B5CF6; }

        .badge-pending {
            background: #FEF3C7;
            color: #92400E;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }
        .badge-approved {
            background: #DBEAFE;
            color: #1E40AF;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }
        .badge-completed {
            background: #D1FAE5;
            color: #065F46;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }
        .badge-rejected {
            background: #FEE2E2;
            color: #991B1B;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }

        .withdrawal-table {
            width: 100%;
            font-size: 13px;
            border-collapse: collapse;
        }
        .withdrawal-table thead th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            color: #64748B;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid #E2E8F0;
        }
        .withdrawal-table tbody td {
            padding: 12px 10px;
            border-bottom: 0.5px solid #F1F5F9;
            color: #0F172A;
            vertical-align: middle;
        }
        .withdrawal-table tbody tr:hover {
            background: #FAFBFC;
        }
        .withdrawal-table tbody tr:last-child td {
            border-bottom: none;
        }
        .withdrawal-table .amount {
            font-weight: 600;
            color: #0F172A;
        }
        .withdrawal-table .net-amount {
            color: #10B981;
            font-weight: 600;
        }
        .withdrawal-table .fee-amount {
            color: #F59E0B;
            font-size: 12px;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #94A3B8;
        }
        .empty-state .empty-icon {
            font-size: 48px;
            display: block;
            margin-bottom: 12px;
            color: #D7DEE6;
        }
        .empty-state .empty-title {
            font-size: 16px;
            font-weight: 500;
            color: #0F172A;
            margin-bottom: 4px;
        }
        .empty-state .empty-subtitle {
            font-size: 13px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        .status-dot.pending { background: #F59E0B; }
        .status-dot.approved { background: #3B82F6; }
        .status-dot.completed { background: #10B981; }
        .status-dot.rejected { background: #E24B4A; }
    </style>

    <div style="background:#F8FAFC; padding:20px 24px; min-height:560px;">

        <!-- HEADER -->
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
            <div>
                <div style="font-size:18px; font-weight:600; color:#0F172A;">📋 Withdrawal History</div>
                <div style="font-size:13px; color:#64748B;">View all your withdrawal requests and their status</div>
            </div>
            <a href="{{ route('driver.withdraw') }}" 
               style="background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:10px 20px; font-size:13px; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                <i class="ti ti-plus"></i> New Withdrawal
            </a>
        </div>

        <!-- BALANCE SUMMARY CARDS -->
        <div class="balance-summary">
            <div class="summary-card">
                <div class="label">💰 Available Balance</div>
                <div class="value green">{{ $driver->formatted_available_balance ?? '0 F' }}</div>
            </div>
            <div class="summary-card">
                <div class="label">📦 Total Earned</div>
                <div class="value blue">{{ $driver->formatted_total_earnings ?? '0 F' }}</div>
            </div>
            <div class="summary-card">
                <div class="label">💸 Total Withdrawn</div>
                <div class="value orange">{{ $driver->formatted_total_withdrawn ?? '0 F' }}</div>
            </div>
            <div class="summary-card">
                <div class="label">📊 Total Requests</div>
                <div class="value purple">{{ $withdrawals->count() ?? 0 }}</div>
            </div>
        </div>

        <!-- WITHDRAWAL HISTORY TABLE -->
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:20px;">
            
            @if(isset($withdrawals) && $withdrawals->count() > 0)
            <div style="overflow-x:auto;">
                <table class="withdrawal-table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Amount</th>
                            <th>Fee (5%)</th>
                            <th>Net Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withdrawals as $withdrawal)
                        <tr>
                            <td style="font-weight:500;">#{{ $withdrawal->id }}</td>
                            <td class="amount">{{ $withdrawal->formatted_amount }}</td>
                            <td class="fee-amount">{{ $withdrawal->formatted_fee }}</td>
                            <td class="net-amount">{{ $withdrawal->formatted_net_amount }}</td>
                            <td>
                                <span style="display:flex; align-items:center; gap:6px;">
                                    @if($withdrawal->payment_method == 'mtn')
                                        <span style="background:#FEF3C7; color:#F59E0B; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:600;">MTN</span>
                                    @elseif($withdrawal->payment_method == 'orange')
                                        <span style="background:#FEE2E2; color:#E24B4A; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:600;">ORANGE</span>
                                    @else
                                        <span style="background:#DBEAFE; color:#2563EB; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:600;">BANK</span>
                                    @endif
                                    {{ $withdrawal->account_details }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-{{ $withdrawal->status }}">
                                    <span class="status-dot {{ $withdrawal->status }}"></span>
                                    {{ $withdrawal->status_label }}
                                </span>
                            </td>
                            <td style="color:#64748B; font-size:12px;">
                                {{ $withdrawal->requested_at ? $withdrawal->requested_at->format('d M Y, h:i A') : 'N/A' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <i class="ti ti-inbox empty-icon"></i>
                <div class="empty-title">No Withdrawals Yet</div>
                <div class="empty-subtitle">You haven't made any withdrawal requests yet.</div>
                <a href="{{ route('driver.withdraw') }}" style="color:#14B8A6; text-decoration:none; margin-top:8px; display:inline-block;">
                    <i class="ti ti-arrow-right"></i> Make your first withdrawal
                </a>
            </div>
            @endif

            <!-- Back to Dashboard -->
            <div style="margin-top:16px; text-align:center;">
                <a href="{{ route('driver.dashboard') }}" style="color:#64748B; text-decoration:none; font-size:13px;">
                    <i class="ti ti-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

    </div>
@endsection