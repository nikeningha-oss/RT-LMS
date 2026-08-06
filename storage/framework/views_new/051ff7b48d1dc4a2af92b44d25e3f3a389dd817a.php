<!--
    TRACKLANE REGISTER PAGE
    This page allows new users to create an account
-->



<?php $__env->startSection('title', 'Register - Tracklane'); ?>

<?php $__env->startSection('content'); ?>

<!-- REGISTER CONTAINER -->
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center" 
     style="background: linear-gradient(135deg, #0B1220 0%, #1A2332 100%);">
    
    <div class="row justify-content-center w-100">
        <div class="col-md-6 col-lg-5 col-xl-4">
            
            <!-- REGISTER CARD -->
            <div class="card border-0 shadow-lg rounded-4" style="background: #FFFFFF;">
                <div class="card-body p-5">
                    
                    <!-- =========================
                         BACK BUTTON
                    ========================== -->
                    <a href="<?php echo e(route('home')); ?>" 
                       style="display: inline-flex; align-items: center; gap: 6px; color: #64748B; text-decoration: none; font-size: 13px; margin-bottom: 16px; transition: color 0.2s;"
                       onmouseover="this.style.color='#14B8A6'" 
                       onmouseout="this.style.color='#64748B'">
                        <i class="ti ti-arrow-left" style="font-size: 16px;"></i> Back to Home
                    </a>
                    
                    <!-- =========================
                         APP TITLE & LOGO
                    ========================== -->
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3"
                             style="width: 56px; height: 56px; background: #14B8A6;">
                            <i class="ti ti-route" style="font-size:28px; color: #0B1220;"></i>
                        </div>
                        
                        <h4 class="fw-700 mb-1" style="color: #0F172A; font-family: 'Inter', sans-serif; letter-spacing: -0.02em;">
                            Tracklane
                        </h4>
                        
                        <p class="text-muted" style="font-size: 13px; font-family: 'Inter', sans-serif;">
                            Create your account to start using the system
                        </p>
                    </div>

                    <!-- =========================
                         REGISTER FORM START
                    ========================== -->
                    <form method="POST" action="<?php echo e(route('register')); ?>">
                        
                        <?php echo csrf_field(); ?>

                        <!-- FULL NAME -->
                        <div class="mb-3">
                            <label class="form-label fw-500" style="font-size: 13px; color: #0F172A; font-family: 'Inter', sans-serif;">
                                <i class="ti ti-user me-1" style="font-size: 16px;"></i> Full Name
                            </label>
                            <input type="text" 
                                   name="name" 
                                   class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   placeholder="Enter your full name" 
                                   value="<?php echo e(old('name')); ?>"
                                   required
                                   style="border-radius: 8px; padding: 10px 14px; font-size: 14px; border: 0.5px solid #E2E8F0; font-family: 'Inter', sans-serif;">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small mt-1">
                                    <i class="ti ti-alert-circle me-1"></i><?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label class="form-label fw-500" style="font-size: 13px; color: #0F172A; font-family: 'Inter', sans-serif;">
                                <i class="ti ti-mail me-1" style="font-size: 16px;"></i> Email
                            </label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   placeholder="Enter your email" 
                                   value="<?php echo e(old('email')); ?>"
                                   required
                                   style="border-radius: 8px; padding: 10px 14px; font-size: 14px; border: 0.5px solid #E2E8F0; font-family: 'Inter', sans-serif;">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small mt-1">
                                    <i class="ti ti-alert-circle me-1"></i><?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- PHONE NUMBER -->
                        <div class="mb-3">
                            <label class="form-label fw-500" style="font-size: 13px; color: #0F172A; font-family: 'Inter', sans-serif;">
                                <i class="ti ti-phone me-1" style="font-size: 16px;"></i> Phone Number
                            </label>
                            <input type="tel" 
                                   name="phone" 
                                   class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   placeholder="Enter your phone number (e.g., +237 690000000)" 
                                   value="<?php echo e(old('phone')); ?>"
                                   style="border-radius: 8px; padding: 10px 14px; font-size: 14px; border: 0.5px solid #E2E8F0; font-family: 'Inter', sans-serif;">
                            <small class="text-muted" style="font-size: 11px; font-family: 'Inter', sans-serif;">
                                <i class="ti ti-info-circle me-1"></i> This will be used for communication
                            </small>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small mt-1">
                                    <i class="ti ti-alert-circle me-1"></i><?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- ✅ NEW: LICENSE NUMBER - Only for Drivers -->
                        <div class="mb-3" id="licenseField" style="display: none;">
                            <label class="form-label fw-500" style="font-size: 13px; color: #0F172A; font-family: 'Inter', sans-serif;">
                                <i class="ti ti-id me-1" style="font-size: 16px;"></i> Driver's License Number <span style="color: #E24B4A;">*</span>
                            </label>
                            <input type="text" 
                                   name="license_number" 
                                   id="license_number"
                                   class="form-control <?php $__errorArgs = ['license_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   placeholder="Enter your driver's license number" 
                                   value="<?php echo e(old('license_number')); ?>"
                                   style="border-radius: 8px; padding: 10px 14px; font-size: 14px; border: 0.5px solid #E2E8F0; font-family: 'Inter', sans-serif;">
                            <small class="text-muted" style="font-size: 11px; font-family: 'Inter', sans-serif;">
                                <i class="ti ti-info-circle me-1"></i> Required for driver registration. Must be a valid license number.
                            </small>
                            <?php $__errorArgs = ['license_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small mt-1">
                                    <i class="ti ti-alert-circle me-1"></i><?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- PASSWORD -->
                        <div class="mb-3">
                            <label class="form-label fw-500" style="font-size: 13px; color: #0F172A; font-family: 'Inter', sans-serif;">
                                <i class="ti ti-lock me-1" style="font-size: 16px;"></i> Password
                            </label>
                            <div class="position-relative">
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       placeholder="Create a password" 
                                       required
                                       style="border-radius: 8px; padding: 10px 14px; font-size: 14px; border: 0.5px solid #E2E8F0; font-family: 'Inter', sans-serif;">
                                <span class="position-absolute" 
                                      style="right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 5;"
                                      onclick="togglePassword('password')">
                                    <i class="ti ti-eye" id="password-icon" style="color: #94A3B8; font-size: 18px;"></i>
                                </span>
                            </div>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small mt-1">
                                    <i class="ti ti-alert-circle me-1"></i><?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted" style="font-size: 11px; font-family: 'Inter', sans-serif;">
                                <i class="ti ti-info-circle me-1"></i> Minimum 8 characters
                            </small>
                        </div>

                        <!-- CONFIRM PASSWORD -->
                        <div class="mb-3">
                            <label class="form-label fw-500" style="font-size: 13px; color: #0F172A; font-family: 'Inter', sans-serif;">
                                <i class="ti ti-check me-1" style="font-size: 16px;"></i> Confirm Password
                            </label>
                            <div class="position-relative">
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation"
                                       class="form-control" 
                                       placeholder="Confirm your password" 
                                       required
                                       style="border-radius: 8px; padding: 10px 14px; font-size: 14px; border: 0.5px solid #E2E8F0; font-family: 'Inter', sans-serif;">
                                <span class="position-absolute" 
                                      style="right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 5;"
                                      onclick="togglePassword('password_confirmation')">
                                    <i class="ti ti-eye" id="password_confirmation-icon" style="color: #94A3B8; font-size: 18px;"></i>
                                </span>
                            </div>
                        </div>

                        <!-- ROLE SELECTION -->
                        <div class="mb-3">
                            <label class="form-label fw-500" style="font-size: 13px; color: #0F172A; font-family: 'Inter', sans-serif;">
                                <i class="ti ti-user-tag me-1" style="font-size: 16px;"></i> I am a:
                            </label>
                            <div class="d-flex gap-3">
                                <div class="form-check flex-fill">
                                    <input class="form-check-input" type="radio" name="role" 
                                           id="role_customer" value="customer" checked
                                           onchange="toggleLicenseField()">
                                    <label class="form-check-label d-flex align-items-center gap-2" 
                                           for="role_customer" 
                                           style="font-size: 13px; font-family: 'Inter', sans-serif;">
                                        <i class="ti ti-user" style="color: #14B8A6; font-size: 16px;"></i> Customer
                                    </label>
                                </div>
                                <div class="form-check flex-fill">
                                    <input class="form-check-input" type="radio" name="role" 
                                           id="role_driver" value="driver"
                                           onchange="toggleLicenseField()">
                                    <label class="form-check-label d-flex align-items-center gap-2" 
                                           for="role_driver" 
                                           style="font-size: 13px; font-family: 'Inter', sans-serif;">
                                        <i class="ti ti-truck" style="color: #F59E0B; font-size: 16px;"></i> Driver
                                    </label>
                                </div>
                            </div>
                            <small class="text-muted" style="font-size: 11px; font-family: 'Inter', sans-serif;">
                                <i class="ti ti-info-circle me-1"></i> Select your role. Customers are auto-approved. Drivers need admin approval.
                            </small>
                        </div>

                        <!-- TERMS & CONDITIONS -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="terms" 
                                       id="terms" required
                                       style="border-color: #E2E8F0;">
                                <label class="form-check-label" for="terms" 
                                       style="font-size: 12px; color: #64748B; font-family: 'Inter', sans-serif;">
                                    I agree to the 
                                    <a href="#" class="text-decoration-none" 
                                       style="color: #14B8A6; font-weight: 500;">
                                        Terms of Service
                                    </a> 
                                    and 
                                    <a href="#" class="text-decoration-none" 
                                       style="color: #14B8A6; font-weight: 500;">
                                        Privacy Policy
                                    </a>
                                </label>
                            </div>
                        </div>

                        <!-- SUBMIT BUTTON -->
                        <button type="submit" class="btn w-100 py-2 fw-600" 
                                style="background: #14B8A6; color: #0B1220; border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 14px; transition: all 0.2s ease;">
                            <i class="ti ti-user-plus me-2"></i> Create Account
                        </button>

                    </form>
                    <!-- =========================
                         REGISTER FORM END
                    ========================== -->

                    <!-- SWITCH TO LOGIN -->
                    <div class="text-center mt-4">
                        <p class="text-muted" style="font-size: 13px; font-family: 'Inter', sans-serif; margin-bottom: 0;">
                            Already have an account?
                        </p>
                        <a href="<?php echo e(route('login')); ?>" class="text-decoration-none fw-500" 
                           style="color: #14B8A6; font-size: 14px; font-family: 'Inter', sans-serif; transition: all 0.2s ease;">
                            Login here <i class="ti ti-arrow-right ms-1"></i>
                        </a>
                    </div>

                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-4">
                <p style="color: rgba(255,255,255,0.4); font-size: 11px; font-family: 'Inter', sans-serif;">
                    <i class="ti ti-copyright me-1"></i> 2026 Tracklane. All rights reserved.
                </p>
            </div>
            
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'ti ti-eye-off';
        } else {
            field.type = 'password';
            icon.className = 'ti ti-eye';
        }
    }

    // ✅ NEW: Toggle license field visibility based on role selection
    function toggleLicenseField() {
        const roleDriver = document.getElementById('role_driver');
        const licenseField = document.getElementById('licenseField');
        const licenseInput = document.getElementById('license_number');
        
        if (roleDriver.checked) {
            licenseField.style.display = 'block';
            licenseInput.setAttribute('required', 'required');
        } else {
            licenseField.style.display = 'none';
            licenseInput.removeAttribute('required');
        }
    }

    // ✅ Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleLicenseField();
    });
</script>

<style>
    /* Custom styles for the register page */
    .form-control:focus {
        border-color: #14B8A6 !important;
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1) !important;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
    }
    
    .form-check-input:checked {
        background-color: #14B8A6;
        border-color: #14B8A6;
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1);
        border-color: #14B8A6;
    }
    
    a:hover {
        color: #0D9488 !important;
    }

    #licenseField {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.guest', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\logistics_system\resources\views/auth/register.blade.php ENDPATH**/ ?>