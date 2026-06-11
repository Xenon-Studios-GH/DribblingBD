@extends('shop.layouts.shop', ['title' => 'Size Guide'])

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
    <h1 class="text-3xl font-bold text-gray-900 text-center mb-2">Size Guide</h1>
    <p class="text-sm text-gray-500 text-center mb-10">Find your perfect fit</p>

    <div class="overflow-x-auto">
        <table class="size-guide-table">
            <thead>
                <tr>
                    <th class="size-label">
                        <span class="serif text-xs text-gray-400 tracking-wider">(Inch)</span>
                    </th>
                    <th>
                        <div class="flex flex-col items-center gap-1.5">
                            <svg width="28" height="16" viewBox="0 0 28 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 8H27M27 8L21 2M27 8L21 14" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="serif text-[11px] text-gray-500 uppercase tracking-widest">Chest</span>
                        </div>
                    </th>
                    <th>
                        <div class="flex flex-col items-center gap-1.5">
                            <svg width="16" height="28" viewBox="0 0 16 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 1V27M8 27L2 21M8 27L14 21" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="serif text-[11px] text-gray-500 uppercase tracking-widest">Length</span>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="size-label">M</td>
                    <td>38</td>
                    <td>27</td>
                </tr>
                <tr>
                    <td class="size-label">L</td>
                    <td>40</td>
                    <td>28</td>
                </tr>
                <tr>
                    <td class="size-label">XL</td>
                    <td>42</td>
                    <td>29</td>
                </tr>
                <tr>
                    <td class="size-label">2XL</td>
                    <td>44</td>
                    <td>30</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@push('styles')
<style>
    .size-guide-table {
        width: 100%;
        max-width: 480px;
        margin: 0 auto;
        border-collapse: collapse;
        font-family: 'Cormorant Garamond', 'Times New Roman', serif;
    }

    .size-guide-table th,
    .size-guide-table td {
        border: 1px solid black;
        padding: 16px 24px;
        text-align: center;
        font-size: 17px;
        color: black;
        background: white;
    }

    .size-guide-table thead th {
        padding: 20px 24px 16px;
        vertical-align: bottom;
    }

    .size-guide-table .size-label {
        font-weight: 600;
        letter-spacing: 0.02em;
        color: black;
    }

    .size-guide-table tbody tr:last-child td {
        border-bottom: 1px solid black;
    }

    .serif {
        font-family: 'Cormorant Garamond', 'Times New Roman', serif;
    }

    @media (max-width: 480px) {
        .size-guide-table th,
        .size-guide-table td {
            padding: 12px 16px;
            font-size: 15px;
        }
        .size-guide-table thead th {
            padding: 16px 16px 12px;
        }
    }
</style>
@endpush
@endsection
