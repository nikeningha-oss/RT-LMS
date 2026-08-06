@extends('layouts.dashboard')

@section('title', 'User Approvals - Tracklane')
@section('page-title', 'User Approvals')

@section('dashboard-content')

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <div>
            <div style="font-size:16px; font-weight:500; color:#0F172A;">👤 Pending Approvals</div>
            <div style="font-size:12px; color:#64748B;">Review and approve new user registrations</div>
        </div>
        <span style="background:#FEF3C7; color:#92400E; padding:4px 12px; border-radius:12px; font-size:13px; font-weight:500;">
            {{ $pendingUsers->count() }} pending
        </span>
    </div>

    @if(session('success'))
        <div style="background:#D1FAE5; color:#065F46; padding:12px 16px; border-radius:8px; margin-bottom:16px;">
            <i class="ti ti-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#FEE2E2; color:#991B1B; padding:12px 16px; border-radius:8px; margin-bottom:16px;">
            <i class="ti ti-alert-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Pending Users -->
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:16px; margin-bottom:16px;">
        <div style="font-size:14px; font-weight:600; color:#0F172A; margin-bottom:12px;">⏳ Pending Approvals</div>
        
        @if($pendingUsers->count() > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%; font-size:12px; border-collapse:collapse;">
                <thead>
                    <tr style="color:#64748B; border-bottom:1px solid #E2E8F0;">
                        <th style="padding:8px; text-align:left;">Name</th>
                        <th style="padding:8px; text-align:left;">Email</th>
                        <th style="padding:8px; text-align:left;">Role</th>
                        <th style="padding:8px; text-align:left;">Registered</th>
                        <th style="padding:8px; text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingUsers as $user)
                    <tr style="border-bottom:0.5px solid #E2E8F0;">
                        <td style="padding:8px; font-weight:500; color:#0F172A;">{{ $user->name }}</td>
                        <td style="padding:8px; color:#64748B;">{{ $user->email }}</td>
                        <td style="padding:8px;">
                            <span style="background:#CCFBF1; color:#0D9488; padding:2px 10px; border-radius:12px; font-size:11px;">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td style="padding:8px; color:#64748B;">{{ $user->created_at->diffForHumans() }}</td>
                        <td style="padding:8px; text-align:center;">
                            <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" style="background:#10B981; color:#FFFFFF; border:none; border-radius:6px; padding:4px 12px; font-size:11px; cursor:pointer;">
                                    <i class="ti ti-check"></i> Approve
                                </button>
                            </form>
                            <!-- ✅ FIXED: Using data attribute instead of inline onclick -->
                            <button data-user-id="{{ $user->id }}" 
                                    class="reject-btn"
                                    style="background:#E24B4A; color:#FFFFFF; border:none; border-radius:6px; padding:4px 12px; font-size:11px; cursor:pointer;">
                                <i class="ti ti-x"></i> Reject
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align:center; padding:20px; color:#94A3B8;">
            <i class="ti ti-user-check" style="font-size:32px; display:block; margin-bottom:8px;"></i>
            No pending approvals
        </div>
        @endif
    </div>

    <!-- Approved Users -->
    <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:16px;">
        <div style="font-size:14px; font-weight:600; color:#0F172A; margin-bottom:12px;">✅ Recently Approved</div>
        
        @if($approvedUsers->count() > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%; font-size:12px; border-collapse:collapse;">
                <thead>
                    <tr style="color:#64748B; border-bottom:1px solid #E2E8F0;">
                        <th style="padding:8px; text-align:left;">Name</th>
                        <th style="padding:8px; text-align:left;">Email</th>
                        <th style="padding:8px; text-align:left;">Role</th>
                        <th style="padding:8px; text-align:left;">Approved</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approvedUsers as $user)
                    <tr style="border-bottom:0.5px solid #E2E8F0;">
                        <td style="padding:8px; font-weight:500; color:#0F172A;">{{ $user->name }}</td>
                        <td style="padding:8px; color:#64748B;">{{ $user->email }}</td>
                        <td style="padding:8px;">
                            <span style="background:#D1FAE5; color:#065F46; padding:2px 10px; border-radius:12px; font-size:11px;">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td style="padding:8px; color:#64748B;">{{ $user->approved_at ? $user->approved_at->diffForHumans() : 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align:center; padding:20px; color:#94A3B8;">
            No approved users yet
        </div>
        @endif
    </div>

</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; backdrop-filter:blur(4px);">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:90%; max-width:450px; background:#FFFFFF; border-radius:16px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <span style="font-size:18px; font-weight:600; color:#0F172A;">❌ Reject User</span>
            <button onclick="closeRejectModal()" style="background:none; border:none; font-size:24px; cursor:pointer; color:#94A3B8;">✕</button>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="margin-bottom:12px;">
                <label style="font-size:13px; font-weight:500; color:#0F172A;">Rejection Reason (Optional)</label>
                <textarea name="rejection_reason" 
                          style="width:100%; padding:10px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:13px; margin-top:4px; min-height:80px; resize:vertical;"
                          placeholder="Why is this user being rejected?"></textarea>
            </div>
            <button type="submit" 
                    style="width:100%; background:#E24B4A; color:#FFFFFF; border:none; border-radius:8px; padding:10px; font-size:13px; font-weight:500; cursor:pointer;">
                <i class="ti ti-x me-1"></i> Confirm Rejection
            </button>
        </form>
    </div>
</div>

<script>
    // ============================================================
    // EVENT LISTENERS FOR REJECT BUTTONS
    // ============================================================
    
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.reject-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var userId = this.getAttribute('data-user-id');
                openRejectModal(userId);
            });
        });
    });

    // ============================================================
    // MODAL FUNCTIONS
    // ============================================================

    function openRejectModal(userId) {
        document.getElementById('rejectModal').style.display = 'block';
        document.getElementById('rejectForm').action = '/admin/users/' + userId + '/reject';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRejectModal();
        }
    });

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        var modal = document.getElementById('rejectModal');
        if (e.target === modal) {
            closeRejectModal();
        }
    });
</script>

@endsection 