/**
 * Admin Dashboard JS
 * Loads current user's products, categories, and brands, and handles modal actions
 */

let currentPage = 1;
let perPage = 9;
let allCategories = [];
let allBrands = [];

document.addEventListener('DOMContentLoaded', () => {
  loadUserProducts();
  loadCategories();
  loadBrands();

  document.getElementById('searchBtn').addEventListener('click', handleSearch);
  document.getElementById('saveCategoryBtn').addEventListener('click', saveCategory);
  document.getElementById('saveBrandBtn').addEventListener('click', saveBrand);
  document.getElementById('saveEditProductBtn').addEventListener('click', saveEditProduct);

  document.getElementById('searchInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') handleSearch();
  });
});

function handleSearch() {
  const q = document.getElementById('searchInput').value.trim();
  loadUserProducts(1, q);
}

function loadUserProducts(page = 1, query = '') {
  currentPage = page;
  const container = document.getElementById('productsContainer');
  container.innerHTML = `<div class="col-12 text-center py-5 text-muted">Loading...</div>`;

  const url = new URL('../actions/view_user_products_action.php', window.location.href);
  url.searchParams.set('page', page);
  url.searchParams.set('per_page', perPage);

  fetch(url.toString())
    .then(res => res.json())
    .then(data => {
      if (data.status !== 'success') throw new Error(data.message || 'Failed to load');

      let products = data.data || [];
      if (query) {
        const ql = query.toLowerCase();
        products = products.filter(p => (
          (p.product_title || '').toLowerCase().includes(ql) ||
          (p.product_keywords || '').toLowerCase().includes(ql)
        ));
      }

      renderProducts(products);
      renderPagination(data.total_pages || 1, data.page || 1);
    })
    .catch(err => {
      container.innerHTML = `<div class="col-12"><div class="alert alert-danger">${err.message}</div></div>`;
    });
}

function renderProducts(products) {
  const container = document.getElementById('productsContainer');
  if (!products.length) {
    container.innerHTML = `<div class="col-12 text-center py-5 text-muted">No products yet. Click "Add Product" to create one.</div>`;
    return;
  }

  let html = '';
  products.forEach(p => {
    const img = p.product_image ? `../${p.product_image}` : '../uploads/default-product.jpg';
    const price = Number(p.product_price || 0).toFixed(2);

    html += `
      <div class="col-md-6 col-xl-4">
        <div class="card h-100">
          <img src="${img}" alt="${escapeHtml(p.product_title)}" class="product-image" onerror="this.src='../uploads/default-product.jpg'"/>
          <div class="card-body d-flex flex-column">
            <h6 class="mb-1">${escapeHtml(p.product_title)}</h6>
            <div class="text-primary fw-bold mb-1">GHS ${price}</div>
            <div class="mb-2">
              <span class="badge badge-cat text-white me-1">${escapeHtml(p.cat_name)}</span>
              <span class="badge badge-brand text-white">${escapeHtml(p.brand_name)}</span>
            </div>
            <div class="mt-auto d-flex gap-2">
              <button class="btn btn-sm btn-outline" onclick="openEditModal(${p.product_id})">
                <i class="bi bi-pencil"></i> Edit
              </button>
              <button class="btn btn-sm btn-outline text-danger border-danger" onclick="confirmDelete(${p.product_id}, '${escapeHtml(p.product_title).replace(/'/g, "\\'")}')">
                <i class="bi bi-trash"></i> Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    `;
  });

  container.innerHTML = html;
}

