// DriddlingBD - Premium Sports Jersey & Corporate Apparel
// Main JavaScript File

// ==================== DATA ====================
const products = [
  {
    id: 1,
    name: "Pro Cricket Jersey 2024",
    category: "Cricket",
    price: 2499,
    originalPrice: 2999,
    image: "https://images.unsplash.com/photo-1517649763962-0c623066013b?w=600&h=600&fit=crop",
    badge: "Best Seller",
    rating: 4.8,
    reviews: 124,
    colors: ["#1E3A5F", "#2563EB", "#0EA5E9", "#10B981"],
    sizes: ["S", "M", "L", "XL", "2XL"],
    description: "Premium quality cricket jersey with advanced moisture-wicking technology. Features breathable fabric and ergonomic design.",
    specs: ["100% Polyester", "Moisture-wicking", "Breathable mesh", "UV protection"]
  },
  {
    id: 2,
    name: "Elite Football Jersey",
    category: "Football",
    price: 1999,
    originalPrice: 2499,
    image: "https://images.unsplash.com/photo-1522778119026-d647f0565c6a?w=600&h=600&fit=crop",
    badge: "New",
    rating: 4.9,
    reviews: 89,
    colors: ["#2563EB", "#EF4444", "#F59E0B", "#10B981"],
    sizes: ["S", "M", "L", "XL", "2XL", "3XL"],
    description: "Professional grade football jersey with lightweight, breathable fabric.",
    specs: ["Lightweight polyester", "Quick-dry", "Sublimation printed"]
  },
  {
    id: 3,
    name: "Esports Pro Gaming Jersey",
    category: "Esports",
    price: 1799,
    originalPrice: 2299,
    image: "https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600&h=600&fit=crop",
    badge: null,
    rating: 4.7,
    reviews: 156,
    colors: ["#7C3AED", "#EC4899", "#10B981", "#F59E0B"],
    sizes: ["XS", "S", "M", "L", "XL", "2XL"],
    description: "Designed for professional esports athletes with anti-static fabric.",
    specs: ["Anti-static", "Odor-resistant", "Extended sleeves"]
  },
  {
    id: 4,
    name: "NBA Basketball Jersey",
    category: "Basketball",
    price: 2299,
    originalPrice: 2799,
    image: "https://images.unsplash.com/photo-1546519638-68e109498ffc?w=600&h=600&fit=crop",
    badge: null,
    rating: 4.6,
    reviews: 78,
    colors: ["#EF4444", "#2563EB", "#F59E0B", "#7C3AED"],
    sizes: ["S", "M", "L", "XL", "2XL"],
    description: "Authentic NBA-style basketball jersey with premium construction.",
    specs: ["Double-layer mesh", "Reinforced stitching", "Athletic fit"]
  },
  {
    id: 5,
    name: "Corporate Executive Polo",
    category: "Corporate",
    price: 1499,
    originalPrice: 1899,
    image: "https://images.unsplash.com/photo-1586363104862-3a5e2ab60d99?w=600&h=600&fit=crop",
    badge: "Premium",
    rating: 4.9,
    reviews: 234,
    colors: ["#1E3A5F", "#FFFFFF", "#0F172A", "#78716C"],
    sizes: ["S", "M", "L", "XL", "2XL", "3XL"],
    description: "Elegant corporate polo for business professionals.",
    specs: ["60% Cotton", "Anti-shrink", "Embroidery ready"]
  },
  {
    id: 6,
    name: "Team Uniform Pack",
    category: "Team Uniform",
    price: 8999,
    originalPrice: 11999,
    image: "https://images.unsplash.com/photo-1577223625816-7546f13df25d?w=600&h=600&fit=crop",
    badge: "Bulk Deal",
    rating: 4.8,
    reviews: 45,
    colors: ["#2563EB", "#0EA5E9", "#F59E0B", "#EF4444"],
    sizes: ["Mixed"],
    description: "Complete team uniform package for 10 players.",
    specs: ["10 complete sets", "Custom logo", "Size variety"]
  },
  {
    id: 7,
    name: "Performance Gym Wear",
    category: "Gym Wear",
    price: 1299,
    originalPrice: 1699,
    image: "https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=600&h=600&fit=crop",
    badge: null,
    rating: 4.5,
    reviews: 167,
    colors: ["#1E3A5F", "#F59E0B", "#7C3AED", "#0EA5E9"],
    sizes: ["S", "M", "L", "XL", "2XL"],
    description: "High-performance gym wear with compression fit.",
    specs: ["Compression fit", "4-way stretch", "Sweat-wicking"]
  },
  {
    id: 8,
    name: "School Uniform Set",
    category: "School Uniform",
    price: 2499,
    originalPrice: 2999,
    image: "https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&h=600&fit=crop",
    badge: "School Special",
    rating: 4.7,
    reviews: 312,
    colors: ["#1E3A5F", "#FFFFFF", "#0F172A", "#78716C"],
    sizes: ["XS", "S", "M", "L", "XL"],
    description: "Complete school uniform set with durable fabric.",
    specs: ["Durable poly-cotton", "Easy care", "Fade resistant"]
  }
];

