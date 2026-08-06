@extends('layouts.dashboard')

@section('title', 'Manage Customers - Tracklane')
@section('page-title', 'Customers Management')

@section('dashboard-content')

<style>
    .status-active { background: #D1FAE5; color: #065F46; }
    .status-pending { background: #FEF3C7; color: #92400E; }
    .status-rejected { background: #FEE2E2; color: #991B1B; }
    .status-unknown { background: #F1F5F9; color: #64748B; }
</style>

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <!-- Header -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <div>
            <div style="font-size:16px; font-weight:500; color:#0F172A;">👥 Manage Customers</div>
            <div style="font-size:12px; color:#64748B;">View and manage all customers in the system</div>
        </div>
        <div style="display:flex; gap:8px;">
            <span style="background:#CCFBF1; color:#0D9488; padding:4px 12px; border-radius:12px; font-size:12px;">
                Total: {{ isset($customers) ? $customers->count() : 0 }}
            </span>
        </div>
    </div>

    <!-- ✅ FIXED: Stats Summary - Using approval_status -->
    @php
        $totalCustomers = isset($customers) ? $customers->count() : 0;
        $activeCustomers = isset($customers) ? $customers->where('approval_status', 'approved')->count() : 0;
        $pendingCustomers = isset($customers) ? $customers->where('approval_status', 'pending')->count() : 0;
        $rejectedCustomers = isset($customers) ? $customers->where('approval_status', 'rejected')->count() : 0;
    @endphp

    <div style="display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:8px; margin-bottom:16px;">
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 12px; text-align:center;">
            <div style="font-size:10px; color:#64748B;">Total Customers</div>
            <div style="font-size:16px; font-weight:600; color:#0F172A;">{{ $totalCustomers }}</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 12px; text-align:center;">
            <div style="font-size:10px; color:#64748B;">✅ Active</div>
            <div style="font-size:16px; font-weight:600; color:#10B981;">{{ $activeCustomers }}</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 12px; text-align:center;">
            <div style="font-size:10px; color:#64748B;">⏳ Pending</div>
            <div style="font-size:16px; font-weight:600; color:#F59E0B;">{{ $pendingCustomers }}</div>
        </div>
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:8px; padding:8px 12px; text-align:center;">
            <div style="font-size:10px; color:#64748B;">❌ Rejected</div>
            <div style="font-size:16px; font-weight:600; color:#E24B4A;">{{ $rejectedCustomers }}</div>
        </div>
    </div>

    <!-- Customers Table -->
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:16px;">
        
        @if(isset($customers) && $customers->count() > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%; font-size:12px; border-collapse:collapse;">
                <thead>
                    <tr style="color:#64748B; border-bottom:1px solid #E2E8F0;">
                        <th style="padding:8px; text-align:left;">Customer</th>
                        <th style="padding:8px; text-align:left;">Email</th>
                        <th style="padding:8px; text-align:left;">Phone</th>
                        <th style="padding:8px; text-align:left;">Joined</th>
                        <th style="padding:8px; text-align:left;">Orders</th>
                        <th style="padding:8px; text-align:left;">Approval Status</th>
                        <th style="padding:8px; text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    @php
                        // ✅ CORRECT: Get approval status
                        $status = $customer->approval_status ?? 'pending';
                    @endphp
                    <tr style="border-bottom:0.5px solid #E2E8F0;">
                        <td style="padding:8px; font-weight:500; color:#0F172A;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="width:28px; height:28px; border-radius:50%; background:#CCFBF1; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; color:#0D9488;">
                                    {{ strtoupper(substr($customer->name ?? 'N/A', 0, 2)) }}
                                </div>
                                {{ $customer->name ?? 'N/A' }}
                            </div>
                        </td>
                        <td style="padding:8px; color:#64748B;">{{ $customer->email ?? 'N/A' }}</td>
                        <td style="padding:8px; color:#64748B;">{{ $customer->phone ?? 'N/A' }}</td>
                        <td style="padding:8px; color:#64748B;">
                            {{ optional($customer->created_at)->format('M d, Y') ?? 'N/A' }}
                        </td>
                        <td style="padding:8px; color:#0F172A; text-align:center;">
                            {{ $customer->orders->count() ?? 0 }}
                        </td>
                        <td style="padding:8px;">
                            {{-- ✅ CORRECT: Check approval_status --}}
                            @if($status === 'approved' || $status === 'active')
                                <span class="status-active" style="padding:3px 10px; border-radius:12px; font-size:11px; display:inline-block;">
                                    ✅ Active
                                </span>
                            @elseif($status === 'pending')
                                <span class="status-pending" style="padding:3px 10px; border-radius:12px; font-size:11px; display:inline-block;">
                                    ⏳ Pending
                                </span>
                            @elseif($status === 'rejected')
                                <span class="status-rejected" style="padding:3px 10px; border-radius:12px; font-size:11px; display:inline-block;">
                                    ❌ Rejected
                                </span>
                            @else
                                <span class="status-unknown" style="padding:3px 10px; border-radius:12px; font-size:11px; display:inline-block;">
                                    ❓ Unknown
                                </span>
                            @endif
                        </td>
                        <td style="padding:8px; text-align:center;">
                            <div style="display:flex; gap:4px; justify-content:center; flex-wrap:wrap;">
                                <!-- Edit Button -->
                                <a href="{{ route('admin.customers.edit', $customer->id) }}" 
                                   style="background:#14B8A6; color:#FFFFFF; border:none; border-radius:6px; padding:4px 10px; font-size:11px; cursor:pointer; text-decoration:none; display:inline-block;">
                                    <i class="ti ti-edit"></i> Edit
                                </a>
                                
                                {{-- ✅ Approve Button (only for pending) --}}
                                @if($status === 'pending')
                                    <button class="approve-customer-btn" 
                                            data-customer-id="{{ $customer->id }}"
                                            style="background:#10B981; color:#FFFFFF; border:none; border-radius:6px; padding:4px 10px; font-size:11px; cursor:pointer;">
                                        <i class="ti ti-check"></i> Approve
                                    </button>
                                @endif
                                
                                {{-- ✅ Reject Button (only for pending) --}}
                                @if($status === 'pending')
                                    <button class="reject-customer-btn" 
                                            data-customer-id="{{ $customer->id }}"
                                            style="background:#E24B4A; color:#FFFFFF; border:none; border-radius:6px; padding:4px 10px; font-size:11px; cursor:pointer;">
                                        <i class="ti ti-x"></i> Reject
                                    </button>
                                @endif
                                
                                {{-- Toggle Status (for active/inactive) --}}
                                <button data-customer-id="{{ $customer->id }}" 
                                        class="toggle-customer-btn"
                                        style="background:#3B82F6; color:#FFFFFF; border:none; border-radius:6px; padding:4px 10px; font-size:11px; cursor:pointer;">
                                    <i class="ti ti-toggle"></i> Toggle
                                </button>
                                
                                {{-- Delete Button --}}
                                <button data-customer-id="{{ $customer->id }}" 
                                        class="delete-customer-btn"
                                        style="background:#E24B4A; color:#FFFFFF; border:none; border-radius:6px; padding:4px 10px; font-size:11px; cursor:pointer;">
                                    <i class="ti ti-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align:center; padding:40px 20px; color:#94A3B8;">
            <i class="ti ti-users" style="font-size:48px; display:block; margin-bottom:12px; color:#D7DEE6;"></i>
            <div style="font-size:16px; font-weight:500; color:#0F172A;">No Customers Yet</div>
            <p style="font-size:13px; margin-top:4px;">Customers will appear here once they register.</p>
        </div>
        @endif
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the CSRF token from the meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        // ✅ Approve Customer
        document.querySelectorAll('.approve-customer-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var customerId = this.getAttribute('data-customer-id');
                if (confirm('Approve this customer?')) {
                    fetch('/admin/users/' + customerId + '/approve', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('✅ Customer approved successfully!');
                            location.reload();
                        } else {
                            alert(data.message || 'Error approving customer');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error approving customer');
                    });
                }
            });
        });

        // ✅ Reject Customer
        document.querySelectorAll('.reject-customer-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var customerId = this.getAttribute('data-customer-id');
                var reason = prompt('Why are you rejecting this customer?');
                if (reason !== null) {
                    fetch('/admin/users/' + customerId + '/reject', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ rejection_reason: reason })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('❌ Customer rejected successfully!');
                            location.reload();
                        } else {
                            alert(data.message || 'Error rejecting customer');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error rejecting customer');
                    });
                }
            });
        });

        // Toggle Customer Status (is_available)
        document.querySelectorAll('.toggle-customer-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var customerId = this.getAttribute('data-customer-id');
                if (confirm('Toggle customer availability status?')) {
                    fetch('/admin/customers/' + customerId + '/toggle-status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error toggling customer status');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error toggling customer status');
                    });
                }
            });
        });

        // Delete Customer
        document.querySelectorAll('.delete-customer-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var customerId = this.getAttribute('data-customer-id');
                if (confirm('Are you sure you want to delete this customer?')) {
                    fetch('/admin/customers/' + customerId, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error deleting customer');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting customer');
                    });
                }
            });
        });
    });
</script>

@endsection