function renderPagination(totalPages, currentPage) {
  const container = document.getElementById('paginationContainer');
  if (totalPages <= 1) { container.innerHTML = ''; return; }

  let html = '<ul class="pagination">';
  const prevDisabled = currentPage <= 1 ? ' disabled' : '';
  const nextDisabled = currentPage >= totalPages ? ' disabled' : '';

  html += `<li class="page-item${prevDisabled}"><a class="page-link" href="#" onclick="loadUserProducts(${currentPage-1}); return false;">&laquo;</a></li>`;
  for (let i=1;i<=totalPages;i++) {
    if (i===1 || i===totalPages || (i>=currentPage-2 && i<=currentPage+2)) {
      html += `<li class="page-item ${i===currentPage?'active':''}"><a class="page-link" href="#" onclick="loadUserProducts(${i}); return false;">${i}</a></li>`;
    } else if (i===currentPage-3 || i===currentPage+3) {
      html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }
  }
  html += `<li class="page-item${nextDisabled}"><a class="page-link" href="#" onclick="loadUserProducts(${currentPage+1}); return false;">&raquo;</a></li>`;
  html += '</ul>';
  container.innerHTML = html;
}

function loadCategories() {
  fetch('../actions/fetch_category_action.php')
    .then(r=>r.json())
    .then(d=>{
      const ul = document.getElementById('categoriesList');
      if (d.status === 'success') {
        const cats = (d.data || d.categories || []);
        allCategories = cats; // Store globally for edit modal
        const display = cats.slice(0, 8);
        if (!display.length) { ul.innerHTML = '<li class="list-group-item text-muted">No categories yet</li>'; return; }
        ul.innerHTML = display.map(c => `<li class="list-group-item d-flex justify-content-between align-items-center">${escapeHtml(c.cat_name)}<span class="badge bg-light text-muted">#${c.cat_id}</span></li>`).join('');
        populateEditCategoryDropdown();
      } else {
        ul.innerHTML = `<li class="list-group-item text-danger">${d.message || 'Failed to load'}</li>`;
      }
    })
}

function loadBrands() {
  fetch('../actions/fetch_brand_action.php')
    .then(r=>r.json())
    .then(d=>{
      const ul = document.getElementById('brandsList');
      if (d.status === 'success') {
        const brands = (d.data || d.brands || []);
        allBrands = brands; // Store globally for edit modal
        const display = brands.slice(0, 8);
        if (!display.length) { ul.innerHTML = '<li class="list-group-item text-muted">No brands yet</li>'; return; }
        ul.innerHTML = display.map(b => `<li class="list-group-item d-flex justify-content-between align-items-center">${escapeHtml(b.brand_name)}<span class="badge bg-light text-muted">#${b.brand_id}</span></li>`).join('');
        populateEditBrandDropdown();
      } else {
        ul.innerHTML = `<li class="list-group-item text-danger">${d.message || 'Failed to load'}</li>`;
      }
    })
}

function saveCategory() {
  const name = document.getElementById('catNameInput').value.trim();
  const fb = document.getElementById('catFeedback');
  if (!name) { fb.textContent = 'Category name is required'; return; }
  fb.textContent = '';

  const form = new FormData();
  form.append('cat_name', name);

  fetch('../actions/add_category_action.php', { method:'POST', body: form })
    .then(r=>r.json())
    .then(d=>{
      if (d.status === 'success') {
        document.getElementById('catNameInput').value = '';
        const modal = bootstrap.Modal.getInstance(document.getElementById('categoryModal'));
        modal && modal.hide();
        loadCategories();
      } else {
        fb.textContent = d.message || 'Failed to add category';
      }
    })
    .catch(()=> fb.textContent = 'Network error')
}

function saveBrand() {
  const name = document.getElementById('brandNameInput').value.trim();
  const fb = document.getElementById('brandFeedback');
  if (!name) { fb.textContent = 'Brand name is required'; return; }
  fb.textContent = '';

  const form = new FormData();
  form.append('brand_name', name);

  fetch('../actions/add_brand_action.php', { method:'POST', body: form })
    .then(r=>r.json())
    .then(d=>{
      if (d.status === 'success') {
        document.getElementById('brandNameInput').value = '';
        const modal = bootstrap.Modal.getInstance(document.getElementById('brandModal'));
        modal && modal.hide();
        loadBrands();
      } else {
        fb.textContent = d.message || 'Failed to add brand';
      }
    })
    .catch(()=> fb.textContent = 'Network error')
}

