@php
    $user = auth()->user();
    $canViewRoles = $user && (
        $user->can('view_any_role') || 
        $user->hasRole('super_admin') || 
        $user->hasRole('Administrador')
    );
@endphp

@if(!$canViewRoles)
<style>
    a[href*="shield/roles"],
    [data-label="Filament Shield"],
    [data-label="Roles"],
    li:has(a[href*="shield/roles"]) {
        display: none !important;
        visibility: hidden !important;
    }
</style>
@endif