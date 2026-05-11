// === CURSOR ===
const cursor = document.getElementById("cursor");
const ring = document.getElementById("cursorRing");

const isDesktop = window.matchMedia("(hover: hover) and (pointer: fine)").matches;

if (isDesktop && cursor && ring) {
  let mx = 0,
    my = 0,
    rx = 0,
    ry = 0;

  document.addEventListener("mousemove", (e) => {
    mx = e.clientX;
    my = e.clientY;

    cursor.style.left = mx - 5 + "px";
    cursor.style.top = my - 5 + "px";
  });

  function animateRing() {
    rx += (mx - rx - 18) * 0.15;
    ry += (my - ry - 18) * 0.15;

    ring.style.left = rx + "px";
    ring.style.top = ry + "px";

    requestAnimationFrame(animateRing);
  }

  animateRing();

  document.querySelectorAll("a, button, .product-card, .filter-btn, .cart-icon").forEach((el) => {
    el.addEventListener("mouseenter", () => {
      cursor.classList.add("active");
      ring.classList.add("active");
    });

    el.addEventListener("mouseleave", () => {
      cursor.classList.remove("active");
      ring.classList.remove("active");
    });
  });
}

// === PAGE NAVIGATION ===
function showPage(id) {
  document.querySelectorAll(".page").forEach((p) => {
    p.classList.remove("active");
  });

  document.getElementById(id).classList.add("active");

  document.querySelectorAll(".nav-links a").forEach((a) => {
    a.classList.remove("active");
  });

  document.getElementById("nav-" + id).classList.add("active");

  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });

  if (id === "products") {
    renderProducts("all");
  }
}

// === PRODUCTS DATA ===
const allProducts = [
  {
    id: 1,
    name: "Gayo Natural",
    origin: "Aceh",
    category: "sumatera",
    price: 125000,
    weight: "200g",
    process: "Natural",
    roast: "Medium Light",
    notes: "Blueberry - Dark Chocolate - Floral",
    image: "gayon.jpg",
  },

  {
    id: 2,
    name: "Gayo Honey",
    origin: "Aceh",
    category: "sumatera",
    price: 135000,
    weight: "200g",
    process: "Honey",
    roast: "Medium",
    notes: "Peach - Caramel - Brown Sugar",
    image: "gayo 1.jpg",
  },

  {
    id: 3,
    name: "Lintong Wet Hulled",
    origin: "Sumatera Utara",
    category: "sumatera",
    price: 120000,
    weight: "200g",
    process: "Wet Hulled",
    roast: "Medium Dark",
    notes: "Earthy - Cedar - Dark Fruit",
    image: "lintong.jpg",
  },

  {
    id: 4,
    name: "Malabar Washed",
    origin: "Jawa Barat",
    category: "jawa",
    price: 115000,
    weight: "200g",
    process: "Washed",
    roast: "Medium",
    notes: "Jasmine - Citrus - Clean",
    image: "malabar.jpg",
  },

  {
    id: 5,
    name: "Ijen Natural",
    origin: "Jawa Timur",
    category: "jawa",
    price: 130000,
    weight: "200g",
    process: "Natural",
    roast: "Light",
    notes: "Strawberry - Wine - Tropical",
    image: "ijen.jpg",
  },

  {
    id: 6,
    name: "Toraja Sapan",
    origin: "Sulawesi",
    category: "sulawesi",
    price: 145000,
    weight: "200g",
    process: "Wet Hulled",
    roast: "Medium",
    notes: "Dark Choc - Woody - Nutmeg",
    image: "toraja.jpg",
    badge: "Best Seller",
  },

  {
    id: 7,
    name: "Enrekang Natural",
    origin: "Sulawesi",
    category: "sulawesi",
    price: 140000,
    weight: "200g",
    process: "Natural",
    roast: "Medium Light",
    notes: "Raisin - Tamarind - Spice",
    image: "enrekang.jpg",
  },

  {
    id: 8,
    name: "Flores Bajawa",
    origin: "NTT",
    category: "flores",
    price: 155000,
    weight: "200g",
    process: "Washed",
    roast: "Light-Medium",
    notes: "Passion Fruit - Bergamot - Honey",
    image: "flores.jpg",
    badge: "New",
  },

  {
    id: 9,
    name: "Manggarai Washed",
    origin: "NTT",
    category: "flores",
    price: 150000,
    weight: "200g",
    process: "Washed",
    roast: "Medium Light",
    notes: "Green Apple - Lemon - Floral",
    image: "manggarai.jpg",
  },

  {
    id: 10,
    name: "Sunrise Blend",
    origin: "House Blend",
    category: "blend",
    price: 110000,
    weight: "200g",
    process: "Mixed",
    roast: "Medium",
    notes: "Balanced - Chocolate - Nutty",
    image: "sunrice.jpg",
  },

  {
    id: 11,
    name: "Midnight Espresso",
    origin: "House Blend",
    category: "blend",
    price: 105000,
    weight: "200g",
    process: "Mixed",
    roast: "Dark",
    notes: "Bold - Bittersweet - Full Body",
    image: "exspresso.jpg",
    badge: "Espresso",
  },

  {
    id: 12,
    name: "Golden Hour",
    origin: "House Blend",
    category: "blend",
    price: 115000,
    weight: "200g",
    process: "Mixed",
    roast: "Light",
    notes: "Bright - Fruity - Tea-like",
    image: "golden.jpg",
  },
];

