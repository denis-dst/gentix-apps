<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Name -->
    <div class="col-span-1 md:col-span-2">
        <label for="name" class="block font-medium text-sm text-gray-700 mb-1">Organizer Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" id="name" value="{{ old('name', $tenant->name ?? '') }}" 
               class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm text-gray-900" 
               required placeholder="e.g. Gentix Organization">
        @error('name')
            <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Email -->
    <div>
        <label for="email" class="block font-medium text-sm text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
        <input type="email" name="email" id="email" value="{{ old('email', $tenant->email ?? '') }}" 
               class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm text-gray-900" 
               required placeholder="contact@organizer.com">
        @error('email')
            <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Phone -->
    <div>
        <label for="phone" class="block font-medium text-sm text-gray-700 mb-1">Phone Number</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $tenant->phone ?? '') }}" 
               class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm text-gray-900" 
               placeholder="+62 812 3456 7890">
        @error('phone')
            <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Status -->
    <div class="col-span-1 md:col-span-2">
        <label for="status" class="block font-medium text-sm text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
        <select name="status" id="status" class="w-full md:w-1/2 border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm text-gray-900 font-medium" required>
            <option value="active" {{ old('status', $tenant->status ?? '') === 'active' ? 'selected' : '' }}>🟢 Active</option>
            <option value="inactive" {{ old('status', $tenant->status ?? '') === 'inactive' ? 'selected' : '' }}>⚪ Inactive</option>
            <option value="suspended" {{ old('status', $tenant->status ?? '') === 'suspended' ? 'selected' : '' }}>🔴 Suspended</option>
        </select>
        <p class="mt-1 text-xs text-gray-500">Only active organizers can create and manage events.</p>
        @error('status')
            <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>

    <!-- Address -->
    <div class="col-span-1 md:col-span-2">
        <label for="address" class="block font-medium text-sm text-gray-700 mb-1">Address</label>
        <textarea name="address" id="address" rows="3" 
                  class="w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg shadow-sm text-gray-900" 
                  placeholder="Full office or operating address...">{{ old('address', $tenant->address ?? '') }}</textarea>
        @error('address')
            <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
        @enderror
    </div>
</div>