const categories = [
  { name: "Cricket Jersey", icon: "🏏", count: 24 },
  { name: "Football Jersey", icon: "⚽", count: 18 },
  { name: "Esports Jersey", icon: "🎮", count: 12 },
  { name: "Basketball Jersey", icon: "🏀", count: 15 },
  { name: "Corporate Polo", icon: "👔", count: 20 },
  { name: "Team Uniform", icon: "👥", count: 8 },
  { name: "Gym Wear", icon: "💪", count: 16 },
  { name: "School Uniform", icon: "🎓", count: 14 }
];

const blogPosts = [
  { id: 1, title: "The Evolution of Sports Jersey Technology", excerpt: "Discover how modern fabric technology is revolutionizing athletic performance.", image: "https://images.unsplash.com/photo-1517649763962-0c623066013b?w=600&h=400&fit=crop", date: "May 15, 2024", category: "Technology" },
  { id: 2, title: "Custom Team Jerseys: Making Your Brand Stand Out", excerpt: "Learn how custom jerseys can boost team identity and professionalism.", image: "https://images.unsplash.com/photo-1522778119026-d647f0565c6a?w=600&h=400&fit=crop", date: "May 12, 2024", category: "Business" },
  { id: 3, title: "Corporate Apparel: The Key to Brand Consistency", excerpt: "Why investing in quality corporate wear matters for your business image.", image: "https://images.unsplash.com/photo-1586363104862-3a5e2ab60d99?w=600&h=400&fit=crop", date: "May 10, 2024", category: "Corporate" },
  { id: 4, title: "Sublimation Printing: Endless Design Possibilities", excerpt: "Explore the benefits of sublimation printing for vibrant, long-lasting designs.", image: "https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600&h=400&fit=crop", date: "May 8, 2024", category: "Printing" }
];

// ==================== STATE ====================
let cart = JSON.parse(localStorage.getItem('driddling_cart')) || [];
let wishlist = JSON.parse(localStorage.getItem('driddling_wishlist')) || [];
let selectedFilters = { category: 'all', priceRange: 'all', sort: 'popular' };

// ==================== INIT ====================
document.addEventListener('DOMContentLoaded', () => {
  initNavigation();
  initScrollEffects();
  initAnimations();
  initProductCards();
  initCartFunctionality();
  initFAQ();
  initMarquee();
  initCounters();
});

// ==================== NAVIGATION ====================
function initNavigation() {
  const navbar = document.querySelector('.navbar');
  const mobileToggle = document.querySelector('.mobile-menu-btn');
  const navMenu = document.querySelector('.nav-menu');
  const navMenuClose = document.querySelector('.nav-menu-close');

  window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
      navbar?.classList.add('scrolled');
    } else {
      navbar?.classList.remove('scrolled');
    }
  });

  mobileToggle?.addEventListener('click', () => {
    navMenu?.classList.add('active');
    document.body.style.overflow = 'hidden';
  });

  navMenuClose?.addEventListener('click', () => {
    navMenu?.classList.remove('active');
    document.body.style.overflow = '';
  });

  document.querySelectorAll('.nav-menu .nav-link').forEach(link => {
    link.addEventListener('click', () => {
      navMenu?.classList.remove('active');
      document.body.style.overflow = '';
    });
  });

  updateCartCount();
}

