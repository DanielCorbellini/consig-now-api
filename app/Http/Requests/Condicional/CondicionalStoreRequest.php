<?php

namespace App\Http\Requests\Condicional;

use Illuminate\Foundation\Http\FormRequest;

class CondicionalStoreRequest extends FormRequest
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
            'representante_id' => 'required|integer|exists:representantes,id',
            'data_entrega' => 'required|date',
            'data_prevista_retorno' => 'required|date|after_or_equal:data_entrega',
            'status' => 'required|in:aberta,finalizada,em_cobranca',
            'almoxarifado_id' => 'required|integer|exists:almoxarifados,id',
        ];
    }

    public function messages(): array
    {
        return [
            'representante_id.required' => 'O ID do representante é obrigatório.',
            'representante_id.integer' => 'O ID do representante deve ser um número inteiro.',
            'representante_id.exists' => 'O representante informado não existe.',

            'data_entrega.required' => 'A data de entrega é obrigatória.',
            'data_entrega.date' => 'A data de entrega deve ser uma data válida.',

            'data_prevista_retorno.required' => 'A data prevista de retorno é obrigatória.',
            'data_prevista_retorno.date' => 'A data prevista de retorno deve ser uma data válida.',
            'data_prevista_retorno.after_or_equal' => 'A data prevista de retorno deve ser igual ou posterior à data de entrega.',

            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status deve ser um dos seguintes: aberta, finalizada, em_cobranca.',
        ];
    }
}
