<?php

declare(strict_types=1);

namespace Mostafax\AnalyticsSuite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string|max:1000',
            'layout'           => 'nullable|array',
            'settings'         => 'nullable|array',
            'is_public'        => 'nullable|boolean',
            'is_default'       => 'nullable|boolean',
            'permissions'      => 'nullable|array',
        ];
    }
}
