<?php

namespace App\Http\Requests\condicional;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CondicionalItemStore extends FormRequest
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
        $condicionalId = $this->route('id'); // pega {id} da rota /condicionais/{id}/itens

        return [
            'produto_id' => [
                'required',
                Rule::exists('produtos', 'id'),
                Rule::unique('condicional_itens')->where(function ($query) use ($condicionalId) {
                    return $query->where('condicional_id', $condicionalId);
                }),
            ],
            'quantidade_entregue' => 'required|integer|min:1',
            'quantidade_devolvida' => 'nullable|integer|lte:quantidade_entregue',
            'quantidade_vendida' => 'nullable|integer|lte:quantidade_entregue',
        ];
    }


    public function messages()
    {
        return [
            'produto_id.required' => 'O campo produto_id é obrigatório.',
            'produto_id.unique' => 'Este produto já foi adicionado a esta condicional.',
            'produto_id.exists' => 'O produto especificado não existe.',

            'quantidade_entregue.required' => 'O campo quantidade_entregue é obrigatório.',
            'quantidade_entregue.integer' => 'O campo quantidade_entregue deve ser um número inteiro.',
            'quantidade_entregue.min' => 'O campo quantidade_entregue deve ser no mínimo 1.',

            'quantidade_devolvida.integer' => 'O campo quantidade_devolvida deve ser um número inteiro.',
            'quantidade_devolvida.lte' => 'O campo quantidade_devolvida deve ser menor ou igual a quantidade_entregue.',
            'quantidade_devolvida.min' => 'O campo quantidade_devolvida deve ser no mínimo 0',

            'quantidade_vendida.integer' => 'O campo quantidade_vendida deve ser um número inteiro.',
            'quantidade_vendida.lte' => 'O campo quantidade_vendida deve ser menor ou igual a quantidade_entregue.',
            'quantidade_entregue.min' => 'O campo quantidade_entregue deve ser no mínimo 0'
        ];
    }
}
