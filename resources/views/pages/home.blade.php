@extends('layouts.app')

@section('title', 'Trang chủ - Nông Sản Sạch')

@section('content')

    <x-container class="py-10">
        {{-- Kiểm tra Alpine.js --}}
        <section
            class="mt-10 rounded-xl bg-white p-6 shadow-sm"
            x-data="{ dem: 0 }"
        >

            <h2 class="text-xl font-bold">
                Kiểm tra Alpine.js
            </h2>

            <p class="mt-3">
                Số lần nhấn:
                <span x-text="dem"></span>
            </p>

            <div class="mt-4 flex gap-3">

                <x-button @click="dem++">
                    Tăng
                </x-button>

                <x-button color="gray" @click="dem = 0">
                    Reset
                </x-button>

            </div>

        </section>

        {{-- Banner --}}
        <section class="rounded-2xl bg-green-100 p-6 md:p-12">

            <h1 class="text-3xl font-bold text-green-800 md:text-5xl">
                Nông sản sạch từ nông trại
            </h1>

            <p class="mt-4 max-w-xl text-gray-700">
                Truy xuất nguồn gốc rõ ràng.
                Mua sắm nông sản tươi sạch và an toàn.
            </p>

            <div class="mt-6">
                <x-button>
                    Khám phá sản phẩm
                </x-button>
            </div>

        </section>

        {{-- Sản phẩm --}}
        <section class="mt-10">

            <h2 class="mb-6 text-2xl font-bold">
                Sản phẩm nổi bật
            </h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">

                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <h3 class="font-semibold">
                        Rau sạch
                    </h3>

                    <p class="mt-2 text-gray-500">
                        Rau được thu hoạch từ nông trại.
                    </p>

                    <div class="mt-4">
                        <x-button color="green">
                            Xem sản phẩm
                        </x-button>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <h3 class="font-semibold">
                        Trái cây
                    </h3>

                    <p class="mt-2 text-gray-500">
                        Trái cây tươi, có nguồn gốc rõ ràng.
                    </p>

                    <div class="mt-4">
                        <x-button>
                            Xem sản phẩm
                        </x-button>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <h3 class="font-semibold">
                        Nông sản theo mùa
                    </h3>

                    <p class="mt-2 text-gray-500">
                        Các sản phẩm được thu hoạch theo mùa vụ.
                    </p>

                    <div class="mt-4">
                        <x-button>
                            Xem sản phẩm
                        </x-button>
                    </div>
                </div>

            </div>

        </section>

    </x-container>

@endsection