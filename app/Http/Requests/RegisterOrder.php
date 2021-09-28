<?php

namespace App\Http\Requests;

use App\Services\Sber\SberRegisterParams;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterOrder extends FormRequest {

    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'customer.name' => 'required|string',
            'customer.second_name' => 'required|string',
            'customer.patronymic' => 'required|string',
            'customer.phone' => 'required|regex:/^\d{11}$/',
            'customer.email' => 'required|email',
            'customer.passport_serial' => 'required|numeric',
            'customer.passport_num' => 'required|numeric',
            'customer.passport_date' => 'required|date',
            'product.uuid' => 'required|uuid',
            'product.title' => 'required|string',
            'product.vin' => 'required|string',
            'product.price' => 'required|integer',
            'settings.returnUrl' => 'required|url',
            'settings.failUrl' => 'required|url',
            'settings.paymentType' => [
                'required',
                Rule::in(SberRegisterParams::PAYMENT_TYPES)
            ],
        ];
    }
}
