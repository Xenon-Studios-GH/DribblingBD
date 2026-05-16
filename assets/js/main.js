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
    colors: ["#FF6B00", "#3B82F6", "#00E5A8", "#FF3366"],
    sizes: ["S", "M", "L", "XL", "2XL"],
    description: "Premium quality cricket jersey with advanced moisture-wicking technology. Features breathable fabric and ergonomic design for maximum comfort on the field.",
    specs: [
      "100% Polyester fabric",
      "Moisture-wicking technology",
      "Breathable mesh panels",
      "UV protection"
    ]
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
    colors: ["#3B82F6", "#FF3366", "#FFD700", "#00E5A8"],
    sizes: ["S", "M", "L", "XL", "2XL", "3XL"],
    description: "Professional grade football jersey designed for peak performance. Features lightweight, breathable fabric with strategic ventilation zones.",
    specs: [
      "Lightweight polyester",
      "Quick-dry technology",
      "Ribbed collar and cuffs",
      "Sublimation printed graphics"
    ]
  },
  {
    id: 3,
    name: "Esports Pro Gaming Jersey",
    category: "Esports",
    price: 1799,
    originalPrice: 2299,
    image: "https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600&h=600&fit=crop",
    badge: "Hot",
    rating: 4.7,
    reviews: 156,
    colors: ["#8B5CF6", "#EC4899", "#10B981", "#F59E0B"],
    sizes: ["XS", "S", "M", "L", "XL", "2XL"],
    description: "Designed for professional esports athletes. Features anti-static fabric and ergonomic cut for long gaming sessions.",
    specs: [
      "Anti-static fabric",
      "Odor-resistant",
      "Extended sleeves for mouse movement",
      "Customizable name & number"
    ]
  },
  {
    id: 4,
    name: "NBA Star Basketball Jersey",
    category: "Basketball",
    price: 2299,
    originalPrice: 2799,
    image: "https://images.unsplash.com/photo-1546519638-68e109498ffc?w=600&h=600&fit=crop",
    badge: null,
    rating: 4.6,
    reviews: 78,
    colors: ["#FF3366", "#3B82F6", "#FFD700", "#8B5CF6"],
    sizes: ["S", "M", "L", "XL", "2XL"],
    description: "Authentic NBA-style basketball jersey with premium construction. Features breathable mesh and athletic fit.",
    specs: [
      "Double-layer mesh",
      "Reinforced stitching",
      "Athletic fit design",
      "Player name ready"
    ]
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
    colors: ["#1E3A5F", "#FFFFFF", "#000000", "#8B4513"],
    sizes: ["S", "M", "L", "XL", "2XL", "3XL"],
    description: "Elegant corporate polo shirt for business professionals. Features premium cotton blend with minimal shrinkage.",
    specs: [
      "60% Cotton, 40% Polyester",
      "Anti-shrink treatment",
      "Reinforced collar",
      "Embroidery ready"
    ]
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
    colors: ["#3B82F6", "#00E5A8", "#FF6B00", "#FF3366"],
    sizes: ["Mixed"],
    description: "Complete team uniform package for 10 players. Includes jersey, shorts, and socks. Custom branding included.",
    specs: [
      "10 complete sets",
      "Custom team logo",
      "Matching colors",
      "Size variety included"
    ]
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
    colors: ["#1E3A5F", "#FF6B00", "#8B5CF6", "#00E5A8"],
    sizes: ["S", "M", "L", "XL", "2XL"],
    description: "High-performance gym wear designed for intense workouts. Features compression fit and sweat-wicking fabric.",
    specs: [
      "Compression fit",
      "4-way stretch",
      "Sweat-wicking",
      "Reflective details"
    ]
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
    colors: ["#1E3A5F", "#FFFFFF", "#000000", "#8B4513"],
    sizes: ["XS", "S", "M", "L", "XL"],
    description: "Complete school uniform set including shirt, pants/skirt, and tie. Durable fabric for everyday wear.",
    specs: [
      "Durable poly-cotton",
      "Easy care fabric",
      "School logo placement",
      "Fade resistant"
    ]
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
  {
    id: 1,
    title: "The Evolution of Sports Jersey Technology",
    excerpt: "Discover how modern fabric technology is revolutionizing athletic performance.",
    image: "https://images.unsplash.com/photo-1517649763962-0c623066013b?w=600&h=400&fit=crop",
    date: "May 15, 2024",
    category: "Technology"
  },
  {
    id: 2,
    title: "Custom Team Jerseys: Making Your Brand Stand Out",
    excerpt: "Learn how custom jerseys can boost team identity and professionalism.",
    image: "https://images.unsplash.com/photo-1522778119026-d647f0565c6a?w=600&h=400&fit=crop",
    date: "May 12, 2024",
    category: "Business"
  },
  {
    id: 3,
    title: "Corporate Apparel: The Key to Brand Consistency",
    excerpt: "Why investing in quality corporate wear matters for your business image.",
    image: "https://images.unsplash.com/photo-1586363104862-3a5e2ab60d99?w=600&h=400&fit=crop",
    date: "May 10, 2024",
    category: "Corporate"
  },
  {
    id: 4,
    title: "Sublimation Printing: Endless Design Possibilities",
    excerpt: "Explore the benefits of sublimation printing for vibrant, long-lasting designs.",
    image: "https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600&h=400&fit=crop",
    category: "Printing"
  }
];

