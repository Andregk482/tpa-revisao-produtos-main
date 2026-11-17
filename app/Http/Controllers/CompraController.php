<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Venda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CompraController extends Controller
{
    public function index()
    {
        $vendas = Venda::with('produtos')
            ->where('cliente_id', Auth::id())
            ->latest()
            ->get();

        return view('compras.index', compact('vendas'));
    }

    public function create()
    {
        $produtos = Produto::orderBy('nome')->get();

        return view('compras.create', compact('produtos'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'itens' => ['required', 'array', 'min:1'],
            'itens.*' => ['required', 'integer', 'min:0'],
        ], [
            'itens.required' => 'Informe as quantidades dos produtos.',
            'itens.array' => 'Os itens da compra são inválidos.',
            'itens.min' => 'Selecione pelo menos um produto.',
            'itens.*.integer' => 'As quantidades devem ser números inteiros.',
            'itens.*.min' => 'As quantidades não podem ser negativas.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $itens = $request->input('itens', []);

            if (is_array($itens)) {
                foreach (array_keys($itens) as $produtoId) {
                    if (filter_var((string) $produtoId, FILTER_VALIDATE_INT) === false || (int) $produtoId < 1) {
                        $validator->errors()->add('itens', 'Um dos produtos selecionados é inválido.');
                        break;
                    }
                }
            }
        });

        $dados = $validator->validate();

        // Quantidades iguais a zero não fazem parte da compra.
        $quantidades = collect($dados['itens'])
            ->mapWithKeys(fn ($quantidade, $produtoId) => [(int) $produtoId => (int) $quantidade])
            ->filter(fn ($quantidade) => $quantidade > 0);

        if ($quantidades->isEmpty()) {
            throw ValidationException::withMessages([
                'itens' => 'Informe uma quantidade maior que zero para pelo menos um produto.',
            ]);
        }

        DB::transaction(function () use ($quantidades) {
            // O bloqueio e a conferência acontecem antes da criação da venda.
            // Assim, uma compra sem estoque suficiente não deixa registros parciais.
            $produtos = Produto::whereIn('id', $quantidades->keys()->all())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($produtos->count() !== $quantidades->count()) {
                throw ValidationException::withMessages([
                    'itens' => 'Um ou mais produtos selecionados não existem mais.',
                ]);
            }

            foreach ($quantidades as $produtoId => $quantidade) {
                $produto = $produtos->get($produtoId);

                if ($quantidade > $produto->estoque) {
                    throw ValidationException::withMessages([
                        'itens' => "Estoque insuficiente para {$produto->nome}. Disponível: {$produto->estoque}.",
                    ]);
                }
            }

            $venda = Venda::create([
                'cliente_id' => Auth::id(),
            ]);

            foreach ($quantidades as $produtoId => $quantidade) {
                $produto = $produtos->get($produtoId);

                $venda->produtos()->attach($produto->id, [
                    'quantidade' => $quantidade,
                    'preco_unitario' => $produto->preco,
                ]);

                // Bônus: baixa o estoque depois de registrar o preço e a quantidade vendidos.
                $produto->decrement('estoque', $quantidade);
            }
        });

        return redirect()
            ->route('compras.index')
            ->with('sucesso', 'Compra realizada!');
    }
}
