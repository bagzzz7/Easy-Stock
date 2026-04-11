@extends('layouts.app')

@section('title', 'Point of Sale')

@section('content')
<div class="pos-modern">
    {{-- Header --}}
    <div class="pos-header">
        <div class="pos-header-left">
            <div class="pos-badge">Point of Sale</div>
            <h1 class="pos-title">New Transaction</h1>
            <p class="pos-subtitle">Process customer sales quickly and efficiently</p>
        </div>
        <div class="pos-header-right">
            <div class="date-time">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ now()->format('l, F j, Y') }}</span>
                <i class="fas fa-clock ms-2"></i>
                <span id="currentTime">{{ now()->format('h:i A') }}</span>
            </div>
        </div>
    </div>

    <div class="pos-layout">
        {{-- Left Column - Cart --}}
        <div class="pos-cart">
            <div class="cart-card">
                <div class="cart-header">
                    <div class="cart-header-left">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>Shopping Cart</h3>
                    </div>
                    <span class="cart-badge" id="itemCountBadge">0 items</span>
                </div>

                {{-- Search Section --}}
                <div class="search-section">
                    <div class="search-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" 
                               id="searchMedicine" 
                               class="search-input" 
                               placeholder="Search medicine by name, brand, or generic name..."
                               autocomplete="off"
                               autofocus>
                        <button id="searchBtn" class="search-btn">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                    <div id="searchResults" class="search-results"></div>
                </div>

                {{-- Cart Items --}}
                <div class="cart-items">
                    <div class="items-header">
                        <span>Item</span>
                        <span class="text-center">Qty</span>
                        <span class="text-end">Amount</span>
                        <span></span>
                    </div>
                    <div id="cartBody" class="items-list">
                        <div class="empty-cart">
                            <i class="fas fa-shopping-cart"></i>
                            <p>Your cart is empty</p>
                            <span>Search and add medicines to begin</span>
                        </div>
                    </div>
                </div>

                {{-- Cart Footer --}}
                <div class="cart-footer">
                    <div class="cart-summary">
                        <div class="summary-row">
                            <span>Total Items:</span>
                            <strong id="totalItems">0</strong>
                        </div>
                        <div class="summary-row total">
                            <span>Total Amount:</span>
                            <strong id="cartTotal">₱0.00</strong>
                        </div>
                    </div>
                    <button id="clearCart" class="btn-clear">
                        <i class="fas fa-trash-alt"></i>
                        <span>Clear Cart</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Right Column - Transaction Details --}}
        <div class="pos-transaction">
            <div class="transaction-card">
                <div class="transaction-header">
                    <i class="fas fa-receipt"></i>
                    <h3>Transaction Details</h3>
                </div>

                <form id="saleForm" action="{{ route('sales.store') }}" method="POST">
                    @csrf
                    <div id="itemsContainer"></div>

                    {{-- Summary --}}
                    <div class="summary-section">
                        <div class="summary-item">
                            <span>Subtotal</span>
                            <span id="summarySubtotal">₱0.00</span>
                        </div>
                        <div class="summary-item">
                            <span>Discount</span>
                            <div class="discount-input">
                                <span class="currency">₱</span>
                                <input type="number" 
                                       step="0.01" 
                                       id="discount" 
                                       name="discount" 
                                       value="0" 
                                       min="0">
                            </div>
                        </div>
                        <div class="summary-item">
                            <span>Tax (12% VAT)</span>
                            <span id="summaryTax">₱0.00</span>
                            <input type="hidden" id="tax" name="tax" value="0">
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-item grand-total">
                            <span>GRAND TOTAL</span>
                            <span id="summaryGrandTotal">₱0.00</span>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="payment-section">
                        <label class="section-label">Payment Method</label>
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cash" checked>
                                <div class="option-content">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>Cash</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="card">
                                <div class="option-content">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Card</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="mobile_payment">
                                <div class="option-content">
                                    <i class="fas fa-mobile-alt"></i>
                                    <span>Mobile</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="notes-section">
                        <label class="section-label">Notes (Optional)</label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="2" 
                                  placeholder="Add notes about this transaction..."></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="transaction-actions">
                        <button type="submit" id="completeSale" class="btn-complete" disabled>
                            <i class="fas fa-check-circle"></i>
                            <span>Complete Sale</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Quick Add Section --}}
            <div class="quick-add-card">
                <div class="quick-add-header">
                    <i class="fas fa-bolt"></i>
                    <h3>Quick Add</h3>
                </div>
                <div class="category-filters">
                    <button class="cat-btn active" data-category="all">All</button>
                    @foreach($categories ?? [] as $category)
                    <button class="cat-btn" data-category="{{ $category->id }}">{{ $category->name }}</button>
                    @endforeach
                </div>
                <div class="quick-items" id="quickAddContainer">
                    @forelse($medicines->take(12) as $medicine)
                    <button class="quick-item" 
                            data-id="{{ $medicine->id }}"
                            data-name="{{ $medicine->name }}"
                            data-price="{{ $medicine->selling_price }}"
                            data-stock="{{ $medicine->quantity }}">
                        <div class="item-icon">
                            <i class="fas fa-capsules"></i>
                        </div>
                        <div class="item-info">
                            <div class="item-name">{{ Str::limit($medicine->name, 20) }}</div>
                            <div class="item-price">₱{{ number_format($medicine->selling_price, 2) }}</div>
                            <div class="item-stock">Stock: {{ $medicine->quantity }}</div>
                        </div>
                    </button>
                    @empty
                    <div class="empty-quick">No medicines available</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* POS Modern CSS */
