

<?php $__env->startSection('title', 'Admin Login - Tracklane'); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center" 
     style="background: linear-gradient(135deg, #0B1220 0%, #1A2332 100%);">
    
    <div class="row justify-content-center w-100">
        <div class="col-md-6 col-lg-5 col-xl-4">
            
            <div class="auth-card border-0 shadow-lg" style="border-radius: 16px; padding: 32px 28px;">
                
                <!-- Back Button -->
                <a href="<?php echo e(route('home')); ?>" 
                   style="display: inline-flex; align-items: center; gap: 6px; color: #64748B; text-decoration: none; font-size: 13px; margin-bottom: 16px;"
                   onmouseover="this.style.color='#14B8A6'" 
                   onmouseout="this.style.color='#64748B'">
                    <i class="ti ti-arrow-left" style="font-size: 16px;"></i> Back to Home
                </a>
                
                <!-- Logo -->
                <div class="text-center mb-4">
                    <div class="app-logo" style="background: #E24B4A;">
                        <i class="ti ti-crown" style="font-size:28px; color:#FFFFFF;"></i>
                    </div>
                    <h4 class="app-title mb-1">Admin Access</h4>
                    <p class="text-muted" style="font-size: 13px; font-family: 'Inter', sans-serif;">
                        Secure login for administrators only
                    </p>
                </div>

                <?php if(session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" 
                         style="border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13px; border: none;">
                        <i class="ti ti-alert-circle me-2"></i> <?php echo e(session('error')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if(session('status')): ?>
                    <div class="alert alert-success alert-dismissible fade show" 
                         style="border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13px; border: none;">
                        <i class="ti ti-check-circle me-2"></i> <?php echo e(session('status')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('admin.login')); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label class="form-label"><i class="ti ti-mail"></i> Admin Email</label>
                        <input type="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="admin@tracklane.com" value="<?php echo e(old('email')); ?>" required autofocus>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="ti ti-lock"></i> Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="password" 
                                   class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   placeholder="Enter admin password" required>
                            <span class="position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer;"
                                  onclick="togglePassword('password')">
                                <i class="ti ti-eye" id="password-icon" style="color: #94A3B8; font-size: 18px;"></i>
                            </span>
                        </div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <a href="#" class="text-decoration-none" 
                           style="color: #14B8A6; font-size: 12px;">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit" class="btn w-100 py-2 fw-600" 
                            style="background: #E24B4A; color: #FFFFFF; border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 14px; transition: all 0.2s ease;">
                        <i class="ti ti-login me-2"></i> Admin Login
                    </button>

                </form>

                <div class="text-center mt-4">
                    <p style="font-size: 11px; color: #94A3B8; font-family: 'Inter', sans-serif;">
                        <i class="ti ti-shield me-1"></i> This area is restricted to authorized personnel
                    </p>
                </div>

            </div>
            
            <div class="text-center mt-4">
                <p style="color: rgba(255,255,255,0.35); font-size: 11px;">
                    <i class="ti ti-copyright me-1"></i> 2026 Tracklane. All rights reserved.
                </p>
            </div>
            
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
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
</script>

<style>
    .auth-card .form-control:focus {
        border-color: #E24B4A !important;
        box-shadow: 0 0 0 3px rgba(226, 75, 74, 0.1) !important;
    }
    .auth-card .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(226, 75, 74, 0.35);
    }
    .auth-card .form-check-input:checked {
        background-color: #E24B4A;
        border-color: #E24B4A;
    }
    .auth-card .form-check-input:focus {
        box-shadow: 0 0 0 3px rgba(226, 75, 74, 0.15);
        border-color: #E24B4A;
    }
    .auth-card a:hover {
        color: #0D9488 !important;
    }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.guest', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\logistics_system\resources\views/auth/admin-login.blade.php ENDPATH**/ ?>