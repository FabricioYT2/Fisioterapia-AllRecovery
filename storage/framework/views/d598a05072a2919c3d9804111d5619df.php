<?php
    $user = auth()->user();
    $canViewRoles = $user && (
        $user->can('view_any_role') || 
        $user->hasRole('super_admin') || 
        $user->hasRole('Administrador')
    );
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$canViewRoles): ?>
<style>
    a[href*="shield/roles"],
    [data-label="Filament Shield"],
    [data-label="Roles"],
    li:has(a[href*="shield/roles"]) {
        display: none !important;
        visibility: hidden !important;
    }
</style>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php /**PATH C:\Users\Mauri Cruz\Documents\Proyectos\Filament\resources\views/filament/shield-script.blade.php ENDPATH**/ ?>