// Helper function to escape HTML
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Populate category dropdown in edit modal
function populateEditCategoryDropdown() {
  const select = document.getElementById('editProductCategory');
  if (!select) return;
  select.innerHTML = '<option value="">Select Category</option>' + 
    allCategories.map(c => `<option value="${c.cat_id}">${escapeHtml(c.cat_name)}</option>`).join('');
}

// Populate brand dropdown in edit modal
function populateEditBrandDropdown() {
  const select = document.getElementById('editProductBrand');
  if (!select) return;
  select.innerHTML = '<option value="">Select Brand</option>' + 
    allBrands.map(b => `<option value="${b.brand_id}">${escapeHtml(b.brand_name)}</option>`).join('');
}

// Open edit modal and populate with product data
function openEditModal(productId) {
  fetch(`../actions/view_single_product_action.php?id=${productId}`)
    .then(r => r.json())
    .then(d => {
      if (d.status === 'success' && d.data) {
        const product = d.data;
        document.getElementById('editProductId').value = product.product_id;
        document.getElementById('editProductTitle').value = product.product_title || '';
        document.getElementById('editProductPrice').value = product.product_price || '';
        document.getElementById('editProductCategory').value = product.product_cat || '';
        document.getElementById('editProductBrand').value = product.product_brand || '';
        document.getElementById('editProductDesc').value = product.product_desc || '';
        document.getElementById('editProductKeywords').value = product.product_keywords || '';
        
        // Clear previous feedback
        document.getElementById('editProductFeedback').innerHTML = '';
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
        modal.show();
      } else {
        alert('Failed to load product details: ' + (d.message || 'Unknown error'));
      }
    })
    .catch(err => {
      alert('Error loading product: ' + err.message);
    });
}

// Save edited product
function saveEditProduct() {
  const productId = document.getElementById('editProductId').value;
  const title = document.getElementById('editProductTitle').value.trim();
  const price = document.getElementById('editProductPrice').value.trim();
  const category = document.getElementById('editProductCategory').value;
  const brand = document.getElementById('editProductBrand').value;
  const description = document.getElementById('editProductDesc').value.trim();
  const keywords = document.getElementById('editProductKeywords').value.trim();
  
  const feedback = document.getElementById('editProductFeedback');
  
  // Validation
  if (!title || !price) {
    feedback.innerHTML = '<div class="alert alert-danger">Title and Price are required</div>';
    return;
  }
  
  // Build form data
  const formData = new FormData();
  formData.append('product_id', productId);
  formData.append('product_title', title);
  formData.append('product_price', price);
  formData.append('product_cat', category);
  formData.append('product_brand', brand);
  formData.append('product_desc', description);
  formData.append('product_keywords', keywords);
  
  // Submit
  fetch('../actions/update_product_action.php', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(d => {
    if (d.status === 'success') {
      feedback.innerHTML = '<div class="alert alert-success">Product updated successfully!</div>';
      setTimeout(() => {
        bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
        loadUserProducts(currentPage); // Reload products
      }, 1000);
    } else {
      feedback.innerHTML = `<div class="alert alert-danger">${d.message || 'Failed to update product'}</div>`;
    }
  })
  .catch(err => {
    feedback.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
  });
}

// Confirm and delete product
function confirmDelete(productId, title) {
  if (!confirm(`Are you sure you want to delete "${title}"? This action cannot be undone.`)) {
    return;
  }
  
  const formData = new FormData();
  formData.append('product_id', productId);
  
  fetch('../actions/delete_product_action.php', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(d => {
    if (d.status === 'success') {
      alert('Product deleted successfully!');
      loadUserProducts(currentPage); // Reload products
    } else {
      alert('Failed to delete product: ' + (d.message || 'Unknown error'));
    }
  })
  .catch(err => {
    alert('Error deleting product: ' + err.message);
  });
}

// Helper function to escape HTML
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text ?? '';
  return div.innerHTML;
}
