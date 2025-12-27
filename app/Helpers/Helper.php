<?php

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;

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

if (!function_exists('transMsg')) {
    function transMsg($key, $request = null)
    {
        $lang = $request?->header('Accept-Language') ?? 'ar';

        $key = strtolower($key);

        $cleanKey = str_replace('_', ' ', $key);

        if (Lang::has("messages.$key", $lang)) {
            return __("messages.$key", [], $lang);
        }

        return $cleanKey;
    }
}



if (! function_exists('full_url')) {
    function full_url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        // لو already URL (http / https)
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // public disk
        return url(Storage::url($path));
    }
}
