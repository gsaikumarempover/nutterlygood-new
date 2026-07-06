# Nutterly Good - Website Management Guide

A step-by-step guide for updating products, banners, content, and all site settings.

---

## Table of Contents

1. [Logging In](#1-logging-in)
2. [Dashboard Overview](#2-dashboard-overview)
3. [Managing Products](#3-managing-products)
4. [Managing Product Categories](#4-managing-product-categories)
5. [Homepage Banners & Hero Slider](#5-homepage-banners--hero-slider)
6. [Homepage Sections](#6-homepage-sections)
7. [Product Images & HD Images](#7-product-images--hd-images)
8. [Blogs](#8-blogs)
9. [Google Reviews](#9-google-reviews)
10. [Contact Page](#10-contact-page)
11. [About Us Page](#11-about-us-page)
12. [Footer & Navigation Menus](#12-footer--navigation-menus)
13. [Shop Page Settings](#13-shop-page-settings)
14. [WooCommerce Settings](#14-woocommerce-settings)
15. [Payment Gateway (Razorpay)](#15-payment-gateway-razorpay)
16. [Shipping (Shiprocket)](#16-shipping-shiprocket)
17. [Coupons & Discounts](#17-coupons--discounts)
18. [Orders Management](#18-orders-management)
19. [Pages & Page Templates](#19-pages--page-templates)
20. [Quick Reference Cheat Sheet](#20-quick-reference-cheat-sheet)

---

## 1. Logging In

**Admin URL:** `http://dev.nutterlygood.com/wp-admin/` (local: `http://localhost/nutterlyGood/wp-admin/`)

- Enter your **username** and **password**
- Click **Log In**

**Tip:** Bookmark this URL for quick access.

---

## 2. Dashboard Overview

After logging in, you'll see the WordPress Dashboard. Key sections in the left sidebar:

| Menu Item | What It Controls |
|---|---|
| **Products** | Add, edit, delete products and categories |
| **WooCommerce** | Orders, coupons, settings, reports |
| **Posts** | Blog articles |
| **Media** | All uploaded images and files |
| **Pages** | Static pages (About, Contact, etc.) |
| **Appearance** | Menus, widgets |
| **Elementor** | Visual page builder (homepage) |
| **RevSlider** | Homepage hero slider |

---

## 3. Managing Products

### Adding a New Product

1. Go to **Products > Add New** in the left sidebar
2. Fill in these fields:

| Field | What to Enter | Example |
|---|---|---|
| **Product name** (top) | Full product name | Coffee Mocha Almonds |
| **Product description** (main editor) | Long description with details, ingredients, benefits | Rich text with images |
| **Product short description** (below) | One-line tagline shown near price | Premium almonds with coffee coating |

3. On the right sidebar:
   - **Product image** - Click "Set product image" to upload the main photo
   - **Product gallery** - Click "Add product gallery images" for additional photos (3-4 recommended)

4. In the **Product data** section (middle of page), configure:

#### General Tab
| Field | What to Enter |
|---|---|
| **Regular Price** | Original MRP (e.g., 475) |
| **Sale Price** | Discounted price (e.g., 399) |

#### Inventory Tab
| Field | What to Enter |
|---|---|
| **SKU** | Unique code (e.g., NG-CMA-250) |
| **Stock status** | In Stock / Out of Stock / On Backorder |
| **Manage stock?** | Check this to enable stock tracking |
| **Stock quantity** | Number of items available |

#### Shipping Tab
| Field | What to Enter |
|---|---|
| **Weight** | Product weight (e.g., 0.25) |
| **Dimensions** | Length, Width, Height in cm |

#### Linked Products Tab
| Field | What to Enter |
|---|---|
| **Upsells** | Products to suggest at higher price |
| **Cross-sells** | Products shown in cart as suggestions |

5. Click **Publish** (or **Update** if editing)

### Custom Product Fields (Nutterly Good Specific)

Scroll down below the main editor to find these custom fields:

| Field Name | What to Enter | Example |
|---|---|---|
| **Product Subtitle** | Short tagline for the product card | "Rich coffee-coated almonds" |
| **MRP** | Maximum Retail Price | 475 |
| **Offer Price** | Current selling price | 399 |
| **Country of Origin** | Where product is made | India |
| **Shelf Life** | How long it lasts | 6 months from manufacturing |
| **Ingredients** | List of ingredients | Almonds, Coffee Extract, Cocoa |
| **Packed By** | Manufacturer/packer name | Nutterly Good Foods Pvt Ltd |
| **Show Offer Badge** | Toggle for "% OFF" badge on product card | Yes / No |

### Size/Weight Variants

For products that come in multiple sizes (250g, 500g, 1kg, 2kg):

1. Edit the product
2. Look for **"Farmley Sizes"** meta box
3. Each row has:
   - **Weight**: e.g., "250 g"
   - **Price**: selling price for this weight
   - **MRP**: original price for this weight
   - **Image**: optional image for this size
4. Click **"Add Row"** for each weight variant
5. Products in these categories **auto-generate** weight tiers:
   - Dry Fruits, Almonds, Cashews, Cranberry, Kishmish, Walnuts
   - Tiers: 250g, 500g, 1kg, 2kg (auto-calculated prices)

### Product Status Options

| Status | Meaning |
|---|---|
| **Published** | Live on the website |
| **Draft** | Saved but not visible |
| **Pending Review** | Submitted for review |
| **Private** | Only visible to logged-in admins |

### Duplicating a Product

1. Go to **Products > All Products**
2. Hover over the product
3. Click **"Duplicate"**
4. Edit the copy with new details

### Deleting a Product

1. Go to **Products > All Products**
2. Hover over the product
3. Click **"Trash"** (moves to trash, can be restored)

---

## 4. Managing Product Categories

### Adding a New Category

1. Go to **Products > Categories**
2. Fill in:
   - **Name**: Category name (e.g., "Protein Bars")
   - **Slug**: URL-friendly name (e.g., "protein-bars")
   - **Parent**: Select a parent category if this is a sub-category
   - **Description**: Brief description (optional)
   - **Thumbnail**: Upload a category icon/image
3. Click **Add New Category**

### Editing a Category

1. Go to **Products > Categories**
2. Hover over the category name
3. Click **Edit**
4. Update fields and click **Update**

### Current Categories

| Category | Slug | Type |
|---|---|---|
| Dry Fruits | dry-fruits | Main |
| Chips | chips | Main |
| Mixes | mixes | Main |
| Brittles | brittles | Main |
| Mouth Fresheners | mouth-fresheners | Main |

### Category Icons (Homepage)

Category icons on the homepage are loaded from a JSON file. To update:

1. Go to **Media > Library**
2. Upload the new icon image
3. Note the attachment ID
4. Contact developer to update `nutterly-category-icons.json` in the site root

**Recommended icon size:** 200x200 pixels, PNG or SVG format

---

## 5. Homepage Banners & Hero Slider

### Hero Slider (Main Banner)

The homepage hero slider uses **Revolution Slider**:

1. Go to **RevSlider** in the left sidebar
2. Find the slider named **"main-home"**
3. Click **Edit** on the slider
4. To change a slide:
   - Click on the slide thumbnail
   - Change the **background image** (recommended: 1920x800 pixels)
   - Edit the **text layers** (title, subtitle, button)
   - Adjust **timing** and **animations**
5. Click **Save** when done

**Image specifications:**
- Format: JPG or PNG
- Size: 1920 x 800 pixels
- File size: Under 500KB for fast loading

### Homepage Banner Grid

Below the hero slider, there's a grid of promotional banners. These are managed via **Elementor**:

1. Go to **Pages** in the left sidebar
2. Find **"Home"** page
3. Click **Edit with Elementor**
4. Scroll to the banner grid section
5. Click on each banner to edit:
   - **Background image**
   - **Title text**
   - **Link URL**
6. Click **Update** to save

**Banner image specifications:**
- Size: 600x400 pixels (each banner)
- Format: JPG or WebP

---

## 6. Homepage Sections

The homepage is built with **Elementor** page builder. To edit any section:

1. Go to **Pages > Home**
2. Click **Edit with Elementor**
3. Each section is editable by clicking on it

### Homepage Sections Layout

| Section | What It Shows | How to Edit |
|---|---|---|
| **Hero Slider** | Large rotating banner | RevSlider (see Section 5) |
| **Category Icons** | Row of 5 category icons | Hardcoded (contact developer) |
| **Featured Products** | 8 product cards in grid | Elementor widget (auto-populated) |
| **Premium Snacks** | Horizontal scrolling products | Elementor widget (auto-populated) |
| **Blog Posts** | Latest 3 blog posts | Elementor widget (auto-populated) |
| **Google Reviews** | Customer review carousel | PHP section (see Section 9) |
| **Newsletter** | Email signup form | PHP section (auto-managed) |

### Featured Products Section

This section shows 8 products. To change which products appear:

**Option A: By Category (Recommended)**
1. Edit the product
2. Assign it to the correct category
3. Products from these categories appear: Dry Fruits, Chips, Mixes, Brittles, Mouth Fresheners

**Option B: Manual Override (Developer Required)**
Contact developer to modify `inc/farmley/home-featured-products.php`

### Premium Snacks Section

Similar to Featured Products but shows 9 products in horizontal layout. Same category-based filtering applies.

### Blog Section on Homepage

Shows the 3 most recent published posts. To add a post to the homepage:

1. Go to **Posts > Add New**
2. Write your blog post
3. Click **Publish**
4. It automatically appears in the homepage blog section

---

## 7. Product Images & HD Images

### Image Requirements

| Image Type | Recommended Size | Format | Where Used |
|---|---|---|---|
| **Product Main Image** | 1200x1200 px | WebP or PNG | Product cards, product page |
| **Product Gallery** | 1200x1200 px | WebP or PNG | Product page gallery |
| **Category Thumbnail** | 200x200 px | PNG or SVG | Category icons |
| **Blog Featured Image** | 1200x630 px | JPG or PNG | Blog listing, social sharing |
| **Hero Banner** | 1920x800 px | JPG | Homepage slider |
| **Promo Banner** | 600x400 px | JPG or WebP | Homepage banner grid |

### Uploading Product Images

1. Go to **Products > All Products**
2. Click on the product to edit
3. **Main Image:**
   - In the right sidebar, click **"Set product image"**
   - Upload or select from Media Library
   - Click **"Set product image"**
4. **Gallery Images:**
   - In the right sidebar, click **"Add product gallery images"**
   - Select multiple images
   - Click **"Add to gallery"**

### HD Image Generation

For generating high-quality product images:

1. **Prepare a prompt** describing the product shot
2. **Use AI image generation** (requires API key setup)
3. **Save to:** `wp-content/uploads/ng-media/misc/`
4. **Update database:** The `_ng_hd_image_id` field on each product links to the HD image

### Image Naming Convention

Follow this pattern: `Product-Name-Variant.webp`

Examples:
- `Coffee-Mocha-Almonds.webp` (main image)
- `Coffee-Mocha-Almonds-1.webp` (gallery image)

### Where Images Appear

| Location | Image Source |
|---|---|
| Product card (shop page) | `_ng_hd_image_id` meta field, falls back to featured image |
| Product card hover | First different AI gallery image |
| Product page main | Featured image |
| Product page gallery | Product gallery images |
| Category page | Featured image |

---

## 8. Blogs

### Adding a New Blog Post

1. Go to **Posts > Add New**
2. Fill in:
   - **Title**: Blog post title
   - **Content**: Main blog content (use the visual editor)
   - **Featured Image**: Click in the right sidebar to set
   - **Categories**: Assign to a category
   - **Tags**: Add relevant tags
3. Click **Publish**

### Blog Post Tips

- **Featured Image:** Always set one (1200x630 px recommended)
- **Categories:** Use existing categories or create new ones
- **Excerpt:** Write a short summary in the "Excerpt" field (right sidebar)
- **Content:** Use headings (H2, H3) for structure

### Blog Posts on Homepage

The 3 most recent published posts automatically appear in the homepage blog section. No extra configuration needed.

### Special Blog Content

Some blog posts have enhanced formatting. If your post slug matches one of these, special content is auto-appended:

- `protein-rich-trail-mixes-for-busy-days`
- `a-guide-to-choosing-quality-almonds`
- `how-to-store-dry-fruits-at-home`
- `healthy-snacking-with-nuts-and-seeds`
- `why-soaked-dry-fruits-are-better-for-digestion`
- `best-mouth-fresheners-for-after-meals`

Contact developer if you need to add more enhanced blog posts.

---

## 9. Google Reviews

### Current Setup

The Google Reviews section on the homepage displays customer reviews. It works in two ways:

**Method 1: Google Places API (Automatic)**
- Reviews are fetched automatically from Google
- Configured via WordPress options:
  - `ng_farmley_google_places_api_key` - Your Google API key
  - `ng_farmley_google_place_id` - Your business place ID
- Reviews are cached for 24 hours

**Method 2: Hardcoded Reviews (Fallback)**
- If the API is not configured, 6 default reviews are shown
- These can be edited by a developer in `inc/farmley/home-google-reviews.php`

### Updating Reviews via API

1. Get a **Google Places API key** from Google Cloud Console
2. Find your **Place ID** at: https://developers.google.com/maps/documentation/places/web-service/place-id
3. Add to WordPress database via **phpMyAdmin** or ask developer to set:
   ```
   Option name: ng_farmley_google_places_api_key
   Option value: YOUR_API_KEY_HERE

   Option name: ng_farmley_google_place_id
   Option value: YOUR_PLACE_ID_HERE
   ```

### Adding/Editing Hardcoded Reviews

Contact developer to edit the `nuttergood_farmley_google_reviews_defaults()` function in:
`wp-content/themes/nuttergood/inc/farmley/home-google-reviews.php`

Each review has:
- **Author name**
- **Rating** (1-5 stars)
- **Review text**
- **Profile image** (optional)

---

## 10. Contact Page

### Editing Contact Details

The contact page uses a custom template. Contact information is stored in the theme code.

**To update contact details, contact the developer to edit:**
`wp-content/themes/nuttergood/inc/farmley/contact-page.php`

The following can be updated:

| Field | Where to Update |
|---|---|
| **Email address** | Developer edits `contact-page.php` |
| **Phone number** | Developer edits `contact-page.php` |
| **WhatsApp number** | Developer edits `contact-page.php` |
| **Office address** | Developer edits `contact-page.php` |
| **Business hours** | Developer edits `contact-page.php` |
| **Google Map embed** | Developer edits `contact-page.php` |

### Contact Form

The contact form on the page sends emails to the site admin. Form submissions go to the email configured in **Settings > General**.

### Updating Contact Form Email Recipient

1. Go to **Settings > General**
2. Update **Administration Email Address**
3. Contact form submissions will be sent to this email

---

## 11. About Us Page

### Editing About Page Content

The About page uses a custom template. Key sections:

| Section | How to Update |
|---|---|
| **Hero Image** | Replace `ng-media/about/ng-about-hero.jpg` via Media Library |
| **Brand Story** | Developer edits `inc/farmley/about-page.php` |
| **Stats Bar** | Developer edits `inc/farmley/about-page.php` |
| **Category Grid** | Developer edits `inc/farmley/about-page.php` |
| **Promise Section** | Developer edits `inc/farmley/about-page.php` |

### About Page Template

The page uses the "Farmley About" template. To view:
1. Go to **Pages > About Us**
2. Click **Edit**
3. In the right sidebar, check **Page Attributes > Template**

---

## 12. Footer & Navigation Menus

### Editing Footer Content

The footer has three main areas:

#### Brand Column (Left)
| Element | How to Update |
|---|---|
| **Logo** | Developer updates `ng_farmley_footer_logo_id` option |
| **Tagline** | Developer edits `inc/farmley/footer.php` |
| **Address** | Developer edits `inc/farmley/footer.php` |
| **Email** | Developer edits `inc/farmley/footer.php` |
| **Phone** | Developer edits `inc/farmley/footer.php` |

#### Navigation Columns (Middle)
Three menu columns. To edit:

1. Go to **Appearance > Menus**
2. Select the menu from the dropdown:
   - **Footer Menu 1** - Quick Links
   - **Footer Menu 2** - Shop
   - **Footer Menu 3** - Customer Care
3. Add, remove, or reorder menu items
4. Click **Save Menu**

#### Bottom Bar
| Element | How to Update |
|---|---|
| **Social Icons** | Developer edits SVGs in `inc/farmley/footer.php` |
| **Copyright Text** | Developer edits `inc/farmley/footer.php` |
| **Policy Links** | Auto-generated (Privacy, Refund, Terms) |

### Editing Main Navigation (Header Menu)

1. Go to **Appearance > Menus**
2. Select **"Main Menu"** (or the header menu)
3. Add/remove/reorder items
4. Click **Save Menu**

### Creating a New Menu

1. Go to **Appearance > Menus**
2. Click **"create a new menu"**
3. Name it and click **Create Menu**
4. Add pages from the left panel
5. Click **Save Menu**

---

## 13. Shop Page Settings

### Shop Page Layout

The shop page uses a custom layout with:
- **3-column product grid**
- **Sidebar with filters** (category, price, discount)
- **12 products per page**
- **Sort options:** Popularity, Price Low-High, Price High-Low

### Changing Products Per Page

Contact developer to modify `nuttergood_farmley_shop_product_list_atts()` in:
`wp-content/themes/nuttergood/inc/farmley/shop.php`

### Category Pages

Category pages use the `content-product-farmley.php` template. Product cards show:
- Main image (with hover swap)
- Product name
- Weight/size pills
- Price with MRP strikethrough
- Add to Cart + Buy Now buttons
- Wishlist, Quick View, Compare icons

---

## 14. WooCommerce Settings

### General Settings

1. Go to **WooCommerce > Settings**
2. **General tab:**
   - Store Address
   - General options (currency, selling locations)

### Product Settings

1. Go to **WooCommerce > Settings > Products**
2. **Shop page:** Select your shop page
3. **Weight unit:** kg / g
4. **Dimensions unit:** cm

### Inventory Settings

1. Go to **WooCommerce > Settings > Products > Inventory**
2. Enable stock management
3. Set low stock threshold
4. Set out of stock threshold

### Email Settings

1. Go to **WooCommerce > Settings > Emails**
2. Configure:
   - New order notifications
   - Processing order
   - Completed order
   - Refunded order
   - Failed order

---

## 15. Payment Gateway (Razorpay)

### Configuring Razorpay

1. Go to **WooCommerce > Settings > Payments**
2. Click **Manage** next to Razorpay
3. Enter:
   - **Razorpay Key ID** (from Razorpay dashboard)
   - **Razorpay Key Secret** (from Razorpay dashboard)
4. Enable/disable as needed
5. Click **Save Changes**

### Getting Razorpay Credentials

1. Log in to https://dashboard.razorpay.com
2. Go to **Settings > API Keys**
3. Generate keys
4. Copy Key ID and Key Secret

---

## 16. Shipping (Shiprocket)

### Configuring Shiprocket

1. Go to **WooCommerce > Settings > Shipping**
2. Click on **Shiprocket** shipping method
3. Enter your Shiprocket API credentials
4. Configure shipping zones and rates

### Shiprocket Dashboard

1. Log in to https://app.shiprocket.in
2. Manage shipments, track orders, generate labels

---

## 17. Coupons & Discounts

### Creating a Coupon

1. Go to **WooCommerce > Coupons > Add Coupon**
2. Fill in:
   - **Coupon code** (e.g., WELCOME20)
   - **Description** (optional)
   - **Discount type:** Percentage / Fixed cart / Fixed product
   - **Coupon amount:** (e.g., 20 for 20% off)
   - **Usage restriction:** Min spend, max spend, products, categories
   - **Usage limits:** Per coupon, per user
3. Click **Publish**

### Newsletter Coupon System

The site auto-generates coupons for newsletter subscribers:
- Format: `NG20` + random hash
- 20% discount
- Created automatically when someone signs up for the newsletter

---

## 18. Orders Management

### Viewing Orders

1. Go to **WooCommerce > Orders**
2. See all orders with status:
   - **Pending payment** - Awaiting payment
   - **Processing** - Paid, needs fulfillment
   - **On hold** - Awaiting confirmation
   - **Completed** - Fulfilled and shipped
   - **Cancelled** - Cancelled by customer/admin
   - **Refunded** - Payment refunded
   - **Failed** - Payment failed

### Processing an Order

1. Click on the order
2. Review order details
3. Update status to **"Processing"** or **"Completed"**
4. Add order notes for internal tracking
5. Click **Update**

### Printing Invoices

1. Go to **WooCommerce > Orders**
2. Select orders
3. Use Shiprocket plugin to generate invoices and shipping labels

---

## 19. Pages & Page Templates

### Available Page Templates

| Template | When to Use |
|---|---|
| **Default** | Standard page layout |
| **Full Width** | Page without sidebar |
| **Blank** | No header or footer (for landing pages) |
| **Farmley About** | About Us page (auto-assigned) |
| **Farmley Contact** | Contact page (auto-assigned) |

### How to Change Page Template

1. Go to **Pages > All Pages**
2. Click on the page to edit
3. In the right sidebar, find **Page Attributes**
4. Select the **Template** from dropdown
5. Click **Update**

### Creating a New Page

1. Go to **Pages > Add New**
2. Enter the page title
3. Add content using:
   - **Visual editor** for simple content
   - **Edit with Elementor** for visual design
4. Set the page template if needed
5. Click **Publish**

---

## 20. Quick Reference Cheat Sheet

### Common Tasks - Quick Links

| Task | Where to Go |
|---|---|
| Add a product | Products > Add New |
| Edit a product | Products > All Products > Click product |
| Add a blog post | Posts > Add New |
| Upload an image | Media > Add New |
| Create a coupon | WooCommerce > Coupons > Add Coupon |
| View orders | WooCommerce > Orders |
| Edit menus | Appearance > Menus |
| Edit homepage | Pages > Home > Edit with Elementor |
| Edit hero slider | RevSlider > Edit main-home |
| Change site logo | Appearance > Customize > Site Identity |
| Change footer | Developer edits `inc/farmley/footer.php` |

### Image Size Quick Reference

| Image | Size | Format |
|---|---|---|
| Product image | 1200x1200 px | WebP/PNG |
| Category icon | 200x200 px | PNG/SVG |
| Hero banner | 1920x800 px | JPG |
| Blog featured | 1200x630 px | JPG/PNG |
| Promo banner | 600x400 px | JPG/WebP |

### Important File Locations

| File | Purpose |
|---|---|
| `wp-content/themes/nuttergood/inc/farmley/footer.php` | Footer content |
| `wp-content/themes/nuttergood/inc/farmley/contact-page.php` | Contact page |
| `wp-content/themes/nuttergood/inc/farmley/about-page.php` | About page |
| `wp-content/themes/nuttergood/inc/farmley/home-google-reviews.php` | Google Reviews |
| `wp-content/themes/nuttergood/inc/farmley/product-cards.php` | Product card layout |
| `wp-content/themes/nuttergood/inc/farmley/product-meta.php` | Product custom fields |
| `wp-content/uploads/ng-media/misc/` | Product images |
| `wp-content/uploads/ng-media/banners/` | Banner images |

### WordPress Admin Keyboard Shortcuts

| Shortcut | Action |
|---|---|
| `S` | Save draft |
| `Ctrl + Enter` | Publish/Update |
| `Shift + Alt + H` | Shortcuts help |

---

## Support

For any issues or questions:
- **Technical issues:** Contact your developer
- **Content updates:** Follow this guide
- **Emergency:** Contact site administrator

---

*Last updated: July 2026*
*Site: Nutterly Good (nutterlyGood)*
