<x-app-layout title="Product toevoegen">
    <main>
        <x-ui.section
            eyebrow="Producten"
            title="Product toevoegen"
            description="Registreer een nieuw product in het systeem."
        >
            <x-ui.card>
                <x-products.form
                    :action="route('products.store')"
                    button-text="Toevoegen"
                />
            </x-ui.card>
        </x-ui.section>
    </main>
</x-app-layout>
