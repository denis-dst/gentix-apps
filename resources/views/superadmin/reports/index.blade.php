<x-app-layout>
    <x-slot name="title">Laporan Global</x-slot>
    @include('reports.operational', ['scope' => 'superadmin'])
</x-app-layout>
