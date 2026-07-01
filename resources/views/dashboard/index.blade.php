<x-app-layout title="Dashboard">
    <header class="border-b border-line bg-surface">
        <x-ui.container>
            <div class="flex flex-col gap-4 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-brand-600">{{ ucfirst(auth()->user()->role) }} dashboard</p>
                    <h1 class="text-2xl font-semibold leading-tight text-ink sm:text-3xl">Hallo, {{ auth()->user()->name }}</h1>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <x-ui.badge variant="brand">{{ ucfirst(auth()->user()->role) }} dashboard</x-ui.badge>
                    <x-ui.button variant="secondary" href="{{ route('home') }}">Home</x-ui.button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-ui.button variant="secondary" type="submit">Uitloggen</x-ui.button>
                    </form>
                </div>
            </div>
        </x-ui.container>
    </header>

    <main>
        <x-ui.section
            eyebrow="Account"
            title="Je bent ingelogd als {{ auth()->user()->role }}"
            description="Je ziet hieronder alleen de onderdelen die passen bij jouw rol binnen Kniploket Tiko."
        >
            <div class="grid gap-4 lg:grid-cols-3">
                <x-ui.card title="Naam" description="{{ auth()->user()->name }}">
                    <x-ui.badge variant="success">Actief</x-ui.badge>
                </x-ui.card>

                <x-ui.card title="E-mail" description="{{ auth()->user()->email }}">
                    <x-ui.badge variant="brand">Geverifieerde invoer</x-ui.badge>
                </x-ui.card>

                <x-ui.card title="Laatste update" description="{{ auth()->user()->updated_at->format('d-m-Y H:i') }}">
                    <x-ui.badge variant="warning">{{ ucfirst(auth()->user()->role) }}</x-ui.badge>
                </x-ui.card>
            </div>
        </x-ui.section>

        <x-ui.section
            eyebrow="Rechten"
            title="Wat jij kunt doen"
            description="Deze modules zijn gebaseerd op de rollen uit de casus: eigenaar, medewerker en klant."
            class="pt-0"
        >
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($modules as $module)
                    <x-ui.card title="{{ $module['title'] }}" description="{{ $module['description'] }}">
                        <x-ui.badge variant="brand">{{ $module['badge'] }}</x-ui.badge>
                    </x-ui.card>
                @endforeach
            </div>
        </x-ui.section>

        @if ($reports)
            <x-ui.section
                eyebrow="Managementinformatie"
                title="Rapportages voor de eigenaar"
                description="Alleen de eigenaar ziet deze managementoverzichten."
                class="pt-0"
            >
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach ($reports as $report)
                        <x-ui.card title="{{ $report }}">
                            <x-ui.badge variant="warning">Rapportage</x-ui.badge>
                        </x-ui.card>
                    @endforeach
                </div>
            </x-ui.section>
        @endif

        @if (auth()->user()->isOwner())
            <x-ui.section
                eyebrow="Technische log"
                title="Recente acties"
                description="Deze lijst komt uit de technische logs en gebruikt een join met de users tabel."
                class="pt-0"
            >
                <x-ui.card>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[42rem] text-left text-sm">
                            <thead class="border-b border-line text-xs uppercase tracking-wide text-muted">
                                <tr>
                                    <th class="py-3 pr-4 font-semibold">Actie</th>
                                    <th class="py-3 pr-4 font-semibold">Gebruiker</th>
                                    <th class="py-3 pr-4 font-semibold">Melding</th>
                                    <th class="py-3 font-semibold">Datum</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line">
                                @forelse ($latestLogs as $log)
                                    <tr>
                                        <td class="py-3 pr-4 font-medium text-ink">{{ $log->action }}</td>
                                        <td class="py-3 pr-4 text-muted">{{ $log->user_name ?? 'Onbekend' }}</td>
                                        <td class="py-3 pr-4 text-muted">{{ $log->message }}</td>
                                        <td class="py-3 text-muted">{{ \Illuminate\Support\Carbon::parse($log->created_at)->format('d-m-Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-muted">Er zijn nog geen technische logregels.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>
            </x-ui.section>
        @endif
    </main>
</x-app-layout>