// === RENDER PRODUCTS ===
function renderProducts(filter) {
  const grid = document.getElementById("productsGrid");

  const products = filter === "all" ? allProducts : allProducts.filter((p) => p.category === filter);

  grid.innerHTML = products
    .map(
      (p) => `
      <div class="product-card product-card-full">

        <div class="product-img-wrap featured-img">

          <img src="${p.image}" alt="${p.name}" />

          ${
            p.badge
              ? `
              <div class="product-badge"
                style="${p.badge === "New" ? "background:var(--accent);color:var(--black);" : ""}">
                ${p.badge}
              </div>
            `
              : ""
          }

          <button
            class="add-to-cart"
            onclick="addToCart('${p.name}', ${p.price}, '☕')">

            + Tambah ke Keranjang

          </button>

        </div>

        <div class="product-info">

          <div class="product-origin">
            ${p.origin} - ${p.process}
          </div>

          <div class="product-name">
            ${p.name}
          </div>

          <div
            style="
              font-size:10px;
              color:#aaa;
              letter-spacing:1px;
              margin-bottom:8px;
            ">
            ${p.notes}
          </div>

          <div class="product-price">
            ${p.weight} —
            <strong>
              Rp ${p.price.toLocaleString("id-ID")}
            </strong>
          </div>

        </div>

      </div>
    `,
    )
    .join("");
}

// === FILTER PRODUCTS ===
function filterProducts(cat, btn) {
  document.querySelectorAll(".filter-btn").forEach((b) => {
    b.classList.remove("active");
  });

  btn.classList.add("active");

  renderProducts(cat);
}

// === CART ===
let cart = [];

function addToCart(name, price, emoji) {
  const existing = cart.find((i) => i.name === name);

  if (existing) {
    existing.qty++;
  } else {
    cart.push({
      name,
      price,
      emoji,
      qty: 1,
    });
  }

  updateCartUI();
  showToast();
}

function updateCartUI() {
  const count = cart.reduce((s, i) => s + i.qty, 0);

  document.getElementById("cartCount").textContent = count;

  const total = cart.reduce((s, i) => s + i.price * i.qty, 0);

  document.getElementById("cartTotal").textContent = "Rp " + total.toLocaleString("id-ID");

  const itemsEl = document.getElementById("cartItems");
  const emptyEl = document.getElementById("cartEmpty");

  if (cart.length === 0) {
    emptyEl.style.display = "block";
    itemsEl.innerHTML = "";
    itemsEl.appendChild(emptyEl);
    return;
  }

  emptyEl.style.display = "none";

  itemsEl.innerHTML = cart
    .map(
      (item, i) => `
      <div class="cart-item">

        <div class="cart-item-img">
          ${item.emoji}
        </div>

        <div class="cart-item-info">

          <div class="cart-item-name">
            ${item.name}
          </div>

          <div class="cart-item-price">
            Rp ${item.price.toLocaleString("id-ID")}
          </div>

          <div class="cart-item-qty">

            <button
              class="qty-btn"
              onclick="changeQty(${i}, -1)">
              −
            </button>

            <span>${item.qty}</span>

            <button
              class="qty-btn"
              onclick="changeQty(${i}, 1)">
              +
            </button>

          </div>

        </div>

      </div>
    `,
    )
    .join("");
}

function changeQty(i, delta) {
  cart[i].qty += delta;

  if (cart[i].qty <= 0) {
    cart.splice(i, 1);
  }

  updateCartUI();
}

function toggleCart() {
  document.getElementById("cartSidebar").classList.toggle("open");

  document.getElementById("cartOverlay").classList.toggle("active");
}

function checkout() {
  if (cart.length === 0) return;

  const total = cart.reduce((s, i) => s + i.price * i.qty, 0);

  alert("Fitur pembayaran akan segera hadir! 🎉\nTotal: Rp " + total.toLocaleString("id-ID"));
}

// === TOAST ===
function showToast() {
  const toast = document.getElementById("toast");

  toast.classList.add("show");

  setTimeout(() => {
    toast.classList.remove("show");
  }, 2000);
}

// === CONTACT FORM ===
function submitForm() {
  const name = document.getElementById("formName").value.trim();

  const email = document.getElementById("formEmail").value.trim();

  if (!name || !email) {
    alert("Mohon isi nama dan email terlebih dahulu.");
    return;
  }

  document.getElementById("contactForm").style.display = "none";

  document.getElementById("formSuccess").style.display = "block";
}

function resetForm() {
  document.getElementById("contactForm").style.display = "block";

  document.getElementById("formSuccess").style.display = "none";

  document.getElementById("formName").value = "";
  document.getElementById("formEmail").value = "";
  document.getElementById("formTopic").value = "";
  document.getElementById("formMessage").value = "";
}

// === INIT ===
renderProducts("all");

// === GLOBAL FUNCTIONS ===
window.showPage = showPage;
window.toggleCart = toggleCart;
window.addToCart = addToCart;
window.changeQty = changeQty;
window.checkout = checkout;
window.filterProducts = filterProducts;
window.submitForm = submitForm;
window.resetForm = resetForm;
