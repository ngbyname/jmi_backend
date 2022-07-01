<li>
    <a href="javascript:void(0);" class="container-refresh">
        <i class="fa fa-refresh"></i>
    </a>
</li>
<script>
    $('.container-refresh').off('click').on('click', function() {
        $.admin.reload();
        $.admin.toastr.success('<?php echo e(__('admin.refresh_succeeded'), false); ?>', '', {positionClass:"toast-top-center"});
    });
</script>
<?php /**PATH /Users/a13221514/Documents/shopping-app-backend-main/jmi-app/vendor/encore/laravel-admin/src/../resources/views/components/refresh-btn.blade.php ENDPATH**/ ?>