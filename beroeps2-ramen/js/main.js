// Ramen Delivery - Main JavaScript

// Cart Management
class CartManager {
    constructor() {
        this.cart = this.loadCart();
        this.init();
    }
    
    init() {
        this.updateCartDisplay();
        this.attachEventListeners();
    }
    
    loadCart() {
        const cartData = localStorage.getItem('ramen_cart');
        return cartData ? JSON.parse(cartData) : [];
    }
    
    saveCart() {
        localStorage.setItem('ramen_cart', JSON.stringify(this.cart));
        this.updateCartDisplay();
    }
    
    addToCart(dishId, dishName, dishPrice) {
        const existingItem = this.cart.find(item => item.dish_id === dishId);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.cart.push({
                dish_id: dishId,
                name: dishName,
                price: parseFloat(dishPrice),
                quantity: 1
            });
        }
        
        this.saveCart();
        this.showNotification(`${dishName} toegevoegd aan winkelwagen!`);
    }
    
    removeFromCart(dishId) {
        this.cart = this.cart.filter(item => item.dish_id !== dishId);
        this.saveCart();
    }
    
    updateQuantity(dishId, quantity) {
        const item = this.cart.find(item => item.dish_id === dishId);
        if (item) {
            if (quantity <= 0) {
                this.removeFromCart(dishId);
            } else {
                item.quantity = quantity;
                this.saveCart();
            }
        }
    }
    
    getTotal() {
        return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    }
    
    getItemCount() {
        return this.cart.reduce((count, item) => count + item.quantity, 0);
    }
    
    clearCart() {
        this.cart = [];
        this.saveCart();
    }
    
    updateCartDisplay() {
        // Update cart FAB
        const cartFab = document.getElementById('cartFab');
        const cartCount = document.getElementById('cartCount');
        
        if (cartFab && cartCount) {
            const count = this.getItemCount();
            if (count > 0) {
                cartFab.style.display = 'flex';
                cartCount.textContent = count;
            } else {
                cartFab.style.display = 'none';
            }
        }
        
        // Update cart items on order page
        this.renderCartItems();
    }
    
    renderCartItems() {
        const cartItemsContainer = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');
        const placeOrderForm = document.getElementById('placeOrderForm');
        
        if (!cartItemsContainer) return;
        
        if (this.cart.length === 0) {
            cartItemsContainer.innerHTML = '<div class="empty-state"><i class="fas fa-shopping-basket"></i><p>Je winkelwagen is leeg</p></div>';
            if (cartTotal) cartTotal.innerHTML = '<strong>Totaal: €0,00</strong>';
            if (placeOrderForm) placeOrderForm.style.display = 'none';
            return;
        }
        
        let html = '';
        this.cart.forEach(item => {
            html += `
                <div class="cart-item" data-dish-id="${item.dish_id}">
                    <div class="cart-item-info">
                        <div class="cart-item-name">${this.escapeHtml(item.name)}</div>
                        <div class="cart-item-price">€${this.formatPrice(item.price)} per stuk</div>
                    </div>
                    <div class="cart-item-controls">
                        <div class="quantity-controls">
                            <button class="quantity-btn decrease-btn" data-dish-id="${item.dish_id}" data-quantity="${item.quantity}">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="quantity">${item.quantity}</span>
                            <button class="quantity-btn increase-btn" data-dish-id="${item.dish_id}" data-quantity="${item.quantity}">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <button class="btn btn-sm btn-danger remove-cart-item-btn" data-dish-id="${item.dish_id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        
        cartItemsContainer.innerHTML = html;
        
        if (cartTotal) {
            const total = this.getTotal();
            cartTotal.innerHTML = `<strong>Totaal: €${this.formatPrice(total)}</strong>`;
        }
        
        if (placeOrderForm) {
            const cartDataInput = document.getElementById('cartData');
            if (cartDataInput) {
                cartDataInput.value = JSON.stringify(this.cart);
            }
            placeOrderForm.style.display = 'block';
        }
    }
    
    formatPrice(price) {
        return price.toFixed(2).replace('.', ',');
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    showNotification(message) {
        // Simple notification
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    attachEventListeners() {
        // Add to cart buttons
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', (e) => {
                const dishId = parseInt(button.dataset.dishId);
                const dishName = button.dataset.dishName;
                const dishPrice = button.dataset.dishPrice;
                this.addToCart(dishId, dishName, dishPrice);
            });
        });
        
        // Cart FAB click
        const cartFab = document.getElementById('cartFab');
        if (cartFab) {
            cartFab.addEventListener('click', () => {
                window.location.href = 'order.php';
            });
        }
        
        // Quantity buttons (delegated event listeners for dynamically added buttons)
        document.addEventListener('click', (e) => {
            if (e.target.closest('.decrease-btn')) {
                const btn = e.target.closest('.decrease-btn');
                const dishId = parseInt(btn.dataset.dishId);
                const currentQty = parseInt(btn.dataset.quantity);
                this.updateQuantity(dishId, currentQty - 1);
            }
            if (e.target.closest('.increase-btn')) {
                const btn = e.target.closest('.increase-btn');
                const dishId = parseInt(btn.dataset.dishId);
                const currentQty = parseInt(btn.dataset.quantity);
                this.updateQuantity(dishId, currentQty + 1);
            }
            if (e.target.closest('.remove-cart-item-btn')) {
                const btn = e.target.closest('.remove-cart-item-btn');
                const dishId = parseInt(btn.dataset.dishId);
                this.removeFromCart(dishId);
            }
        });
    }
}

// Dashboard Tabs
function initDashboardTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.dataset.tab;
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            button.classList.add('active');
            const targetContent = document.getElementById(targetTab + 'Tab');
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
}

// Search functionality
function initSearch() {
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            const searchInput = searchForm.querySelector('input[name="search"]');
            if (!searchInput.value.trim()) {
                e.preventDefault();
            }
        });
    }
}

// Auto-refresh order status (optional)
function initOrderStatusRefresh() {
    const orderCards = document.querySelectorAll('.order-card');
    if (orderCards.length > 0) {
        // Could implement AJAX refresh here
        // For now, just a placeholder
    }
}

// Clear cart after successful order
function clearCartAfterOrder() {
    const successAlert = document.querySelector('.alert-success');
    if (successAlert && cartManager) {
        cartManager.clearCart();
    }
}

// Page loader functionality
function initPageLoader() {
    // Verberg loader wanneer pagina geladen is
    window.addEventListener('load', function() {
        const loader = document.getElementById('pageLoader');
        if (loader) {
            setTimeout(function() {
                loader.classList.add('hidden');
                setTimeout(function() {
                    loader.style.display = 'none';
                }, 300);
            }, 100);
        }
    });
    
    // Fallback: verberg loader na max 3 seconden
    setTimeout(function() {
        const loader = document.getElementById('pageLoader');
        if (loader) {
            loader.classList.add('hidden');
            setTimeout(function() {
                loader.style.display = 'none';
            }, 300);
        }
    }, 3000);
}

// Mobile Menu Toggle
function initMobileMenu() {
    const menuToggle = document.getElementById('mobileMenuToggle');
    const navLinks = document.getElementById('navLinks');
    const body = document.body;
    
    if (menuToggle && navLinks) {
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'nav-overlay';
        body.appendChild(overlay);
        
        function toggleMenu() {
            menuToggle.classList.toggle('active');
            navLinks.classList.toggle('active');
            overlay.classList.toggle('active');
            body.style.overflow = navLinks.classList.contains('active') ? 'hidden' : '';
        }
        
        function closeMenu() {
            menuToggle.classList.remove('active');
            navLinks.classList.remove('active');
            overlay.classList.remove('active');
            body.style.overflow = '';
        }
        
        menuToggle.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', closeMenu);
        
        // Close menu when clicking a link
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    closeMenu();
                }
            });
        });
        
        // Close menu on window resize if desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                closeMenu();
            }
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Initialize mobile menu
    initMobileMenu();
    
    // Initialize page loader
    initPageLoader();
    
    // Initialize cart manager
    if (typeof CartManager !== 'undefined') {
        window.cartManager = new CartManager();
    }
    
    // Clear cart if order was successful
    clearCartAfterOrder();
    
    // Initialize dashboard tabs
    initDashboardTabs();
    
    // Initialize search
    initSearch();
    
    // Initialize order status refresh
    initOrderStatusRefresh();
    
    // Image fallback handlers
    document.querySelectorAll('img[data-fallback]').forEach(img => {
        img.addEventListener('error', function() {
            this.src = this.dataset.fallback;
        });
    });
    
    // Delete dish confirmation
    document.querySelectorAll('.delete-dish-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Weet je zeker dat je dit gerecht wilt verwijderen?')) {
                e.preventDefault();
            }
        });
    });
});

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