const reviews = [
  {
    id: 1,
    name: "Rahul Sharma",
    image: "https://randomuser.me/api/portraits/men/32.jpg",
    rating: 5,
    text: "Excellent quality! The jerseys exceeded my expectations. The fabric is premium and the printing is crisp.",
    date: "May 10, 2024"
  },
  {
    id: 2,
    name: "Priya Patel",
    image: "https://randomuser.me/api/portraits/women/44.jpg",
    rating: 5,
    text: "We ordered team uniforms for our cricket club. The customization options are amazing and delivery was fast.",
    date: "May 8, 2024"
  },
  {
    id: 3,
    name: "Amit Kumar",
    image: "https://randomuser.me/api/portraits/men/52.jpg",
    rating: 4,
    text: "Great corporate polo shirts for our office. The quality is consistent and fits perfectly.",
    date: "May 5, 2024"
  },
  {
    id: 4,
    name: "Sneha Reddy",
    image: "https://randomuser.me/api/portraits/women/68.jpg",
    rating: 5,
    text: "Amazing esports jerseys for our gaming team. The designs are professional and fabric is comfortable.",
    date: "May 3, 2024"
  }
];

// ==================== STATE ====================
let cart = JSON.parse(localStorage.getItem('driddling_cart')) || [];
let wishlist = JSON.parse(localStorage.getItem('driddling_wishlist')) || [];
let currentPage = 'home';
let selectedFilters = {
  category: 'all',
  priceRange: 'all',
  sort: 'popular'
};

// ==================== DOM ELEMENTS ====================
document.addEventListener('DOMContentLoaded', () => {
  initNavigation();
  initScrollEffects();
  initAnimations();
  initProductCards();
  initCartFunctionality();
  initModals();
  initForms();
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

  // Scroll effect
  window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
      navbar?.classList.add('scrolled');
    } else {
      navbar?.classList.remove('scrolled');
    }
  });

  // Mobile menu toggle
  mobileToggle?.addEventListener('click', () => {
    navMenu?.classList.add('active');
    document.body.style.overflow = 'hidden';
  });

  navMenuClose?.addEventListener('click', () => {
    navMenu?.classList.remove('active');
    document.body.style.overflow = '';
  });

  // Close mobile menu on link click
  document.querySelectorAll('.nav-menu .nav-link').forEach(link => {
    link.addEventListener('click', () => {
      navMenu?.classList.remove('active');
      document.body.style.overflow = '';
    });
  });

  // Cart icon count update
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
  // Cursor glow effect
  const cursorGlow = document.createElement('div');
  cursorGlow.className = 'cursor-glow';
  document.body.appendChild(cursorGlow);

  document.addEventListener('mousemove', (e) => {
    cursorGlow.style.left = e.clientX + 'px';
    cursorGlow.style.top = e.clientY + 'px';
  });

  // Hero particles
  createParticles();

  // Magnetic buttons
  initMagneticButtons();

  // Smooth scroll for anchor links
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

function createParticles() {
  const heroSection = document.querySelector('.hero-section');
  if (!heroSection) return;

  const particlesContainer = document.createElement('div');
  particlesContainer.className = 'particles';

  for (let i = 0; i < 30; i++) {
    const particle = document.createElement('div');
    particle.className = 'particle';
    particle.style.left = Math.random() * 100 + '%';
    particle.style.top = Math.random() * 100 + '%';
    particle.style.animationDelay = Math.random() * 15 + 's';
    particle.style.animationDuration = (15 + Math.random() * 10) + 's';
    
    // Vary particle colors
    const colors = ['#00E5A8', '#3B82F6', '#FF6B00'];
    particle.style.background = colors[Math.floor(Math.random() * colors.length)];
    
    particlesContainer.appendChild(particle);
  }

  heroSection.appendChild(particlesContainer);
}

