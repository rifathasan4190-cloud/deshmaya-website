@extends('layouts.app')
@section('title', 'Our Products')
@section('description', 'Explore fresh fruits, vegetables and agricultural products sourced in Bangladesh for export.')
@section('content')
<x-page-banner title="Our Products" subtitle="Fresh produce, thoughtfully selected for global markets." tone="produce" image="/images/banners/products.jpg" />

<section class="section-shell">
    <div class="container-shell">
        <x-section-heading eyebrow="From our sourcing network" title="Fresh from Bangladesh" description="Browse our current selection of export-ready agricultural products." center />
        <nav class="category-pills" aria-label="Filter products by category">
            <a @class(['category-pill', 'is-active' => ! $selectedCategory]) href="{{ route('products.index') }}" @if(! $selectedCategory) aria-current="page" @endif>All Products</a>
            @foreach($categories as $category)
                <a @class(['category-pill', 'is-active' => $selectedCategory === $category->slug]) href="{{ route('products.category', $category->slug) }}" @if($selectedCategory === $category->slug) aria-current="page" @endif>{{ $category->name }}</a>
            @endforeach
        </nav>

        @if($products->isNotEmpty())
            <div
                class="product-catalog"
                x-data="{
                    quickView: null,
                    previousFocus: null,
                    openQuickView(product, event) {
                        this.previousFocus = event.currentTarget;
                        this.quickView = product;
                        document.body.classList.add('quick-view-open');
                        this.$nextTick(() => this.$refs.quickViewClose.focus());
                    },
                    closeQuickView() {
                        if (! this.quickView) return;
                        this.quickView = null;
                        document.body.classList.remove('quick-view-open');
                        this.$nextTick(() => this.previousFocus?.focus());
                    },
                    trapQuickViewFocus(event) {
                        if (event.key !== 'Tab') return;
                        const focusable = this.$refs.quickViewDialog.querySelectorAll('a[href], button:not([disabled])');
                        const first = focusable[0];
                        const last = focusable[focusable.length - 1];
                        if (event.shiftKey && document.activeElement === first) {
                            event.preventDefault();
                            last.focus();
                        } else if (! event.shiftKey && document.activeElement === last) {
                            event.preventDefault();
                            first.focus();
                        }
                    }
                }"
                @keydown.escape.window="closeQuickView()"
            >
                <div class="product-grid">
                    @foreach($products as $product)
                        <x-product-card :product="$product" :quick-view="true" />
                    @endforeach
                </div>

                <div
                    class="quick-view"
                    x-cloak
                    x-show="quickView"
                    x-transition.opacity.duration.200ms
                    @click.self="closeQuickView()"
                >
                    <section
                        class="quick-view__dialog"
                        x-ref="quickViewDialog"
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="quick-view-title"
                        @keydown="trapQuickViewFocus($event)"
                    >
                        <button class="quick-view__close" type="button" aria-label="Close quick view" x-ref="quickViewClose" @click="closeQuickView()">×</button>
                        <div class="quick-view__visual">
                            <template x-if="quickView?.image">
                                <img class="quick-view__image" :src="quickView.image" :alt="quickView.name">
                            </template>
                            <template x-if="! quickView?.image">
                                <div class="picture-placeholder quick-view__placeholder" role="img" :aria-label="quickView?.name"></div>
                            </template>
                        </div>
                        <div class="quick-view__content">
                            <p class="eyebrow" x-text="quickView?.category"></p>
                            <h2 id="quick-view-title" x-text="quickView?.name"></h2>
                            <p class="quick-view__intro" x-show="quickView?.shortDescription" x-text="quickView?.shortDescription"></p>
                            <p class="quick-view__description" x-show="quickView?.description" x-text="quickView?.description"></p>
                            <section class="quick-view__specs" x-show="Object.keys(quickView?.specs ?? {}).length">
                                <h3>Product details</h3>
                                <dl>
                                    <template x-for="(value, label) in (quickView?.specs ?? {})" :key="label">
                                        <div>
                                            <dt x-text="label"></dt>
                                            <dd x-text="Array.isArray(value) ? value.join(', ') : value"></dd>
                                        </div>
                                    </template>
                                </dl>
                            </section>
                            <p class="quick-view__category">Category: <span x-text="quickView?.category"></span></p>
                            <div class="quick-view__actions">
                                <a class="button button--primary" :href="quickView?.quoteUrl">Request a Quote <span aria-hidden="true">→</span></a>
                                <a class="quick-view__details-link" :href="quickView?.productUrl">View full details</a>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        @else
            <div class="empty-state empty-state--large">
                <span class="empty-state__icon" aria-hidden="true">✳</span>
                <h2>{{ $selectedCategory ? 'More products coming soon' : 'Our collection is being prepared' }}</h2>
                <p>Please contact our team to discuss your sourcing requirements.</p>
                <a class="button button--primary" href="{{ route('contact') }}">Talk to our team</a>
            </div>
        @endif
    </div>
</section>

<section class="cta-band">
    <div class="container-shell cta-band__inner">
        <div>
            <p class="eyebrow text-white/75">Sourcing something specific?</p>
            <h2>Tell us what your market needs.</h2>
        </div>
        <a class="button button--light" href="{{ route('request-quote') }}">Request a Quote <span aria-hidden="true">→</span></a>
    </div>
</section>
@endsection
