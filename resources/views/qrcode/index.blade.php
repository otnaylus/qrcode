<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator - SCCR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .color-wrapper {
            position: relative;
            display: inline-block;
        }
        .color-wrapper input[type="color"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .color-preview {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="mb-8 flex items-center justify-between gap-4">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-24 w-24 object-contain">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">QR Code Generator</h1>
                <p class="text-gray-600">Generate QR Code dengan kustomisasi warna dan logo</p>
            </div>
            <img src="{{ asset('images/qrcode.png') }}" alt="QR Sample" class="h-24 w-24 object-contain">
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Form Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Input Data</h2>
                
                <form action="{{ route('qrcode.generate') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Data QR Code <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="data" 
                            rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('data') border-red-500 @enderror"
                            placeholder="Masukkan URL atau data yang akan dijadikan QR Code..."
                            required>{{ old('data') }}</textarea>
                        @error('data')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Lebar (px) <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="width" 
                                value="{{ old('width', 400) }}"
                                min="100" 
                                max="1000"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('width') border-red-500 @enderror"
                                required>
                            @error('width')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tinggi (px) <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="height" 
                                value="{{ old('height', 400) }}"
                                min="100" 
                                max="1000"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('height') border-red-500 @enderror"
                                required>
                            @error('height')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Color Pickers -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Finder Patterns (3 Kotak Pojok - Luar)</label>
                        <div class="flex items-center gap-3">
                            <div class="color-wrapper">
                                <div class="color-preview" id="finderPreview" style="background-color: #000000;"></div>
                                <input 
                                    type="color" 
                                    name="finder_color" 
                                    value="{{ old('finder_color', '#000000') }}"
                                    onchange="document.getElementById('finderPreview').style.backgroundColor = this.value; document.getElementById('finderHex').value = this.value;"
                                >
                            </div>
                            <input 
                                type="text" 
                                id="finderHex"
                                value="{{ old('finder_color', '#000000') }}"
                                class="w-28 px-2 py-1 border border-gray-300 rounded text-sm font-mono"
                                readonly
                            >
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Finder Patterns (3 Kotak Pojok - Dalam)</label>
                        <div class="flex items-center gap-3">
                            <div class="color-wrapper">
                                <div class="color-preview" id="finderInnerPreview" style="background-color: #000000;"></div>
                                <input 
                                    type="color" 
                                    name="finder_inner_color" 
                                    value="{{ old('finder_inner_color', '#000000') }}"
                                    onchange="document.getElementById('finderInnerPreview').style.backgroundColor = this.value; document.getElementById('finderInnerHex').value = this.value;"
                                >
                            </div>
                            <input 
                                type="text" 
                                id="finderInnerHex"
                                value="{{ old('finder_inner_color', '#000000') }}"
                                class="w-28 px-2 py-1 border border-gray-300 rounded text-sm font-mono"
                                readonly
                            >
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Data Modules (Kotak Dalam)</label>
                        <div class="flex items-center gap-3">
                            <div class="color-wrapper">
                                <div class="color-preview" id="dataPreview" style="background-color: #000000;"></div>
                                <input 
                                    type="color" 
                                    name="data_color" 
                                    value="{{ old('data_color', '#000000') }}"
                                    onchange="document.getElementById('dataPreview').style.backgroundColor = this.value; document.getElementById('dataHex').value = this.value;"
                                >
                            </div>
                            <input 
                                type="text" 
                                id="dataHex"
                                value="{{ old('data_color', '#000000') }}"
                                class="w-28 px-2 py-1 border border-gray-300 rounded text-sm font-mono"
                                readonly
                            >
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Background</label>
                        <div class="flex items-center gap-3">
                            <div class="color-wrapper">
                                <div class="color-preview" id="bgPreview" style="background-color: #FFFFFF;"></div>
                                <input 
                                    type="color" 
                                    name="bg_color" 
                                    value="{{ old('bg_color', '#FFFFFF') }}"
                                    onchange="document.getElementById('bgPreview').style.backgroundColor = this.value; document.getElementById('bgHex').value = this.value;"
                                >
                            </div>
                            <input 
                                type="text" 
                                id="bgHex"
                                value="{{ old('bg_color', '#FFFFFF') }}"
                                class="w-28 px-2 py-1 border border-gray-300 rounded text-sm font-mono"
                                readonly
                            >
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Logo (Opsional)
                        </label>
                        <input 
                            type="file" 
                            name="logo" 
                            accept="image/png,image/jpg,image/jpeg"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('logo') border-red-500 @enderror"
                        >
                        <p class="text-sm text-gray-500 mt-1">Format: PNG, JPG, JPEG. Maksimal 2MB. Logo akan ditempatkan di tengah QR Code.</p>
                        @error('logo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 font-medium"
                    >
                        Generate QR Code
                    </button>
                </form>
            </div>

            <!-- Result Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Hasil QR Code</h2>
                
                @if(session('qr_code_url'))
                    <div class="text-center">
                        <img 
                            src="{{ session('qr_code_url') }}" 
                            alt="Generated QR Code"
                            class="mx-auto mb-4 border border-gray-200 rounded-lg"
                            style="max-width: 100%; height: auto;"
                        >
                        
                        <div class="flex gap-2 justify-center">
                            <a 
                                href="{{ session('qr_code_url') }}" 
                                target="_blank"
                                class="bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 transition duration-200"
                            >
                                Lihat Full Size
                            </a>
                            
                            @if(session('qr_code_id'))
                                <a 
                                    href="{{ route('qrcode.download', session('qr_code_id')) }}"
                                    class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition duration-200"
                                >
                                    Download PNG
                                </a>
                            @endif
                        </div>

                        <div class="mt-4 p-3 bg-gray-100 rounded-md">
                            <p class="text-sm text-gray-600 break-all">
                                <span class="font-medium">File:</span> {{ session('qr_code_url') }}
                            </p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-400">
                        <svg class="mx-auto h-24 w-24 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <p>QR Code akan muncul di sini setelah digenerate</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- History Section -->
        @if($qrCodes->count() > 0)
            <div class="mt-8 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Riwayat Generate</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dimensi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warna</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($qrCodes as $qr)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $qr->id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate" title="{{ $qr->data }}">
                                        {{ $qr->data }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $qr->width }} x {{ $qr->height }} px
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <div class="w-4 h-4 rounded border" style="background-color: {{ $qr->finder_color }}" title="Finder Luar"></div>
                                            <div class="w-4 h-4 rounded border" style="background-color: {{ $qr->finder_inner_color }}" title="Finder Dalam"></div>
                                            <div class="w-4 h-4 rounded border" style="background-color: {{ $qr->data_color }}" title="Data"></div>
                                            <div class="w-4 h-4 rounded border" style="background-color: {{ $qr->bg_color }}" title="BG"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($qr->logo_path)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Ya
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Tidak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <a href="{{ route('qrcode.download', $qr->id) }}" class="text-blue-600 hover:text-blue-800">Download</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200 text-center">
            <div class="flex flex-wrap items-center justify-center gap-3 text-xs">
                <a href="https://pindahkedigital.com" target="_blank" class="flex items-center gap-2 px-3 py-1.5 bg-cyan-50 border border-cyan-200 rounded-lg text-cyan-700 hover:bg-cyan-100 hover:border-cyan-300 transition-all duration-300">
                    <svg class="w-3.5 h-3.5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    <span class="font-medium">pindahkedigital.com</span>
                </a>

                <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-200 rounded-lg text-blue-700">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="font-medium">Dwi Sulyanto</span>
                </div>

                <a href="mailto:otnaylus@gmail.com" class="flex items-center gap-2 px-3 py-1.5 bg-purple-50 border border-purple-200 rounded-lg text-purple-700 hover:bg-purple-100 hover:border-purple-300 transition-all duration-300">
                    <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-medium">otnaylus@gmail.com</span>
                </a>

                <a href="https://wa.me/6281929295060" target="_blank" class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-700 hover:bg-emerald-100 hover:border-emerald-300 transition-all duration-300">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span class="font-medium">+6281-92929-5060</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
