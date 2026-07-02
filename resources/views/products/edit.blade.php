<x-app-layout title="Product wijzigen">
    <main>
        <x-ui.section
            eyebrow="Producten"
            title="Product wijzigen"
            description="Pas de productgegevens aan."
        >
            <x-ui.card>
                <x-products.form
                    :product="$product"
                    :action="route('products.update', $product->id)"
                    method="PUT"
                    button-text="Wijzigen"
                />
            </x-ui.card>
        </x-ui.section>
    </main>
</x-app-layout>
