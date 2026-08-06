<!--
    ============================================================
    TRACKLANE LOGIN PAGE
    ============================================================
    This page displays the login form where users sign in.
    
    HOW IT WORKS:
    1. User enters email and password
    2. Clicks "Sign In" button
    3. Form submits to /login (POST)
    4. AuthController@login processes the request
    5. If successful → Redirect to dashboard
    6. If failed → Show error message
    
    LAYOUT STRUCTURE:
    - Extends 'layouts.guest' (guest layout file)
    - Content is placed inside the <?php $__env->startSection('content'); ?> block
    - <?php $__env->startPush('scripts'); ?> adds JavaScript at the bottom
-->



<?php $__env->startSection('title', 'Login - Tracklane'); ?>

<?php $__env->startSection('content'); ?>

<!-- ============================================================
     LOGIN CONTAINER
     ============================================================ -->
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center" 
     style="background: linear-gradient(135deg, #0B1220 0%, #1A2332 100%);">
    
    <div class="row justify-content-center w-100">
        <div class="col-md-6 col-lg-5 col-xl-4">
            
            <!-- ============================================================
                 LOGIN CARD
                 ============================================================ -->
            <div class="auth-card border-0 shadow-lg" style="border-radius: 16px; padding: 32px 28px;">
                
                <!-- ============================================================
                     BACK BUTTON
                     ============================================================ -->
                <a href="<?php echo e(route('home')); ?>" 
                   style="display: inline-flex; align-items: center; gap: 6px; color: #64748B; text-decoration: none; font-size: 13px; margin-bottom: 16px; transition: color 0.2s;"
                   onmouseover="this.style.color='#14B8A6'" 
                   onmouseout="this.style.color='#64748B'">
                    <i class="ti ti-arrow-left" style="font-size: 16px;"></i> Back to Home
                </a>
                
                <!-- ============================================================
                     APP TITLE & LOGO
                     ============================================================ -->
                <div class="text-center mb-4">
                    <div class="app-logo">
                        <i class="ti ti-route"></i>
                    </div>
                    <h4 class="app-title mb-1">Tracklane</h4>
                    <p class="text-muted" style="font-size: 13px; font-family: 'Inter', sans-serif; margin-bottom: 0;">
                        Welcome back! Sign in to your account
                    </p>
                </div>

                <!-- ============================================================
                     SESSION STATUS MESSAGES
                     ============================================================ -->
                <?php if(session('status')): ?>
                    <div class="alert alert-success alert-dismissible fade show" 
                         style="border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13px; border: none;">
                        <i class="ti ti-check-circle me-2"></i> 
                        <?php echo e(session('status')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- ============================================================
                     LOGIN FORM
                     ============================================================ -->
                <form method="POST" action="<?php echo e(route('login')); ?>">
                    
                    <?php echo csrf_field(); ?>

                    <!-- ============================================================
                         EMAIL FIELD
                         ============================================================ -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="ti ti-mail"></i> Email Address
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
                               autofocus>
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

                    <!-- ============================================================
                         PASSWORD FIELD
                         ============================================================ -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="ti ti-lock"></i> Password
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
                                   placeholder="Enter your password" 
                                   required>
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
                    </div>

                    <!-- ============================================================
                         REMEMBER ME & FORGOT PASSWORD
                         ============================================================ -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                        
                        <?php if(Route::has('password.request')): ?>
                            <a href="<?php echo e(route('password.request')); ?>" class="text-decoration-none" 
                               style="color: #14B8A6; font-size: 12px; font-weight: 500;">
                                Forgot password?
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- ============================================================
                         SUBMIT BUTTON
                         ============================================================ -->
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="ti ti-login me-2"></i> Sign in
                    </button>

                </form>
                <!-- END OF FORM -->

                <!-- ============================================================
                     REGISTER LINK
                     ============================================================ -->
                <div class="text-center mt-4">
                    <p class="text-muted" style="font-size: 13px; font-family: 'Inter', sans-serif; margin-bottom: 0;">
                        Don't have an account?
                    </p>
                    <a href="<?php echo e(route('register')); ?>" class="text-decoration-none fw-500" 
                       style="color: #14B8A6; font-size: 14px; font-family: 'Inter', sans-serif; transition: all 0.2s ease;">
                        Create one now <i class="ti ti-arrow-right ms-1"></i>
                    </a>
                </div>

            </div>
            <!-- END LOGIN CARD -->
            
            <!-- ============================================================
                 FOOTER
                 ============================================================ -->
            <div class="text-center mt-4">
                <p style="color: rgba(255,255,255,0.35); font-size: 11px; font-family: 'Inter', sans-serif; letter-spacing: 0.02em;">
                    <i class="ti ti-copyright me-1"></i> 2026 Tracklane. All rights reserved.
                </p>
            </div>
            
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<!-- ============================================================
     JAVASCRIPT SECTION
     ============================================================ -->
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
        border-color: #14B8A6 !important;
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1) !important;
    }
    
    .auth-card .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(20, 184, 166, 0.35);
    }
    
    .auth-card .form-check-input:checked {
        background-color: #14B8A6;
        border-color: #14B8A6;
    }
    
    .auth-card .form-check-input:focus {
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
        border-color: #14B8A6;
    }
    
    .auth-card a:hover {
        color: #0D9488 !important;
    }
    
    .auth-card .alert-success {
        background: #CCFBF1;
        color: #0F766E;
    }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.guest', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\logistics_system\resources\views/auth/login.blade.php ENDPATH**/ ?>