function initMagneticButtons() {
  const buttons = document.querySelectorAll('.glow-button, .btn-primary');
  
  buttons.forEach(btn => {
    btn.addEventListener('mousemove', (e) => {
      const rect = btn.getBoundingClientRect();
      const x = e.clientX - rect.left - rect.width / 2;
      const y = e.clientY - rect.top - rect.height / 2;
      
      btn.style.transform = `translate(${x * 0.1}px, ${y * 0.1}px) scale(1.05)`;
    });
    
    btn.addEventListener('mouseleave', () => {
      btn.style.transform = 'translate(0, 0) scale(1)';
    });
  });
}

// ==================== PRODUCT CARDS ====================
function initProductCards() {
  // Generate product grid if container exists
  const productGrid = document.querySelector('.product-grid');
  if (productGrid) {
    renderProducts(products);
  }

  // Category cards click handler
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
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
          </button>
          <button class="action-btn quick-view-btn" data-id="${product.id}" title="Quick View">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
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

  // Attach event listeners
  attachProductListeners();
}

function attachProductListeners() {
  // Add to cart
  document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const productId = parseInt(btn.dataset.id);
      addToCart(productId);
    });
  });

  // Wishlist
  document.querySelectorAll('.wishlist-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const productId = parseInt(btn.dataset.id);
      toggleWishlist(productId, btn);
    });
  });

  // Quick view
  document.querySelectorAll('.quick-view-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const productId = parseInt(btn.dataset.id);
      openQuickView(productId);
    });
  });

  // Product card click
  document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', () => {
      const productId = card.dataset.id;
      window.location.href = `product.html?id=${productId}`;
    });
  });
}

// ==================== CART FUNCTIONALITY ====================
function initCartFunctionality() {
  updateCartUI();
  
  // Cart page specific
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
    cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
  }
}

function updateCartUI() {
  const cartTotal = document.querySelector('.cart-total');
  const cartSubtotal = document.querySelector('.cart-subtotal');
  const checkoutBtn = document.querySelector('.checkout-btn');

  if (cartTotal || cartSubtotal) {
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    if (cartTotal) cartTotal.textContent = `₹${total.toLocaleString()}`;
    if (cartSubtotal) cartSubtotal.textContent = `₹${total.toLocaleString()}`;
  }

  if (checkoutBtn) {
    checkoutBtn.disabled = cart.length === 0;
  }
}

function renderCartItems() {
  const container = document.querySelector('.cart-items-container');
  if (!container) return;

  if (cart.length === 0) {
    container.innerHTML = `
      <div class="text-center py-20">
        <div class="text-6xl mb-4">🛒</div>
        <h3 class="text-xl font-semibold mb-2">Your cart is empty</h3>
        <p class="text-gray-400 mb-6">Looks like you haven't added anything yet.</p>
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
        <p class="text-gray-400 text-sm mb-2">${item.category}</p>
        <div class="quantity-control">
          <button class="quantity-btn minus" data-id="${item.id}">−</button>
          <span class="quantity-value">${item.quantity}</span>
          <button class="quantity-btn plus" data-id="${item.id}">+</button>
        </div>
      </div>
      <div class="cart-item-price text-right">
        <p class="text-accent-neon font-bold text-xl">₹${(item.price * item.quantity).toLocaleString()}</p>
        <button class="remove-item text-gray-400 hover:text-red-500 text-sm mt-2" data-id="${item.id}">Remove</button>
      </div>
    </div>
  `).join('');

  // Attach cart item listeners
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
    button.innerHTML = `
      <svg width="20" height="20" viewBox="0 0 24 24" fill="#FF6B00" stroke="#FF6B00" stroke-width="2">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
      </svg>
    `;
  } else {
    wishlist.splice(index, 1);
    showToast('Removed from wishlist', 'info');
    button.innerHTML = `
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
      </svg>
    `;
  }

  localStorage.setItem('driddling_wishlist', JSON.stringify(wishlist));
}

// ==================== MODALS ====================
function initModals() {
  // Quick view modal
  const quickViewModal = document.getElementById('quickViewModal');
  if (quickViewModal) {
    quickViewModal.querySelector('.modal-close')?.addEventListener('click', closeQuickView);
    quickViewModal.addEventListener('click', (e) => {
      if (e.target === quickViewModal) closeQuickView();
    });
  }

  // Size guide modal
  document.querySelector('.size-guide-btn')?.addEventListener('click', () => {
    document.getElementById('sizeGuideModal')?.classList.add('active');
  });

  document.querySelector('.size-guide-modal .modal-close')?.addEventListener('click', () => {
    document.getElementById('sizeGuideModal')?.classList.remove('active');
  });
}