// ==================== SCROLL EFFECTS ====================
function initScrollEffects() {
  const reveals = document.querySelectorAll('.reveal');

  const revealOnScroll = () => {
    reveals.forEach(el => {
      const windowHeight = window.innerHeight;
      const elementTop = el.getBoundingClientRect().top;
      const revealPoint = 100;

      if (elementTop < windowHeight - revealPoint) {
        el.classList.add('active');
      }
    });
  };

  window.addEventListener('scroll', revealOnScroll);
  revealOnScroll();
}

// ==================== ANIMATIONS ====================
function initAnimations() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
}

// ==================== PRODUCT CARDS ====================
function initProductCards() {
  const productGrid = document.querySelector('.product-grid');
  if (productGrid) {
    renderProducts(products);
  }

  document.querySelectorAll('.category-card').forEach(card => {
    card.addEventListener('click', () => {
      const category = card.dataset.category;
      window.location.href = `shop.html?category=${category}`;
    });
  });
}

function renderProducts(productsToRender, container = '.product-grid') {
  const grid = document.querySelector(container);
  if (!grid) return;

  grid.innerHTML = productsToRender.map(product => `
    <div class="product-card" data-id="${product.id}">
      <div class="product-image">
        <img src="${product.image}" alt="${product.name}" loading="lazy">
        ${product.badge ? `<span class="product-badge">${product.badge}</span>` : ''}
        <div class="product-actions">
          <button class="action-btn wishlist-btn" data-id="${product.id}" title="Add to Wishlist">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
          </button>
          <button class="action-btn quick-view-btn" data-id="${product.id}" title="Quick View">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
          </button>
        </div>
      </div>
      <div class="product-info">
        <span class="product-category">${product.category}</span>
        <h3 class="product-title">${product.name}</h3>
        <div class="product-price">
          <span class="current-price">₹${product.price.toLocaleString()}</span>
          ${product.originalPrice ? `<span class="original-price">₹${product.originalPrice.toLocaleString()}</span>` : ''}
        </div>
        <button class="add-to-cart-btn" data-id="${product.id}">Add to Cart</button>
      </div>
    </div>
  `).join('');

  attachProductListeners();
}

function attachProductListeners() {
  document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const productId = parseInt(btn.dataset.id);
      addToCart(productId);
    });
  });

  document.querySelectorAll('.wishlist-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const productId = parseInt(btn.dataset.id);
      toggleWishlist(productId, btn);
    });
  });

  document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', () => {
      const productId = card.dataset.id;
      window.location.href = `product.html?id=${productId}`;
    });
  });
}

// ==================== CART ====================
function initCartFunctionality() {
  updateCartUI();
  if (document.querySelector('.cart-items-container')) {
    renderCartItems();
  }
}

function addToCart(productId, quantity = 1) {
  const product = products.find(p => p.id === productId);
  if (!product) return;

  const existingItem = cart.find(item => item.id === productId);
  
  if (existingItem) {
    existingItem.quantity += quantity;
  } else {
    cart.push({ ...product, quantity });
  }

  saveCart();
  updateCartCount();
  showToast(`${product.name} added to cart!`, 'success');
}

function removeFromCart(productId) {
  cart = cart.filter(item => item.id !== productId);
  saveCart();
  updateCartCount();
  renderCartItems?.();
  updateCartUI();
}

function updateCartQuantity(productId, change) {
  const item = cart.find(item => item.id === productId);
  if (item) {
    item.quantity += change;
    if (item.quantity <= 0) {
      removeFromCart(productId);
    } else {
      saveCart();
      renderCartItems?.();
      updateCartUI();
    }
  }
}

function saveCart() {
  localStorage.setItem('driddling_cart', JSON.stringify(cart));
}

function updateCartCount() {
  const cartCount = document.querySelector('.cart-count');
  if (cartCount) {
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
  }
}

function updateCartUI() {
  const cartTotal = document.querySelector('.cart-total');
  const cartSubtotal = document.querySelector('.cart-subtotal');

  if (cartTotal || cartSubtotal) {
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    if (cartTotal) cartTotal.textContent = `₹${total.toLocaleString()}`;
    if (cartSubtotal) cartSubtotal.textContent = `₹${total.toLocaleString()}`;
  }
}

