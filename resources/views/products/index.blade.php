<x-app-layout title="Producten">
    <main>
        <x-ui.section
            eyebrow="Producten"
            title="Product overzicht"
            description="Alle productgegevens voor eigenaar en medewerker."
        >
            <div class="d-flex justify-content-end mb-3">
                <x-ui.button href="{{ route('products.create') }}">Product toevoegen</x-ui.button>
            </div>

            <x-ui.card>
                @if (count($products) === 0)
                    <p class="mb-0 text-muted">Er zijn geen producten beschikbaar.</p>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="small text-uppercase text-muted">
                                <tr>
                                    <th>Naam</th>
                                    <th>Barcode</th>
                                    <th>Prijs</th>
                                    <th>Voorraad</th>
                                    <th>Status</th>
                                    <th>Omschrijving</th>
                                    <th class="text-end">Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td class="fw-semibold">{{ $product->naam }}</td>
                                        <td>{{ $product->barcode }}</td>
                                        <td>€ {{ number_format((float) $product->prijs, 2, ',', '.') }}</td>
                                        <td>{{ $product->voorraad }}</td>
                                        <td>{{ $product->status }}</td>
                                        <td class="text-muted">{{ $product->omschrijving }}</td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <x-ui.button size="sm" variant="secondary" href="{{ route('products.edit', $product->id) }}">Wijzigen</x-ui.button>
                                                <form method="POST" action="{{ route('products.destroy', $product->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-ui.button size="sm" variant="danger" type="submit">Verwijderen</x-ui.button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-ui.card>
        </x-ui.section>
    </main>
</x-app-layout>
