

<?php $__env->startSection('title', 'Edit Customer - Tracklane'); ?>
<?php $__env->startSection('page-title', 'Edit Customer'); ?>

<?php $__env->startSection('dashboard-content'); ?>

<div style="background:#F8FAFC; padding:18px 22px; min-height:560px;">

    <div style="max-width:600px; margin:0 auto;">

        <!-- Header -->
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <a href="<?php echo e(route('admin.customers')); ?>" style="color:#64748B; text-decoration:none; font-size:20px;">
                <i class="ti ti-arrow-left"></i>
            </a>
            <div>
                <div style="font-size:16px; font-weight:500; color:#0F172A;">✏️ Edit Customer</div>
                <div style="font-size:12px; color:#64748B;">Update customer information</div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if(session('success')): ?>
            <div style="background:#D1FAE5; color:#065F46; padding:12px 16px; border-radius:8px; margin-bottom:16px;">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div style="background:#FEE2E2; color:#991B1B; padding:12px 16px; border-radius:8px; margin-bottom:16px;">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div style="background:#FEE2E2; color:#991B1B; padding:12px 16px; border-radius:8px; margin-bottom:16px;">
                <ul style="margin:0; padding-left:20px;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Edit Form -->
        <div style="background:#FFFFFF; border:0.5px solid #E2E8F0; border-radius:16px; padding:24px;">

            <form method="POST" action="<?php echo e(route('admin.customers.update', $customer->id)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <!-- Name -->
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#0F172A; margin-bottom:4px;">
                        <i class="ti ti-user me-1" style="color:#14B8A6;"></i> Full Name <span style="color:#E24B4A;">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="<?php echo e(old('name', $customer->name)); ?>"
                           class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           required
                           style="width:100%; padding:10px 14px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px;">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="color:#E24B4A; font-size:12px; margin-top:4px;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Email -->
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#0F172A; margin-bottom:4px;">
                        <i class="ti ti-mail me-1" style="color:#14B8A6;"></i> Email Address <span style="color:#E24B4A;">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           value="<?php echo e(old('email', $customer->email)); ?>"
                           class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           required
                           style="width:100%; padding:10px 14px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px;">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="color:#E24B4A; font-size:12px; margin-top:4px;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Phone -->
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#0F172A; margin-bottom:4px;">
                        <i class="ti ti-phone me-1" style="color:#14B8A6;"></i> Phone Number
                    </label>
                    <input type="text" 
                           name="phone" 
                           value="<?php echo e(old('phone', $customer->phone)); ?>"
                           class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           style="width:100%; padding:10px 14px; border:0.5px solid #E2E8F0; border-radius:8px; font-size:14px;">
                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div style="color:#E24B4A; font-size:12px; margin-top:4px;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Status -->
                <div style="margin-bottom:20px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#0F172A; margin-bottom:4px;">
                        <i class="ti ti-toggle me-1" style="color:#14B8A6;"></i> Status
                    </label>
                    <div style="display:flex; gap:16px;">
                        <label style="font-size:13px; color:#0F172A; display:flex; align-items:center; gap:6px;">
                            <input type="radio" name="is_active" value="1" <?php echo e($customer->is_available ? 'checked' : ''); ?>> 
                            <span style="color:#10B981;">🟢 Active</span>
                        </label>
                        <label style="font-size:13px; color:#0F172A; display:flex; align-items:center; gap:6px;">
                            <input type="radio" name="is_active" value="0" <?php echo e(!$customer->is_available ? 'checked' : ''); ?>> 
                            <span style="color:#94A3B8;">🔴 Inactive</span>
                        </label>
                    </div>
                </div>

                <!-- Stats -->
                <div style="background:#F8FAFC; padding:12px; border-radius:8px; margin-bottom:20px;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; font-size:13px;">
                        <div>
                            <span style="color:#64748B;">Joined:</span>
                            <span style="color:#0F172A; font-weight:500;">
                                <?php echo e(optional($customer->created_at)->format('M d, Y') ?? 'N/A'); ?>

                            </span>
                        </div>
                        <div>
                            <span style="color:#64748B;">Total Orders:</span>
                            <span style="color:#0F172A; font-weight:500;">
                                <?php echo e($customer->orders->count() ?? 0); ?>

                            </span>
                        </div>
                        <div>
                            <span style="color:#64748B;">Role:</span>
                            <span style="color:#0F172A; font-weight:500; text-transform:capitalize;">
                                <?php echo e($customer->role ?? 'N/A'); ?>

                            </span>
                        </div>
                        <div>
                            <span style="color:#64748B;">Status:</span>
                            <span style="color:#0F172A; font-weight:500;">
                                <?php echo e($customer->approval_status ?? 'N/A'); ?>

                            </span>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div style="display:flex; gap:12px;">
                    <button type="submit" 
                            style="background:#0D9488; color:#FFFFFF; border:none; border-radius:8px; padding:10px 32px; font-size:14px; font-weight:500; cursor:pointer;">
                        <i class="ti ti-save me-2"></i> Update Customer
                    </button>
                    <a href="<?php echo e(route('admin.customers')); ?>" 
                       style="background:#FFFFFF; color:#64748B; border:0.5px solid #E2E8F0; border-radius:8px; padding:10px 24px; font-size:14px; text-decoration:none;">
                        Cancel
                    </a>
                </div>

            </form>
        </div>

    </div>
</div>

<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\logistics_system\resources\views/admin/customers-edit.blade.php ENDPATH**/ ?>