function openQuickView(productId) {
  const product = products.find(p => p.id === productId);
  if (!product) return;

  const modal = document.getElementById('quickViewModal');
  if (!modal) return;

  modal.querySelector('.quick-view-image').src = product.image;
  modal.querySelector('.quick-view-name').textContent = product.name;
  modal.querySelector('.quick-view-category').textContent = product.category;
  modal.querySelector('.quick-view-price').textContent = `₹${product.price.toLocaleString()}`;
  modal.querySelector('.quick-view-desc').textContent = product.description;

  // Add to cart button
  modal.querySelector('.quick-add-cart').dataset.id = product.id;

  modal.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeQuickView() {
  const modal = document.getElementById('quickViewModal');
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
}

// ==================== FORMS ====================
function initForms() {
  // Contact form
  const contactForm = document.querySelector('.contact-form');
  contactForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    showToast('Message sent successfully! We\'ll get back to you soon.', 'success');
    contactForm.reset();
  });

  // Newsletter form
  const newsletterForm = document.querySelector('.newsletter-form');
  newsletterForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    showToast('Thanks for subscribing!', 'success');
    newsletterForm.reset();
  });

  // Checkout form
  const checkoutForm = document.querySelector('.checkout-form');
  checkoutForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    showToast('Order placed successfully!', 'success');
    cart = [];
    saveCart();
    updateCartCount();
    window.location.href = 'index.html';
  });

  // Login form
  const loginForm = document.querySelector('.login-form');
  loginForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    showToast('Login successful!', 'success');
  });

  // Register form
  const registerForm = document.querySelector('.register-form');
  registerForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    showToast('Account created successfully!', 'success');
  });
}

// ==================== FAQ ====================
function initFAQ() {
  document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
      const item = question.parentElement;
      const isActive = item.classList.contains('active');

      // Close all
      document.querySelectorAll('.faq-item').forEach(faq => {
        faq.classList.remove('active');
      });

      // Toggle current
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

  // Duplicate content for seamless loop
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

// ==================== TOAST NOTIFICATIONS ====================
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
    <span class="toast-icon">${type === 'success' ? '✓' : type === 'error' ? '✕' : 'ℹ'}</span>
    <span class="toast-message">${message}</span>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = 'slideIn 0.4s ease reverse';
    setTimeout(() => toast.remove(), 400);
  }, 3000);
}

// ==================== SHOP PAGE ====================
function initShopPage() {
  if (!document.querySelector('.shop-page')) return;

  // Category filter
  document.querySelectorAll('.filter-category').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-category').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      selectedFilters.category = btn.dataset.category;
      filterProducts();
    });
  });

  // Price filter
  document.querySelectorAll('.filter-price').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-price').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      selectedFilters.priceRange = btn.dataset.range;
      filterProducts();
    });
  });

  // Sort
  document.querySelector('.sort-select')?.addEventListener('change', (e) => {
    selectedFilters.sort = e.target.value;
    filterProducts();
  });

  // Search
  document.querySelector('.search-input')?.addEventListener('input', (e) => {
    filterProducts(e.target.value);
  });
}

function filterProducts(searchQuery = '') {
  let filtered = [...products];

  // Category filter
  if (selectedFilters.category !== 'all') {
    filtered = filtered.filter(p => p.category.toLowerCase().includes(selectedFilters.category.toLowerCase()));
  }

  // Price filter
  if (selectedFilters.priceRange !== 'all') {
    const [min, max] = selectedFilters.priceRange.split('-').map(Number);
    filtered = filtered.filter(p => p.price >= min && p.price <= max);
  }

  // Search
  if (searchQuery) {
    filtered = filtered.filter(p => p.name.toLowerCase().includes(searchQuery.toLowerCase()));
  }

  // Sort
  switch (selectedFilters.sort) {
    case 'price-low':
      filtered.sort((a, b) => a.price - b.price);
      break;
    case 'price-high':
      filtered.sort((a, b) => b.price - a.price);
      break;
    case 'newest':
      filtered.reverse();
      break;
    case 'popular':
    default:
      filtered.sort((a, b) => b.reviews - a.reviews);
  }

  renderProducts(filtered);
}

