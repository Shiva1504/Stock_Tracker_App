# Stock Tracker App

A Laravel-based application for tracking product stock across multiple retailers. Built following the [Laracasts In-Stock-Tracker](https://github.com/laracasts/In-Stock-Tracker) tutorial.

## Features

### ✅ Core Functionality
- **Product Management**: Add and manage products to track
- **Retailer Management**: Add and manage retailers
- **Stock Tracking**: Track stock status, prices, and URLs for each product at each retailer
- **Stock Status Checking**: Check if products are in stock or out of stock

### ✅ Web Interface
- **Dashboard**: Comprehensive overview with statistics and quick actions
- **Product Management**: Add and view products with stock status
- **Retailer Management**: Add retailers and track their inventory
- **Responsive Design**: Modern UI built with Tailwind CSS

### ✅ Automation & Notifications
- **Automated Stock Checking**: Background jobs to check stock status
- **Email Notifications**: Get notified when products come back in stock
- **Web Scraping**: Automatically check retailer websites for stock status
- **Command Line Tools**: Manual stock checking and job dispatching

### ✅ Data Management
- **Database Migrations**: Proper database structure for products, retailers, and stock
- **Eloquent Relationships**: Clean model relationships and queries
- **Validation**: Form validation and data integrity checks

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Shiva1504/Stock_Tracker_App.git
   cd Stock_Tracker_App
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   ```

5. **Start the development server**
   ```bash
   php artisan serve
   ```

## Usage

### Web Interface

1. **Dashboard** (`/`): Overview of all products, retailers, and stock status
2. **Products** (`/products`): Add and manage products
3. **Retailers** (`/retailers`): Add retailers and track their inventory

### Command Line Tools

#### Check Stock Status
```bash
# Check all products
php artisan stock:check

# Check specific product
php artisan stock:check "Product Name"
```

#### Dispatch Stock Check Job
```bash
# Manually trigger stock checking
php artisan stock:dispatch-check
```

### Adding Products and Stock

1. **Add a Product**:
   - Go to `/products`
   - Enter product name and click "Add Product"

2. **Add a Retailer**:
   - Go to `/retailers`
   - Enter retailer name and click "Add Retailer"

3. **Add Stock Information**:
   - On the retailers page, select a product
   - Enter price, URL, SKU (optional), and stock status
   - Click "Add Stock"

## Database Structure

### Products Table
- `id` - Primary key
- `name` - Product name (unique)
- `created_at`, `updated_at` - Timestamps

### Retailers Table
- `id` - Primary key
- `name` - Retailer name (unique)
- `created_at`, `updated_at` - Timestamps

### Stock Table
- `id` - Primary key
- `product_id` - Foreign key to products
- `retailer_id` - Foreign key to retailers
- `price` - Price in cents (integer)
- `url` - Product URL
- `sku` - Stock keeping unit (optional)
- `in_stock` - Boolean stock status
- `created_at`, `updated_at` - Timestamps

## Features in Detail

### Stock Status Checking
The application can automatically check stock status by:
- Making HTTP requests to product URLs
- Parsing response content for stock indicators
- Updating database with current status
- Sending notifications when products come back in stock

### Notification System
When a product comes back in stock:
- Email notifications are sent to all users
- Includes product name, retailer, price, and direct link
- Uses Laravel's notification system

### Web Scraping Logic
The stock checking job looks for common indicators:
- **Out of Stock**: "out of stock", "sold out", "unavailable", "backorder"
- **In Stock**: "add to cart", "buy now", "in stock", "available"

## Development

### Running Tests
```bash
php artisan test
```

### Database Seeding
```bash
php artisan db:seed
```

### Queue Processing
For background job processing:
```bash
php artisan queue:work
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Acknowledgments

- Built following the [Laracasts In-Stock-Tracker](https://github.com/laracasts/In-Stock-Tracker) tutorial
- Uses Laravel 11 framework
- Styled with Tailwind CSS
