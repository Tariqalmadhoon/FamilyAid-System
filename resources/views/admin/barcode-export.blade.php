<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.barcode_export.title') }}
        </h2>
    </x-slot>

    <style>
        .a4-sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            border: 2px solid #0f766e;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
            position: relative;
            padding: 18mm 14mm 14mm;
            box-sizing: border-box;
        }

        .a4-sheet .corner {
            position: absolute;
            width: 26px;
            height: 26px;
            border: 3px solid #14b8a6;
        }

        .corner.top-right {
            top: 10px;
            right: 10px;
            border-left: 0;
            border-bottom: 0;
        }

        .corner.bottom-left {
            bottom: 10px;
            left: 10px;
            border-right: 0;
            border-top: 0;
        }

        .qr-box {
            max-width: 520px;
            margin: 14px auto 0;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 14px;
            background: #f8fafc;
        }

        .qr-box img {
            width: 100%;
            height: auto;
            display: block;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        @media print {
            nav,
            header,
            #toast-container,
            .no-print {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            main {
                animation: none !important;
            }

            .a4-wrap {
                padding: 0 !important;
            }

            .a4-sheet {
                width: 100%;
                min-height: 277mm;
                margin: 0;
                border: 2px solid #0f766e;
                box-shadow: none;
                page-break-after: always;
            }
        }
    </style>

    <div class="py-8 a4-wrap">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 no-print flex justify-end">
                <button
                    type="button"
                    onclick="window.print()"
                    class="px-4 py-2 bg-teal-600 text-white rounded-md text-sm font-medium hover:bg-teal-700"
                >
                    {{ __('messages.barcode_export.print') }}
                </button>
            </div>

            <div class="a4-sheet">
                <span class="corner top-right"></span>
                <span class="corner bottom-left"></span>

                <div class="text-center">
                    <h1 class="text-3xl font-bold text-slate-900 leading-relaxed">
                        {{ __('messages.barcode_export.registration_title') }}
                    </h1>
                    <p class="mt-3 text-lg text-slate-700 font-medium">
                        {{ __('messages.barcode_export.scan_instruction') }}
                    </p>
                </div>

                <div class="qr-box">
                    <img src="{{ $qrImageUrl }}" alt="QR Code">
                </div>

                <div class="text-center mt-6">
                    <p class="text-base text-slate-700">{{ __('messages.barcode_export.or_visit') }}</p>
                    <a href="{{ $registrationUrl }}" target="_blank" class="mt-2 inline-block text-teal-700 text-lg font-semibold underline" dir="ltr">
                        {{ $registrationUrl }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

