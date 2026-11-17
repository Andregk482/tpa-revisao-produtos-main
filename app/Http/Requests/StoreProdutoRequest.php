<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'string', 'in:Eletrônicos,Vestuário,Casa,Alimentos'],
            'garantia_meses' => ['required', 'integer', 'between:0,36'],
            'preco' => ['required', 'numeric', 'gt:0', 'decimal:0,2', 'max:999999.99'],
            'estoque' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Informe o nome do produto.',
            'nome.string' => 'O nome deve ser um texto.',
            'nome.max' => 'O nome pode ter no máximo 255 caracteres.',
            'categoria.required' => 'Selecione uma categoria.',
            'categoria.in' => 'Selecione uma categoria válida.',
            'garantia_meses.required' => 'Informe a garantia em meses.',
            'garantia_meses.integer' => 'A garantia deve ser um número inteiro.',
            'garantia_meses.between' => 'A garantia deve estar entre 0 e 36 meses.',
            'preco.required' => 'Informe o preço do produto.',
            'preco.numeric' => 'O preço deve ser numérico.',
            'preco.gt' => 'O preço deve ser maior que zero.',
            'preco.decimal' => 'O preço deve ter no máximo duas casas decimais.',
            'preco.max' => 'O preço não pode ultrapassar R$ 999.999,99.',
            'estoque.required' => 'Informe a quantidade em estoque.',
            'estoque.integer' => 'O estoque deve ser um número inteiro.',
            'estoque.min' => 'O estoque não pode ser negativo.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'categoria' => 'categoria',
            'garantia_meses' => 'garantia (meses)',
            'preco' => 'preço',
            'estoque' => 'estoque',
        ];
    }
}
