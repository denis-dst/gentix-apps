<x-app-layout>
    <x-slot name="title">Tenant Trash</x-slot>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 shadow-sm" role="alert">
        <p class="font-bold">Success</p>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-sm border-t-4 border-red-600 shadow-md overflow-hidden">
        <div class="px-4 py-3 border-b border-[#dee2e6] flex justify-between items-center bg-white">
            <h3 class="text-lg font-medium">Deleted Organizers</h3>
            <a href="{{ route('superadmin.tenants.index') }}" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded shadow-sm transition">
                <i class="fas fa-arrow-left mr-1"></i> Back to Active
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#f8f9fa] border-b border-[#dee2e6] text-[#495057] font-bold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Organizer</th>
                        <th class="px-4 py-3">Deleted At</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#dee2e6]">
                    @forelse($tenants as $tenant)
                    <tr class="hover:bg-[#f2f2f2] transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-red-50 border border-red-100 flex items-center justify-center font-bold text-red-600 text-xs uppercase">
                                    {{ substr($tenant->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-[#343a40]">{{ $tenant->name }}</div>
                                    <div class="text-xs text-[#6c757d]">{{ $tenant->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-[#6c757d]">{{ $tenant->deleted_at->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <form action="{{ route('superadmin.tenants.restore', $tenant->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded shadow-sm transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                        Restore
                                    </button>
                                </form>
                                <form action="{{ route('superadmin.tenants.force-delete', $tenant->id) }}" method="POST" onsubmit="return confirm('PERMANENTLY delete this organizer? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded shadow-sm transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Force Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-12 text-center text-[#6c757d] italic">Trash is empty.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($tenants->hasPages())
        <div class="px-4 py-3 border-t border-[#dee2e6] bg-[#f8f9fa]">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
