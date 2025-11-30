# VendorConnect Ghana - README

## 🎉 Wedding Planning E-Commerce Platform

VendorConnect Ghana is a comprehensive digital marketplace connecting wedding planners with couples planning their special day in Ghana.

---

## 🌟 Key Features

### For Customers (Couples)
- 🔍 Browse and search wedding service packages
- 🛒 Shopping cart with customizable package items
- 💳 Secure payment with Paystack (Mobile Money, Cards, Bank Transfer)
- 📅 Book consultations with planners
- ⭐ Rate and review services
- 📍 Location-based vendor discovery
- 💝 Save favorites to wishlist

### For Vendors
- 📊 Professional dashboard to manage services
- 📦 Create and manage service packages
- 📅 Set consultation availability
- 💰 Subscription tiers (Starter GHS 29/month, Premium GHS 79/month)
- 📈 Track bookings and sales
- ⭐ Build reputation through reviews

---

## 🛠️ Tech Stack

- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript
- **Framework:** Bootstrap 5
- **Payment:** Paystack API
- **Server:** Apache (MAMP/XAMPP)

---

## 🚀 Quick Start

### Prerequisites
- XAMPP installed
- Paystack account (get free test keys)

### Installation

1. **Clone the repository**
   ```bash
   cd /Applications/MAMP/htdocs/
   git clone https://github.com/Nanaafiaasante/Authenticate-Activity-1.git
   cd Authenticate-Activity-1
   ```

2. **Import database**
   - Open phpMyAdmin: `http://localhost/phpMyAdmin`
   - Create database: `ecommerce_2025A_nana_asante`
   - Import: `db/dbforlab.sql`

3. **Configure database**
   
   Edit `settings/db_cred.php`:
   ```php
   define('SERVER', 'localhost');
   define('USERNAME', 'root');
   define('PASSWD', 'root');
   define('DATABASE', 'ecommerce_2025A_nana_asante');
   ```

4. **Configure Paystack**
   
   Edit `settings/paystack_config.php`:
   ```php
   define('PAYSTACK_SECRET_KEY', 'sk_test_YOUR_KEY');
   define('PAYSTACK_PUBLIC_KEY', 'pk_test_YOUR_KEY');
   ```

5. **Access the application**
   ```
   http://localhost/Authenticate-Activity-1/
   ```

---

## 📁 Project Structure

```
├── actions/          # API endpoints (50+ files)
├── admin/            # Vendor dashboard
├── classes/          # Business logic classes
├── controllers/      # Request handlers
├── css/              # Custom stylesheets
├── db/               # Database schema
├── js/               # JavaScript files
├── login/            # Authentication pages
├── settings/         # Configuration files
├── uploads/          # User-uploaded content
└── view/             # Customer-facing pages
```

---

## 🔐 Security Features

✅ **Password hashing** with bcrypt  
✅ **Prepared statements** for SQL injection prevention  
✅ **XSS protection** with output escaping  
✅ **Session-based authentication**  
✅ **Secure payment processing** via Paystack  
✅ **Input validation** on client and server  

---

## 💳 Test Payment

Use these Paystack test cards:

**Success:**
- Card: `4084084084084081`
- CVV: `408`
- Expiry: `12/30`
- PIN: `0000`
- OTP: `123456`

**Decline:**
- Card: `5060990580000217634`

---

## 📚 Documentation

- **Technical Docs:** [PROJECT_DOCUMENTATION.md](PROJECT_DOCUMENTATION.md)


---



---

## 🌍 African Digital Fund Alignment

This project addresses ADF priority areas:

✅ **Financial Inclusion** - Mobile money payment integration  
✅ **Market Access** - Connects vendors with customers nationwide  
✅ **Affordable Services** - Tiered subscription (GHS 29-79/month)  
✅ **Digital Inclusion** - Web-based, accessible from any device  
✅ **TRL 6-7** - Fully functional operational system  

---

## 📊 Database Schema

15 tables with comprehensive relationships:

- `customer` - Users (customers & planners)
- `products` - Service packages
- `cart` - Shopping cart
- `orders` - Completed orders
- `payment` - Payment records
- `consultations` - Booking system
- `wishlist` - Saved items
- And more...

See full schema in `db/dbforlab.sql`

---

## 🎨 Design System

**Colors:**
- Primary: Emerald Green (#1e4d2b)
- Accent: Gold (#C9A961)
- Background: Light gradients

**Typography:**
- Headings: Playfair Display (elegant serif)
- Body: Inter (clean sans-serif)

**Visual Elements:**
- Botanical corner decorations
- Gold accent dots
- Gradient backgrounds
- Professional card layouts

---

## 🔄 User Flows

### Customer Journey
```
Browse Products → Add to Cart → Checkout → Pay with Paystack → Order Confirmation
```

### Vendor Journey
```
Register as Planner → Select Subscription → Create Service Packages → Receive Bookings
```

### Consultation Flow
```
View Vendor Profile → Book Consultation → Select Date/Time → Pay Fee → Confirmed
```

---

## 📱 Pages Overview

### Public Pages
- Homepage with hero section
- Product listing with filters
- Product search results
- Single product details
- Vendor public profiles

### Customer Pages
- Shopping cart
- Checkout
- Order history
- Consultation booking
- Wishlist
- Payment success

### Vendor Dashboard
- My products
- Add/edit products
- Manage categories & brands
- Set availability
- View consultations
- Analytics (future)

### Authentication
- Login
- Register (role selection)
- Subscription selection
- Password reset

---

## 🔌 Key API Endpoints

### Products
- `GET /actions/view_all_products_action.php`
- `POST /actions/filter_products_action.php`
- `GET /actions/search_products_action.php`

### Cart
- `GET /actions/get_cart_action.php`
- `POST /actions/add_to_cart_action.php`
- `POST /actions/update_quantity_action.php`

### Payment
- `POST /actions/paystack_init_transaction.php`
- `POST /actions/paystack_verify_payment.php`

### Orders
- `GET /actions/get_user_orders_action.php`
- `POST /actions/submit_rating_action.php`

---



## 🚀 Future Enhancements

1. ✨ **AI-powered recommendations** based on browsing history
2. 📧 **Email notifications** for orders and bookings
3. 📱 **SMS reminders** via Africa's Talking
4. 💬 **Real-time chat** between customers and vendors
5. 📊 **Advanced analytics** dashboard for vendors
6. 🌐 **Multi-language support** (English, Twi, French)
7. 🔍 **SEO optimization** with meta tags
8. 📲 **Progressive Web App** for offline access
9. 🔗 **Social media integration** for sharing
10. 🎯 **Marketing tools** for vendors (email campaigns)


---

## 📈 Project Stats

- **Lines of Code:** 15,000+ (estimated)
- **Files:** 100+
- **Database Tables:** 15
- **API Endpoints:** 50+
- **Pages:** 30+
- **Development Time:** 8 weeks

---




**Built with ❤️ for Ghana's wedding industry**

---
