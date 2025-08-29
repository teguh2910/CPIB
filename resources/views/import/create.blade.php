@extends('layouts.app')

@section('title', 'Create Import Notification')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-800">Create Import Notification</h1>
                    <p class="text-gray-600 mt-1">Fill in the details for the new import notification</p>
                </div>

                <form action="{{ route('import.store') }}" method="POST" class="p-6">
                    @csrf

                    <!-- Tab Navigation -->
                    <div class="mb-6">
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button type="button" id="tab-basic"
                                    class="tab-button active whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                                    data-tab="basic">
                                    Basic Information
                                </button>
                                <button type="button" id="tab-details"
                                    class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                                    data-tab="details">
                                    Import Details
                                </button>
                                <button type="button" id="tab-documents"
                                    class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                                    data-tab="documents">
                                    Documents
                                </button>
                                <button type="button" id="tab-additional"
                                    class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                                    data-tab="additional">
                                    Additional Info
                                </button>
                            </nav>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Basic Information Tab -->
                        <div id="basic-content" class="tab-pane active">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="notification_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Notification Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="notification_number" name="notification_number"
                                        value="{{ old('notification_number') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notification_number') border-red-500 @enderror"
                                        required>
                                    @error('notification_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="import_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Import Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="import_date" name="import_date"
                                        value="{{ old('import_date') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('import_date') border-red-500 @enderror"
                                        required>
                                    @error('import_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="importer_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Importer Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="importer_name" name="importer_name"
                                        value="{{ old('importer_name') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('importer_name') border-red-500 @enderror"
                                        required>
                                    @error('importer_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="importer_address" class="block text-sm font-medium text-gray-700 mb-2">
                                        Importer Address
                                    </label>
                                    <textarea id="importer_address" name="importer_address" rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('importer_address') border-red-500 @enderror">{{ old('importer_address') }}</textarea>
                                    @error('importer_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="exporter_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Exporter Name
                                    </label>
                                    <input type="text" id="exporter_name" name="exporter_name"
                                        value="{{ old('exporter_name') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('exporter_name') border-red-500 @enderror">
                                    @error('exporter_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="exporter_address" class="block text-sm font-medium text-gray-700 mb-2">
                                        Exporter Address
                                    </label>
                                    <textarea id="exporter_address" name="exporter_address" rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('exporter_address') border-red-500 @enderror">{{ old('exporter_address') }}</textarea>
                                    @error('exporter_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Import Details Tab -->
                        <div id="details-content" class="tab-pane hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="product_description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Product Description <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="product_description" name="product_description" rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('product_description') border-red-500 @enderror"
                                        required>{{ old('product_description') }}</textarea>
                                    @error('product_description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="hs_code" class="block text-sm font-medium text-gray-700 mb-2">
                                        HS Code
                                    </label>
                                    <input type="text" id="hs_code" name="hs_code" value="{{ old('hs_code') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('hs_code') border-red-500 @enderror">
                                    @error('hs_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                        Quantity
                                    </label>
                                    <input type="number" id="quantity" name="quantity" step="0.01"
                                        value="{{ old('quantity') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('quantity') border-red-500 @enderror">
                                    @error('quantity')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                                        Unit
                                    </label>
                                    <select id="unit" name="unit"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('unit') border-red-500 @enderror">
                                        <option value="">Select Unit</option>
                                        <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilogram
                                        </option>
                                        <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pieces
                                        </option>
                                        <option value="ton" {{ old('unit') == 'ton' ? 'selected' : '' }}>Ton</option>
                                        <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter
                                        </option>
                                        <option value="m3" {{ old('unit') == 'm3' ? 'selected' : '' }}>Cubic Meter
                                        </option>
                                    </select>
                                    @error('unit')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="value_usd" class="block text-sm font-medium text-gray-700 mb-2">
                                        Value (USD)
                                    </label>
                                    <input type="number" id="value_usd" name="value_usd" step="0.01"
                                        value="{{ old('value_usd') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('value_usd') border-red-500 @enderror">
                                    @error('value_usd')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="origin_country" class="block text-sm font-medium text-gray-700 mb-2">
                                        Origin Country
                                    </label>
                                    <input type="text" id="origin_country" name="origin_country"
                                        value="{{ old('origin_country') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('origin_country') border-red-500 @enderror">
                                    @error('origin_country')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Documents Tab -->
                        <div id="documents-content" class="tab-pane hidden">
                            <div class="space-y-6">
                                <div>
                                    <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Invoice Number
                                    </label>
                                    <input type="text" id="invoice_number" name="invoice_number"
                                        value="{{ old('invoice_number') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('invoice_number') border-red-500 @enderror">
                                    @error('invoice_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="packing_list_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Packing List Number
                                    </label>
                                    <input type="text" id="packing_list_number" name="packing_list_number"
                                        value="{{ old('packing_list_number') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('packing_list_number') border-red-500 @enderror">
                                    @error('packing_list_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="bill_of_lading_number"
                                        class="block text-sm font-medium text-gray-700 mb-2">
                                        Bill of Lading Number
                                    </label>
                                    <input type="text" id="bill_of_lading_number" name="bill_of_lading_number"
                                        value="{{ old('bill_of_lading_number') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bill_of_lading_number') border-red-500 @enderror">
                                    @error('bill_of_lading_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="container_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Container Number
                                    </label>
                                    <input type="text" id="container_number" name="container_number"
                                        value="{{ old('container_number') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('container_number') border-red-500 @enderror">
                                    @error('container_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info Tab -->
                        <div id="additional-content" class="tab-pane hidden">
                            <div class="space-y-6">
                                <div>
                                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                                        Remarks
                                    </label>
                                    <textarea id="remarks" name="remarks" rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('remarks') border-red-500 @enderror">{{ old('remarks') }}</textarea>
                                    @error('remarks')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status
                                    </label>
                                    <select id="status" name="status"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>
                                            Draft</option>
                                        <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>
                                            Submitted</option>
                                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>
                                            Approved</option>
                                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>
                                            Rejected</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
                        <a href="{{ route('import.index') }}"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-200">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                            Create Import Notification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');

                    // Remove active class from all buttons and panes
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                        btn.classList.add('border-transparent', 'text-gray-500',
                            'hover:text-gray-700', 'hover:border-gray-300');
                    });

                    tabPanes.forEach(pane => {
                        pane.classList.add('hidden');
                        pane.classList.remove('active');
                    });

                    // Add active class to clicked button and corresponding pane
                    this.classList.add('active', 'border-blue-500', 'text-blue-600');
                    this.classList.remove('border-transparent', 'text-gray-500',
                        'hover:text-gray-700', 'hover:border-gray-300');

                    const targetPane = document.getElementById(targetTab + '-content');
                    if (targetPane) {
                        targetPane.classList.remove('hidden');
                        targetPane.classList.add('active');
                    }
                });
            });
        });
    </script>

    <style>
        .tab-button.active {
            border-color: #3b82f6;
            color: #2563eb;
        }

        .tab-button {
            border-color: transparent;
            color: #6b7280;
        }

        .tab-button:hover {
            color: #374151;
            border-color: #d1d5db;
        }
    </style>
@endsection
