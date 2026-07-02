<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $this->authorizeProductManagement();

        $products = DB::select('CALL sp_producten_overzicht()');

        return view('products.index', ['products' => $products]);
    }

    public function create(): View
    {
        $this->authorizeProductManagement();

        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeProductManagement();

        $validated = $request->validate($this->rules());
        $result = DB::selectOne(
            'CALL sp_product_toevoegen(?, ?, ?, ?, ?, ?, ?)',
            $this->procedureData($validated),
        );

        if (! $result?->gelukt) {
            return back()->withInput()->with('error', 'Product is niet toegevoegd.');
        }

        return redirect()
            ->route('products.index')
            ->with('status', 'Product is toegevoegd.');
    }

    public function edit(int $product): View
    {
        $this->authorizeProductManagement();

        $product = DB::selectOne('CALL sp_product_zoeken(?)', [$product]);
        abort_unless($product, 404);

        return view('products.edit', ['product' => $product]);
    }

    public function update(Request $request, int $product): RedirectResponse
    {
        $this->authorizeProductManagement();

        $validated = $request->validate($this->rules());
        $result = DB::selectOne(
            'CALL sp_product_wijzigen(?, ?, ?, ?, ?, ?, ?, ?)',
            [$product, ...$this->procedureData($validated)],
        );

        if (! $result?->gelukt) {
            return back()->withInput()->with('error', 'Product is niet gewijzigd.');
        }

        return redirect()
            ->route('products.index')
            ->with('status', 'Product is gewijzigd.');
    }

    public function destroy(int $product): RedirectResponse
    {
        $this->authorizeProductManagement();
        $result = DB::selectOne('CALL sp_product_verwijderen(?)', [$product]);

        return back()->with($result?->gelukt ? 'status' : 'error', $result?->gelukt ? 'Product is verwijderd.' : 'Product was al verwijderd.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'naam' => ['required', 'string', 'max:150'],
            'barcode' => ['required', 'string', 'max:20'],
            'prijs' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'voorraad' => ['required', 'integer', 'min:0'],
            'houdbaarheidsdatum' => ['nullable', 'date'],
            'omschrijving' => ['nullable', 'string', 'max:255'],
            'opmerking' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<int, mixed>
     */
    private function procedureData(array $validated): array
    {
        return [
            $validated['naam'],
            $validated['barcode'],
            $validated['prijs'],
            $validated['voorraad'],
            $validated['houdbaarheidsdatum'] ?? null,
            $validated['omschrijving'] ?? null,
            $validated['opmerking'] ?? null,
        ];
    }

    private function authorizeProductManagement(): void
    {
        /** @var User|null $user */
        $user = auth()->user();

        abort_unless($user?->isOwner() || $user?->isEmployee(), 403);
    }
}
