<?php

namespace App\Http\Requests\Vendas;

use Illuminate\Foundation\Http\FormRequest;

class VendasStoreRequest extends FormRequest
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
            'representante_id' => ['required', 'integer', 'exists:representantes,id'],
            'cliente_id'       => ['required', 'integer', 'exists:clientes,id'],
            'condicional_id'   => ['required', 'integer', 'exists:condicionais,id'],
            'data_venda'      => ['required', 'date'],
            'forma_pagamento'  => ['required', 'string', 'in:dinheiro,cartao,pix,outro'],
        ];
    }

    public function messages(): array
    {
        return [
            'representante_id.required' => 'O ID do representante é obrigatório.',
            'representante_id.integer'  => 'O ID do representante deve ser um número inteiro.',
            'representante_id.exists'   => 'O representante informado não existe.',
            'cliente_id.required'       => 'O ID do cliente é obrigatório.',
            'cliente_id.integer'        => 'O ID do cliente deve ser um número inteiro.',
            'cliente_id.exists'         => 'O cliente informado não existe.',
            'condicional_id.required'   => 'O ID da condicional é obrigatório.',
            'condicional_id.integer'    => 'O ID da condicional deve ser um número inteiro.',
            'condicional_id.exists'     => 'A condicional informada não existe.',
            'data_venda.required'      => 'A data da venda é obrigatória.',
            'data_venda.date'          => 'A data da venda deve ser uma data válida.',
            'valor_total.required'     => 'O valor total é obrigatório.',
            'valor_total.numeric'      => 'O valor total deve ser um número.',
            'valor_total.min'          => 'O valor total deve ser maior ou igual a zero.',
            'forma_pagamento.required'  => 'A forma de pagamento é obrigatória.',
            'forma_pagamento.in'        => 'A forma de pagamento deve ser uma das seguintes: dinheiro, cartao, pix, outro.',
        ];
    }
}
