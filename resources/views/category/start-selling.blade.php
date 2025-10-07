@extends('partials.layout')

@section('content')
    {{-- Category Header --}}
    <x-category-header 
        title="Start selling"
        description="Get your store up and running with your first products, payments and shipping setup."
    />

    {{-- Articles Grid --}}
    <section class="flex flex-col gap-12 items-center justify-start px-6 sm:px-12 md:px-16 lg:px-24 xl:px-32 py-24 w-full">
        {{-- First Row --}}
        <div class="flex flex-col lg:flex-row gap-6 items-stretch justify-start w-full max-w-[1440px]">
            <div class="flex-1">
                <x-article-card 
                    title="How to create your first simple product"
                    description="Learn the basics of adding products to your Magento store, including ..."
                    difficulty="beginner"
                    icon="product"
                    readTime="5 minute read"
                    link="/docs/main/products/simple-product"
                />
            </div>
            
            <div class="flex-1">
                <x-article-card 
                    title="Set up shipping rates by country or region"
                    description="Configure shipping methods and rates for different geographical locations to ..."
                    difficulty="intermediate"
                    icon="shipping"
                    readTime="8 minute read"
                    link="/docs/main/shipping/rates"
                />
            </div>
            
            <div class="flex-1">
                <x-article-card 
                    title="Enable credit card payments securely"
                    description="Set up secure payment processing with popular payment gateways and ensure ..."
                    difficulty="intermediate"
                    icon="payment"
                    readTime="10 minute read"
                    link="/docs/main/payments/credit-cards"
                />
            </div>
        </div>

        {{-- Second Row --}}
        <div class="flex flex-col lg:flex-row gap-6 items-stretch justify-start w-full max-w-[1440px]">
            <div class="flex-1">
                <x-article-card 
                    title="Add free shipping over a minimum cart value"
                    description="Create promotional shipping rules to encourage large orders and increase customer ..."
                    difficulty="beginner"
                    icon="cart"
                    readTime="4 minute read"
                    link="/docs/main/shipping/free-shipping"
                />
            </div>
            
            <div class="flex-1">
                <x-article-card 
                    title="Configure tax rules for multiple regions"
                    description="Set up tax calculations for different countries and states to ensure compliance with local ..."
                    difficulty="advanced"
                    icon="tax"
                    readTime="8 minute read"
                    link="/docs/main/tax/rules"
                />
            </div>
            
            <div class="flex-1">
                <x-article-card 
                    title="Sell digital products or downloadable files"
                    description="Learn how to set up downloadable products, manage file security and ..."
                    difficulty="intermediate"
                    icon="download"
                    readTime="10 minute read"
                    link="/docs/main/products/downloadable"
                />
            </div>
        </div>
    </section>
@endsection