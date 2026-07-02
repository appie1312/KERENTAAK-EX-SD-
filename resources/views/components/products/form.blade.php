@props([
    'product' => null,
    'action',
    'method' => 'POST',
    'buttonText',
])

<form method="POST" action="{{ $action }}" class="row g-3">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="col-md-6">
        <x-ui.input label="Naam" name="naam" value="{{ old('naam', $product->naam ?? '') }}" :error="$errors->first('naam')" />
    </div>

    <div class="col-md-6">
        <x-ui.input label="Barcode" name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}" :error="$errors->first('barcode')" />
    </div>

    <div class="col-md-4">
        <x-ui.input label="Prijs" name="prijs" type="number" step="0.01" min="0" value="{{ old('prijs', $product->prijs ?? '') }}" :error="$errors->first('prijs')" />
    </div>

    <div class="col-md-4">
        <x-ui.input label="Voorraad" name="voorraad" type="number" min="0" value="{{ old('voorraad', $product->voorraad ?? '') }}" :error="$errors->first('voorraad')" />
    </div>

    <div class="col-md-4">
        <x-ui.input label="Houdbaarheidsdatum" name="houdbaarheidsdatum" type="date" value="{{ old('houdbaarheidsdatum', isset($product->houdbaarheidsdatum) ? \Illuminate\Support\Carbon::parse($product->houdbaarheidsdatum)->format('Y-m-d') : '') }}" :error="$errors->first('houdbaarheidsdatum')" />
    </div>

    <div class="col-12">
        <x-ui.textarea label="Omschrijving" name="omschrijving" :error="$errors->first('omschrijving')">{{ old('omschrijving', $product->omschrijving ?? '') }}</x-ui.textarea>
    </div>

    <div class="col-12">
        <x-ui.textarea label="Opmerking" name="opmerking" :error="$errors->first('opmerking')">{{ old('opmerking', $product->opmerking ?? '') }}</x-ui.textarea>
    </div>

    <div class="col-12 d-flex gap-2">
        <x-ui.button type="submit">{{ $buttonText }}</x-ui.button>
        <x-ui.button variant="secondary" href="{{ route('products.index') }}">Annuleren</x-ui.button>
    </div>
</form>
