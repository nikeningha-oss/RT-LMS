@extends('layouts.dashboard')

@section('title', 'Withdrawal Management - Tracklane')

@section('dashboard-content')

    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .stat-card {
            background: #FFFFFF;
            border: 0.5px solid #E2E8F0;
            border-radius: 10px;
            padding: 14px 16px;
            transition: all 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }
        .stat-card .label {
            font-size: 11px;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 500;
        }
        .stat-card .value {
            font-size: 22px;
            font-weight: 700;
            color: #0F172A;
            margin-top: 2px;
        }
        .stat-card .value.pending { color: #F59E0B; }
        .stat-card .value.completed { color: #10B981; }
        .stat-card .value.approved { color: #3B82F6; }
        .stat-card .value.rejected { color: #E24B4A; }

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
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            color: #64748B;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid #E2E8F0;
        }
        .withdrawal-table tbody td {
            padding: 10px 8px;
            border-bottom: 0.5px solid #F1F5F9;
            color: #0F172A;
            vertical-align: middle;
        }
        .withdrawal-table tbody tr:hover {
            background: #FAFBFC;
        }
        .withdrawal-table .amount {
            font-weight: 600;
            color: #0F172A;
        }
        .withdrawal-table .net-amount {
            color: #10B981;
            font-weight: 600;
        }

        .btn-approve {
            background: #10B981;
            color: #FFFFFF;
            border: none;
            border-radius: 6px;
            padding: 4px 14px;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-approve:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .btn-reject {
            background: #E24B4A;
            color: #FFFFFF;
            border: none;
            border-radius: 6px;
            padding: 4px 14px;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-reject:hover {
            background: #C0392B;
            transform: translateY(-1px);
        }

        .btn-complete {
            background: #3B82F6;
            color: #FFFFFF;
            border: none;
            border-radius: 6px;
            padding: 4px 14px;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-complete:hover {
            background: #2563EB;
            transform: translateY(-1px);
        }

        .btn-view {
            background: #F1F5F9;
            color: #475569;
            border: none;
            border-radius: 6px;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-view:hover {
            background: #E2E8F0;
        }

        .btn-add {
            background: #3B82F6;
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-add:hover {
            background: #2563EB;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-content {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 24px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .modal-content .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .modal-content .modal-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #0F172A;
            margin: 0;
        }
        .modal-content .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #94A3B8;
        }
        .modal-content .modal-close:hover {
            color: #0F172A;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 0.5px solid #F1F5F9;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-row .detail-label {
            color: #64748B;
            font-size: 13px;
        }
        .detail-row .detail-value {
            color: #0F172A;
            font-weight: 500;
            font-size: 13px;
            text-align: right;
        }
        .detail-row .detail-value.amount {
            color: #10B981;
            font-weight: 700;
            font-size: 16px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #94A3B8;
        }
        .empty-state .empty-icon {
            font-size: 40px;
            display: block;
            margin-bottom: 8px;
        }

        .tab-container {
            display: flex;
            gap: 4px;
            margin-bottom: 16px;
            background: #F1F5F9;
            border-radius: 10px;
            padding: 4px;
        }
        .tab-btn {
            padding: 8px 20px;
            border: none;
            background: transparent;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            color: #64748B;
        }
        .tab-btn.active {
            background: #FFFFFF;
            color: #0F172A;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }
        .tab-btn:hover:not(.active) {
            color: #0F172A;
        }

        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }

        .action-buttons {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
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

        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 13px;
            color: #0F172A;
            transition: all 0.2s;
            background: #FFFFFF;
        }
        .form-input:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .form-input::placeholder {
            color: #94A3B8;
        }
        .form-select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 13px;
            color: #0F172A;
            background: #FFFFFF;
            transition: all 0.2s;
        }
        .form-select:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #0F172A;
            margin-bottom: 4px;
        }
        .form-group {
            margin-bottom: 12px;
        }
        .form-hint {
            font-size: 12px;
            color: #64748B;
            margin-top: 4px;
        }
        
        .fee-breakdown {
            background: #F8FAFC;
            border-radius: 8px;
            padding: 12px 14px;
            margin: 8px 0;
        }
        .fee-breakdown .row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            font-size: 13px;
        }
        .fee-breakdown .row.total {
            border-top: 1px solid #E2E8F0;
            padding-top: 6px;
            margin-top: 4px;
            font-weight: 600;
        }
        .fee-breakdown .row.total .net {
            color: #10B981;
        }

        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
            width: 100%;
        }
        .toast {
            background: #FFFFFF;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: slideIn 0.3s ease;
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        .toast.success {
            border-left-color: #10B981;
        }
        .toast.error {
            border-left-color: #E24B4A;
        }
        .toast.info {
            border-left-color: #3B82F6;
        }
        .toast .toast-icon {
            font-size: 20px;
            margin-top: 2px;
        }
        .toast .toast-content {
            flex: 1;
        }
        .toast .toast-title {
            font-weight: 600;
            font-size: 14px;
            color: #0F172A;
        }
        .toast .toast-message {
            font-size: 13px;
            color: #64748B;
            margin-top: 2px;
        }
        .toast .toast-close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #94A3B8;
            padding: 0;
            margin-top: 2px;
        }
        .toast .toast-close:hover {
            color: #0F172A;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        .toast.hiding {
            animation: slideOut 0.3s ease forwards;
        }
    </style>

    <!-- Toast Container for Notifications -->
    <div id="toastContainer" class="toast-container"></div>

    <div style="background:#F8FAFC; padding:20px 24px; min-height:560px;">

        <!-- HEADER -->
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
            <div>
                <div style="font-size:18px; font-weight:600; color:#0F172A;">💸 Withdrawal Management</div>
                <div style="font-size:13px; color:#64748B;">Review and process driver withdrawal requests</div>
            </div>
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <span style="background:#FEF3C7; color:#92400E; padding:4px 14px; border-radius:20px; font-size:13px; font-weight:500;">
                    <i class="ti ti-clock"></i> {{ $totalPendingCount ?? 0 }} Pending
                </span>
                <!-- ✅ ADD WITHDRAWAL BUTTON -->
                <button class="btn-add" onclick="openAddModal()">
                    <i class="ti ti-plus"></i> Add Withdrawal
                </button>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">⏳ Pending</div>
                <div class="value pending">{{ $totalPendingCount ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="label">💰 Pending Amount</div>
                <div class="value pending">{{ number_format($totalPendingAmount ?? 0, 0, ',', ' ') }} F</div>
            </div>
            <div class="stat-card">
                <div class="label">✅ Completed This Month</div>
                <div class="value completed">{{ number_format($totalWithdrawnThisMonth ?? 0, 0, ',', ' ') }} F</div>
            </div>
            <div class="stat-card">
                <div class="label">📊 Total Withdrawals</div>
                <div class="value approved">{{ ($approvedWithdrawals->count() ?? 0) + ($completedWithdrawals->count() ?? 0) }}</div>
            </div>
        </div>

        <!-- TABS -->
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:20px;">

            <div class="tab-container">
                <button class="tab-btn active" data-tab="pending" onclick="switchTab('pending')">
                    ⏳ Pending <span style="background:#FEF3C7; padding:1px 8px; border-radius:12px; font-size:11px;">{{ $totalPendingCount ?? 0 }}</span>
                </button>
                <button class="tab-btn" data-tab="approved" onclick="switchTab('approved')">
                    ✅ Approved <span style="background:#DBEAFE; padding:1px 8px; border-radius:12px; font-size:11px;">{{ $approvedWithdrawals->count() ?? 0 }}</span>
                </button>
                <button class="tab-btn" data-tab="completed" onclick="switchTab('completed')">
                    💰 Completed <span style="background:#D1FAE5; padding:1px 8px; border-radius:12px; font-size:11px;">{{ $completedWithdrawals->count() ?? 0 }}</span>
                </button>
                <button class="tab-btn" data-tab="rejected" onclick="switchTab('rejected')">
                    ❌ Rejected <span style="background:#FEE2E2; padding:1px 8px; border-radius:12px; font-size:11px;">{{ $rejectedWithdrawals->count() ?? 0 }}</span>
                </button>
            </div>

            <!-- TAB 1: PENDING -->
            <div id="tab-pending" class="tab-content active">
                @if(isset($pendingWithdrawals) && $pendingWithdrawals->count() > 0)
                <div style="overflow-x:auto;">
                    <table class="withdrawal-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Driver</th>
                                <th>Amount</th>
                                <th>Net Amount</th>
                                <th>Method</th>
                                <th>Requested</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingWithdrawals as $withdrawal)
                            <tr>
                                <td>#{{ $withdrawal->id }}</td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div style="width:28px; height:28px; border-radius:50%; background:#CCFBF1; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:600; color:#0D9488;">
                                            {{ strtoupper(substr($withdrawal->driver->name ?? 'D', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:500; font-size:13px;">{{ $withdrawal->driver->name ?? 'Unknown' }}</div>
                                            <div style="font-size:11px; color:#64748B;">{{ $withdrawal->driver->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="amount">{{ $withdrawal->formatted_amount }}</td>
                                <td class="net-amount">{{ $withdrawal->formatted_net_amount }}</td>
                                <td>
                                    <span style="font-size:11px; padding:2px 8px; border-radius:4px; background:#F1F5F9;">
                                        {{ $withdrawal->payment_method_label }}
                                    </span>
                                </td>
                                <td style="font-size:12px; color:#64748B;">
                                    {{ $withdrawal->requested_at ? $withdrawal->requested_at->format('d M Y, h:i A') : 'N/A' }}
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-approve" onclick="openApproveModal({{ $withdrawal->id }})">
                                            <i class="ti ti-check"></i> Approve
                                        </button>
                                        <button class="btn-reject" onclick="openRejectModal({{ $withdrawal->id }})">
                                            <i class="ti ti-x"></i> Reject
                                        </button>
                                        <button class="btn-view" onclick="viewDetails({{ $withdrawal->id }})">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <i class="ti ti-check-circle empty-icon" style="color:#10B981;"></i>
                    <div style="font-size:15px; font-weight:500; color:#0F172A;">No Pending Requests</div>
                    <p style="font-size:13px;">All withdrawal requests have been processed.</p>
                </div>
                @endif
            </div>

            <!-- TAB 2: APPROVED -->
            <div id="tab-approved" class="tab-content">
                @if(isset($approvedWithdrawals) && $approvedWithdrawals->count() > 0)
                <div style="overflow-x:auto;">
                    <table class="withdrawal-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Driver</th>
                                <th>Amount</th>
                                <th>Net Amount</th>
                                <th>Method</th>
                                <th>Approved</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approvedWithdrawals as $withdrawal)
                            <tr>
                                <td>#{{ $withdrawal->id }}</td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div style="width:28px; height:28px; border-radius:50%; background:#CCFBF1; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:600; color:#0D9488;">
                                            {{ strtoupper(substr($withdrawal->driver->name ?? 'D', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:500; font-size:13px;">{{ $withdrawal->driver->name ?? 'Unknown' }}</div>
                                            <div style="font-size:11px; color:#64748B;">{{ $withdrawal->driver->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="amount">{{ $withdrawal->formatted_amount }}</td>
                                <td class="net-amount">{{ $withdrawal->formatted_net_amount }}</td>
                                <td>
                                    <span style="font-size:11px; padding:2px 8px; border-radius:4px; background:#F1F5F9;">
                                        {{ $withdrawal->payment_method_label }}
                                    </span>
                                </td>
                                <td style="font-size:12px; color:#64748B;">
                                    {{ $withdrawal->processed_at ? $withdrawal->processed_at->format('d M Y, h:i A') : 'N/A' }}
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <!-- ✅ COMPLETE BUTTON -->
                                        <button class="btn-complete" onclick="openCompleteModal({{ $withdrawal->id }})">
                                            <i class="ti ti-check"></i> Complete
                                        </button>
                                        <button class="btn-view" onclick="viewDetails({{ $withdrawal->id }})">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <i class="ti ti-inbox empty-icon"></i>
                    <div style="font-size:15px; font-weight:500; color:#0F172A;">No Approved Requests</div>
                    <p style="font-size:13px;">There are no approved withdrawal requests.</p>
                </div>
                @endif
            </div>

            <!-- TAB 3: COMPLETED -->
            <div id="tab-completed" class="tab-content">
                @if(isset($completedWithdrawals) && $completedWithdrawals->count() > 0)
                <div style="overflow-x:auto;">
                    <table class="withdrawal-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Driver</th>
                                <th>Amount</th>
                                <th>Net Amount</th>
                                <th>Method</th>
                                <th>Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedWithdrawals as $withdrawal)
                            <tr>
                                <td>#{{ $withdrawal->id }}</td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div style="width:28px; height:28px; border-radius:50%; background:#CCFBF1; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:600; color:#0D9488;">
                                            {{ strtoupper(substr($withdrawal->driver->name ?? 'D', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:500; font-size:13px;">{{ $withdrawal->driver->name ?? 'Unknown' }}</div>
                                            <div style="font-size:11px; color:#64748B;">{{ $withdrawal->driver->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="amount">{{ $withdrawal->formatted_amount }}</td>
                                <td class="net-amount">{{ $withdrawal->formatted_net_amount }}</td>
                                <td>
                                    <span style="font-size:11px; padding:2px 8px; border-radius:4px; background:#F1F5F9;">
                                        {{ $withdrawal->payment_method_label }}
                                    </span>
                                </td>
                                <td style="font-size:12px; color:#64748B;">
                                    {{ $withdrawal->processed_at ? $withdrawal->processed_at->format('d M Y, h:i A') : 'N/A' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <i class="ti ti-inbox empty-icon"></i>
                    <div style="font-size:15px; font-weight:500; color:#0F172A;">No Completed Withdrawals</div>
                    <p style="font-size:13px;">There are no completed withdrawal requests.</p>
                </div>
                @endif
            </div>

            <!-- TAB 4: REJECTED -->
            <div id="tab-rejected" class="tab-content">
                @if(isset($rejectedWithdrawals) && $rejectedWithdrawals->count() > 0)
                <div style="overflow-x:auto;">
                    <table class="withdrawal-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Driver</th>
                                <th>Amount</th>
                                <th>Net Amount</th>
                                <th>Method</th>
                                <th>Rejected</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rejectedWithdrawals as $withdrawal)
                            <tr>
                                <td>#{{ $withdrawal->id }}</td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div style="width:28px; height:28px; border-radius:50%; background:#CCFBF1; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:600; color:#0D9488;">
                                            {{ strtoupper(substr($withdrawal->driver->name ?? 'D', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:500; font-size:13px;">{{ $withdrawal->driver->name ?? 'Unknown' }}</div>
                                            <div style="font-size:11px; color:#64748B;">{{ $withdrawal->driver->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="amount">{{ $withdrawal->formatted_amount }}</td>
                                <td class="net-amount">{{ $withdrawal->formatted_net_amount }}</td>
                                <td>
                                    <span style="font-size:11px; padding:2px 8px; border-radius:4px; background:#F1F5F9;">
                                        {{ $withdrawal->payment_method_label }}
                                    </span>
                                </td>
                                <td style="font-size:12px; color:#64748B;">
                                    {{ $withdrawal->processed_at ? $withdrawal->processed_at->format('d M Y, h:i A') : 'N/A' }}
                                </td>
                                <td style="font-size:12px; color:#64748B; max-width:150px; word-break:break-word;">
                                    {{ $withdrawal->admin_note ?? 'No reason provided' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <i class="ti ti-inbox empty-icon"></i>
                    <div style="font-size:15px; font-weight:500; color:#0F172A;">No Rejected Requests</div>
                    <p style="font-size:13px;">There are no rejected withdrawal requests.</p>
                </div>
                @endif
            </div>

        </div>

    </div>

    <!-- ============================================================ -->
    <!-- ADD WITHDRAWAL MODAL -->
    <!-- ============================================================ -->
    <div id="addModal" class="modal-overlay">
        <div class="modal-content" style="max-width:550px;">
            <div class="modal-header">
                <h3>➕ Add Manual Withdrawal</h3>
                <button class="modal-close" onclick="closeModal('addModal')">✕</button>
            </div>
            <form id="addWithdrawalForm" method="POST" action="{{ route('admin.withdrawals') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Select Driver <span style="color:#E24B4A;">*</span></label>
                    <select name="driver_id" class="form-select" required>
                        <option value="">-- Select Driver --</option>
                        @if(isset($drivers) && $drivers->count() > 0)
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">
                                    {{ $driver->name }} - {{ $driver->user->email ?? 'N/A' }} 
                                    (Balance: {{ number_format($driver->available_balance ?? 0, 0, ',', ' ') }} F)
                                </option>
                            @endforeach
                        @else
                            <option value="">No drivers available</option>
                        @endif
                    </select>
                    <div class="form-hint">Select the driver who requested the withdrawal.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Amount <span style="color:#E24B4A;">*</span></label>
                    <input type="number" name="amount" class="form-input" id="withdrawalAmount" placeholder="Enter amount in FCFA" required min="100" step="100">
                    <div class="form-hint" id="amountHint">Minimum amount: 100 FCFA</div>
                    <div class="fee-breakdown" id="feeBreakdown" style="display:none;">
                        <div class="row">
                            <span>Gross Amount</span>
                            <span id="grossAmount">0 F</span>
                        </div>
                        <div class="row">
                            <span>Fee (5%)</span>
                            <span id="feeAmount" style="color:#F59E0B;">0 F</span>
                        </div>
                        <div class="row total">
                            <span>Net Amount</span>
                            <span class="net" id="netAmount">0 F</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Payment Method <span style="color:#E24B4A;">*</span></label>
                    <select name="payment_method" class="form-select" required>
                        <option value="">-- Select Payment Method --</option>
                        <option value="mtn">📱 MTN Mobile Money</option>
                        <option value="orange">📱 Orange Money</option>
                        <option value="bank">🏦 Bank Transfer</option>
                    </select>
                    <div class="form-hint">Select the payment method for this withdrawal.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Account Details <span style="color:#E24B4A;">*</span></label>
                    <input type="text" name="account_details" class="form-input" placeholder="Phone number, bank account, or reference..." required>
                    <div class="form-hint">Provide the account details for the transfer.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Admin Note (Optional)</label>
                    <textarea name="admin_note" class="form-input" rows="2" placeholder="Add any notes about this withdrawal..."></textarea>
                </div>

                <div style="background:#FEF3C7; padding:10px 14px; border-radius:8px; margin:12px 0;">
                    <div style="font-size:12px; color:#92400E; display:flex; align-items:flex-start; gap:6px;">
                        <i class="ti ti-info-circle" style="margin-top:1px;"></i>
                        <span>The amount will be deducted from the driver's balance immediately. A 5% fee will be applied automatically.</span>
                    </div>
                </div>

                <div style="display:flex; gap:8px; margin-top:16px;">
                    <button type="submit" class="btn-approve" style="padding:10px 24px; font-size:14px; flex:1;">
                        <i class="ti ti-plus"></i> Create Withdrawal
                    </button>
                    <button type="button" class="btn-reject" style="padding:10px 24px; font-size:14px;" onclick="closeModal('addModal')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- APPROVE MODAL -->
    <div id="approveModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>✅ Approve Withdrawal</h3>
                <button class="modal-close" onclick="closeModal('approveModal')">✕</button>
            </div>
            <form id="approveForm" method="POST" action="">
                @csrf
                <div style="margin-bottom:12px;">
                    <p style="color:#64748B; font-size:14px; margin-bottom:8px;">
                        This will approve the withdrawal request. The amount has already been deducted from the driver's balance.
                    </p>
                    <div style="background:#FEF3C7; padding:10px; border-radius:8px; margin-bottom:12px;">
                        <p style="margin:0; font-size:13px; color:#92400E;">
                            <i class="ti ti-info-circle"></i> The driver will be notified once approved.
                        </p>
                    </div>
                    <label style="font-size:13px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">
                        Admin Note (Optional)
                    </label>
                    <textarea name="admin_note" class="form-input" rows="2" placeholder="Add a note about this approval..."></textarea>
                </div>
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn-approve" style="padding:10px 24px; font-size:14px; flex:1;">
                        <i class="ti ti-check"></i> Approve Withdrawal
                    </button>
                    <button type="button" class="btn-reject" style="padding:10px 24px; font-size:14px;" onclick="closeModal('approveModal')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- REJECT MODAL -->
    <div id="rejectModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>❌ Reject Withdrawal</h3>
                <button class="modal-close" onclick="closeModal('rejectModal')">✕</button>
            </div>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div style="margin-bottom:12px;">
                    <p style="color:#64748B; font-size:14px; margin-bottom:8px;">
                        This will reject the withdrawal request. The amount will be refunded to the driver's balance.
                    </p>
                    <div style="background:#FEE2E2; padding:10px; border-radius:8px; margin-bottom:12px;">
                        <p style="margin:0; font-size:13px; color:#991B1B;">
                            <i class="ti ti-alert-circle"></i> The driver will be notified and the amount will be refunded.
                        </p>
                    </div>
                    <label style="font-size:13px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">
                        Reason for Rejection <span style="color:#E24B4A;">*</span>
                    </label>
                    <input type="text" name="reason" class="form-input" placeholder="Why is this request being rejected?" required>
                    <div style="font-size:12px; color:#64748B; margin-top:4px;">
                        This reason will be shown to the driver.
                    </div>
                </div>
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn-reject" style="padding:10px 24px; font-size:14px; flex:1;">
                        <i class="ti ti-x"></i> Reject Withdrawal
                    </button>
                    <button type="button" class="btn-approve" style="padding:10px 24px; font-size:14px;" onclick="closeModal('rejectModal')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- COMPLETE MODAL -->
    <div id="completeModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>💰 Complete Withdrawal</h3>
                <button class="modal-close" onclick="closeModal('completeModal')">✕</button>
            </div>
            <form id="completeForm" method="POST" action="">
                @csrf
                <div style="margin-bottom:12px;">
                    <p style="color:#64748B; font-size:14px; margin-bottom:8px;">
                        This will mark the withdrawal as completed. Confirm that the funds have been sent to the driver.
                    </p>
                    <div style="background:#D1FAE5; padding:10px; border-radius:8px; margin-bottom:12px;">
                        <p style="margin:0; font-size:13px; color:#065F46;">
                            <i class="ti ti-info-circle"></i> The driver will be notified that the payment has been sent.
                        </p>
                    </div>
                    <label style="font-size:13px; font-weight:500; color:#0F172A; display:block; margin-bottom:4px;">
                        Payment Reference (Optional)
                    </label>
                    <input type="text" name="admin_note" class="form-input" placeholder="Transaction ID or reference number...">
                </div>
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn-complete" style="padding:10px 24px; font-size:14px; flex:1;">
                        <i class="ti ti-check"></i> Mark as Completed
                    </button>
                    <button type="button" class="btn-reject" style="padding:10px 24px; font-size:14px;" onclick="closeModal('completeModal')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DETAIL MODAL -->
    <div id="detailModal" class="modal-overlay">
        <div class="modal-content" style="max-width:550px;">
            <div class="modal-header">
                <h3>📋 Withdrawal Details</h3>
                <button class="modal-close" onclick="closeModal('detailModal')">✕</button>
            </div>
            <div id="detailContent" style="padding:4px 0;">
                <div style="text-align:center; padding:20px;">
                    <i class="ti ti-loader" style="font-size:32px; animation:spin 1s linear infinite;"></i>
                    <p style="margin-top:8px; color:#64748B;">Loading details...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    // ============================================================
    // TOAST NOTIFICATION SYSTEM
    // ============================================================
    function showToast(type, title, message, duration) {
        duration = duration || 4000;
        var container = document.getElementById('toastContainer');
        var toast = document.createElement('div');
        toast.className = 'toast ' + type;
        
        var icons = {
            success: '✅',
            error: '❌',
            info: 'ℹ️'
        };
        
        toast.innerHTML = `
            <span class="toast-icon">${icons[type] || 'ℹ️'}</span>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">✕</button>
        `;
        
        container.appendChild(toast);
        
        setTimeout(function() {
            if (toast.parentElement) {
                toast.classList.add('hiding');
                setTimeout(function() {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }
        }, duration);
        
        toast.addEventListener('click', function() {
            if (toast.parentElement) {
                toast.classList.add('hiding');
                setTimeout(function() {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }
        });
    }

    // ============================================================
    // TAB SWITCHING
    // ============================================================
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(function(btn) {
            btn.classList.remove('active');
        });
        document.querySelector('.tab-btn[data-tab="' + tab + '"]').classList.add('active');
        
        document.querySelectorAll('.tab-content').forEach(function(content) {
            content.classList.remove('active');
        });
        document.getElementById('tab-' + tab).classList.add('active');
    }

    // ============================================================
    // MODAL FUNCTIONS
    // ============================================================
    function openModal(id) {
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // ============================================================
    // ✅ OPEN ADD WITHDRAWAL MODAL
    // ============================================================
    function openAddModal() {
        document.getElementById('addWithdrawalForm').reset();
        document.getElementById('feeBreakdown').style.display = 'none';
        openModal('addModal');
    }

    // ============================================================
    // ✅ FEE CALCULATION FOR ADD MODAL
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        var amountInput = document.getElementById('withdrawalAmount');
        if (amountInput) {
            amountInput.addEventListener('input', function() {
                var val = parseFloat(this.value) || 0;
                var fee = val * 0.05;
                var net = val - fee;
                
                var feeBreakdown = document.getElementById('feeBreakdown');
                var grossAmount = document.getElementById('grossAmount');
                var feeAmount = document.getElementById('feeAmount');
                var netAmount = document.getElementById('netAmount');
                var amountHint = document.getElementById('amountHint');
                
                if (val > 0) {
                    feeBreakdown.style.display = 'block';
                    grossAmount.textContent = val.toLocaleString() + ' F';
                    feeAmount.textContent = fee.toLocaleString() + ' F';
                    netAmount.textContent = net.toLocaleString() + ' F';
                    amountHint.textContent = 'Amount: ' + val.toLocaleString() + ' F | Fee (5%): ' + fee.toLocaleString() + ' F | Net: ' + net.toLocaleString() + ' F';
                } else {
                    feeBreakdown.style.display = 'none';
                    amountHint.textContent = 'Minimum amount: 100 FCFA';
                }
            });
        }
    });

    // ============================================================
    // ✅ OPEN APPROVE MODAL - FIXED URL
    // ============================================================
    function openApproveModal(id) {
        var url = '/admin/withdrawals/' + id + '/approve';
        document.getElementById('approveForm').action = url;
        openModal('approveModal');
    }

    // ============================================================
    // ✅ OPEN REJECT MODAL - FIXED URL
    // ============================================================
    function openRejectModal(id) {
        var url = '/admin/withdrawals/' + id + '/reject';
        document.getElementById('rejectForm').action = url;
        openModal('rejectModal');
    }

    // ============================================================
    // ✅ OPEN COMPLETE MODAL - FIXED URL
    // ============================================================
    function openCompleteModal(id) {
        var url = '/admin/withdrawals/' + id + '/complete';
        document.getElementById('completeForm').action = url;
        openModal('completeModal');
    }

    // ============================================================
    // VIEW DETAILS - FIXED URL
    // ============================================================
    function viewDetails(id) {
        var content = document.getElementById('detailContent');
        content.innerHTML = '<div style="text-align:center; padding:20px;"><i class="ti ti-loader" style="font-size:32px; animation:spin 1s linear infinite;"></i><p style="margin-top:8px; color:#64748B;">Loading details...</p></div>';
        openModal('detailModal');
        
        var url = '/admin/withdrawals/' + id + '/details';
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                var w = data.withdrawal;
                content.innerHTML = `
                    <div style="margin-bottom:8px;">
                        <div class="detail-row">
                            <span class="detail-label">Request ID</span>
                            <span class="detail-value">#${w.id}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Driver</span>
                            <span class="detail-value">${w.driver_name}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email</span>
                            <span class="detail-value">${w.driver_email}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Phone</span>
                            <span class="detail-value">${w.driver_phone || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Amount Requested</span>
                            <span class="detail-value">${w.amount}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Fee (5%)</span>
                            <span class="detail-value" style="color:#F59E0B;">${w.fee}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Net Amount</span>
                            <span class="detail-value amount">${w.net_amount}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Payment Method</span>
                            <span class="detail-value">${w.payment_method}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Account Details</span>
                            <span class="detail-value">${w.account_details}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Status</span>
                            <span class="detail-value"><span class="badge-${w.status.toLowerCase()}">${w.status}</span></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Requested At</span>
                            <span class="detail-value">${w.requested_at}</span>
                        </div>
                        <div class="detail-row" style="border-bottom: none; padding-top:12px; border-top:1px solid #E2E8F0;">
                            <span class="detail-label" style="font-weight:600;">Available Balance</span>
                            <span class="detail-value amount">${w.available_balance}</span>
                        </div>
                        <div class="detail-row" style="border-bottom: none;">
                            <span class="detail-label" style="font-weight:600;">Total Earned</span>
                            <span class="detail-value">${w.total_earned}</span>
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = '<div style="text-align:center; padding:20px; color:#E24B4A;"><i class="ti ti-alert-circle" style="font-size:32px; display:block; margin-bottom:8px;"></i><p>' + (data.message || 'Error loading details') + '</p></div>';
            }
        })
        .catch(function(error) {
            content.innerHTML = '<div style="text-align:center; padding:20px; color:#E24B4A;"><i class="ti ti-alert-circle" style="font-size:32px; display:block; margin-bottom:8px;"></i><p>Error loading details</p></div>';
        });
    }

    // ============================================================
    // ✅ HANDLE MODAL FORM SUBMISSIONS WITH TOAST MESSAGES
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        // Approve Form
        document.getElementById('approveForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            var url = form.action;
            var formData = new FormData(form);
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    showToast('success', '✅ Approved!', data.message || 'Withdrawal approved successfully!');
                    closeModal('approveModal');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    showToast('error', '❌ Error', data.message || 'Failed to approve withdrawal.');
                }
            })
            .catch(function(error) {
                showToast('error', '❌ Error', 'An unexpected error occurred.');
            });
        });

        // Reject Form
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            var url = form.action;
            var formData = new FormData(form);
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    showToast('success', '❌ Rejected!', data.message || 'Withdrawal rejected and refunded successfully!');
                    closeModal('rejectModal');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    showToast('error', '❌ Error', data.message || 'Failed to reject withdrawal.');
                }
            })
            .catch(function(error) {
                showToast('error', '❌ Error', 'An unexpected error occurred.');
            });
        });

        // Complete Form
        document.getElementById('completeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            var url = form.action;
            var formData = new FormData(form);
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    showToast('success', '💰 Completed!', data.message || 'Withdrawal marked as completed successfully!');
                    closeModal('completeModal');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    showToast('error', '❌ Error', data.message || 'Failed to complete withdrawal.');
                }
            })
            .catch(function(error) {
                showToast('error', '❌ Error', 'An unexpected error occurred.');
            });
        });
    });

    // ============================================================
    // CLOSE MODALS ON ESCAPE
    // ============================================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('addModal');
            closeModal('approveModal');
            closeModal('rejectModal');
            closeModal('completeModal');
            closeModal('detailModal');
        }
    });

    // ============================================================
    // CLOSE MODALS ON OUTSIDE CLICK
    // ============================================================
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            closeModal(e.target.id);
        }
    });
    </script>
@endsection