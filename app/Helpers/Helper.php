<?php

if (! function_exists('translate_permission')) {
    function translate_permission($permissionName)
    {
        $parts = explode('_', $permissionName);

        if (count($parts) < 2) {
            return $permissionName;
        }

        [$module, $action] = $parts;

        $modules = [
            'users' => 'المستخدمين',
            'roles' => 'الأدوار',
        ];

        $actions = [
            'view' => 'عرض',
            'create' => 'إضافة',
            'edit' => 'تعديل',
            'delete' => 'حذف',
        ];

        $moduleLabel = $modules[$module] ?? $module;
        $actionLabel = $actions[$action] ?? $action;

        return "{$actionLabel} {$moduleLabel}";
    }
}


if (! function_exists('lang')) {
    function lang(?string $ar, ?string $en, \Illuminate\Http\Request $request): string
    {
        $ar ??= '';
        $en ??= '';
        $locale = $request->header('Accept-Language', 'en');
        return strtolower($locale) === 'ar' ? $ar : $en;
    }
}