function renderCartItems() {
  const container = document.querySelector('.cart-items-container');
  if (!container) return;

  if (cart.length === 0) {
    container.innerHTML = `
      <div class="empty-state">
        <div class="empty-state-icon">🛒</div>
        <h3 class="text-xl font-semibold mb-2">Your cart is empty</h3>
        <p class="text-secondary mb-6">Looks like you haven't added anything yet.</p>
        <a href="shop.html" class="btn-primary">Continue Shopping</a>
      </div>
    `;
    return;
  }

  container.innerHTML = cart.map(item => `
    <div class="cart-item" data-id="${item.id}">
      <img src="${item.image}" alt="${item.name}" class="cart-item-image">
      <div class="cart-item-details flex-1">
        <h4 class="font-semibold mb-1">${item.name}</h4>
        <p class="text-secondary text-sm mb-3">${item.category}</p>
        <div class="quantity-control">
          <button class="quantity-btn minus" data-id="${item.id}">−</button>
          <span class="quantity-value">${item.quantity}</span>
          <button class="quantity-btn plus" data-id="${item.id}">+</button>
        </div>
      </div>
      <div class="text-right">
        <p class="font-bold text-lg">₹${(item.price * item.quantity).toLocaleString()}</p>
        <button class="remove-item mt-2" data-id="${item.id}">Remove</button>
      </div>
    </div>
  `).join('');

  document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
    btn.addEventListener('click', () => updateCartQuantity(parseInt(btn.dataset.id), -1));
  });

  document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
    btn.addEventListener('click', () => updateCartQuantity(parseInt(btn.dataset.id), 1));
  });

  document.querySelectorAll('.remove-item').forEach(btn => {
    btn.addEventListener('click', () => removeFromCart(parseInt(btn.dataset.id)));
  });

  updateCartUI();
}

// ==================== WISHLIST ====================
function toggleWishlist(productId, button) {
  const index = wishlist.indexOf(productId);
  
  if (index === -1) {
    wishlist.push(productId);
    showToast('Added to wishlist!', 'success');
  } else {
    wishlist.splice(index, 1);
    showToast('Removed from wishlist', 'info');
  }

  localStorage.setItem('driddling_wishlist', JSON.stringify(wishlist));
}

// ==================== FORMS ====================
function initForms() {
  const contactForm = document.querySelector('.contact-form');
  contactForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    showToast('Message sent successfully!', 'success');
    contactForm.reset();
  });

  const newsletterForm = document.querySelector('.newsletter-form');
  newsletterForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    showToast('Thanks for subscribing!', 'success');
    newsletterForm.reset();
  });

  const checkoutForm = document.querySelector('.checkout-form');
  checkoutForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    showToast('Order placed successfully!', 'success');
    cart = [];
    saveCart();
    updateCartCount();
    window.location.href = 'index.html';
  });
}

// ==================== FAQ ====================
function initFAQ() {
  document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
      const item = question.parentElement;
      const isActive = item.classList.contains('active');

      document.querySelectorAll('.faq-item').forEach(faq => faq.classList.remove('active'));

      if (!isActive) {
        item.classList.add('active');
      }
    });
  });
}

// ==================== MARQUEE ====================
function initMarquee() {
  const marqueeContent = document.querySelector('.marquee-content');
  if (!marqueeContent) return;
  const items = marqueeContent.innerHTML;
  marqueeContent.innerHTML = items + items;
}