// ==================== PRODUCT DETAIL PAGE ====================
function initProductDetail() {
  if (!document.querySelector('.product-detail-page')) return;

  const params = new URLSearchParams(window.location.search);
  const productId = parseInt(params.get('id')) || 1;
  const product = products.find(p => p.id === productId);

  if (!product) return;

  // Update page content
  document.querySelector('.product-main-image').src = product.image;
  document.querySelector('.product-title').textContent = product.name;
  document.querySelector('.product-category').textContent = product.category;
  document.querySelector('.product-price').textContent = `₹${product.price.toLocaleString()}`;
  document.querySelector('.product-description').textContent = product.description;
  
  // Specs
  const specsList = document.querySelector('.specs-list');
  if (specsList) {
    specsList.innerHTML = product.specs.map(spec => `<li>${spec}</li>`).join('');
  }

  // Rating
  const ratingContainer = document.querySelector('.product-rating');
  if (ratingContainer) {
    ratingContainer.innerHTML = `
      <span class="text-accent-neon font-bold">${product.rating}</span>
      <span class="text-yellow-400">${'★'.repeat(Math.floor(product.rating))}</span>
      <span class="text-gray-400">(${product.reviews} reviews)</span>
    `;
  }

  // Color selection
  const colorOptions = document.querySelector('.color-options');
  if (colorOptions) {
    colorOptions.innerHTML = product.colors.map((color, i) => `
      <button class="color-option ${i === 0 ? 'selected' : ''}" 
              style="background-color: ${color}" 
              data-color="${color}"></button>
    `).join('');

    colorOptions.querySelectorAll('.color-option').forEach(opt => {
      opt.addEventListener('click', () => {
        colorOptions.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
        opt.classList.add('selected');
      });
    });
  }

  // Size selection
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

  // Add to cart
  document.querySelector('.add-to-cart-detail')?.addEventListener('click', () => {
    const quantity = parseInt(document.querySelector('.quantity-input').value) || 1;
    addToCart(product.id, quantity);
  });

  // Gallery thumbnails
  const galleryThumbs = document.querySelector('.gallery-thumbs');
  if (galleryThumbs) {
    galleryThumbs.innerHTML = `
      <button class="gallery-thumb active"><img src="${product.image}" alt=""></button>
      <button class="gallery-thumb"><img src="${product.image}" alt=""></button>
      <button class="gallery-thumb"><img src="${product.image}" alt=""></button>
    `;

    galleryThumbs.querySelectorAll('.gallery-thumb').forEach(thumb => {
      thumb.addEventListener('click', () => {
        galleryThumbs.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
        document.querySelector('.product-main-image').src = thumb.querySelector('img').src;
      });
    });
  }
}

// ==================== CUSTOM JERSEY CONFIGURATOR ====================
function initJerseyConfigurator() {
  if (!document.querySelector('.jersey-configurator')) return;

  // Color selection
  document.querySelectorAll('.color-swatch').forEach(swatch => {
    swatch.addEventListener('click', () => {
      document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('selected'));
      swatch.classList.add('selected');
      updateJerseyPreview();
    });
  });

  // Pattern selection
  document.querySelectorAll('.pattern-option').forEach(pattern => {
    pattern.addEventListener('click', () => {
      document.querySelectorAll('.pattern-option').forEach(p => p.classList.remove('selected'));
      pattern.classList.add('selected');
      updateJerseyPreview();
    });
  });

  // Name and number input
  document.querySelector('.player-name')?.addEventListener('input', updateJerseyPreview);
  document.querySelector('.player-number')?.addEventListener('input', updateJerseyPreview);
}

function updateJerseyPreview() {
  const selectedColor = document.querySelector('.color-swatch.selected')?.style.backgroundColor;
  const selectedPattern = document.querySelector('.pattern-option.selected')?.dataset.pattern;
  const name = document.querySelector('.player-name')?.value || 'YOUR NAME';
  const number = document.querySelector('.player-number')?.value || '10';

  // Update preview (simplified - would need actual jersey template)
  const preview = document.querySelector('.jersey-preview-name');
  const previewNumber = document.querySelector('.jersey-preview-number');

  if (preview) preview.textContent = name.toUpperCase();
  if (previewNumber) previewNumber.textContent = number;
}

// ==================== UTILITY FUNCTIONS ====================
function formatPrice(price) {
  return `₹${price.toLocaleString()}`;
}

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Export for global access
window.DriddlingBD = {
  products,
  categories,
  blogPosts,
  reviews,
  addToCart,
  removeFromCart,
  updateCartQuantity,
  toggleWishlist,
  showToast,
  formatPrice
};

// Initialize shop page if on shop
document.addEventListener('DOMContentLoaded', () => {
  initShopPage();
  initProductDetail();
  initJerseyConfigurator();
});