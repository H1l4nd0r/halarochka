<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Разобрать количество записей на страницу: URL-параметр > кука > дефолт, clamp 1..100.
     */
    protected function perPage(Request $request, int $default = 15): int
    {
        $perPage = (int) ($request->input('per_page') ?? $request->cookie('per_page') ?? $default);
        return max(1, min($perPage, 100));
    }
}
