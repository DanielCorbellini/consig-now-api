<?php

namespace App\Http\Requests\Vendas;

use Illuminate\Foundation\Http\FormRequest;

class VendasIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'representante_id' => ['nullable', 'integer', 'exists:representantes,id'],
            'cliente_id'       => ['nullable', 'integer', 'exists:clientes,id'],
            'condicional_id'   => ['nullable', 'integer', 'exists:condicionais,id'],
            'data_venda'      => ['nullable', 'date'],
            'valor_total'     => ['nullable', 'numeric', 'min:0'],
            'forma_pagamento'  => ['nullable', 'string', 'in:dinheiro,cartao,pix,outro'],
        ];
    }

    public function messages(): array
    {
        return [
            'representante_id.exists' => 'O representante especificado não existe.',
            'cliente_id.exists'       => 'O cliente especificado não existe.',
            'status.in'               => 'O status deve ser um dos seguintes valores: aberta, fechada, cancelada.',
            'data_final.after_or_equal' => 'A data final deve ser uma data posterior ou igual à data inicial.',
            'valor_maximo.gte'        => 'O valor máximo deve ser maior ou igual ao valor mínimo.',
        ];
    }
}
