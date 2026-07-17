<?php

namespace App\Http\Controllers;

class PolicyModalController extends Controller
{
    private const ALLOWED_TYPES = [
        'privacy-policy',
        'terms-of-service',
        'return-refund-policy',
        'delivery-policy',
    ];

    public function show(string $type)
    {
        abort_unless(in_array($type, self::ALLOWED_TYPES, true), 404);

        return view("layouts.components.{$type}");
    }
}
