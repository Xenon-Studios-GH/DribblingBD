    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
        }
      }
    }
  
    //
    const SUPABASE_URL = 'https://xldjecdcljjgthecpsmh.supabase.co';
    const SUPABASE_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InhsZGplY2RjbGpqZ3RoZWNwc21oIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Nzc2NjQ2NjAsImV4cCI6MjA5MzI0MDY2MH0.qbXM3lPQZHpXG7D60FBltNBZM_rg_FRHU3nP7Jl-3wI';          // anon public key (safe for frontend)
    let MAIL_SCRIPT_URL = 'https://script.google.com/macros/s/AKfycbxrelCPCImsjvqpenMat3_AmAxBXgkUGsmpIkb6CuXcPrOpkBNUU1T57jOVOfjK8LKE/exec'; // email-only Apps Script

    // ---- Supabase client (lazy-initialized) ----
    let sb = null;

    function initSupabase() {
      if (sb) return true;
      if (!SUPABASE_URL || !SUPABASE_URL.includes('supabase.co')) return false;
      try {
        sb = window.supabase.createClient(SUPABASE_URL, SUPABASE_KEY, {
          auth: { persistSession: false },
          global: { headers: {} }
        });
        return true;
      } catch (e) {
        console.error('Supabase init error:', e);
        return false;
      }
    }

    // ================================================================
    // CUSTOM DROPDOWN COMPONENT (EKDropdown)
    // ================================================================
    class EKDropdown {
      static instances = {};
      static openInstance = null;

      constructor(config) {
        this.id = config.id;
        this.options = config.options || []; // [{value, label, icon?}]
        this.value = config.value || (this.options.length ? this.options[0].value : '');
        this.placeholder = config.placeholder || 'Select...';
        this.onChange = config.onChange || null;
        this.searchable = config.searchable || false;
        this.variant = config.variant || ''; // 'sm', 'sort', 'dark'
        this.upward = config.upward || false;
        this.triggerStyle = config.triggerStyle || '';
        this.el = null;
        this.isOpen = false;
        this.searchTerm = '';
        EKDropdown.instances[this.id] = this;
      }

      render() {
        const selected = this.options.find(o => o.value === this.value);
        const selectedLabel = selected ? selected.label : this.placeholder;
        const selectedIcon = selected && selected.icon ? selected.icon : '';
        const isPlaceholder = !selected;

        const variantClass = this.variant ? ` ek-dropdown-${this.variant}` : '';
        const upwardClass = this.upward ? ' upward' : '';

        return `<div class="ek-dropdown${variantClass}" id="ekdd-${this.id}" data-ekdd="${this.id}">
                    <button type="button" class="ek-dropdown-trigger${this.isOpen ? ' open' : ''}" onclick="EKDropdown.toggle('${this.id}')" style="${this.triggerStyle}">
                        ${selectedIcon ? `<span class="ek-dd-selected-icon">${selectedIcon}</span>` : ''}
                        <span class="ek-dd-text${isPlaceholder ? ' placeholder' : ''}">${selectedLabel}</span>
                        <i class="fa-solid fa-chevron-down ek-dd-icon"></i>
                    </button>
                    <div class="ek-dropdown-panel${upwardClass}${this.isOpen ? ' open' : ''}">
                        ${this.searchable ? `<div class="ek-dropdown-search"><input type="text" placeholder="Search..." oninput="EKDropdown.onSearch('${this.id}', this.value)"></div>` : ''}
                        <div class="ek-dropdown-options">${this._renderOptions()}</div>
                    </div>
                </div>`;
      }

      _renderOptions(filter = '') {
        const filtered = filter
          ? this.options.filter(o => o.label.toLowerCase().includes(filter.toLowerCase()))
          : this.options;
        if (filtered.length === 0) {
          return '<div class="ek-dropdown-empty">No results found</div>';
        }
        return filtered.map(o => {
          const isSelected = o.value === this.value;
          return `<div class="ek-dropdown-option${isSelected ? ' selected' : ''}" onclick="event.stopPropagation(); EKDropdown.select('${this.id}', '${o.value}')">
                        ${o.icon ? `<span class="ek-dd-opt-icon">${o.icon}</span>` : ''}
                        <span class="ek-dd-opt-label">${o.label}</span>
                        <i class="fa-solid fa-check ek-dd-opt-check"></i>
                    </div>`;
        }).join('');
      }

      mount(container) {
        if (typeof container === 'string') {
          container = document.getElementById(container);
        }
        if (!container) return;
        container.innerHTML = this.render();
        this.el = document.getElementById(`ekdd-${this.id}`);
      }

      refresh() {
        if (!this.el) return;
        this.el.outerHTML = this.render();
        this.el = document.getElementById(`ekdd-${this.id}`);
      }

      setValue(val) {
        this.value = val;
        this.refresh();
        if (this.onChange) this.onChange(val);
      }

      getValue() {
        return this.value;
      }

      updateOptions(newOptions, keepValue) {
        this.options = newOptions;
        if (!keepValue || !this.options.find(o => o.value === this.value)) {
          this.value = this.options.length ? this.options[0].value : '';
        }
        this.refresh();
      }

      open() {
        if (EKDropdown.openInstance && EKDropdown.openInstance !== this) {
          EKDropdown.openInstance.close();
        }
        this.isOpen = true;
        EKDropdown.openInstance = this;
        this.refresh();
        // Focus search if searchable
        if (this.searchable) {
          setTimeout(() => {
            const searchInput = this.el?.querySelector('.ek-dropdown-search input');
            if (searchInput) searchInput.focus();
          }, 50);
        }
      }

      close() {
        this.isOpen = false;
        this.searchTerm = '';
        if (EKDropdown.openInstance === this) EKDropdown.openInstance = null;
        this.refresh();
      }

      static toggle(id) {
        const dd = EKDropdown.instances[id];
        if (!dd) return;
        if (dd.isOpen) dd.close();
        else dd.open();
      }

      static select(id, value) {
        const dd = EKDropdown.instances[id];
        if (!dd) return;
        dd.value = value;
        dd.close();
        if (dd.onChange) dd.onChange(value);
      }

      static onSearch(id, term) {
        const dd = EKDropdown.instances[id];
        if (!dd) return;
        dd.searchTerm = term;
        const optionsEl = dd.el?.querySelector('.ek-dropdown-options');
        if (optionsEl) optionsEl.innerHTML = dd._renderOptions(term);
      }

      static closeAll() {
        Object.values(EKDropdown.instances).forEach(dd => dd.close());
      }

      static get(id) {
        return EKDropdown.instances[id];
      }
    }

    // Close dropdowns on outside click
    document.addEventListener('click', function (e) {
      if (EKDropdown.openInstance && !e.target.closest('.ek-dropdown')) {
        EKDropdown.openInstance.close();
      }
    });

    // Close dropdowns on Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && EKDropdown.openInstance) {
        EKDropdown.openInstance.close();
      }
    });

    // ---- Mail API helper (Apps Script POST) ----
    async function mailPost(data) {
      if (!MAIL_SCRIPT_URL) return null;
      try {
        const res = await fetch(MAIL_SCRIPT_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'text/plain' },
          body: JSON.stringify(data)
        });
        return await res.json();
      } catch (e) { console.error('Mail API error:', e); return null; }
    }

    // ---- Fallback local data when Supabase is not configured ----
    const LOCAL_PRODUCTS = [
      { id: 1, name: "Brazil 2026 'Yellow Gold' Elite", category: ['wc26'], price: 1250, oldPrice: 1650, badge: 'Hot', rating: 4.9, reviews: 342, img: '/products/brazil-2026.webp', desc: 'Designed for the ultimate football enthusiast, the Brazil 2026 Elite Jersey features high-performance mesh ventilation and moisture-wicking technology.', features: ['AeroMesh ventilation zones', 'Moisture-wicking DriFit fabric', 'Embroidered federation badge', 'Heat-pressed name set compatible'], active: true, images: [], sizes: ['S', 'M', 'L', 'XL', 'XXL'] },
      { id: 2, name: 'Argentina Three Star 2026', category: ['wc26'], price: 1350, oldPrice: 1800, badge: 'Limited', rating: 5.0, reviews: 518, img: '/products/argentina-2026.webp', desc: 'Celebrate the legacy of the World Champions with the Argentina 2026 Three-Star Jersey.', features: ['Lightweight AEROREADY fabric', 'Three-star embroidered crest', 'Seamless shoulder construction', 'UV protection UPF 50+'], active: true, images: [], sizes: ['S', 'M', 'L', 'XL', 'XXL'] },
      { id: 3, name: 'Real Madrid 26/27 Third', category: ['clubs'], price: 950, oldPrice: 1200, badge: 'New', rating: 4.8, reviews: 127, img: '/products/realmadrid-third.webp', desc: 'The Real Madrid 26/27 Third Jersey brings a modern twist to a legendary club.', features: ['Player-grade HEAT.RDY tech', 'Reflective heat-transfer details', 'Adidas branding & crest', 'Drop-tail hem coverage'], active: true, images: [], sizes: ['S', 'M', 'L', 'XL', 'XXL'] },
      { id: 4, name: "France 'Bleu' 2026 Pro", category: ['wc26'], price: 1100, oldPrice: 1500, badge: 'Bestseller', rating: 4.7, reviews: 289, img: '/products/france-2026.webp', desc: 'Elegance meets performance in the France 2026 Pro Jersey.', features: ['Dual-layer moisture management', 'Precision-embroidered rooster crest', 'Tapered athletic fit', 'Anti-odor silver ion treatment'], active: true, images: [], sizes: ['S', 'M', 'L', 'XL', 'XXL'] },
      { id: 5, name: 'Germany 2026 Home White', category: ['wc26'], price: 1050, oldPrice: 1400, badge: 'Popular', rating: 4.8, reviews: 198, img: '/products/germany-2026.webp', desc: 'The Germany 2026 Home Jersey returns to classic white with black and gold accents.', features: ['HERITAGE white with gold trim', 'Integrated shoulder ventilation', 'Four-way stretch fabric', 'FIFA Quality Pro certified'], active: true, images: [], sizes: ['S', 'M', 'L', 'XL', 'XXL'] },
      { id: 6, name: 'Barcelona 26/27 Blaugrana', category: ['clubs'], price: 1100, oldPrice: 1400, badge: 'New', rating: 4.8, reviews: 201, img: '/products/barcelona-2627.webp', desc: 'The Barcelona 26/27 Blaugrana Jersey represents the spirit of the Catalan club.', features: ['Iconic blaugrana colors', 'Lightweight AEROREADY fabric', 'Seamless construction', 'FIFA Quality Pro certified'], active: true, images: [], sizes: ['S', 'M', 'L', 'XL', 'XXL'] },
      { id: 7, name: 'Portugal 2026 Away Kit', category: ['wc26'], price: 1150, oldPrice: 1550, badge: 'Premium', rating: 4.6, reviews: 156, img: '/products/portugal-2026.webp', desc: 'The Portugal 2026 Away Jersey showcases a bold red design with green and gold detailing.', features: ['Azulejo-inspired texture pattern', 'Premium Portuguese fabric', 'Cristiano Ronaldo #7 compatible', 'Gold-foil federation badge'], active: true, images: [], sizes: ['S', 'M', 'L', 'XL', 'XXL'] },
      { id: 8, name: 'Manchester United 26/27', category: ['clubs'], price: 1000, oldPrice: 1300, badge: 'Classic', rating: 4.7, reviews: 234, img: '/products/manutd-2627.webp', desc: 'The Manchester United 26/27 Home Jersey pays homage to the Red Devils.', features: ['Iconic red devil design', 'Raised embroidery crest', 'AEROREADY moisture control', 'Old Trafford exclusive detailing'], active: true, images: [], sizes: ['S', 'M', 'L', 'XL', 'XXL'] },
      { id: 9, name: 'Japan 2026 Sakura Edition', category: ['wc26'], price: 1200, oldPrice: 1600, badge: 'Exclusive', rating: 4.9, reviews: 89, img: '/products/japan-2026.webp', desc: 'The Japan 2026 Sakura Edition Jersey is a masterpiece of design and culture.', features: ['Cherry blossom woven pattern', 'Color-shift indigo fabric', 'JFA premium crest', 'Limited edition numbering'], active: true, images: [], sizes: ['S', 'M', 'L', 'XL', 'XXL'] },
      { id: 10, name: 'PSG 26/27 Parisien', category: ['clubs'], price: 980, oldPrice: 1280, badge: 'Trending', rating: 4.5, reviews: 312, img: '/products/psg-2627.webp', desc: 'The Paris Saint-Germain 26/27 Home Jersey embodies Parisian elegance.', features: ['Jordan Brand collaboration', 'Eiffel Tower neck detail', 'Heat-transfer club crest', 'Tour-de-France stripe accents'], active: true, images: [], sizes: ['S', 'M', 'L', 'XL', 'XXL'] },
      { id: 11, name: 'Liverpool 26/27 Anfield', category: ['clubs'], price: 1050, oldPrice: 1380, badge: 'Hot', rating: 4.8, reviews: 267, img: '/products/liverpool-2627.webp', desc: 'The Liverpool 26/27 Home Jersey captures the passion of Anfield.', features: ['Deepened Anfield red shade', 'Kop-inspired geometric pattern', 'Nike DriFit ADV technology', 'YNWA embroidery detail'], active: true, images: [], sizes: ['S', 'M', 'L', 'XL', 'XXL'] },
      { id: 12, name: 'AC Milan 26/27 Rossoneri', category: ['clubs'], price: 1020, oldPrice: 1350, badge: 'Heritage', rating: 4.7, reviews: 178, img: '/products/acmilan-2627.webp', desc: 'The AC Milan 26/27 Home Jersey celebrates the Rossoneri legacy.', features: ['Iconic Rossoneri stripes', 'PUMA dryCELL technology', 'San Siro inner neck print', 'Authentic Italian craftsmanship'], active: true, images: [], sizes: ['S', 'M', 'L', 'XL', 'XXL'] },
    ];

    const CATEGORIES = [
      { id: 'all', name: 'Home', icon: 'fa-solid fa-layer-group' },
      { id: 'wc26', name: 'World Cup 2026', icon: 'fa-solid fa-trophy' },
      { id: 'clubs', name: 'Club Edition', icon: 'fa-solid fa-shield-halved' },
      { id: 'player', name: 'Player Edition', icon: 'fa-solid fa-user-ninja' },
      { id: 'fan', name: 'Fan Edition', icon: 'fa-solid fa-users' },
    ];

    let BANNERS = [
      { id: 1, title: "", subtitle: '', img: '/hero-bg.webp', category: 'all' },
      { id: 2, title: "", subtitle: '', img: '/b2.webp', category: 'wc26' },
      { id: 3, title: '', subtitle: '', img: '/hero-banner.webp', category: 'wc26' },
    ];

    // ==================== STATE ====================
    let PRODUCTS = [];
    let cart = [];
    let currentView = 'home';
    let currentCategory = 'all';
    let searchQuery = '';
    let selectedProductId = null;
    let selectedSize = '';
    let activeCoupon = null;
    let currentBannerIdx = 0;
    let flashSales = [];
    let activeFlashSale = null;
    let flashCountdownInterval = null;
    let backendConnected = false;
    let deliveryZone = 'outside';
    let storeSettings = { storeName: 'DribblingBD', whatsappNumber: '8801577078101', freeShippingThreshold: 3000, dhakaCharge: 80, outsideCharge: 120 };
    let CATEGORY_IMAGES = {};
    let quickAddSelectedSize = {};
    let currentZoomObserver = null;

    // ===== WISHLIST SYSTEM =====
    let wishlist = [];

    function loadWishlistFromStorage() {
      try {
        const saved = localStorage.getItem('ek_wishlist');
        if (saved) wishlist = JSON.parse(saved);
      } catch (e) { wishlist = []; }
    }

    function saveWishlistToStorage() {
      try {
        localStorage.setItem('ek_wishlist', JSON.stringify(wishlist));
      } catch (e) { console.warn('Wishlist save failed:', e); }
    }

    function isInWishlist(productId) {
      return wishlist.includes(productId);
    }

    function toggleWishlist(productId, event) {
      if (event) { event.stopPropagation(); event.preventDefault(); }
      const idx = wishlist.indexOf(productId);
      if (idx > -1) {
        wishlist.splice(idx, 1);
        addToast('Removed from wishlist', 'info');
      } else {
        wishlist.push(productId);
        addToast('Added to wishlist!', 'success');
      }
      saveWishlistToStorage();
      updateWishlistBadge();
      // Animate the heart that was clicked
      if (event) {
        const heartEl = event.currentTarget || event.target.closest('.wishlist-heart');
        if (heartEl) {
          heartEl.classList.remove('pop');
          void heartEl.offsetWidth; // force reflow
          heartEl.classList.add('pop');
          // Update the heart icon state
          if (isInWishlist(productId)) {
            heartEl.classList.add('active');
          } else {
            heartEl.classList.remove('active');
          }
        }
      }
      // Re-render product grid to update all heart states
      if (currentView === 'home') renderProducts();
    }

    function updateWishlistBadge() {
      const badge = document.getElementById('wishlistCount');
      if (badge) {
        if (wishlist.length > 0) {
          badge.style.display = 'flex';
          badge.textContent = wishlist.length;
        } else {
          badge.style.display = 'none';
        }
      }
    }

    function renderWishlist() {
      const container = document.getElementById('wishlist-container');
      if (!container) return;
      const wishlistProducts = PRODUCTS.filter(p => wishlist.includes(p.id));
      if (wishlistProducts.length === 0) {
        container.innerHTML = `
            <div class="max-w-7xl mx-auto px-4 lg:px-8 py-20 text-center">
                <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400 mb-10 justify-center"><button onclick="goHome()" class="hover:text-black transition-colors cursor-pointer">Home</button><i class="fa-solid fa-chevron-right text-[8px]"></i><span class="text-black">Wishlist</span></nav>
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-8"><i class="fa-regular fa-heart text-4xl text-gray-200"></i></div>
                <h2 class="text-3xl lg:text-5xl italic-black uppercase tracking-tighter mb-4">Your Wishlist is Empty</h2>
                <p class="text-gray-400 text-sm font-medium max-w-md mx-auto mb-8">Save your favorite jerseys here so you can easily find them later.</p>
                <button onclick="goHome()" class="bg-black text-white px-10 py-4 rounded-full font-black uppercase text-[11px] tracking-widest hover:bg-[#8cc63f] hover:text-black transition-all cursor-pointer">Browse Collection</button>
            </div>`;
        return;
      }
      container.innerHTML = `
        <div class="max-w-7xl mx-auto px-4 lg:px-8 py-8 lg:py-12">
            <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400 mb-10"><button onclick="goHome()" class="hover:text-black transition-colors cursor-pointer">Home</button><i class="fa-solid fa-chevron-right text-[8px]"></i><span class="text-black">Wishlist</span></nav>
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl lg:text-5xl italic-black uppercase tracking-tighter">My Wishlist</h1>
                    <p class="text-gray-400 text-[11px] font-bold uppercase tracking-widest mt-2">${wishlistProducts.length} item${wishlistProducts.length !== 1 ? 's' : ''} saved</p>
                </div>
                <button onclick="clearWishlist()" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-red-500 transition-colors cursor-pointer"><i class="fa-solid fa-trash-can mr-1"></i> Clear All</button>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-8">
                ${wishlistProducts.map(p => {
        const effectivePrice = getEffectivePrice(p);
        const dp = getEffectiveDiscount(p);
        const isFlashProduct = getFlashPrice(p.id) !== null;
        return `<div class="group cursor-pointer" onclick="showProductDetail(${p.id})">
                    <div class="aspect-[3/4] rounded-[1.5rem] lg:rounded-[2rem] overflow-hidden bg-gray-50 mb-4 relative border border-gray-100 group-hover:shadow-2xl transition-all duration-300">
                        <img src="${p.img}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" onerror="handleImgError(this,'${p.name.replace(/'/g, "\\'")}')">
                        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                            ${isFlashProduct ? '<span class="bg-red-600 text-white text-[8px] lg:text-[9px] font-black px-2.5 py-1 uppercase tracking-widest">⚡ Flash</span>' : `<span class="bg-black text-white text-[8px] lg:text-[9px] font-black px-2.5 py-1 uppercase tracking-widest">${p.badge}</span>`}
                            ${dp >= 20 ? `<span class="bg-red-600 text-white text-[8px] lg:text-[9px] font-black px-2.5 py-1 uppercase tracking-widest">-${dp}%</span>` : ''}
                        </div>
                        <div class="wishlist-heart active" onclick="toggleWishlist(${p.id}, event)"><i class="fa-regular fa-heart"></i><i class="fa-solid fa-heart"></i></div>
                    </div>
                    <h3 class="text-[11px] lg:text-[12px] font-black uppercase tracking-tight mb-1.5 truncate group-hover:text-[#8cc63f] transition-colors">${p.name}</h3>
                    <div class="flex items-center gap-2 lg:gap-3"><span class="text-lg lg:text-2xl font-black italic ${isFlashProduct ? 'text-red-600' : ''}">&#2547;${effectivePrice}</span><span class="text-[10px] lg:text-[11px] text-gray-300 line-through font-bold">&#2547;${p.oldPrice}</span></div>
                    <button onclick="event.stopPropagation();openWishlistMoveModal(${p.id})" class="mt-3 w-full bg-black text-white py-2.5 rounded-xl font-black uppercase text-[9px] tracking-widest hover:bg-[#8cc63f] hover:text-black transition-all cursor-pointer"><i class="fa-solid fa-bag-shopping mr-1"></i> Move to Bag</button>
                </div>`;
      }).join('')}
            </div>
        </div>`;
    }

    function clearWishlist() {
      if (wishlist.length === 0) return;
      wishlist = [];
      saveWishlistToStorage();
      updateWishlistBadge();
      renderWishlist();
      addToast('Wishlist cleared', 'info');
    }

    function quickAddToCartFromWishlist(productId) {
      openWishlistMoveModal(productId);
    }

    // Wishlist Move to Bag Modal
    let wishlistMoveProductId = null;
    let wishlistMoveSelectedSize = '';
    let wishlistMoveQuantity = 1;

    function openWishlistMoveModal(productId) {
      const p = PRODUCTS.find(x => x.id === productId);
      if (!p) return;
      wishlistMoveProductId = productId;
      wishlistMoveSelectedSize = '';
      wishlistMoveQuantity = 1;

      const sizes = (p.sizes && p.sizes.length > 0 ? p.sizes : ['S', 'M', 'L', 'XL', 'XXL']);

      document.getElementById('wishlistMoveProductInfo').innerHTML = `
                <img src="${p.img}" class="w-16 h-16 rounded-xl object-cover border border-gray-100" onerror="handleImgError(this,'')">
                <div>
                    <p class="font-black uppercase text-sm">${p.name}</p>
                    <p class="text-lg font-black italic mt-1 ${getFlashPrice(p.id) !== null ? 'text-red-600' : ''}">&#2547;${getEffectivePrice(p)}</p>
                </div>`;

      document.getElementById('wishlistMoveSizes').innerHTML = sizes.map(s =>
        `<button onclick="selectWishlistMoveSize('${s}')" data-wmsize="${s}" class="h-10 px-4 rounded-xl font-black border-2 transition-all cursor-pointer text-sm bg-white border-gray-100 hover:border-black wishlist-move-size-btn">${s}</button>`
      ).join('');

      document.getElementById('wishlistMoveQty').textContent = '1';
      document.getElementById('wishlistMoveModal').style.display = 'flex';
    }

    function closeWishlistMoveModal(event) {
      if (event && event.target !== event.currentTarget) return;
      document.getElementById('wishlistMoveModal').style.display = 'none';
      wishlistMoveProductId = null;
    }

    function selectWishlistMoveSize(size) {
      wishlistMoveSelectedSize = size;
      document.querySelectorAll('.wishlist-move-size-btn').forEach(btn => {
        if (btn.dataset.wmsize === size) {
          btn.classList.remove('bg-white', 'border-gray-100', 'hover:border-black');
          btn.classList.add('bg-black', 'text-white', 'border-black');
        } else {
          btn.classList.remove('bg-black', 'text-white', 'border-black');
          btn.classList.add('bg-white', 'border-gray-100', 'hover:border-black');
        }
      });
    }

    function changeWishlistMoveQty(delta) {
      wishlistMoveQuantity = Math.max(1, wishlistMoveQuantity + delta);
      document.getElementById('wishlistMoveQty').textContent = wishlistMoveQuantity;
    }

    function confirmWishlistMove() {
      if (!wishlistMoveProductId) return;
      if (!wishlistMoveSelectedSize) {
        addToast('Please select a size', 'error');
        return;
      }
      const p = PRODUCTS.find(x => x.id === wishlistMoveProductId);
      if (!p) return;
      const key = wishlistMoveProductId + '-' + wishlistMoveSelectedSize;
      const existing = cart.find(i => i.key === key);
      if (existing) {
        existing.qty += wishlistMoveQuantity;
        addToast(p.name + ' (' + wishlistMoveSelectedSize + ') quantity updated', 'info');
      } else {
        cart.push({ key, id: wishlistMoveProductId, name: p.name, img: p.img, size: wishlistMoveSelectedSize, price: getEffectivePrice(p), qty: wishlistMoveQuantity });
        addToast(p.name + ' (' + wishlistMoveSelectedSize + ') added to bag!', 'success');
      }
      // Remove from wishlist
      const idx = wishlist.indexOf(wishlistMoveProductId);
      if (idx > -1) { wishlist.splice(idx, 1); saveWishlistToStorage(); updateWishlistBadge(); }
      saveCartToStorage();
      updateCartBadge();
      closeWishlistMoveModal();
      renderWishlist();
      toggleCart(true);
    }

    // Category display helpers
    const CATEGORY_MAP = {
      wc26: 'World Cup 2026',
      clubs: 'Club Edition',
      player: 'Player Edition',
      fan: 'Fan Edition',
    };

    function getCategoryNames(categories) {
      const cats = Array.isArray(categories) ? categories : (categories ? [categories] : []);
      return cats.map(c => CATEGORY_MAP[c] || c).join(' / ');
    }

    function getCategoryFirst(categories) {
      const cats = Array.isArray(categories) ? categories : (categories ? [categories] : []);
      return cats[0] || 'all';
    }

    // ==================== SEO URL ROUTING ====================
    const BASE_URL = 'https://dribblingbd.com';

    function generateSlug(name) {
      return name.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
    }
    function getProductBySlug(slug) { return PRODUCTS.find(p => generateSlug(p.name) === slug); }
    function updateCanonicalURL() {
      const canonicalUrl = 'https://dribblingbd.com' + window.location.pathname;
      const c = document.getElementById('canonicalLink');
      if (c) c.href = canonicalUrl;
      const u = document.querySelector('meta[property="og:url"]');
      if (u) u.content = canonicalUrl;
    }
    function updateMetaTitle(title) { document.title = title + ' | DribblingBD'; }
    function updatePageMeta(title, description) {
      document.title = title + ' | DribblingBD';
      let metaDesc = document.querySelector('meta[name="description"]');
      if (!metaDesc) { metaDesc = document.createElement('meta'); metaDesc.name = 'description'; document.head.appendChild(metaDesc); }
      metaDesc.content = description;
      setOGTag('og:title', title + ' | DribblingBD');
      setOGTag('og:description', description);
      setOGTag('og:url', 'https://dribblingbd.com' + window.location.pathname);
    }
    function setOGTag(property, content) {
      let tag = document.querySelector('meta[property="' + property + '"]');
      if (!tag) { tag = document.createElement('meta'); tag.setAttribute('property', property); document.head.appendChild(tag); }
      tag.content = content;
    }
    function injectProductSchema(p) {
      const existing = document.getElementById('dynamicProductSchema');
      if (existing) existing.remove();
      const allImages = [p.img, ...(p.images || [])].filter(Boolean);
      const schema = {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": p.name,
        "description": p.desc,
        "image": allImages,
        "sku": "DB-" + p.id,
        "brand": { "@type": "Brand", "name": "DribblingBD" },
        "offers": {
          "@type": "Offer",
          "url": "https://dribblingbd.com/product/" + generateSlug(p.name),
          "priceCurrency": "BDT",
          "price": getEffectivePrice(p),
          "availability": "https://schema.org/InStock",
          "seller": { "@type": "Organization", "name": "DribblingBD" }
        },
        "aggregateRating": {
          "@type": "AggregateRating",
          "ratingValue": p.rating || 4.5,
          "reviewCount": p.reviews || 1
        }
      };
      const script = document.createElement('script');
      script.type = 'application/ld+json';
      script.id = 'dynamicProductSchema';
      script.textContent = JSON.stringify(schema);
      document.head.appendChild(script);
    }

    function handleURLRouting() {
      const path = window.location.pathname.replace(/\/+$/, '');
      const segments = path.split('/').filter(Boolean);
      const urlParams = new URLSearchParams(window.location.search);
      
      if (segments[0] === 'product' && segments[1]) {
        const p = getProductBySlug(segments[1]);
        if (p) {
          selectedProductId = p.id;
          selectedSize = '';
          navigateTo('product');
          renderProductDetail();
          injectProductSchema(p);
          updatePageMeta(p.name, 'Buy ' + p.name + ' in Bangladesh. ' + p.desc.substring(0, 140) + '. Price: \u09F3' + getEffectivePrice(p) + '. Free shipping on orders over \u09F33000. Shop now at DribblingBD.');
          updateCanonicalURL();
          return;
        }
      }
      const policyPages = ['size-guide', 'shipping-policy', 'return-exchange', 'privacy-policy', 'terms-of-service'];
      if (policyPages.includes(segments[0])) {
        openPolicyPage(segments[0], true);
        updateCanonicalURL();
        return;
      }
      if (segments[0] === 'shop') {
        currentCategory = segments[1] || 'all';
        selectedProductId = null; searchQuery = '';
        document.getElementById('desktopSearch').value = '';
        document.getElementById('searchClearBtn').classList.add('hidden');
        navigateTo('home'); renderHome();
        const catName = (CATEGORIES.find(c => c.id === currentCategory) || {}).name || 'Shop';
        updatePageMeta(catName + ' Jerseys', 'Browse premium ' + catName.toLowerCase() + ' jerseys in Bangladesh. Authentic quality, fast delivery. Shop at DribblingBD.');
        updateCanonicalURL();
        return;
      }
      if (segments[0] === 'checkout') { navigateTo('checkout'); renderCheckout(); updateCanonicalURL(); return; }
      if (segments[0] === 'wishlist') { navigateTo('wishlist'); renderWishlist(); updateCanonicalURL(); return; }
      if (segments[0] === 'order-confirmed') { navigateTo('success'); updateCanonicalURL(); return; }
      goHome();
    }
    function pushURL(params) {
      let url;
      if (params.product) {
        url = '/product/' + params.product;
      } else if (params.view && params.view !== 'home') {
        const policyPages = ['size-guide', 'shipping-policy', 'return-exchange', 'privacy-policy', 'terms-of-service'];
        if (policyPages.includes(params.view)) {
          url = '/' + params.view;
        } else if (params.view === 'shop') {
          url = currentCategory !== 'all' ? '/shop/' + currentCategory : '/shop';
        } else if (params.view === 'checkout') {
          url = '/checkout';
        } else if (params.view === 'wishlist') {
          url = '/wishlist';
        } else if (params.view === 'success') {
          url = '/order-confirmed';
        } else {
          url = '/';
        }
      } else {
        url = '/';
      }
      history.pushState({}, '', url);
      updateCanonicalURL();
    }
    window.addEventListener('popstate', function () { handleURLRouting(); });

    // ==================== TOAST ====================
    function addToast(message, type = 'info') {
      const container = document.getElementById('toastContainer');
      const icons = { success: 'fa-solid fa-check-circle', error: 'fa-solid fa-exclamation-circle', info: 'fa-solid fa-info-circle' };
      const colors = { success: 'bg-[#8cc63f] text-black', error: 'bg-red-600 text-white', info: 'bg-black text-white' };
      const id = 'toast-' + Date.now();
      const el = document.createElement('div');
      el.id = id;
      el.className = `pointer-events-auto flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl toast-animate max-w-sm cursor-pointer ${colors[type]}`;
      el.onclick = () => { el.classList.remove('toast-animate'); el.classList.add('toast-animate-out'); setTimeout(() => el.remove(), 250); };
      el.innerHTML = `<i class="${icons[type]} text-sm"></i><span class="text-[11px] font-black uppercase tracking-wider">${message}</span><i class="fa-solid fa-xmark text-[10px] ml-2 opacity-60"></i>`;
      container.appendChild(el);
      setTimeout(() => { const t = document.getElementById(id); if (t) { t.classList.remove('toast-animate'); t.classList.add('toast-animate-out'); setTimeout(() => t.remove(), 250); } }, 3250);
    }

    // ==================== NAVIGATION ====================
    function navigateTo(view) {
      // Stop coupon polling when leaving checkout
      if (currentView === 'checkout' && view !== 'checkout') {
        stopCouponPolling();
      }
      currentView = view;
      document.querySelectorAll('.view-section').forEach(v => v.classList.remove('active'));
      document.getElementById('view-' + view).classList.add('active');
      window.scrollTo({ top: 0, behavior: 'smooth' });
      // Update bottom nav active state
      if (view === 'home') bnSetActive('home');
      else if (view === 'wishlist') bnSetActive('wishlist');
      else if (view === 'checkout') bnSetActive('bag');
      else bnSetActive(null);
    }
    function bnSetActive(tab) {
      document.querySelectorAll('.bottom-nav-item').forEach(i => i.classList.remove('active'));
      if (tab) { const item = document.querySelector('[data-bn="' + tab + '"]'); if (item) item.classList.add('active'); }
    }
    function navigateToWithURL(view, params) { navigateTo(view); pushURL(params); }
    function goHome() {
      currentCategory = 'all'; searchQuery = ''; selectedProductId = null; selectedSize = '';
      document.getElementById('desktopSearch').value = ''; document.getElementById('searchClearBtn').classList.add('hidden');
      navigateTo('home'); renderHome(); pushURL(null);
      updateMetaTitle('DribblingBD – Premium Jersey Shop in Bangladesh');
    }
    function filterByCategory(catId) {
      currentCategory = catId; selectedProductId = null; searchQuery = '';
      document.getElementById('desktopSearch').value = ''; document.getElementById('searchClearBtn').classList.add('hidden');
      navigateTo('home'); renderHome(); pushURL({ view: 'shop' });
      const catName = (CATEGORIES.find(c => c.id === catId) || {}).name || 'Shop';
      updateMetaTitle(catName + ' Jerseys – DribblingBD');
    }
    function handleSearch(val) {
      searchQuery = val.toLowerCase();
      selectedProductId = null;
      // Sync both search inputs
      document.getElementById('desktopSearch').value = val;
      document.getElementById('mobileSearchInput').value = val;
      const clearBtn = document.getElementById('searchClearBtn');
      const searchIcon = document.getElementById('searchIconBtn');
      if (val) {
        clearBtn.classList.remove('hidden');
        if (searchIcon) searchIcon.classList.add('hidden');
      } else {
        clearBtn.classList.add('hidden');
        if (searchIcon) searchIcon.classList.remove('hidden');
      }

      if (val.trim()) {
        currentCategory = 'all';
      }

      currentView = 'home';
      document.querySelectorAll('.view-section').forEach(v => v.classList.remove('active'));
      document.getElementById('view-home').classList.add('active');
      renderHome();
    }
    function clearSearch() {
      document.getElementById('desktopSearch').value = '';
      document.getElementById('mobileSearchInput') && (document.getElementById('mobileSearchInput').value = '');
      document.getElementById('searchClearBtn').classList.add('hidden');
      const searchIcon = document.getElementById('searchIconBtn');
      if (searchIcon) searchIcon.classList.remove('hidden');
      searchQuery = ''; selectedProductId = null; renderHome();
    }
    let detailQuantity = 1;

    function changeDetailQty(delta) {
      detailQuantity = Math.max(1, detailQuantity + delta);
      const el = document.getElementById('detailQty');
      if (el) el.textContent = detailQuantity;
    }

    function showProductDetail(id) {
      selectedProductId = id; selectedSize = '';
      detailQuantity = 1;
      navigateTo('product'); renderProductDetail();
      const p = PRODUCTS.find(x => x.id === id);
      if (p) { pushURL({ product: generateSlug(p.name) }); updateMetaTitle(p.name); }
    }

    // ==================== CART ====================
    function addToCart(id, buyNow) {
      if (!selectedSize) { addToast('Please select a size first', 'error'); return; }
      const p = PRODUCTS.find(x => x.id === id); if (!p) return;
      const key = id + '-' + selectedSize;
      const existing = cart.find(i => i.key === key);
      const qty = detailQuantity || 1;
      const effectivePrice = getEffectivePrice(p);
      if (existing) { existing.qty += qty; addToast(p.name + ' (' + selectedSize + ') quantity updated', 'info'); }
      else { cart.push({ key, id, name: p.name, img: p.img, size: selectedSize, price: effectivePrice, qty }); addToast(p.name + ' added to bag!', 'success'); }
      saveCartToStorage();
      updateCartBadge();
      // Meta Pixel: Track AddToCart event
      if (typeof fbq === 'function') {
        fbq('track', 'AddToCart', {
          content_name: p.name,
          content_ids: [p.id],
          content_type: 'product',
          value: getEffectivePrice(p),
          currency: 'BDT'
        });
      }
      if (buyNow) { navigateTo('checkout'); renderCheckout(); pushURL({ view: 'checkout' }); }
      else toggleCart(true);
    }
    function updateCartQty(key, qty) {
      if (qty < 1) { cart = cart.filter(i => i.key !== key); addToast('Item removed from bag', 'info'); }
      else { const item = cart.find(i => i.key === key); if (item) item.qty = qty; }
      saveCartToStorage();
      renderCart(); updateCartBadge();
      if (currentView === 'checkout') renderCheckout();
    }
    function removeFromCart(key) { cart = cart.filter(i => i.key !== key); addToast('Item removed from bag', 'info'); renderCart(); updateCartBadge(); saveCartToStorage(); if (currentView === 'checkout') renderCheckout(); }
    function clearCart() { cart = []; activeCoupon = null; saveCartToStorage(); updateCartBadge(); }
    function getSubtotal() { return cart.reduce((a, i) => a + i.price * i.qty, 0); }
    function getCartCount() { return cart.reduce((a, i) => a + i.qty, 0); }
    function getDiscount() { return activeCoupon ? Math.round(getSubtotal() * activeCoupon.percent / 100) : 0; }
    function getDeliveryCharge() {
      const subtotal = getSubtotal();
      if (subtotal >= storeSettings.freeShippingThreshold) return 0;
      return deliveryZone === 'dhaka' ? storeSettings.dhakaCharge : storeSettings.outsideCharge;
    }
    function getTotal() { return getSubtotal() - getDiscount() + getDeliveryCharge(); }
    function autoDetectDeliveryZone(district) {
      const d = district.toLowerCase().trim();
      deliveryZone = d.includes('dhaka') ? 'dhaka' : 'outside';
      updateCartBadge();
      if (currentView === 'checkout') {
        const delivery = getDeliveryCharge(), total = getSubtotal() - getDiscount() + delivery;
        const delEl = document.getElementById('checkoutDelivery'), totEl = document.getElementById('checkoutTotal');
        if (delEl) delEl.innerHTML = delivery === 0 ? '<span class="text-[#8cc63f]">FREE</span>' : '&#2547;' + delivery;
        if (totEl) totEl.innerHTML = '&#2547;' + total;
      }
    }

    // ===== BANGLADESH DISTRICTS (All 64) =====
    const BD_DISTRICTS = [
      'Bagerhat', 'Bandarban', 'Barguna', 'Barishal', 'Bhola', 'Bogra', 'Brahmanbaria', 'Chandpur',
      'Chattogram', 'Chuadanga', 'Comilla', 'Cox\'s Bazar', 'Cumilla', 'Dhaka', 'Dinajpur', 'Faridpur',
      'Feni', 'Gaibandha', 'Gazipur', 'Gopalganj', 'Habiganj', 'Jamalpur', 'Jashore', 'Jhalokati',
      'Jhenaidah', 'Joypurhat', 'Khagrachhari', 'Khulna', 'Kishoreganj', 'Kurigram', 'Kushtia', 'Lakshmipur',
      'Lalmonirhat', 'Madaripur', 'Magura', 'Manikganj', 'Meherpur', 'Moulvibazar', 'Munshiganj', 'Mymensingh',
      'Naogaon', 'Narail', 'Narayanganj', 'Narsingdi', 'Natore', 'Nawabganj', 'Netrokona', 'Nilphamari',
      'Noakhali', 'Pabna', 'Panchagarh', 'Patuakhali', 'Pirojpur', 'Rajbari', 'Rajshahi', 'Rangamati',
      'Rangpur', 'Satkhira', 'Shariatpur', 'Sherpur', 'Sirajganj', 'Sunamganj', 'Sylhet', 'Tangail', 'Thakurgaon'
    ];
    const BD_DISTRICT_OPTIONS = BD_DISTRICTS.map(d => ({
      value: d,
      label: d,
      icon: d.toLowerCase() === 'dhaka' ? '<i class="fa-solid fa-location-dot" style="color:#8cc63f"></i>' : '<i class="fa-solid fa-map-pin" style="color:#999"></i>'
    }));

    // Initialize checkout district dropdown after renderCheckout
    function initCheckoutDistrictDropdown() {
      const container = document.getElementById('checkoutDistrictContainer');
      if (!container) return;
      // Remove existing instance if any
      if (EKDropdown.get('checkoutDistrict')) {
        delete EKDropdown.instances['checkoutDistrict'];
      }
      new EKDropdown({
        id: 'checkoutDistrict',
        value: '',
        placeholder: 'Select District *',
        options: BD_DISTRICT_OPTIONS,
        searchable: true,
        triggerStyle: 'width:100%;padding:16px;border-radius:12px;border:1px solid #f3f4f6;background:#f9fafb;font-size:14px;font-weight:700;',
        onChange: function (val) {
          const hiddenInput = document.getElementById('fdistrict');
          if (hiddenInput) hiddenInput.value = val;
          autoDetectDeliveryZone(val);
          // Reset error state
          const trigger = document.querySelector('#ekdd-checkoutDistrict .ek-dropdown-trigger');
          if (trigger) { trigger.style.borderColor = ''; trigger.style.boxShadow = ''; }
        }
      }).mount('checkoutDistrictContainer');
    }

    // ===== IMAGE LIGHTBOX =====
    let lightboxImages = [];
    let lightboxIndex = 0;

    function openLightbox(images, startIndex) {
      lightboxImages = images || [];
      lightboxIndex = startIndex || 0;
      if (lightboxImages.length === 0) return;
      const overlay = document.getElementById('ekLightbox');
      const img = document.getElementById('ekLightboxImg');
      const counter = document.getElementById('ekLightboxCounter');
      if (!overlay || !img) return;
      img.src = lightboxImages[lightboxIndex];
      if (counter) counter.textContent = (lightboxIndex + 1) + ' / ' + lightboxImages.length;
      // Show/hide nav arrows
      const prevBtn = overlay.querySelector('.ek-lightbox-nav.prev');
      const nextBtn = overlay.querySelector('.ek-lightbox-nav.next');
      if (prevBtn) prevBtn.style.display = lightboxImages.length > 1 ? 'flex' : 'none';
      if (nextBtn) nextBtn.style.display = lightboxImages.length > 1 ? 'flex' : 'none';
      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
      const overlay = document.getElementById('ekLightbox');
      if (overlay) overlay.classList.remove('active');
      document.body.style.overflow = '';
    }

    function lightboxNav(dir) {
      if (lightboxImages.length <= 1) return;
      lightboxIndex = (lightboxIndex + dir + lightboxImages.length) % lightboxImages.length;
      const img = document.getElementById('ekLightboxImg');
      const counter = document.getElementById('ekLightboxCounter');
      if (img) {
        img.style.opacity = '0';
        img.style.transform = 'scale(0.95)';
        setTimeout(() => {
          img.src = lightboxImages[lightboxIndex];
          img.style.opacity = '1';
          img.style.transform = 'scale(1)';
        }, 150);
      }
      if (counter) counter.textContent = (lightboxIndex + 1) + ' / ' + lightboxImages.length;
    }

    // Keyboard support for lightbox
    document.addEventListener('keydown', function (e) {
      const overlay = document.getElementById('ekLightbox');
      if (!overlay || !overlay.classList.contains('active')) return;
      if (e.key === 'Escape') closeLightbox();
      else if (e.key === 'ArrowLeft') lightboxNav(-1);
      else if (e.key === 'ArrowRight') lightboxNav(1);
    });
    function updateCartBadge() {
      const badge = document.getElementById('cartCount'), badgeMobile = document.getElementById('cartCountMobile');
      const subEl = document.getElementById('cartSubtotal');
      const count = getCartCount(), subtotal = getSubtotal();
      if (count > 0) {
        if (badge) { badge.innerText = count; badge.classList.add('visible'); }
        if (badgeMobile) { badgeMobile.innerText = count; badgeMobile.classList.add('visible'); }
      } else {
        if (badge) badge.classList.remove('visible');
        if (badgeMobile) badgeMobile.classList.remove('visible');
      }
      if (subEl) subEl.innerHTML = '&#2547;' + subtotal.toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      // Bottom nav cart badge
      const bnCartBadge = document.getElementById('bnCartBadge');
      if (bnCartBadge) {
        if (count > 0) { bnCartBadge.innerText = count; bnCartBadge.classList.add('visible'); }
        else { bnCartBadge.classList.remove('visible'); }
      }
      // Bottom nav wishlist badge
      const bnWishBadge = document.getElementById('bnWishlistBadge');
      if (bnWishBadge) {
        const wc = wishlist.length;
        if (wc > 0) { bnWishBadge.innerText = wc; bnWishBadge.classList.add('visible'); }
        else { bnWishBadge.classList.remove('visible'); }
      }
    }
    // Bottom navbar navigation
    function bnGo(tab) {
      document.querySelectorAll('.bottom-nav-item').forEach(i => i.classList.remove('active'));
      const item = document.querySelector('[data-bn="' + tab + '"]');
      if (item) item.classList.add('active');
      if (tab === 'home') { filterByCategory('all'); }
      else if (tab === 'search') { toggleMobileSearch(true); }
      else if (tab === 'wishlist') { navigateTo('wishlist'); renderWishlist(); pushURL({ view: 'wishlist' }); }
      else if (tab === 'bag') { toggleCart(true); }
      else if (tab === 'menu') { toggleMobileMenu(true); }
    }
    // Show/hide bottom nav based on view
    function showBottomNav(show) {
      const bn = document.getElementById('bottomNav');
      if (bn) { if (show) bn.classList.add('active'); else bn.classList.remove('active'); }
    }
    function toggleCart(show) {
      const overlay = document.getElementById('cartDrawerOverlay'), drawer = document.getElementById('cartDrawer');
      if (show) { overlay.classList.remove('invisible'); overlay.classList.add('opacity-100'); drawer.classList.remove('translate-x-full'); renderCart(); document.body.classList.add('scroll-locked'); }
      else { overlay.classList.add('invisible'); overlay.classList.remove('opacity-100'); drawer.classList.add('translate-x-full'); document.body.classList.remove('scroll-locked'); }
    }
    function toggleMobileMenu(show) {
      const overlay = document.getElementById('mobileMenuOverlay'), drawer = document.getElementById('mobileMenu');
      if (show) { overlay.classList.remove('invisible'); overlay.classList.add('opacity-100'); drawer.classList.remove('-translate-x-full'); renderMobileMenu(); document.body.classList.add('scroll-locked'); }
      else { overlay.classList.add('invisible'); overlay.classList.remove('opacity-100'); drawer.classList.add('-translate-x-full'); document.body.classList.remove('scroll-locked'); }
    }
    function toggleMobileSearch(show) { const o = document.getElementById('mobileSearchOverlay'); if (show && !o.classList.contains('active')) { o.classList.add('active'); document.getElementById('mobileSearchInput').focus(); document.body.classList.add('scroll-locked'); } else { o.classList.remove('active'); document.body.classList.remove('scroll-locked'); } }

    // ==================== COUPON (Supabase) ====================
    async function applyCoupon(code) {
      const subtotal = getSubtotal();
      if (code.toUpperCase() === 'DRIBBLE10') { activeCoupon = { code: 'DRIBBLE10', percent: 10 }; addToast('Coupon DRIBBLE10 applied! 10% off', 'success'); saveCartToStorage(); return true; }
      activeCoupon = null; addToast('Invalid coupon code', 'error'); return false;
    }
    function removeCoupon() {
      activeCoupon = null;
      addToast('Coupon removed', 'info');
      saveCartToStorage();
      if (currentView === 'checkout') renderCheckout();
    }
    async function validateActiveCoupon() {
      return true;
    }
    let couponValidationInterval = null;
    let couponRealtimeChannel = null;

    async function validateActiveCoupon() {
      if (!activeCoupon) return true;
      if (!backendConnected || !sb) return true;

      try {
        const { data: coupon, error } = await sb.from('coupons')
          .select('*')
          .eq('code', activeCoupon.code)
          .eq('active', true)
          .single();

        // Coupon was deleted or deactivated
        if (error || !coupon) {
          addToast('Invalid coupon', 'error');
          activeCoupon = null;
          saveCartToStorage();
          updateCartBadge();
          if (currentView === 'checkout') renderCheckout();
          if (currentView === 'home' || currentView === 'product') renderCart();
          return false;
        }

        // Coupon usage limit reached
        if (coupon.max_uses > 0 && coupon.used_count >= coupon.max_uses) {
          addToast('Coupon "' + activeCoupon.code + '" usage limit reached', 'error');
          activeCoupon = null;
          saveCartToStorage();
          updateCartBadge();
          if (currentView === 'checkout') renderCheckout();
          if (currentView === 'home' || currentView === 'product') renderCart();
          return false;
        }

        return true;
      } catch (e) {
        console.error('Coupon validation error:', e);
        return true; // Don't remove on network error
      }
    }

    function startCouponPolling() {
      stopCouponPolling();
      couponValidationInterval = setInterval(validateActiveCoupon, 15000); // Every 15 seconds
    }

    function stopCouponPolling() {
      if (couponValidationInterval) {
        clearInterval(couponValidationInterval);
        couponValidationInterval = null;
      }
    }

    function setupCouponRealtime() {
      if (!backendConnected || !sb || couponRealtimeChannel) return;

      try {
        couponRealtimeChannel = sb
          .channel('coupon-changes-' + Date.now())
          .on('postgres_changes', {
            event: '*',
            schema: 'public',
            table: 'coupons'
          }, (payload) => {
            if (!activeCoupon) return;

            const updated = payload.new;
            const deleted = payload.eventType === 'DELETE';

            // Check if this is our active coupon
            const couponCode = deleted
              ? (payload.old && payload.old.code)
              : (updated && updated.code);

            if (couponCode !== activeCoupon.code) return;

            // Admin deleted the coupon
            if (deleted) {
              addToast('Invalid coupon', 'error');
              activeCoupon = null;
              saveCartToStorage();
              updateCartBadge();
              if (currentView === 'checkout') renderCheckout();
              if (currentView === 'home' || currentView === 'product') renderCart();
              return;
            }

            // Admin deactivated it
            if (!updated.active) {
              addToast('Invalid coupon', 'error');
              activeCoupon = null;
              saveCartToStorage();
              updateCartBadge();
              if (currentView === 'checkout') renderCheckout();
              if (currentView === 'home' || currentView === 'product') renderCart();
              return;
            }

            // Usage limit reached
            if (updated.max_uses > 0 && updated.used_count >= updated.max_uses) {
              addToast('Coupon "' + activeCoupon.code + '" usage limit reached!', 'error');
              activeCoupon = null;
              saveCartToStorage();
              updateCartBadge();
              if (currentView === 'checkout') renderCheckout();
              if (currentView === 'home' || currentView === 'product') renderCart();
              return;
            }
          })
          .subscribe((status) => {
            if (status === 'SUBSCRIBED') {
              console.log('[DribblingBD] Coupon realtime connected');
            }
          });
      } catch (e) {
        console.warn('Coupon realtime setup failed:', e);
      }
    }


    function openSizeGuide() { document.getElementById('sizeGuideModal').classList.remove('hidden'); document.getElementById('sizeGuideModal').classList.add('flex'); }
    function closeSizeGuide() { document.getElementById('sizeGuideModal').classList.add('hidden'); document.getElementById('sizeGuideModal').classList.remove('flex'); }

    // ==================== PRODUCT LOADING (Supabase) ====================
    function showProductSkeleton() {
      const grid = document.getElementById('product-grid'); if (!grid) return;
      grid.innerHTML = Array(8).fill('').map(() => '<div class="animate-pulse"><div class="aspect-[3/4] rounded-[1.5rem] lg:rounded-[2rem] skeleton-shimmer mb-4 lg:mb-6 border border-gray-100"></div><div class="h-3 skeleton-shimmer rounded-full w-3/4 mb-2"></div><div class="flex gap-2"><div class="h-4 skeleton-shimmer rounded-full w-16"></div><div class="h-3 skeleton-shimmer rounded-full w-12 mt-1"></div></div></div>').join('');
    }

    function showLoadingOverlay(text = 'Processing...') {
      let overlay = document.getElementById('globalLoadingOverlay');
      if (!overlay) {
        overlay = document.createElement('div'); overlay.id = 'globalLoadingOverlay';
        overlay.style.cssText = 'position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);display:none;align-items:center;justify-content:center;';
        overlay.innerHTML = '<div style="background:#fff;border-radius:1.5rem;padding:2rem 3rem;text-align:center;box-shadow:0 25px 50px rgba(0,0,0,0.25)"><div style="width:48px;height:48px;border:4px solid #e5e7eb;border-top-color:#8cc63f;border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 1rem"></div><p id="globalLoadingText" style="font-size:13px;font-weight:700;color:#333"></p></div>';
        document.body.appendChild(overlay);
      }
      document.getElementById('globalLoadingText').textContent = text; overlay.style.display = 'flex';
    }
    function hideLoadingOverlay() { const o = document.getElementById('globalLoadingOverlay'); if (o) o.style.display = 'none'; }

    async function loadProducts() {
      showProductSkeleton();
      backendConnected = initSupabase();
      if (backendConnected) {
        try {
          // Load settings and products in parallel
          const [productsRes, settingsRes] = await Promise.all([
            sb.from('products').select('*').eq('active', true).order('id'),
            sb.from('settings').select('*').eq('key', 'store_settings').single()
          ]);
          // Parse settings
          if (settingsRes.data) {
            const s = settingsRes.data.value;
            storeSettings.freeShippingThreshold = Number(s.freeShippingThreshold) || 3000;
            storeSettings.dhakaCharge = Number(s.dhakaCharge) || 80;
            storeSettings.outsideCharge = Number(s.outsideCharge) || 120;
            storeSettings.storeName = s.storeName || 'DribblingBD';
            storeSettings.whatsappNumber = s.whatsappNumber || '8801577078101';
          }
          // Load mail script URL from Supabase settings
          try {
            const mailRes = await sb.from('settings').select('value').eq('key', 'mail_script_url').single();
            if (mailRes && mailRes.data && mailRes.data.value) {
              const savedUrl = typeof mailRes.data.value === 'string' ? mailRes.data.value.replace(/"/g, '') : '';
              if (savedUrl && savedUrl.startsWith('https://')) MAIL_SCRIPT_URL = savedUrl;
            }
          } catch (mailErr) { console.warn('Mail URL load failed:', mailErr); }
          // Parse products — normalize array fields
          if (productsRes.data && productsRes.data.length > 0) {
            PRODUCTS = productsRes.data.map(p => ({
              ...p,
              category: Array.isArray(p.category) ? p.category : (p.category ? [p.category] : []),
              images: p.images || [],
              features: p.features || [],
              sizes: p.sizes || ['S', 'M', 'L', 'XL', 'XXL'],
              price: Number(p.price),
              oldPrice: Number(p.old_price),
              rating: Number(p.rating) || 4.5,
              reviews: Number(p.reviews) || 0
            }));
            return;
          }
        } catch (e) { console.error('Supabase load error:', e); backendConnected = false; }
      }
      PRODUCTS = LOCAL_PRODUCTS;
    }

    // ==================== RENDERING (UNCHANGED) ====================
    function renderNav() {
      const nav = document.getElementById('desktopCatNav'); if (!nav) return;
      nav.innerHTML = CATEGORIES.map((c, i) => `${i > 0 ? '<div class="nav-cat-sep"></div>' : ''}<button onclick="filterByCategory('${c.id}')" class="nav-cat-item ${currentCategory === c.id ? 'active' : ''}">${c.name}</button>`).join('');
    }

    function renderBanner() {
      if (BANNERS.length === 0) {
        document.getElementById('banner-container').innerHTML = '<div class="absolute inset-0 cursor-pointer" onclick="filterByCategory(\'all\')"><img src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=1200&q=80" class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display=\'none\'"><div class="absolute inset-0 flex flex-col justify-center px-6 lg:px-16"><span class="text-[#8cc63f] text-[9px] lg:text-xs font-black tracking-[0.4em] uppercase mb-2 lg:mb-4">DRIBBLINGBD</span><h2 class="text-xl lg:text-5xl xl:text-6xl italic-black leading-[0.85] mb-4 lg:mb-8 max-w-3xl uppercase text-white">Premium Jerseys</h2><p class="text-gray-300 text-xs lg:text-sm font-bold uppercase tracking-widest max-w-md">Authentic Thai-grade football apparel.</p></div></div>';
        return;
      }
      const b = BANNERS[currentBannerIdx];
      document.getElementById('banner-container').innerHTML = `
    <div onclick="filterByCategory('${b.category}')" class="absolute inset-0 cursor-pointer">
        <img src="${b.img}" class="absolute inset-0 w-full h-full object-cover transition-all duration-1000 ease-in-out" onerror="this.style.display='none'">
        <div class="absolute inset-0 flex flex-col justify-center px-6 lg:px-16 text-white">
            <span class="text-[#8cc63f] text-[9px] lg:text-xs font-black tracking-[0.4em] uppercase mb-2 lg:mb-4">${b.subtitle}</span>
            <h2 class="text-xl lg:text-5xl xl:text-6xl italic-black leading-[0.85] mb-4 lg:mb-8 max-w-3xl uppercase">${b.title}</h2>
        </div>
    </div>
    ${BANNERS.length > 1 ? `<div class="absolute bottom-4 right-6 lg:bottom-6 lg:right-12 flex gap-2 lg:gap-3 z-10">${BANNERS.map((_, i) => `<button onclick="event.stopPropagation(); currentBannerIdx=${i}; renderBanner()" class="w-2.5 h-2.5 lg:w-3 lg:h-3 rounded-full transition-all cursor-pointer ${i === currentBannerIdx ? 'bg-white w-6 lg:w-8' : 'bg-white/40 hover:bg-white/60'}"></button>`).join('')}</div>` : ''}`;
    }

    function renderProducts() {
      const sortVal = EKDropdown.get('sortSelect') ? EKDropdown.get('sortSelect').getValue() : 'default';
      let filtered = PRODUCTS.filter(p => {
        const mc = currentCategory === 'all' || (Array.isArray(p.category) ? p.category.includes(currentCategory) : p.category === currentCategory);
        const ms = p.name.toLowerCase().includes(searchQuery) || (Array.isArray(p.category) ? p.category.join(' ').toLowerCase() : p.category.toLowerCase()).includes(searchQuery);
        return mc && ms;
      });
      if (sortVal === 'price-asc') filtered.sort((a, b) => a.price - b.price);
      else if (sortVal === 'price-desc') filtered.sort((a, b) => b.price - a.price);
      const title = currentCategory === 'all' ? 'Elite Drops' : (CATEGORIES.find(c => c.id === currentCategory) || {}).name || 'Elite Drops';
      document.getElementById('grid-title').innerText = title;
      document.getElementById('productCount').innerText = filtered.length + ' product' + (filtered.length !== 1 ? 's' : '') + ' found';
      const grid = document.getElementById('product-grid');
      if (filtered.length === 0) {
        grid.innerHTML = `<div class="col-span-full text-center py-20"><div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6"><i class="fa-solid fa-magnifying-glass text-3xl text-gray-300"></i></div><h3 class="text-2xl font-black uppercase tracking-tight mb-3">No Products Found</h3><p class="text-gray-400 text-sm font-medium mb-8 max-w-sm mx-auto">We could not find any kits matching your search.</p><button onclick="goHome()" class="bg-black text-white px-8 py-4 rounded-full font-black uppercase text-[11px] tracking-widest hover:bg-[#8cc63f] hover:text-black transition-all cursor-pointer">View All Kits</button></div>`;
      } else {
        grid.innerHTML = filtered.map((p, i) => {
          const effectivePrice = getEffectivePrice(p);
          const dp = getEffectiveDiscount(p);
          const isFlashProduct = getFlashPrice(p.id) !== null;
          const sizes = (p.sizes && p.sizes.length > 0 ? p.sizes : ['S', 'M', 'L', 'XL', 'XXL']);
          const selSize = quickAddSelectedSize[p.id] || '';
          return `<div class="group cursor-pointer" onclick="showProductDetail(${p.id})" data-product-slug="${generateSlug(p.name)}">
                <div class="aspect-[3/4] rounded-[1.5rem] lg:rounded-[2rem] overflow-hidden bg-gray-50 mb-4 lg:mb-6 relative border border-gray-100 group-hover:shadow-2xl transition-all duration-300">
                    <img src="${p.img}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" onerror="handleImgError(this,'${p.name.replace(/'/g, "\\'")}')">
                    <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                        ${isFlashProduct ? '<span class="bg-red-600 text-white text-[8px] lg:text-[9px] font-black px-2.5 py-1 uppercase tracking-widest">⚡ Flash</span>' : `<span class="bg-black text-white text-[8px] lg:text-[9px] font-black px-2.5 py-1 uppercase tracking-widest">${p.badge}</span>`}
                        ${dp >= 20 ? `<span class="bg-red-600 text-white text-[8px] lg:text-[9px] font-black px-2.5 py-1 uppercase tracking-widest">-${dp}%</span>` : ''}
                    </div>
                    <div class="wishlist-heart ${isInWishlist(p.id) ? 'active' : ''}" onclick="toggleWishlist(${p.id}, event)"><i class="fa-regular fa-heart"></i><i class="fa-solid fa-heart"></i></div>
                    <div class="quick-add-overlay" onclick="event.stopPropagation()">
                        <div class="quick-add-sizes">${sizes.map(s => `<button onclick="event.stopPropagation();selectQuickAddSize(${p.id},'${s}')" class="quick-add-size-btn ${selSize === s ? 'selected' : ''}">${s}</button>`).join('')}</div>
                        <button onclick="event.stopPropagation();quickAddToCart(${p.id})" class="quick-add-bag-btn" ${!selSize ? 'disabled' : ''}><i class="fa-solid fa-bag-shopping mr-1"></i> ${selSize ? 'Add to Bag (' + selSize + ')' : 'Select Size'}</button>
                    </div>
                </div>
                <h3 class="text-[11px] lg:text-[12px] font-black uppercase tracking-tight mb-1.5 lg:mb-2 truncate group-hover:text-[#8cc63f] transition-colors">${p.name}</h3>
                <div class="flex items-center gap-2 lg:gap-3"><span class="text-lg lg:text-2xl font-black italic ${isFlashProduct ? 'text-red-600' : ''}">&#2547;${effectivePrice}</span><span class="text-[10px] lg:text-[11px] text-gray-300 line-through font-bold">&#2547;${p.oldPrice}</span></div>
            </div>`;
        }).join('');
      }
    }

    function renderProductDetail() {
      const container = document.getElementById('product-detail-container'); if (!container) return;
      const p = PRODUCTS.find(x => x.id === selectedProductId);
      if (!p && PRODUCTS.length > 0) { container.innerHTML = `<div class="max-w-7xl mx-auto px-4 lg:px-8 py-20 text-center"><h2 class="text-3xl font-black uppercase mb-4">Product Not Found</h2><button onclick="goHome()" class="bg-black text-white px-8 py-4 rounded-full font-black uppercase text-[11px] tracking-widest hover:bg-[#8cc63f] hover:text-black transition-all cursor-pointer">Back to Shop</button></div>`; return; }
      if (!p) { container.innerHTML = '<div class="max-w-7xl mx-auto px-4 lg:px-8 py-32 text-center"><div class="skeleton-pulse"><div class="h-8 bg-gray-100 rounded-xl w-64 mx-auto mb-8"></div></div></div>'; return; }
      const catArray = Array.isArray(p.category) ? p.category : [p.category];
      const catName = catArray.map(c => {
        return CATEGORY_MAP[c] || c;
      }).join(' / ');
      const effectivePrice = getEffectivePrice(p);
      const dp = getEffectiveDiscount(p);
      const isFlashProduct = getFlashPrice(p.id) !== null;
      const similar = PRODUCTS.filter(x => {
        const xCats = Array.isArray(x.category) ? x.category : [x.category];
        return xCats.some(c => catArray.includes(c)) && x.id !== p.id;
      }).slice(0, 4);
      const allImages = [p.img, ...(p.images || [])].filter((img, i, arr) => img && arr.indexOf(img) === i);
      const hasMultipleImages = allImages.length > 1;
      currentGalleryImages = allImages;
      container.innerHTML = `
        <div class="max-w-7xl mx-auto px-4 lg:px-8 py-6 lg:py-10">
            <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400 mb-8 flex-wrap">
                <button onclick="goHome()" class="hover:text-black transition-colors cursor-pointer">Home</button><i class="fa-solid fa-chevron-right text-[8px]"></i>
                <button onclick="filterByCategory('${catArray[0] || 'all'}')" class="hover:text-black transition-colors cursor-pointer">${catName}</button><i class="fa-solid fa-chevron-right text-[8px]"></i>
                <span class="text-black truncate max-w-[200px]">${p.name}</span>
            </nav>
            <div class="grid lg:grid-cols-12 gap-8 lg:gap-20">
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-[2rem] lg:rounded-[3rem] p-6 lg:p-14 border border-gray-100 aspect-square relative shadow-sm">
                        <div class="gallery-main w-full h-full relative" id="galleryMain" onclick="openLightbox(currentGalleryImages, currentGalleryIndex || 0)" style="cursor:zoom-in">
                            <img src="${allImages[0] || p.img}" id="mainProductImg" class="main-product-img w-full h-full object-contain" alt="${p.name}" onerror="handleImgError(this,'${p.name.replace(/'/g, "\\'")}')">
                            ${dp >= 20 ? `<div class="absolute top-4 right-4 bg-red-600 text-white text-[11px] font-black px-3 py-1.5 rounded-full uppercase tracking-wider z-20">-${dp}% Off</div>` : ''}
                            <div class="enlarge-hint"><i class="fa-solid fa-expand mr-1"></i> Tap to Enlarge</div>
                        </div>
                        ${hasMultipleImages ? `<div class="gallery-thumbs" id="galleryThumbs">${allImages.map((img, i) => `<div class="gallery-thumb ${i === 0 ? 'active' : ''}" onclick="switchGalleryImage(${i})"><img src="${img}" alt="View ${i + 1}" onerror="handleImgError(this,'Image ${i + 1}')"></div>`).join('')}</div>` : ''}
                    </div>
                </div>
                <div class="lg:col-span-5 flex flex-col justify-center">
                    <span class="bg-[#8cc63f]/10 text-[#8cc63f] text-[9px] font-black uppercase tracking-[0.3em] px-4 py-1 rounded-full inline-block mb-4 w-fit">${catName}</span>
                    ${isFlashProduct ? '<span class="bg-red-600 text-white text-[9px] font-black uppercase tracking-[0.3em] px-4 py-1 rounded-full inline-block mb-4 w-fit ml-2">⚡ Flash Sale</span>' : ''}
                    <h1 class="text-3xl lg:text-5xl xl:text-6xl italic-black uppercase leading-[0.9] tracking-tighter mb-6">${p.name}</h1>
                    <div class="flex items-center gap-3 mb-8 flex-wrap">
                        <span class="text-4xl lg:text-5xl font-black italic ${isFlashProduct ? 'text-red-600' : ''}">&#2547;${effectivePrice}</span>
                        <span class="text-lg text-gray-300 line-through font-bold">&#2547;${p.oldPrice}</span>
                        <span class="bg-red-50 text-red-600 text-[10px] font-black px-2 py-1 rounded-md uppercase">Save &#2547;${p.oldPrice - effectivePrice}</span>
                        <div class="flex items-center gap-2 ml-auto">
                            <button onclick="toggleWishlist(${p.id}, event)" class="w-9 h-9 lg:w-10 lg:h-10 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center hover:border-red-400 hover:bg-red-50 transition-all cursor-pointer shrink-0 ${isInWishlist(p.id) ? 'border-red-300 bg-red-50' : ''}" title="Add to Wishlist">
                                <i class="fa-regular fa-heart text-sm ${isInWishlist(p.id) ? 'hidden' : 'text-gray-400'}"></i><i class="fa-solid fa-heart text-sm ${isInWishlist(p.id) ? 'text-red-500' : 'hidden'}"></i>
                            </button>
                            <button onclick="shareProduct()" id="shareBtn" class="w-9 h-9 lg:w-10 lg:h-10 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center hover:border-[#8cc63f] hover:bg-[#8cc63f]/10 transition-all cursor-pointer shrink-0" title="Share">
                                <i class="fa-solid fa-share-nodes text-sm text-gray-400"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-8 p-5 lg:p-6 bg-gray-50 rounded-2xl border border-gray-100">
                        <h4 class="text-[10px] font-black uppercase tracking-widest mb-3 border-b border-gray-200 pb-2">Pro Specifications</h4>
                        <p class="text-[13px] lg:text-[14px] text-gray-600 font-medium leading-relaxed text-justify">${p.desc}</p>
                    </div>
                    <div class="mb-8 grid grid-cols-2 gap-2">${(p.features || []).map(f => `<div class="flex items-center gap-2 text-[11px] font-bold text-gray-600"><i class="fa-solid fa-check text-[#8cc63f] text-[10px]"></i>${f}</div>`).join('')}</div>
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Select Size</p>
                            <button onclick="openSizeGuide()" class="text-[10px] font-bold uppercase tracking-widest text-[#8cc63f] hover:text-black transition-colors cursor-pointer flex items-center gap-1"><i class="fa-solid fa-ruler"></i> Size Guide</button>
                        </div>
                        <div class="grid grid-cols-5 gap-2 lg:gap-3">
                                                        ${(p.sizes && p.sizes.length > 0 ? p.sizes : ['S', 'M', 'L', 'XL', 'XXL']).map(s => `<button onclick="selectSize('${s}')" data-size="${s}" class="size-btn-select h-12 lg:h-14 rounded-xl lg:rounded-2xl font-black border-2 transition-all cursor-pointer text-sm ${selectedSize === s ? 'bg-black text-white border-black scale-105 shadow-xl' : 'bg-white border-gray-100 hover:border-black'}">${s}</button>`).join('')}
                        </div>
                                            <p id="sizeWarningText" class="text-red-400 text-[10px] font-bold mt-2 uppercase tracking-wider" ${selectedSize ? 'style="display:none"' : ''}>* Please select a size</p>
                    </div>
                    <div class="flex items-center gap-4 mb-6">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Quantity</p>
                        <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                            <button onclick="changeDetailQty(-1)" class="w-10 h-10 flex items-center justify-center hover:bg-gray-50 transition-colors cursor-pointer"><i class="fa-solid fa-minus text-[10px]"></i></button>
                            <span id="detailQty" class="w-10 h-10 flex items-center justify-center font-black text-sm border-l border-r border-gray-200">1</span>
                            <button onclick="changeDetailQty(1)" class="w-10 h-10 flex items-center justify-center hover:bg-gray-50 transition-colors cursor-pointer"><i class="fa-solid fa-plus text-[10px]"></i></button>
                        </div>
                    </div>
                    <div class="flex gap-3 mb-6">
                        <button onclick="addToCart(${p.id}, false)" class="flex-1 bg-white border-2 border-black py-4 lg:py-5 rounded-2xl font-black uppercase text-[11px] tracking-widest flex items-center justify-center gap-3 hover:bg-gray-50 transition-all cursor-pointer"><i class="fa-solid fa-bag-shopping"></i> Add to Bag</button>
                        <button onclick="addToCart(${p.id}, true)" class="flex-1 bg-black text-white py-4 lg:py-5 rounded-2xl font-black uppercase text-[11px] tracking-widest hover:bg-[#8cc63f] hover:text-black transition-all cursor-pointer">Buy Now</button>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        ${[{ icon: 'fa-solid fa-truck-fast', label: 'Free Delivery' }, { icon: 'fa-solid fa-shield-halved', label: 'Authenticity' }, { icon: 'fa-solid fa-rotate-left', label: 'Easy Returns' }].map(t => `<div class="flex flex-col items-center gap-1.5 p-3 bg-gray-50 rounded-xl border border-gray-100"><i class="${t.icon} text-[#8cc63f] text-sm"></i><span class="text-[9px] font-black uppercase tracking-wider text-gray-500">${t.label}</span></div>`).join('')}
                    </div>
                </div>
            </div>
            ${similar.length > 0 ? `<section class="mt-20 lg:mt-32"><div class="mb-10"><h3 class="text-[10px] font-black uppercase tracking-[0.5em] text-gray-400 mb-2">You May Also Like</h3><h2 class="text-2xl lg:text-4xl italic-black uppercase tracking-tighter">Similar Products</h2><div class="w-12 h-1.5 bg-[#8cc63f] mt-3"></div></div><div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-8">${similar.map(s => { const sEffectivePrice = getEffectivePrice(s); const sIsFlash = getFlashPrice(s.id) !== null; return `<div class="group cursor-pointer" onclick="showProductDetail(${s.id})" data-product-slug="${generateSlug(s.name)}"><div class="aspect-[3/4] rounded-[1.5rem] lg:rounded-[2rem] overflow-hidden bg-gray-50 mb-4 relative border border-gray-100 group-hover:shadow-xl transition-all duration-300"><img src="${s.img}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" onerror="handleImgError(this,'${s.name.replace(/'/g, "\\'")}')">${sIsFlash ? '<span class="absolute top-3 left-3 bg-red-600 text-white text-[8px] font-black px-2.5 py-1 uppercase tracking-widest">⚡ Flash</span>' : `<span class="absolute top-3 left-3 bg-black text-white text-[8px] font-black px-2.5 py-1 uppercase tracking-widest">${s.badge}</span>`}</div><h4 class="text-[11px] font-black uppercase tracking-tight mb-1 truncate group-hover:text-[#8cc63f] transition-colors">${s.name}</h4><div class="flex items-center gap-2"><span class="text-lg font-black italic ${sIsFlash ? 'text-red-600' : ''}">&#2547;${sEffectivePrice}</span><span class="text-[10px] text-gray-300 line-through font-bold">&#2547;${s.oldPrice}</span></div></div>`; }).join('')}</div></section>` : ''}
        </div>`;
      setTimeout(initImageZoom, 50);
    }

    function selectSize(s) {
      selectedSize = s;
      document.querySelectorAll('.size-btn-select').forEach(btn => {
        if (btn.dataset.size === s) {
          btn.classList.remove('bg-white', 'border-gray-100', 'hover:border-black');
          btn.classList.add('bg-black', 'text-white', 'border-black', 'scale-105', 'shadow-xl');
        } else {
          btn.classList.remove('bg-black', 'text-white', 'border-black', 'scale-105', 'shadow-xl');
          btn.classList.add('bg-white', 'border-gray-100', 'hover:border-black');
        }
      });
      var warn = document.getElementById('sizeWarningText');
      if (warn) warn.style.display = 'none';
    }

    let currentGalleryImages = [];
    let currentGalleryIndex = 0;
    function switchGalleryImage(idx) {
      if (idx < 0 || idx >= currentGalleryImages.length) return;
      currentGalleryIndex = idx;
      const imgSrc = currentGalleryImages[idx];
      const mainImg = document.getElementById('mainProductImg');
      if (mainImg) mainImg.src = imgSrc;
      document.querySelectorAll('.gallery-thumb').forEach((t, i) => { t.classList.toggle('active', i === idx); });
    }
    function handleImgError(img, label) {
      if (img.dataset.fallback) return; img.dataset.fallback = 'true';
      const w = img.parentElement?.offsetWidth || 300, h = img.parentElement?.offsetHeight || 400;
      img.src = 'data:image/svg+xml,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="' + w + '" height="' + h + '" viewBox="0 0 300 400"><rect fill="#f3f4f6" width="300" height="400"/><text x="150" y="180" text-anchor="middle" fill="#d1d5db" font-family="Arial" font-size="14">' + (label || 'No Image') + '</text></svg>');
    }

    function renderCart() {
      const content = document.getElementById('cartContent');
      const subtotal = getSubtotal(), count = getCartCount(), threshold = storeSettings.freeShippingThreshold;
      const progress = Math.min((subtotal / threshold) * 100, 100);
      const isFree = subtotal >= threshold, remaining = Math.max(0, threshold - subtotal);
      const delivery = getDeliveryCharge();
      content.innerHTML = `
        <div class="flex justify-between items-center mb-6">
            <div><h2 class="text-2xl lg:text-3xl italic-black uppercase tracking-tighter">Your Bag</h2><p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">${count} item${count !== 1 ? 's' : ''}</p></div>
            <button onclick="toggleCart(false)" class="p-3 bg-gray-50 rounded-full hover:bg-gray-100 transition-colors cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
        </div>
        ${cart.length > 0 ? `<div class="mb-6"><div class="flex items-center justify-between mb-2"><span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">${isFree ? 'Free shipping unlocked!' : 'Add &#2547;' + remaining + ' more for free shipping'}</span></div><div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden"><div class="h-full rounded-full transition-all duration-500 ${isFree ? 'bg-[#8cc63f]' : 'bg-black'}" style="width:${progress}%"></div></div></div>` : ''}
        <div class="flex-1 overflow-y-auto space-y-4 scrollbar-hide">
            ${cart.length === 0 ? `<div class="h-full flex flex-col items-center justify-center"><div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6"><i class="fa-solid fa-bag-shopping text-3xl text-gray-200"></i></div><p class="font-black uppercase tracking-widest text-gray-400 mb-2">Your bag is empty</p><button onclick="toggleCart(false);goHome()" class="bg-black text-white px-8 py-3 rounded-full font-black uppercase text-[10px] tracking-widest hover:bg-[#8cc63f] hover:text-black transition-all cursor-pointer mt-6">Start Shopping</button></div>` :
          cart.map(item => `<div class="flex gap-4 items-center bg-gray-50 p-4 rounded-2xl"><img src="${item.img}" class="w-20 h-20 rounded-xl object-cover shadow-sm" onerror="handleImgError(this,'')"><div class="flex-1 min-w-0"><p class="text-[11px] font-black uppercase mb-0.5 truncate">${item.name}</p><p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Size ${item.size}</p><div class="flex items-center justify-between mt-2"><span class="text-sm font-black italic">&#2547;${item.price * item.qty}</span><button onclick="removeFromCart('${item.key}')" class="text-gray-300 hover:text-red-500 transition-colors cursor-pointer"><i class="fa-solid fa-trash-can text-xs"></i></button></div><div class="flex items-center gap-0 mt-2"><button onclick="updateCartQty('${item.key}',${item.qty - 1})" class="w-7 h-7 bg-white border border-gray-200 rounded-lg flex items-center justify-center hover:border-black transition-colors cursor-pointer"><i class="fa-solid fa-minus text-[8px]"></i></button><span class="w-8 h-7 flex items-center justify-center text-xs font-black border-t border-b border-gray-200">${item.qty}</span><button onclick="updateCartQty('${item.key}',${item.qty + 1})" class="w-7 h-7 bg-white border border-gray-200 rounded-lg flex items-center justify-center hover:border-black transition-colors cursor-pointer"><i class="fa-solid fa-plus text-[8px]"></i></button></div></div></div>`).join('')}
        </div>
        ${cart.length > 0 ? `<div class="pt-6 border-t border-gray-100 mt-4"><div class="flex justify-between items-end mb-2"><span class="text-[11px] font-black uppercase text-gray-400 tracking-widest">Subtotal</span><span class="text-3xl font-black italic">&#2547;${subtotal}</span></div><div class="flex justify-between text-[10px] font-bold mb-2"><span class="text-gray-400">Delivery</span><span class="${delivery === 0 ? 'text-[#8cc63f] font-black' : ''}">${delivery === 0 ? 'FREE' : '&#2547;' + delivery}</span></div><p class="text-[10px] text-gray-400 font-medium mb-6">Shipping calculated at checkout</p><button onclick="toggleCart(false);navigateTo('checkout');renderCheckout();pushURL({view:'checkout'})" class="w-full bg-black text-white py-5 rounded-2xl font-black uppercase tracking-widest text-[11px] hover:bg-[#8cc63f] hover:text-black transition-all cursor-pointer mb-2">Checkout</button><button onclick="toggleCart(false);goHome()" class="w-full bg-gray-50 text-black py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-gray-100 transition-all cursor-pointer">Continue Shopping</button></div>` : ''}`;
    }

    function renderMobileMenu() {
      document.getElementById('mobileMenuContent').innerHTML = `
        <div class="h-full flex flex-col">
            <div class="flex justify-between items-center py-4 border-b border-gray-100">
                <div class="flex items-center gap-2"><div class="w-8 h-8 bg-black text-white flex items-center justify-center font-black italic text-xs">DB</div><span class="text-lg italic-black uppercase tracking-tighter">DRIBBLINGBD</span></div>
                <button onclick="toggleMobileMenu(false)" class="nav-icon-btn"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="flex-1 overflow-y-auto">
                <p class="mm-label">Collections</p>
                ${CATEGORIES.map(c => `<div class="mm-link ${currentCategory === c.id ? 'active' : ''}" onclick="filterByCategory('${c.id}');toggleMobileMenu(false)"><div class="mm-ico"><i class="${c.icon}"></i></div><div><p class="text-sm font-black uppercase tracking-wider">${c.name}</p><p class="text-[10px] text-gray-400 font-semibold mt-0.5">${c.id === 'all' ? 'View all jerseys' : c.name + ' collection'}</p></div></div>`).join('')}
                <p class="mm-label mt-2">Support</p>
                <div class="mm-link" onclick="navigateTo('wishlist');renderWishlist();pushURL({view:'wishlist'});toggleMobileMenu(false)"><div class="mm-ico"><i class="fa-solid fa-heart" style="color:#ef4444"></i></div><div><p class="text-sm font-black uppercase tracking-wider">Wishlist</p><p class="text-[10px] text-gray-400 font-semibold mt-0.5">${wishlist.length} saved item${wishlist.length !== 1 ? 's' : ''}</p></div></div>
                <div class="mm-link" onclick="openPolicyPage('size-guide');toggleMobileMenu(false)"><div class="mm-ico"><i class="fa-solid fa-ruler"></i></div><p class="text-sm font-bold text-gray-600">Size Guide</p></div>
                <div class="mm-link" onclick="openPolicyPage('shipping-policy');toggleMobileMenu(false)"><div class="mm-ico"><i class="fa-solid fa-truck-fast"></i></div><p class="text-sm font-bold text-gray-600">Shipping Policy</p></div>
                <div class="mm-link" onclick="openPolicyPage('return-exchange');toggleMobileMenu(false)"><div class="mm-ico"><i class="fa-solid fa-rotate-left"></i></div><p class="text-sm font-bold text-gray-600">Return & Exchange</p></div>
                <div class="mm-link" onclick="openPolicyPage('privacy-policy');toggleMobileMenu(false)"><div class="mm-ico"><i class="fa-solid fa-shield-halved"></i></div><p class="text-sm font-bold text-gray-600">Privacy Policy</p></div>
                <div class="mm-link" onclick="openPolicyPage('terms-of-service');toggleMobileMenu(false)"><div class="mm-ico"><i class="fa-solid fa-file-contract"></i></div><p class="text-sm font-bold text-gray-600">Terms of Service</p></div>
            </div>
            <div class="pt-4 pb-6 border-t border-gray-100">
                <button onclick="toggleMobileMenu(false);toggleCart(true)" class="w-full bg-black text-white py-3.5 rounded-2xl font-black uppercase tracking-widest text-[11px] flex items-center justify-center gap-3 hover:bg-[#8cc63f] hover:text-black transition-all cursor-pointer">
                    <i class="fa-solid fa-bag-shopping"></i> View Bag
                    ${getCartCount() > 0 ? `<span class="bg-[#8cc63f] text-black text-[9px] w-5 h-5 rounded-full flex items-center justify-center font-black">${getCartCount()}</span>` : ''}
                </button>
            </div>
        </div>`;
    }

    async function renderCheckout() {
      await validateActiveCoupon();
      startCouponPolling();
      // Meta Pixel: Track InitiateCheckout event
      if (typeof fbq === 'function' && cart.length > 0) {
        fbq('track', 'InitiateCheckout', {
          content_ids: cart.map(i => i.id),
          content_type: 'product',
          num_items: getCartCount(),
          value: getTotal(),
          currency: 'BDT'
        });
      }
      const container = document.getElementById('checkout-container');
      if (cart.length === 0) { container.innerHTML = `<div class="max-w-7xl mx-auto px-4 py-20 text-center"><h2 class="text-3xl font-black uppercase mb-4">Your Bag is Empty</h2><button onclick="goHome()" class="bg-black text-white px-8 py-4 rounded-full font-black uppercase text-[11px] tracking-widest hover:bg-[#8cc63f] hover:text-black transition-all cursor-pointer mt-6">Browse Collection</button></div>`; return; }
      const subtotal = getSubtotal(), discount = getDiscount(), delivery = getDeliveryCharge(), total = subtotal - discount + delivery;
      container.innerHTML = `
        <div class="max-w-7xl mx-auto px-4 py-12 lg:py-20">
            <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400 mb-10"><button onclick="goHome()" class="hover:text-black transition-colors cursor-pointer">Home</button><i class="fa-solid fa-chevron-right text-[8px]"></i><span class="text-black">Checkout</span></nav>
            <h1 class="text-3xl lg:text-6xl italic-black uppercase tracking-tighter mb-12 text-center">Secure Checkout</h1>
            <div class="grid lg:grid-cols-12 gap-8 lg:gap-16">
                <div class="lg:col-span-8 space-y-8">
                    <div class="bg-white p-6 lg:p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                        <h4 class="text-lg italic-black uppercase mb-6 flex items-center gap-3 tracking-tighter"><span class="w-9 h-9 bg-black text-white rounded-full flex items-center justify-center font-black text-sm">01</span> Personal Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><input type="text" id="fname" placeholder="Full Name *" class="w-full bg-gray-50 p-4 rounded-xl border border-gray-100 outline-none font-bold text-sm focus:border-black transition-colors"></div>
                            <div><input type="text" id="fphone" placeholder="Phone Number *" class="w-full bg-gray-50 p-4 rounded-xl border border-gray-100 outline-none font-bold text-sm focus:border-black transition-colors"></div>
                            <div class="md:col-span-2"><input type="email" id="femail" placeholder="Email Address (Optional - for order confirmation)" class="w-full bg-gray-50 p-4 rounded-xl border border-gray-100 outline-none font-bold text-sm focus:border-black transition-colors"></div>
                        </div>
                    </div>
                    <div class="bg-white p-6 lg:p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                        <h4 class="text-lg italic-black uppercase mb-6 flex items-center gap-3 tracking-tighter"><span class="w-9 h-9 bg-black text-white rounded-full flex items-center justify-center font-black text-sm">02</span> Delivery Destination</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><div id="checkoutDistrictContainer"></div><input type="hidden" id="fdistrict"></div>
                            <div><input type="text" id="fthana" placeholder="Thana *" class="w-full bg-gray-50 p-4 rounded-xl border border-gray-100 outline-none font-bold text-sm focus:border-black transition-colors"></div>
                            <div><input type="text" id="farea" placeholder="Village / Area / Road *" class="w-full bg-gray-50 p-4 rounded-xl border border-gray-100 outline-none font-bold text-sm focus:border-black transition-colors"></div>
                            <div><input type="text" id="flandmark" placeholder="Landmark (Optional)" class="w-full bg-gray-50 p-4 rounded-xl border border-gray-100 outline-none font-bold text-sm focus:border-black transition-colors"></div>
                            <div class="md:col-span-2"><textarea id="fnotes" placeholder="Order Notes (Optional)" rows="3" class="w-full bg-gray-50 p-4 rounded-xl border border-gray-100 outline-none font-bold text-sm focus:border-black transition-colors resize-none"></textarea></div>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-4">
                    <div class="space-y-4 sticky top-28">
                        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                            ${activeCoupon ? `
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Applied Coupon</p>
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-ticket text-[#8cc63f]"></i>
                                            <span class="text-sm font-black text-[#8cc63f]">${activeCoupon.code}</span>
                                            <span class="text-[10px] text-gray-400">(${activeCoupon.percent}% off)</span>
                                        </div>
                                    </div>
                                    <button onclick="removeCoupon()" class="w-8 h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-100 transition-colors cursor-pointer" title="Remove coupon">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </div>
                            ` : `
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Promo Code</p>
                                <div class="flex gap-2">
                                    <input type="text" id="couponInput" placeholder="ENTER CODE" onkeydown="if(event.key==='Enter')handleCouponApply()" class="flex-1 bg-gray-50 p-3.5 rounded-xl border border-gray-100 outline-none font-black text-xs uppercase focus:border-black transition-colors">
                                    <button onclick="handleCouponApply()" id="couponApplyBtn" class="bg-black text-white px-5 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-gray-800 transition-colors cursor-pointer flex items-center gap-2">Apply</button>
                                </div>
                                <div id="couponMsg"></div>
                            `}
                        </div>
                        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">Your Items (${cart.length})</p>
                            <div class="space-y-3 max-h-48 overflow-y-auto scrollbar-hide">${cart.map(i => `<div class="flex gap-3 items-center">
    <img src="${i.img}" class="w-12 h-12 rounded-lg object-cover border border-gray-100" onerror="handleImgError(this,'')">
    <div class="flex-1 min-w-0">
        <p class="text-[10px] font-black uppercase truncate">${i.name}</p>
        <p class="text-[9px] text-gray-400 font-bold">Size ${i.size}</p>
        <div class="flex items-center gap-0 mt-1">
            <button onclick="updateCartQty('${i.key}',${i.qty - 1});renderCheckout()" class="w-6 h-6 bg-gray-100 rounded flex items-center justify-center hover:bg-gray-200 transition-colors cursor-pointer"><i class="fa-solid fa-minus text-[7px]"></i></button>
            <span class="w-6 h-6 flex items-center justify-center text-[10px] font-black">${i.qty}</span>
            <button onclick="updateCartQty('${i.key}',${i.qty + 1});renderCheckout()" class="w-6 h-6 bg-gray-100 rounded flex items-center justify-center hover:bg-gray-200 transition-colors cursor-pointer"><i class="fa-solid fa-plus text-[7px]"></i></button>
            <button onclick="removeFromCart('${i.key}');renderCheckout()" class="ml-2 text-gray-300 hover:text-red-500 transition-colors cursor-pointer"><i class="fa-solid fa-xmark text-[8px]"></i></button>
        </div>
    </div>
    <span class="text-xs font-black">&#2547;${i.price * i.qty}</span>
</div>`).join('')}</div>
                        </div>
                        <div class="bg-black text-white p-8 rounded-[2rem] shadow-2xl">
                            <h4 class="text-base italic-black uppercase mb-6 border-b border-white/10 pb-3 tracking-tighter">Order Summary</h4>
                            <div class="space-y-3 pt-4">
                                <div class="flex justify-between text-[11px] font-bold uppercase opacity-50"><span>Subtotal</span><span>&#2547;${subtotal}</span></div>
                                ${activeCoupon ? `<div class="flex justify-between text-[11px] font-black uppercase text-[#8cc63f]"><span>Discount (${activeCoupon.percent}%)</span><span>-&#2547;${discount}</span></div>` : ''}
                                <div class="flex justify-between text-[11px] font-bold uppercase opacity-50"><span>Delivery</span><span id="checkoutDelivery">${delivery === 0 ? '<span class="text-[#8cc63f]">FREE</span>' : '&#2547;' + delivery}</span></div>
                                <div class="flex justify-between items-end pt-4 border-t border-white/10"><span class="text-sm font-black uppercase">Total</span><span id="checkoutTotal" class="text-3xl italic-black text-[#8cc63f]">&#2547;${total}</span></div>
                            </div>
                            <button onclick="handleConfirmOrder()" id="confirmOrderBtn" class="w-full bg-[#8cc63f] text-black py-5 rounded-2xl font-black uppercase tracking-widest text-xs mt-8 hover:scale-[1.02] transition-all cursor-pointer flex items-center justify-center gap-2"><i class="fa-solid fa-lock text-[10px]"></i><span id="confirmOrderBtnText">Confirm Order</span></button>
                            <button onclick="handleWhatsAppOrder()" class="w-full bg-[#25D366]/20 text-[#25D366] py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] mt-2 hover:bg-[#25D366]/30 transition-all cursor-pointer flex items-center justify-center gap-2"><i class="fa-brands fa-whatsapp text-sm"></i>Order via WhatsApp</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
      // Initialize district dropdown after DOM update
      setTimeout(initCheckoutDistrictDropdown, 50);
    }

    async function handleCouponApply() {
      const input = document.getElementById('couponInput'), msg = document.getElementById('couponMsg'), btn = document.getElementById('couponApplyBtn');
      if (!input.value.trim()) return;
      btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner" style="animation:spin 0.8s linear infinite"></i>';
      const success = await applyCoupon(input.value);
      btn.disabled = false; btn.innerHTML = 'Apply';
      msg.innerHTML = success ? `<p class="mt-2 text-[10px] font-black uppercase text-[#8cc63f]"><i class="fa-solid fa-check-circle mr-1"></i>Coupon applied!</p>` : input.value.trim() ? `<p class="mt-2 text-[10px] font-black uppercase text-red-500">Invalid coupon</p>` : '';
      if (success) renderCheckout();
      input.value = '';
    }

    function validateCheckout() {
      const fields = { fname: 'Name', fphone: 'Phone', fthana: 'Thana', farea: 'Area' };
      let valid = true;
      for (const [id] of Object.entries(fields)) {
        const el = document.getElementById(id);
        if (!el.value.trim()) { el.style.borderColor = '#fca5a5'; el.style.backgroundColor = '#fef2f2'; valid = false; }
        else { el.style.borderColor = ''; el.style.backgroundColor = ''; }
      }
      // Validate district dropdown
      const distInput = document.getElementById('fdistrict');
      const distDD = EKDropdown.get('checkoutDistrict');
      if (!distInput.value.trim()) {
        valid = false;
        const trigger = document.querySelector('#ekdd-checkoutDistrict .ek-dropdown-trigger');
        if (trigger) { trigger.style.borderColor = '#fca5a5'; trigger.style.boxShadow = '0 0 0 1px #fca5a5'; }
        addToast('Please select your district', 'error');
      } else {
        const trigger = document.querySelector('#ekdd-checkoutDistrict .ek-dropdown-trigger');
        if (trigger) { trigger.style.borderColor = ''; trigger.style.boxShadow = ''; }
      }
      const phone = document.getElementById('fphone').value.trim();
      if (phone && !/^01[3-9]\d{8}$/.test(phone)) { document.getElementById('fphone').style.borderColor = '#fca5a5'; addToast('Enter a valid BD phone number', 'error'); valid = false; }
      return valid;
    }

    // ==================== PLACE ORDER (Supabase) ====================
    async function handleConfirmOrder() {
      if (!validateCheckout()) { addToast('Please fill in all required fields', 'error'); return; }
      const btn = document.getElementById('confirmOrderBtn'), btnText = document.getElementById('confirmOrderBtnText');
      if (btn) { btn.disabled = true; btn.style.opacity = '0.7'; btnText.innerHTML = '<i class="fa-solid fa-spinner" style="animation:spin 0.8s linear infinite"></i> Placing Order...'; }
      const name = document.getElementById('fname').value;
      const phone = document.getElementById('fphone').value;
      const email = document.getElementById('femail').value;
      const district = document.getElementById('fdistrict').value;
      const thana = document.getElementById('fthana').value;
      const area = document.getElementById('farea').value;
      const landmark = document.getElementById('flandmark').value;
      const notes = document.getElementById('fnotes').value;
      const subtotal = getSubtotal(), discount = getDiscount(), delivery = getDeliveryCharge(), total = subtotal - discount + delivery;

      const orderId = 'DB-' + Date.now();
      const items = cart.map(i => `  - ${i.name} (${i.size}) x${i.qty} = ৳${i.price * i.qty}`).join('\n');
      const zoneLabel = deliveryZone === 'dhaka' ? 'Inside Dhaka' : 'Outside Dhaka';
      const msg = `*New Order - DribblingBD*\n*Order ID:* ${orderId}\n\n*Customer:* ${name}\n*Phone:* ${phone}\n*Email:* ${email}\n\n*Address:* ${area}, ${thana}, ${district}\n*Delivery Zone:* ${zoneLabel}\n${landmark ? `*Landmark:* ${landmark}\n` : ''}*Items:*\n${items}\n\n*Subtotal:* ৳${subtotal}\n${discount > 0 ? `*Discount:* -৳${discount}\n` : ''}*Delivery (${zoneLabel}):* ${delivery === 0 ? 'FREE' : '৳' + delivery}\n*Total:* ৳${total}${notes ? `\n\n*Notes:* ${notes}` : ''}`;
      
      window.open('https://wa.me/8801577078101?text=' + encodeURIComponent(msg), '_blank');

      clearCart();
      navigateTo('success');
      addToast('Order placed successfully!', 'success');
    }
    function handleWhatsAppOrder() {
      if (!validateCheckout()) { addToast('Please fill in all required fields', 'error'); return; }
      const name = document.getElementById('fname').value, phone = document.getElementById('fphone').value, email = document.getElementById('femail').value;
      const area = document.getElementById('farea').value, thana = document.getElementById('fthana').value, district = document.getElementById('fdistrict').value;
      const landmark = document.getElementById('flandmark').value, notes = document.getElementById('fnotes').value;
      const items = cart.map(i => `  - ${i.name} (${i.size}) x${i.qty} = ৳${i.price * i.qty}`).join('\n');
      const subtotal = getSubtotal(), discount = getDiscount(), delivery = getDeliveryCharge(), total = subtotal - discount + delivery;
      const zoneLabel = deliveryZone === 'dhaka' ? 'Inside Dhaka' : 'Outside Dhaka';
      const msg = `*New Order - DribblingBD*\n\n*Customer:* ${name}\n*Phone:* ${phone}\n*Email:* ${email}\n\n*Address:* ${area}, ${thana}, ${district}\n*Delivery Zone:* ${zoneLabel}\n${landmark ? `*Landmark:* ${landmark}\n` : ''}*Items:*\n${items}\n\n*Subtotal:* ৳${subtotal}\n${discount > 0 ? `*Discount:* -৳${discount}\n` : ''}*Delivery (${zoneLabel}):* ${delivery === 0 ? 'FREE' : '৳' + delivery}\n*Total:* ৳${total}\n${notes ? `\n*Notes:* ${notes}\n` : ''}`;
      window.open('https://wa.me/8801577078101?text=' + encodeURIComponent(msg), '_blank');
    }

    function renderHome() {
      const showHero = currentCategory === 'all' && !searchQuery;
      document.getElementById('hero-slider').style.display = showHero ? 'block' : 'none';
      document.getElementById('popular-cats').style.display = showHero ? 'block' : 'none';
      document.getElementById('trustStrip').style.display = (!searchQuery && currentCategory === 'all') ? 'block' : 'none';
      renderNav(); renderCategoryCards(); renderProducts();
    }

    function renderFooterLinks() {
      document.getElementById('footerShopLinks').innerHTML = CATEGORIES.filter(c => c.id !== 'all').map(c =>
        `<li><button onclick="filterByCategory('${c.id}')" class="text-[11px] font-bold uppercase tracking-widest text-gray-400 hover:text-white transition-colors cursor-pointer">${c.name}</button></li>`
      ).join('') + '<li><button onclick="goHome()" class="text-[11px] font-bold uppercase tracking-widest text-gray-400 hover:text-white transition-colors cursor-pointer">All Kits</button></li>';
    }

    // ==================== SUBSCRIBE (Supabase) ====================
    async function handleSubscribe() {
      const email = document.getElementById('newsletterEmail').value.trim();
      const btn = document.getElementById('subscribeBtn');
      if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { addToast('Please enter a valid email address', 'error'); return; }
      btn.innerHTML = '<i class="fa-solid fa-spinner" style="animation:spin 0.8s linear infinite"></i>'; btn.disabled = true; btn.style.opacity = '0.7';
      try {
        if (backendConnected && sb) {
          const { error } = await sb.from('subscribers').upsert({ email, source: 'newsletter' }, { onConflict: 'email' });
          if (error) { addToast('Subscription failed', 'error'); btn.textContent = 'Subscribe'; btn.disabled = false; btn.style.opacity = '1'; return; }
          addToast('Successfully subscribed!', 'success');
          mailPost({ action: 'sendWelcome', email });
        } else { addToast('Subscribed! (offline mode)', 'success'); }
      } catch (e) { addToast('Something went wrong', 'error'); }
      btn.textContent = 'Subscribed!'; btn.disabled = false; btn.style.opacity = '1'; btn.style.background = '#8cc63f'; btn.style.color = '#000';
      document.getElementById('newsletterEmail').value = '';
      setTimeout(() => { btn.textContent = 'Subscribe'; btn.style.background = ''; btn.style.color = ''; }, 3000);
    }

    // ==================== BACK TO TOP ====================
    window.addEventListener('scroll', () => {
      const btn = document.getElementById('backToTop');
      if (window.innerWidth >= 1024 && window.scrollY > 600) { btn.classList.remove('hidden'); btn.classList.add('flex'); }
      else { btn.classList.add('hidden'); btn.classList.remove('flex'); }
      const nav = document.getElementById('mainNav');
      if (nav) { if (window.scrollY > 10) nav.classList.add('scrolled'); else nav.classList.remove('scrolled'); }
    });

    // ================================================================
    // ==================== ADMIN PANEL (Supabase) ====================
    // ================================================================

    function selectQuickAddSize(productId, size) { quickAddSelectedSize[productId] = size; renderProducts(); }
    // ==================== CART PERSISTENCE ====================
    function saveCartToStorage() {
      try {
        localStorage.setItem('ek_cart', JSON.stringify(cart));
        localStorage.setItem('ek_coupon', JSON.stringify(activeCoupon));
        localStorage.setItem('ek_deliveryZone', deliveryZone);
      } catch (e) { console.warn('Cart save failed:', e); }
    }

    function loadCartFromStorage() {
      try {
        const saved = localStorage.getItem('ek_cart');
        if (saved) {
          cart = JSON.parse(saved);
          cart = cart.filter(item => PRODUCTS.some(p => p.id === item.id));
        }
        const savedCoupon = localStorage.getItem('ek_coupon');
        if (savedCoupon) {
          activeCoupon = JSON.parse(savedCoupon);
          // Validate stored coupon on page load
          setTimeout(validateActiveCoupon, 1000);
        }
        const savedZone = localStorage.getItem('ek_deliveryZone');
        if (savedZone) deliveryZone = savedZone;
      } catch (e) { cart = []; activeCoupon = null; }
    }
    function quickAddToCart(productId) {
      const size = quickAddSelectedSize[productId];
      if (!size) { addToast('Please select a size first', 'error'); return; }
      const p = PRODUCTS.find(x => x.id === productId); if (!p) return;
      const key = productId + '-' + size;
      const existing = cart.find(i => i.key === key);
      const effectivePrice = getEffectivePrice(p);
      if (existing) { existing.qty++; } else { cart.push({ key, id: productId, name: p.name, img: p.img, size, price: effectivePrice, qty: 1 }); }
      addToast(p.name + ' (' + size + ') added to bag!', 'success');
      saveCartToStorage();
      updateCartBadge(); toggleCart(true);
    }

    // ==================== IMAGE ZOOM (INNER ZOOM) ====================
    function initImageZoom() {
      const mainImg = document.getElementById('mainProductImg');
      const galleryMain = document.getElementById('galleryMain');
      if (!mainImg || !galleryMain) return;

      // Remove existing zoom elements if any
      galleryMain.querySelectorAll('.zoom-lens, .zoom-result, .zoom-icon-indicator').forEach(el => el.remove());

      // Create modern zoom icon indicator
      const iconIndicator = document.createElement('div');
      iconIndicator.className = 'zoom-icon-indicator';
      iconIndicator.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M9 21H3v-6"/><path d="m21 3-7 7"/><path d="m3 21 7-7"/></svg>';
      galleryMain.appendChild(iconIndicator);

      // Create zoom lens
      const lens = document.createElement('div');
      lens.className = 'zoom-lens';
      galleryMain.appendChild(lens);

      // Create zoom result — INSIDE galleryMain this time (fixes CSS selector)
      const result = document.createElement('div');
      result.className = 'zoom-result';
      result.style.cursor = 'zoom-in';
      // Click on zoom result opens lightbox
      result.addEventListener('click', function (e) {
        e.stopPropagation();
        openLightbox(currentGalleryImages, currentGalleryIndex || 0);
      });
      galleryMain.appendChild(result);

      const zoomFactor = 2.5;

      function moveLens(e) {
        e.preventDefault();
        const rect = mainImg.getBoundingClientRect();
        let x = e.clientX - rect.left - lens.offsetWidth / 2;
        let y = e.clientY - rect.top - lens.offsetHeight / 2;
        x = Math.max(0, Math.min(x, rect.width - lens.offsetWidth));
        y = Math.max(0, Math.min(y, rect.height - lens.offsetHeight));
        lens.style.left = x + 'px';
        lens.style.top = y + 'px';
        result.style.backgroundImage = "url('" + mainImg.src + "')";
        result.style.backgroundSize = (rect.width * zoomFactor) + 'px ' + (rect.height * zoomFactor) + 'px';
        result.style.backgroundPosition = (-x * zoomFactor) + 'px ' + (-y * zoomFactor) + 'px';
      }

      function activateZoom() {
        lens.classList.add('active');
        result.classList.add('active');
        mainImg.style.opacity = '0';
        iconIndicator.style.opacity = '0';
      }

      function deactivateZoom() {
        lens.classList.remove('active');
        result.classList.remove('active');
        mainImg.style.opacity = '1';
        iconIndicator.style.opacity = '1';
      }

      galleryMain.addEventListener('mouseenter', activateZoom);
      galleryMain.addEventListener('mousemove', moveLens);
      galleryMain.addEventListener('mouseleave', deactivateZoom);

      // Handle thumbnail image change — re-point zoom to new image
      if (currentZoomObserver) { currentZoomObserver.disconnect(); currentZoomObserver = null; }
      currentZoomObserver = new MutationObserver(function () {
        var img = document.getElementById('mainProductImg');
        if (img) {
          result.style.backgroundImage = "url('" + img.src + "')";
        }
      });
      currentZoomObserver.observe(mainImg, { attributes: true, attributeFilter: ['src'] });
    }
    function shareProduct() {
      const url = window.location.href;
      const p = PRODUCTS.find(x => x.id === selectedProductId);
      const text = p ? 'Check out ' + p.name + ' on DribblingBD!' : 'Check out this jersey on DribblingBD!';

      // Try native Web Share API (mobile)
      if (navigator.share) {
        navigator.share({
          title: p ? p.name + ' | DribblingBD' : 'DribblingBD',
          text: text,
          url: url
        }).catch(() => { });
      } else {
        // Fallback: Copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
          addToast('Link copied to clipboard!', 'success');
          const btn = document.getElementById('shareBtn');
          if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-check text-sm text-[#8cc63f]"></i>';
            setTimeout(() => {
              btn.innerHTML = '<i class="fa-solid fa-share-nodes text-sm text-gray-400"></i>';
            }, 2000);
          }
        }).catch(() => {
          addToast('Failed to copy link', 'error');
        });
      }
    }
    // ==================== FLASH SALE ====================

    // Helper: get flash price for a product if active flash sale exists
    function getFlashPrice(productId) {
      const fs = getActiveFlashSale();
      if (fs && fs.product_ids && fs.product_ids.includes(productId)) {
        const p = PRODUCTS.find(x => x.id === productId);
        if (p) {
          return fs.discount_percent ? Math.round(p.oldPrice * (1 - fs.discount_percent / 100)) : p.price;
        }
      }
      return null;
    }

    // Helper: get effective display price (flash price if active, otherwise regular price)
    function getEffectivePrice(p) {
      const flashPrice = getFlashPrice(p.id);
      return flashPrice !== null ? flashPrice : p.price;
    }

    // Helper: get discount percent for a product considering flash sale
    function getEffectiveDiscount(p) {
      const effectivePrice = getEffectivePrice(p);
      return Math.round(((p.oldPrice - effectivePrice) / p.oldPrice) * 100);
    }

    async function loadFlashSales() {
      if (!backendConnected || !sb) return;
      try {
        const { data, error } = await sb.from('flash_sales').select('*').order('created_at', { ascending: false });
        if (error) throw error;
        flashSales = (data || []).map(fs => ({
          ...fs,
          product_ids: Array.isArray(fs.product_ids) ? fs.product_ids : (typeof fs.product_ids === 'string' ? JSON.parse(fs.product_ids || '[]') : [])
        }));
        renderActiveFlashSale();
      } catch (e) {
        console.error('Flash sale load error:', e);
      }
    }

    function getActiveFlashSale() {
      const now = new Date();
      return flashSales.find(fs => {
        if (!fs.active) return false;
        const start = new Date(fs.start_time);
        const end = new Date(fs.end_time);
        return now >= start && now <= end;
      });
    }

    // Render flash sale section — shows ALWAYS when flash sale is active (regardless of show_banner)
    // show_banner only controls the hero banner, not this section
    function renderActiveFlashSale() {
      const section = document.getElementById('flashSaleSection');
      if (!section) return;
      activeFlashSale = getActiveFlashSale();
      if (!activeFlashSale) {
        section.style.display = 'none';
        if (flashCountdownInterval) { clearInterval(flashCountdownInterval); flashCountdownInterval = null; }
        renderBanner();
        return;
      }
      section.style.display = 'block';
      document.getElementById('flashSaleTitle').textContent = activeFlashSale.title || 'Flash Sale';
      document.getElementById('flashSaleSubtitle').textContent = activeFlashSale.subtitle || 'Limited time deals';
      const flashProducts = PRODUCTS.filter(p => activeFlashSale.product_ids.includes(p.id));
      if (flashProducts.length === 0) {
        section.style.display = 'none';
        renderBanner();
        return;
      }
      const grid = document.getElementById('flashProductGrid');
      grid.innerHTML = flashProducts.map(p => {
        const flashPrice = activeFlashSale.discount_percent ? Math.round(p.oldPrice * (1 - activeFlashSale.discount_percent / 100)) : p.price;
        const dp = Math.round(((p.oldPrice - flashPrice) / p.oldPrice) * 100);
        return `<div class="flash-product-card cursor-pointer" onclick="showProductDetail(${p.id})">
                    <div class="aspect-[3/4] relative overflow-hidden">
                        <img src="${p.img}" loading="lazy" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" onerror="handleImgError(this,'${p.name.replace(/'/g, "\\'")}')">
                        <div class="flash-discount-badge">-${dp}%</div>
                    </div>
                    <div class="p-3 lg:p-4">
                        <h3 class="text-[10px] lg:text-[11px] font-black uppercase tracking-tight truncate mb-1">${p.name}</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-base lg:text-xl font-black text-red-600 italic">&#2547;${flashPrice}</span>
                            <span class="text-[10px] text-gray-400 line-through font-bold">&#2547;${p.oldPrice}</span>
                        </div>
                    </div>
                </div>`;
      }).join('');
      startFlashCountdown();
      renderBanner();
    }

    function startFlashCountdown() {
      if (flashCountdownInterval) clearInterval(flashCountdownInterval);
      function update() {
        if (!activeFlashSale) return;
        const end = new Date(activeFlashSale.end_time).getTime();
        const now = Date.now();
        const diff = end - now;
        if (diff <= 0) {
          document.getElementById('flashDays').textContent = '00';
          document.getElementById('flashHours').textContent = '00';
          document.getElementById('flashMins').textContent = '00';
          document.getElementById('flashSecs').textContent = '00';
          clearInterval(flashCountdownInterval);
          flashCountdownInterval = null;
          renderActiveFlashSale();
          return;
        }
        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        document.getElementById('flashDays').textContent = String(d).padStart(2, '0');
        document.getElementById('flashHours').textContent = String(h).padStart(2, '0');
        document.getElementById('flashMins').textContent = String(m).padStart(2, '0');
        document.getElementById('flashSecs').textContent = String(s).padStart(2, '0');
      }
      update();
      flashCountdownInterval = setInterval(update, 1000);
    }

    // Override renderBanner to show flash sale banner on HERO only
    // NOTE: Flash section (#flashSaleSection) shows regardless of show_banner.
    // show_banner only controls whether the hero/banner area shows flash content.
    const _originalRenderBanner = renderBanner;
    renderBanner = function () {
      const fs = getActiveFlashSale();
      // If flash sale is active AND show_banner is true, show flash banner in hero area
      if (fs && fs.show_banner) {
        const container = document.getElementById('banner-container');
        const end = new Date(fs.end_time).getTime();
        const now = Date.now();
        const diff = Math.max(0, end - now);
        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        const bannerImg = fs.banner_img || '';
        container.innerHTML = `
                <div onclick="document.getElementById('flashSaleSection').scrollIntoView({behavior:'smooth'})" class="absolute inset-0 cursor-pointer">
                    ${bannerImg ? `<img src="${bannerImg}" class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display='none'">` : ''}
                    <div class="flash-banner-overlay">
                        <div class="flash-banner-text">
                            <span class="text-yellow-400 text-[9px] lg:text-xs font-black tracking-[0.4em] uppercase">⚡ FLASH SALE</span>
                            <h2 class="text-xl lg:text-4xl font-black uppercase mt-1 leading-tight">${fs.title || 'Flash Sale'}</h2>
                            <p class="text-white/70 text-xs lg:text-sm font-bold mt-1">${fs.subtitle || ''}</p>
                        </div>
                        <div class="flash-banner-timer">
                            <div class="flash-banner-timer-box"><div class="flash-banner-timer-num">${String(h).padStart(2, '0')}</div><div class="flash-banner-timer-lbl">HR</div></div>
                            <span class="text-white/30 font-black text-lg">:</span>
                            <div class="flash-banner-timer-box"><div class="flash-banner-timer-num">${String(m).padStart(2, '0')}</div><div class="flash-banner-timer-lbl">MIN</div></div>
                            <span class="text-white/30 font-black text-lg">:</span>
                            <div class="flash-banner-timer-box"><div class="flash-banner-timer-num">${String(s).padStart(2, '0')}</div><div class="flash-banner-timer-lbl">SEC</div></div>
                        </div>
                    </div>
                </div>`;
        return;
      }
      _originalRenderBanner();
    };

    // ==================== INIT ====================
    renderNav(); renderBanner(); renderCategoryCards(); renderFooterLinks();

    // Initialize Custom Dropdowns
    (function initDropdowns() {
      new EKDropdown({
        id: 'sortSelect',
        value: 'default',
        options: [
          { value: 'default', label: 'Sort by: Featured', icon: '<i class="fa-solid fa-sparkles"></i>' },
          { value: 'price-asc', label: 'Price: Low to High', icon: '<i class="fa-solid fa-arrow-up-short-wide"></i>' },
          { value: 'price-desc', label: 'Price: High to Low', icon: '<i class="fa-solid fa-arrow-down-wide-short"></i>' }
        ],
        variant: 'sort',
        onChange: () => renderProducts()
      }).mount('sortSelectContainer');

      new EKDropdown({
        id: 'navSearchCategory',
        value: 'all',
        options: [
          { value: 'all', label: 'All Items' },
          { value: 'wc26', label: 'World Cup 2026' },
          { value: 'clubs', label: 'Club Edition' },
          { value: 'player', label: 'Player Edition' },
          { value: 'fan', label: 'Fan Edition' }
        ],
        variant: 'sm',
        onChange: function (val) {
          if (val && val !== 'all') {
            filterByCategory(val);
          } else {
            filterByCategory('all');
          }
        }
      }).mount('navSearchCategoryContainer');
    })();;

    window.addEventListener('scroll', function () {
      const btn = document.getElementById('backToTop');
      if (!btn) return;
      if (window.scrollY > 300) {
        btn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
        btn.classList.add('opacity-100', 'translate-y-0');
      } else {
        btn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
        btn.classList.remove('opacity-100', 'translate-y-0');
      }
    });

    window.onload = async function () {
      const pmImgInput = document.getElementById('pmImg');
      const pmTextarea = document.getElementById('pmImages');
      if (pmImgInput) pmImgInput.addEventListener('input', () => renderProductModalGallery());
      if (pmTextarea) pmTextarea.addEventListener('input', () => renderProductModalGallery());
      const bar = document.getElementById('topProgressBar');
      function setProgress(pct) { if (bar) bar.style.width = pct + '%'; }
      try {
        setProgress(15);
        await loadProducts();
        setProgress(50);
        loadCartFromStorage();
        loadWishlistFromStorage();
        updateCartBadge();
        updateWishlistBadge();
        renderCategoryCards();
        setProgress(70);
        await loadPolicyPages();
        await loadFlashSales();
        await loadBanners();
        setProgress(90);
        setupCouponRealtime();
        handleURLRouting();
        showBottomNav(true);
        setProgress(100);
        setTimeout(function () { if (bar) { bar.style.opacity = '0'; setTimeout(function () { bar.style.display = 'none'; bar.style.width = '0%'; bar.style.opacity = '1'; }, 500); } }, 400);
      } catch (e) {
        console.error('Init error:', e);
        renderProducts();
        if (bar) bar.style.display = 'none';
      }
      setInterval(function () { if (BANNERS.length > 0) { currentBannerIdx = (currentBannerIdx + 1) % BANNERS.length; renderBanner(); } }, 5000);
    };
  

    document.getElementById("year").textContent = new Date().getFullYear();
  