.pos-modern {
    max-width: 1600px;
    margin: 0 auto;
    padding: 2rem;
}

/* Header */
.pos-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 24px;
    padding: 2rem;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    overflow: hidden;
}

.pos-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.pos-badge {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 1rem;
    color: white;
}

.pos-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: white;
}

.pos-subtitle {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

.date-time {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 0.5rem 1rem;
    border-radius: 40px;
    font-size: 0.875rem;
    color: white;
}

/* Layout */
.pos-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 1.5rem;
}

/* Cart Card */
.cart-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    height: calc(100vh - 200px);
}

.cart-header {
    padding: 1.25rem 1.5rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cart-header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.cart-header-left i {
    font-size: 1.25rem;
    color: #2563eb;
}

.cart-header-left h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.cart-badge {
    background: #2563eb;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
}

/* Search Section */
.search-section {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    position: relative;
}

.search-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-wrapper i {
    position: absolute;
    left: 1rem;
    color: #94a3b8;
    font-size: 0.875rem;
}

.search-input {
    flex: 1;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 40px;
    font-size: 0.875rem;
    transition: all 0.2s;
    background: white;
}

.search-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.search-btn {
    position: absolute;
    right: 0.25rem;
    top: 0.25rem;
    bottom: 0.25rem;
    background: #2563eb;
    border: none;
    border-radius: 40px;
    padding: 0 1rem;
    color: white;
    cursor: pointer;
    transition: all 0.2s;
}

.search-btn:hover {
    background: #1d4ed8;
}

.search-results {
    position: absolute;
    top: calc(100% - 0.5rem);
    left: 1.5rem;
    right: 1.5rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    border: 1px solid #e2e8f0;
    max-height: 300px;
    overflow-y: auto;
    z-index: 100;
    display: none;
}

/* Cart Items */
.cart-items {
    flex: 1;
    overflow-y: auto;
    padding: 0 1rem;
}

.items-header {
    display: grid;
    grid-template-columns: 1fr 80px 100px 40px;
    padding: 0.75rem 0;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    background: white;
    z-index: 5;
}

.items-list {
    min-height: 200px;
}

.empty-cart {
    text-align: center;
    padding: 3rem 1rem;
    color: #94a3b8;
}

.empty-cart i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-cart p {
    margin: 0 0 0.25rem 0;
    font-weight: 500;
}

.empty-cart span {
    font-size: 0.75rem;
}

.cart-item {
    display: grid;
    grid-template-columns: 1fr 80px 100px 40px;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.cart-item-name {
    font-weight: 500;
    font-size: 0.875rem;
}

.cart-item-price {
    font-size: 0.75rem;
    color: #64748b;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    justify-content: center;
}

.qty-btn {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.qty-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.qty-input {
    width: 40px;
    text-align: center;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0.25rem;
    font-size: 0.75rem;
}

.cart-item-subtotal {
    font-weight: 600;
    font-size: 0.875rem;
    text-align: right;
}

.remove-item {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #ef4444;
    cursor: pointer;
    transition: all 0.2s;
}

.remove-item:hover {
    background: #fee2e2;
    border-color: #ef4444;
}

/* Cart Footer */
.cart-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
}

.cart-summary {
    margin-bottom: 1rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.summary-row.total {
    font-size: 1rem;
    font-weight: 700;
    color: #2563eb;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px dashed #e2e8f0;
}

.btn-clear {
    width: 100%;
    padding: 0.625rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    color: #ef4444;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-clear:hover {
    background: #fee2e2;
    border-color: #ef4444;
}

/* Transaction Card */
.transaction-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
}

.transaction-header {
    padding: 1.25rem 1.5rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.transaction-header i {
    font-size: 1.25rem;
    color: #10b981;
}

.transaction-header h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.summary-section {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 0.875rem;
}

.discount-input {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.discount-input .currency {
    color: #64748b;
}

.discount-input input {
    width: 80px;
    padding: 0.25rem 0.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    text-align: right;
    font-size: 0.875rem;
}

.summary-divider {
    height: 1px;
    background: #e2e8f0;
    margin: 0.75rem 0;
}

.grand-total {
    font-size: 1rem;
    font-weight: 700;
    color: #2563eb;
}

/* Payment Section */
.payment-section {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.section-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 0.75rem;
}

.payment-options {
    display: flex;
    gap: 0.75rem;
}

.payment-option {
    flex: 1;
    cursor: pointer;
}

.payment-option input {
    display: none;
}

.option-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.2s;
}

.payment-option input:checked + .option-content {
    border-color: #2563eb;
    background: #eff6ff;
}

.option-content i {
    font-size: 1.25rem;
    color: #64748b;
}

.payment-option input:checked + .option-content i {
    color: #2563eb;
}

.option-content span {
    font-size: 0.75rem;
    font-weight: 500;
}

/* Notes Section */
.notes-section {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.notes-section textarea {
    width: 100%;
    padding: 0.625rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.875rem;
    resize: vertical;
    font-family: inherit;
}

.notes-section textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Transaction Actions */
.transaction-actions {
    padding: 1.25rem 1.5rem;
}

.btn-complete {
    width: 100%;
    padding: 0.875rem;
    background: #10b981;
    border: none;
    border-radius: 40px;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-complete:hover:not(:disabled) {
    background: #059669;
    transform: translateY(-1px);
}

.btn-complete:disabled {
    background: #94a3b8;
    cursor: not-allowed;
}

/* Quick Add Card */
.quick-add-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.quick-add-header {
    padding: 1rem 1.25rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.quick-add-header i {
    font-size: 1rem;
    color: #f59e0b;
}

.quick-add-header h3 {
    margin: 0;
    font-size: 0.875rem;
    font-weight: 600;
}

.category-filters {
    padding: 0.75rem 1rem;
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    border-bottom: 1px solid #e2e8f0;
}

.cat-btn {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    background: white;
    font-size: 0.7rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.cat-btn.active {
    background: #2563eb;
    border-color: #2563eb;
    color: white;
}

.quick-items {
    padding: 1rem;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
    max-height: 300px;
    overflow-y: auto;
}

.quick-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
    text-align: left;
}

.quick-item:hover {
    border-color: #2563eb;
    background: #f8fafc;
    transform: translateY(-1px);
}

.item-icon {
    width: 40px;
    height: 40px;
    background: #eff6ff;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2563eb;
}

.item-info {
    flex: 1;
}

.item-name {
    font-size: 0.75rem;
    font-weight: 600;
    margin-bottom: 0.125rem;
}

.item-price {
    font-size: 0.7rem;
    color: #2563eb;
    font-weight: 600;
}

.item-stock {
    font-size: 0.6rem;
    color: #94a3b8;
}

.empty-quick {
    text-align: center;
    padding: 2rem;
    color: #94a3b8;
    font-size: 0.875rem;
}

/* Responsive */
@media (max-width: 1024px) {
    .pos-layout {
        grid-template-columns: 1fr;
    }
    
    .cart-card {
        height: auto;
        max-height: 500px;
    }
}

@media (max-width: 768px) {
    .pos-modern {
        padding: 1rem;
    }
    
    .pos-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .items-header {
        grid-template-columns: 1fr 70px 90px 40px;
    }
    
    .quick-items {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
let cart = [];
let total = 0;

// DOM Elements
const searchInput = document.getElementById('searchMedicine');
const searchBtn = document.getElementById('searchBtn');
const searchResults = document.getElementById('searchResults');
const cartBody = document.getElementById('cartBody');
const itemsContainer = document.getElementById('itemsContainer');
const completeSaleBtn = document.getElementById('completeSale');
const cartTotal = document.getElementById('cartTotal');
const itemCountBadge = document.getElementById('itemCountBadge');
const totalItemsSpan = document.getElementById('totalItems');
const summarySubtotal = document.getElementById('summarySubtotal');
const summaryGrandTotal = document.getElementById('summaryGrandTotal');
const discountInput = document.getElementById('discount');
const taxHidden = document.getElementById('tax');

// Update current time
function updateTime() {
    const now = new Date();
    document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}
setInterval(updateTime, 1000);
updateTime();

// Category filtering
document.querySelectorAll('.cat-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const category = this.dataset.category;
        document.querySelectorAll('.quick-item').forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Search functionality
function searchMedicines() {
    const query = searchInput.value.trim();
    if (query.length < 2) {
        searchResults.style.display = 'none';
        return;
    }
    
    fetch(`/medicines/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                searchResults.innerHTML = '<div class="search-result-item">No medicines found</div>';
                searchResults.style.display = 'block';
                return;
            }
            
            let html = '';
            data.forEach(medicine => {
                if (medicine.quantity > 0) {
                    html += `<div class="search-result-item" 
                                data-id="${medicine.id}"
                                data-name="${medicine.name}"
                                data-price="${medicine.selling_price}"
                                data-stock="${medicine.quantity}">
                                <div>
                                    <strong>${medicine.name}</strong>
                                    <small>₱${parseFloat(medicine.selling_price).toFixed(2)}</small>
                                </div>
                                <span class="stock-badge">Stock: ${medicine.quantity}</span>
                            </div>`;
                }
            });
            
            searchResults.innerHTML = html;
            searchResults.style.display = 'block';
            
            document.querySelectorAll('.search-result-item').forEach(item => {
                item.addEventListener('click', function() {
                    addToCart({
                        currentTarget: {
                            dataset: {
                                id: this.dataset.id,
                                name: this.dataset.name,
                                price: this.dataset.price,
                                stock: this.dataset.stock
                            }
                        }
                    });
                });
            });
        })
        .catch(error => {
            console.error('Error:', error);
            searchResults.innerHTML = '<div class="search-result-item text-danger">Error loading results</div>';
            searchResults.style.display = 'block';
        });
}

let searchTimeout;
searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(searchMedicines, 300);
});

searchBtn.addEventListener('click', searchMedicines);
searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') searchMedicines();
});

// Add to cart
function addToCart(e) {
    const btn = e.currentTarget;
    const medicine = {
        id: btn.dataset.id,
        name: btn.dataset.name,
        price: parseFloat(btn.dataset.price),
        stock: parseInt(btn.dataset.stock)
    };
    
    if (medicine.stock <= 0) {
        alert('Out of stock!');
        return;
    }
    
    const existingIndex = cart.findIndex(item => item.id === medicine.id);
    
    if (existingIndex !== -1) {
        if (cart[existingIndex].quantity < medicine.stock) {
            cart[existingIndex].quantity++;
        } else {
            alert(`Only ${medicine.stock} available!`);
            return;
        }
    } else {
        cart.push({
            id: medicine.id,
            name: medicine.name,
            price: medicine.price,
            stock: medicine.stock,
            quantity: 1
        });
    }
    
    updateCart();
    searchInput.value = '';
    searchResults.style.display = 'none';
}

// Update cart display
function updateCart() {
    cartBody.innerHTML = '';
    itemsContainer.innerHTML = '';
    total = 0;
    let totalItems = 0;
    
    if (cart.length === 0) {
        cartBody.innerHTML = `<div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <p>Your cart is empty</p>
            <span>Search and add medicines to begin</span>
        </div>`;
        completeSaleBtn.disabled = true;
        itemCountBadge.textContent = '0 items';
        totalItemsSpan.textContent = '0';
    } else {
        cart.forEach((item, index) => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            totalItems += item.quantity;
            
            cartBody.innerHTML += `
                <div class="cart-item">
                    <div>
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">₱${item.price.toFixed(2)} ea</div>
                    </div>
                    <div class="quantity-controls">
                        <button class="qty-btn" onclick="updateQuantity(${index}, -1)">-</button>
                        <input type="number" class="qty-input" value="${item.quantity}" min="1" max="${item.stock}" onchange="updateQuantityInput(${index}, this.value)">
                        <button class="qty-btn" onclick="updateQuantity(${index}, 1)">+</button>
                    </div>
                    <div class="cart-item-subtotal">₱${subtotal.toFixed(2)}</div>
                    <button class="remove-item" onclick="removeFromCart(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            itemsContainer.innerHTML += `
                <input type="hidden" name="items[${index}][medicine_id]" value="${item.id}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            `;
        });
        
        completeSaleBtn.disabled = false;
        itemCountBadge.textContent = `${totalItems} item${totalItems !== 1 ? 's' : ''}`;
        totalItemsSpan.textContent = totalItems;
    }
    
    cartTotal.textContent = `₱${total.toFixed(2)}`;
    updateGrandTotal();
}

// Quantity functions
window.updateQuantity = function(index, change) {
    const item = cart[index];
    const newQuantity = item.quantity + change;
    
    if (newQuantity < 1) {
        removeFromCart(index);
        return;
    }
    
    if (newQuantity > item.stock) {
        alert(`Only ${item.stock} available!`);
        return;
    }
    
    item.quantity = newQuantity;
    updateCart();
};

window.updateQuantityInput = function(index, value) {
    const item = cart[index];
    const newQuantity = parseInt(value);
    
    if (isNaN(newQuantity) || newQuantity < 1) {
        item.quantity = 1;
    } else if (newQuantity > item.stock) {
        alert(`Only ${item.stock} available!`);
        item.quantity = item.stock;
    } else {
        item.quantity = newQuantity;
    }
    
    updateCart();
};

window.removeFromCart = function(index) {
    if (confirm('Remove this item from cart?')) {
        cart.splice(index, 1);
        updateCart();
    }
};

// Tax calculation
function updateGrandTotal() {
    const discount = parseFloat(discountInput.value) || 0;
    const subtotal = total;
    const taxRate = 0.12;
    const tax = Math.max(0, (subtotal - discount) * taxRate);
    const grandTotal = subtotal - discount + tax;
    
    taxHidden.value = tax.toFixed(2);
    summarySubtotal.textContent = `₱${subtotal.toFixed(2)}`;
    document.getElementById('summaryTax').textContent = `₱${tax.toFixed(2)}`;
    summaryGrandTotal.textContent = `₱${grandTotal.toFixed(2)}`;
}

discountInput.addEventListener('input', updateGrandTotal);

// Clear cart
document.getElementById('clearCart').addEventListener('click', function() {
    if (cart.length > 0 && confirm('Clear entire cart?')) {
        cart = [];
        updateCart();
    }
});

// Quick add buttons
document.querySelectorAll('.quick-item').forEach(btn => {
    btn.addEventListener('click', addToCart);
});

// Form submission
document.getElementById('saleForm').addEventListener('submit', function(e) {
    if (cart.length === 0) {
        e.preventDefault();
        alert('Please add items to cart before completing the sale!');
        return false;
    }
    
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    if (!paymentMethod) {
        e.preventDefault();
        alert('Please select a payment method!');
        return false;
    }
    
    completeSaleBtn.disabled = true;
    completeSaleBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Processing...</span>';
    return true;
});

// Click outside to close search
document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !searchResults.contains(e.target) && e.target !== searchBtn) {
        searchResults.style.display = 'none';
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    completeSaleBtn.disabled = true;
    searchInput.focus();
});
</script>
@endpush