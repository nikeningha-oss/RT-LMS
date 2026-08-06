@extends('layouts.dashboard')

@section('title', 'Request Withdrawal - Tracklane')

@section('dashboard-content')
    <!-- ✅ Changed back to dashboard-content to match layout -->

    <style>
        .balance-card {
            background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%);
            border-radius: 16px;
            padding: 24px 28px;
            color: #FFFFFF;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .balance-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .balance-card .balance-label {
            font-size: 13px;
            opacity: 0.8;
            font-weight: 400;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .balance-card .balance-amount {
            font-size: 36px;
            font-weight: 700;
            margin-top: 4px;
        }
        .balance-card .balance-details {
            display: flex;
            gap: 24px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(255,255,255,0.15);
        }
        .balance-card .balance-details .detail-item {
            font-size: 12px;
            opacity: 0.8;
        }
        .balance-card .balance-details .detail-item strong {
            opacity: 1;
            font-weight: 600;
        }

        .fee-info {
            background: #FEF3C7;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border: 0.5px solid #F59E0B;
        }
        .fee-info .fee-icon {
            font-size: 20px;
            color: #F59E0B;
            margin-top: 1px;
        }
        .fee-info .fee-text {
            font-size: 13px;
            color: #92400E;
        }
        .fee-info .fee-text strong {
            font-weight: 700;
        }

        .payment-method-card {
            background: #FFFFFF;
            border: 0.5px solid #E2E8F0;
            border-radius: 10px;
            padding: 14px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .payment-method-card:hover {
            border-color: #14B8A6;
            background: #F8FAFC;
        }
        .payment-method-card.selected {
            border-color: #14B8A6;
            background: #F0FDFA;
            box-shadow: 0 0 0 2px rgba(20,184,166,0.15);
        }
        .payment-method-card .method-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .payment-method-card .method-icon.mtn { background: #FEF3C7; color: #F59E0B; }
        .payment-method-card .method-icon.orange { background: #FEE2E2; color: #E24B4A; }
        .payment-method-card .method-icon.bank { background: #DBEAFE; color: #2563EB; }
        .payment-method-card .method-info { flex: 1; }
        .payment-method-card .method-info .method-name { font-size: 14px; font-weight: 500; color: #0F172A; }
        .payment-method-card .method-info .method-desc { font-size: 12px; color: #64748B; }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 0.5px solid #E2E8F0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
            background: #FFFFFF;
        }
        .form-input:focus {
            outline: none;
            border-color: #14B8A6;
            box-shadow: 0 0 0 3px rgba(20,184,166,0.1);
        }
        .form-input.error {
            border-color: #E24B4A;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #0F172A;
            margin-bottom: 4px;
        }
        .form-hint {
            font-size: 12px;
            color: #64748B;
            margin-top: 4px;
        }
        .form-error {
            color: #E24B4A;
            font-size: 12px;
            margin-top: 4px;
        }
        .form-group {
            margin-bottom: 16px;
        }
    </style>

    <div style="background:#F8FAFC; padding:20px 24px; min-height:560px;">

        <div style="max-width:600px; margin:0 auto;">

            <!-- HEADER -->
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
                <a href="{{ route('driver.dashboard') }}" style="color:#64748B; text-decoration:none; font-size:20px;">
                    <i class="ti ti-arrow-left"></i>
                </a>
                <div>
                    <div style="font-size:18px; font-weight:600; color:#0F172A;">💸 Request Withdrawal</div>
                    <div style="font-size:13px; color:#64748B;">Withdraw your earnings to your mobile money or bank account</div>
                </div>
            </div>

            <!-- BALANCE CARD -->
            <div class="balance-card">
                <div class="balance-label">💰 Available Balance</div>
                <div class="balance-amount">{{ $driver->formatted_available_balance ?? '0 F' }}</div>
                <div class="balance-details">
                    <div class="detail-item">
                        Total Earned: <strong>{{ $driver->formatted_total_earnings ?? '0 F' }}</strong>
                    </div>
                    <div class="detail-item">
                        Total Withdrawn: <strong>{{ $driver->formatted_total_withdrawn ?? '0 F' }}</strong>
                    </div>
                </div>
            </div>

            <!-- FEE INFO -->
            <div class="fee-info">
                <div class="fee-icon">ℹ️</div>
                <div class="fee-text">
                    <strong>Withdrawal Fee: 5%</strong><br>
                    A 5% platform fee will be deducted from your withdrawal amount.
                    Example: Withdraw 10,000 F → Fee: 500 F → You receive: 9,500 F
                </div>
            </div>

            <!-- WITHDRAWAL FORM -->
            <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:24px;">

                @if(session('error'))
                    <div style="background:#FEE2E2; color:#991B1B; padding:12px 16px; border-radius:8px; margin-bottom:16px;">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="background:#FEE2E2; color:#991B1B; padding:12px 16px; border-radius:8px; margin-bottom:16px;">
                        <ul style="margin:0; padding-left:20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('driver.withdraw.store') }}" id="withdrawForm">
                    @csrf

                    <!-- Amount -->
                    <div class="form-group">
                        <label class="form-label">Amount (FCFA) <span style="color:#E24B4A;">*</span></label>
                        <input type="number" 
                               name="amount" 
                               id="amountInput"
                               class="form-input @error('amount') error @enderror" 
                               placeholder="Enter amount to withdraw"
                               min="1"
                               max="{{ $driver->available_balance ?? 0 }}"
                               value="{{ old('amount') }}"
                               required>
                        <div class="form-hint">
                            <span id="feePreview">Fee: 0 F</span> · 
                            <span id="netPreview">You receive: 0 F</span>
                            <br>
                            <span style="color:#64748B;">Minimum: 1 F · Maximum: {{ number_format($driver->available_balance ?? 0, 0, ',', ' ') }} F</span>
                        </div>
                        @error('amount')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Payment Method -->
                    <div class="form-group">
                        <label class="form-label">Payment Method <span style="color:#E24B4A;">*</span></label>
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px;">
                            <div class="payment-method-card selected" data-method="mtn" onclick="selectMethod(this, 'mtn')">
                                <div class="method-icon mtn">M</div>
                                <div class="method-info">
                                    <div class="method-name">MTN</div>
                                    <div class="method-desc">Mobile Money</div>
                                </div>
                            </div>
                            <div class="payment-method-card" data-method="orange" onclick="selectMethod(this, 'orange')">
                                <div class="method-icon orange">O</div>
                                <div class="method-info">
                                    <div class="method-name">Orange</div>
                                    <div class="method-desc">Mobile Money</div>
                                </div>
                            </div>
                            <div class="payment-method-card" data-method="bank" onclick="selectMethod(this, 'bank')">
                                <div class="method-icon bank">B</div>
                                <div class="method-info">
                                    <div class="method-name">Bank</div>
                                    <div class="method-desc">Transfer</div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="payment_method" id="paymentMethod" value="mtn">
                        @error('payment_method')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Account Details -->
                    <div class="form-group">
                        <label class="form-label" id="accountLabel">MTN Mobile Money Number <span style="color:#E24B4A;">*</span></label>
                        <input type="text" 
                               name="account_details" 
                               class="form-input @error('account_details') error @enderror" 
                               placeholder="e.g., 675123456"
                               value="{{ old('account_details') }}"
                               required>
                        <div class="form-hint" id="accountHint">Enter the phone number registered with MTN Mobile Money</div>
                        @error('account_details')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div style="display:flex; gap:10px; margin-top:8px;">
                        <button type="submit" 
                                style="flex:1; background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:12px 20px; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s;">
                            <i class="ti ti-credit-card me-2"></i> Request Withdrawal
                        </button>
                        <a href="{{ route('driver.dashboard') }}" 
                           style="background:#FFFFFF; color:#64748B; border:0.5px solid #E2E8F0; border-radius:8px; padding:12px 20px; font-size:14px; text-decoration:none;">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>

            <!-- BACK TO WITHDRAWALS -->
            <div style="text-align:center; margin-top:16px;">
                <a href="{{ route('driver.withdrawals') }}" style="color:#14B8A6; text-decoration:none; font-size:13px;">
                    <i class="ti ti-history"></i> View Withdrawal History
                </a>
            </div>

        </div>

    </div>

    <script>
        function selectMethod(element, method) {
            document.querySelectorAll('.payment-method-card').forEach(function(el) {
                el.classList.remove('selected');
            });
            element.classList.add('selected');
            document.getElementById('paymentMethod').value = method;
            
            var label = document.getElementById('accountLabel');
            var hint = document.getElementById('accountHint');
            var input = document.querySelector('input[name="account_details"]');
            
            switch(method) {
                case 'mtn':
                    label.textContent = 'MTN Mobile Money Number *';
                    hint.textContent = 'Enter the phone number registered with MTN Mobile Money';
                    input.placeholder = 'e.g., 675123456';
                    break;
                case 'orange':
                    label.textContent = 'Orange Money Number *';
                    hint.textContent = 'Enter the phone number registered with Orange Money';
                    input.placeholder = 'e.g., 656123456';
                    break;
                case 'bank':
                    label.textContent = 'Bank Account Details *';
                    hint.textContent = 'Enter your bank name and account number';
                    input.placeholder = 'e.g., Bank Name: 1234567890';
                    break;
            }
        }

        document.getElementById('amountInput').addEventListener('input', function() {
            var amount = parseFloat(this.value) || 0;
            var fee = amount * 0.05;
            var net = amount - fee;
            
            document.getElementById('feePreview').textContent = 'Fee: ' + fee.toLocaleString() + ' F';
            document.getElementById('netPreview').textContent = 'You receive: ' + net.toLocaleString() + ' F';
        });

        document.addEventListener('DOMContentLoaded', function() {
            var amountInput = document.getElementById('amountInput');
            if (amountInput.value > 0) {
                amountInput.dispatchEvent(new Event('input'));
            }
        });
    </script>
@endsection