// ==================== COUNTERS ====================
function initCounters() {
  const counters = document.querySelectorAll('.stat-number');
  
  const animateCounter = (counter) => {
    const target = parseInt(counter.dataset.target);
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;

    const updateCounter = () => {
      current += step;
      if (current < target) {
        counter.textContent = Math.floor(current).toLocaleString();
        requestAnimationFrame(updateCounter);
      } else {
        counter.textContent = target.toLocaleString();
      }
    };

    updateCounter();
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach(counter => observer.observe(counter));
}

// ==================== TOAST ====================
function showToast(message, type = 'success') {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `
    <span class="toast-icon">${type === 'success' ? '✓' : 'ℹ'}</span>
    <span>${message}</span>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// ==================== SHOP PAGE ====================
function initShopPage() {
  if (!document.querySelector('.shop-page')) return;

  document.querySelectorAll('.filter-category').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-category').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      selectedFilters.category = btn.dataset.category;
      filterProducts();
    });
  });

  document.querySelectorAll('.filter-price').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-price').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      selectedFilters.priceRange = btn.dataset.range;
      filterProducts();
    });
  });

  document.querySelector('.sort-select')?.addEventListener('change', (e) => {
    selectedFilters.sort = e.target.value;
    filterProducts();
  });

  document.querySelector('.search-input')?.addEventListener('input', (e) => {
    filterProducts(e.target.value);
  });
}

function filterProducts(searchQuery = '') {
  let filtered = [...products];

  if (selectedFilters.category !== 'all') {
    filtered = filtered.filter(p => p.category.toLowerCase().includes(selectedFilters.category.toLowerCase()));
  }

  if (selectedFilters.priceRange !== 'all') {
    const [min, max] = selectedFilters.priceRange.split('-').map(Number);
    filtered = filtered.filter(p => p.price >= min && p.price <= max);
  }

  if (searchQuery) {
    filtered = filtered.filter(p => p.name.toLowerCase().includes(searchQuery.toLowerCase()));
  }

  switch (selectedFilters.sort) {
    case 'price-low': filtered.sort((a, b) => a.price - b.price); break;
    case 'price-high': filtered.sort((a, b) => b.price - a.price); break;
    case 'newest': filtered.reverse(); break;
    default: filtered.sort((a, b) => b.reviews - a.reviews);
  }

  renderProducts(filtered);
}

// ==================== PRODUCT DETAIL ====================
function initProductDetail() {
  if (!document.querySelector('.product-detail-page')) return;

  const params = new URLSearchParams(window.location.search);
  const productId = parseInt(params.get('id')) || 1;
  const product = products.find(p => p.id === productId);

  if (!product) return;

  document.querySelector('.product-main-image').src = product.image;
  document.querySelector('.product-title').textContent = product.name;
  document.querySelector('.product-category').textContent = product.category;
  document.querySelector('.product-price').textContent = `₹${product.price.toLocaleString()}`;
  document.querySelector('.product-description').textContent = product.description;
  
  const specsList = document.querySelector('.specs-list');
  if (specsList) {
    specsList.innerHTML = product.specs.map(spec => `<li>${spec}</li>`).join('');
  }

  const ratingContainer = document.querySelector('.product-rating');
  if (ratingContainer) {
    ratingContainer.innerHTML = `
      <span class="font-semibold">${product.rating}</span>
      <span class="text-secondary">(${product.reviews} reviews)</span>
    `;
  }

  const colorOptions = document.querySelector('.color-options');
  if (colorOptions) {
    colorOptions.innerHTML = product.colors.map((color, i) => `
      <button class="color-option ${i === 0 ? 'selected' : ''}" style="background-color: ${color}" data-color="${color}"></button>
    `).join('');

    colorOptions.querySelectorAll('.color-option').forEach(opt => {
      opt.addEventListener('click', () => {
        colorOptions.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
        opt.classList.add('selected');
      });
    });
  }

  const sizeOptions = document.querySelector('.size-options');
  if (sizeOptions) {
    sizeOptions.innerHTML = product.sizes.map((size, i) => `
      <button class="size-option ${i === 2 ? 'selected' : ''}">${size}</button>
    `).join('');

    sizeOptions.querySelectorAll('.size-option').forEach(opt => {
      opt.addEventListener('click', () => {
        sizeOptions.querySelectorAll('.size-option').forEach(o => o.classList.remove('selected'));
        opt.classList.add('selected');
      });
    });
  }

  document.querySelector('.add-to-cart-detail')?.addEventListener('click', () => {
    const quantity = parseInt(document.querySelector('.quantity-input').value) || 1;
    addToCart(product.id, quantity);
  });
}

// ==================== UTILITIES ====================
document.addEventListener('DOMContentLoaded', () => {
  initForms();
  initShopPage();
  initProductDetail();
});

window.DriddlingBD = {
  products,
  categories,
  blogPosts,
  addToCart,
  removeFromCart,
  updateCartQuantity,
  toggleWishlist,
  showToast
};