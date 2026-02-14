# Chariow Store Manager - WordPress Plugin

A comprehensive WordPress plugin that provides a complete PHP wrapper for the Chariow.dev API, enabling you to manage your digital store directly from WordPress.

## 🚀 Features

- **Complete API Coverage**: Full support for all Chariow API endpoints
  - Store information
  - Products (CRUD operations)
  - Checkout sessions
  - Sales management
  - Customer management
  - License management (activate, deactivate, validate)
  - Discount codes (CRUD operations)
  - Webhook pulses
  
- **Easy Configuration**: Set API key via WordPress admin or environment variable
- **WordPress Integration**: Native WordPress admin interface
- **Developer Friendly**: Clean, well-documented PHP API
- **Rate Limiting**: Automatic rate limit tracking
- **Error Handling**: Comprehensive error handling with WP_Error integration

## 📦 Installation

### Method 1: Manual Installation

1. Download or clone this repository
2. Upload the `chariowdev` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to **Chariow Store** → **Settings** to configure your API key

### Method 2: Via Git

```bash
cd wp-content/plugins
git clone <repository-url> chariowdev
```

Then activate the plugin in WordPress admin.

## ⚙️ Configuration

### Option 1: WordPress Admin (Recommended for most users)

1. Go to **Chariow Store** → **Settings**
2. Enter your Chariow API key
3. Click **Save Changes**
4. Click **Test Connection** to verify

### Option 2: Environment Variable (Recommended for production)

Add to your `wp-config.php`:

```php
define( 'CHARIOW_API_KEY', 'sk_live_your_api_key_here' );
```

Or set as a server environment variable:

```bash
export CHARIOW_API_KEY=sk_live_your_api_key_here
```

### Getting Your API Key

Generate your API key at: [https://app.chariow.com/settings/api](https://app.chariow.com/settings/api)

## 🔌 Shortcodes

The plugin provides shortcodes to easily integrate Chariow into your WordPress content.

### Checkout Button

Displays a button that redirects the user to a Chariow checkout session for a specific product.

```
[chariow_checkout product_id="prd_abc123" label="Buy Now" class="my-button-class"]
```

**Attributes:**
- `product_id`: (Required) The ID of the product in Chariow.
- `label`: (Optional) The text to display on the button. Default: "Buy Now".
- `class`: (Optional) CSS class for the button. Default: "chariow-button".

## 📖 Usage

### Basic Usage

```php
// Get the API client instance
$api = chariow_api();

// Check if API is configured
if ( ! $api->is_configured() ) {
    // Handle not configured
}
```

### Store API

```php
// Get store information
$store = $api->store()->get();

if ( ! is_wp_error( $store ) ) {
    $store_data = $store['data'];
    echo $store_data['name'];
}
```

### Products API

```php
// List all products
$products = $api->products()->list( array(
    'per_page' => 20,
    'cursor'   => 'optional_cursor_for_pagination'
) );

// Get a single product
$product = $api->products()->get( 'prd_abc123' );

// Create a product
$new_product = $api->products()->create( array(
    'name'        => 'My Digital Product',
    'description' => 'Product description',
    'price'       => 99.00,
    // ... other product data
) );

// Update a product
$updated = $api->products()->update( 'prd_abc123', array(
    'name' => 'Updated Product Name'
) );

// Delete a product
$deleted = $api->products()->delete( 'prd_abc123' );
```

### Checkout API

```php
// Initialize a checkout session
$checkout = $api->checkout()->init( array(
    'product_id' => 'prd_abc123',
    'email'      => 'customer@example.com',
    'name'       => 'John Doe'
) );

// Get checkout session
$session = $api->checkout()->get( 'checkout_session_id' );
```

### Sales API

```php
// List all sales
$sales = $api->sales()->list( array(
    'per_page' => 20
) );

// Get a single sale
$sale = $api->sales()->get( 'sale_abc123' );

// Update a sale
$updated_sale = $api->sales()->update( 'sale_abc123', array(
    'status' => 'completed'
) );
```

### Customers API

```php
// List all customers
$customers = $api->customers()->list();

// Get a single customer
$customer = $api->customers()->get( 'cus_abc123' );

// Create a customer
$new_customer = $api->customers()->create( array(
    'email' => 'customer@example.com',
    'name'  => 'Jane Doe'
) );

// Update a customer
$updated_customer = $api->customers()->update( 'cus_abc123', array(
    'name' => 'Jane Smith'
) );
```

### Licenses API

```php
// List all licenses
$licenses = $api->licenses()->list();

// Get a single license
$license = $api->licenses()->get( 'lic_abc123' );

// Activate a license
$activated = $api->licenses()->activate( 'license_key_here', array(
    'instance_id' => 'unique_instance_id',
    'domain'      => 'example.com'
) );

// Deactivate a license
$deactivated = $api->licenses()->deactivate( 'license_key_here', array(
    'instance_id' => 'unique_instance_id'
) );

// Validate a license
$validation = $api->licenses()->validate( 'license_key_here' );

if ( ! is_wp_error( $validation ) && $validation['data']['valid'] ) {
    // License is valid
}
```

### Discounts API

```php
// List all discounts
$discounts = $api->discounts()->list();

// Get a single discount
$discount = $api->discounts()->get( 'dis_abc123' );

// Create a discount
$new_discount = $api->discounts()->create( array(
    'code'       => 'SAVE20',
    'type'       => 'percentage',
    'value'      => 20,
    'expires_at' => '2024-12-31'
) );

// Update a discount
$updated_discount = $api->discounts()->update( 'dis_abc123', array(
    'value' => 25
) );

// Delete a discount
$deleted_discount = $api->discounts()->delete( 'dis_abc123' );
```

### Pulses (Webhooks) API

```php
// List all webhook pulses
$pulses = $api->pulses()->list();

// Get a single pulse
$pulse = $api->pulses()->get( 'pulse_abc123' );
```

## 🔧 Helper Functions

### Check API Configuration

```php
if ( Chariow_Store_Manager_Helper::is_api_key_configured() ) {
    // API key is set
}
```

### Get API Key Source

```php
$source = Chariow_Store_Manager_Helper::get_api_key_source();
// Returns: 'options', 'environment', or 'not_set'
```

### Check Response Success

```php
$response = $api->products()->list();

if ( Chariow_Store_Manager_Helper::is_success( $response ) ) {
    $data = Chariow_Store_Manager_Helper::get_response_data( $response );
    // Process data
}
```

### Format Errors

```php
$response = $api->products()->get( 'invalid_id' );

if ( is_wp_error( $response ) ) {
    $error_message = Chariow_Store_Manager_Helper::format_error( $response );
    echo $error_message;
}
```

## 📊 Response Format

All API responses follow the Chariow API format:

### Successful Response

```php
array(
    'message' => 'success',
    'data'    => array(
        // Response data
    ),
    'errors'  => array()
)
```

### Error Response

```php
WP_Error object with:
- Error code
- Error message
- Error data (including HTTP status code and full response)
```

### Paginated Response

```php
array(
    'message' => 'success',
    'data'    => array(
        'data'       => array( /* items */ ),
        'pagination' => array(
            'next_cursor' => 'cursor_string',
            'prev_cursor' => null,
            'has_more'    => true
        )
    ),
    'errors'  => array()
)
```

## 🔒 Security Best Practices

1. **Use Environment Variables**: For production sites, always use environment variables instead of storing API keys in the database
2. **Restrict Access**: Only give admin access to trusted users
3. **Use HTTPS**: Always use HTTPS on your WordPress site
4. **Keep Updated**: Regularly update the plugin and WordPress core

## 🐛 Debugging

Enable WordPress debug mode to see API request/response logs:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

Logs will be written to `wp-content/debug.log`

## 📝 Rate Limiting

The plugin automatically tracks rate limit information from API responses:

```php
$api = chariow_api();
$api->products()->list();

$rate_limit = $api->get_rate_limit();
// Returns:
// array(
//     'limit'     => 100,
//     'remaining' => 98,
//     'reset'     => 1642089600
// )
```

## 🆘 Troubleshooting

### "API key is not configured" error

- Check that you've entered your API key in Settings or set the `CHARIOW_API_KEY` environment variable
- Verify the API key starts with `sk_live_` or `sk_test_`

### Connection test fails

- Verify your API key is correct
- Check that your server can make outbound HTTPS requests
- Ensure `wp_remote_get()` and `wp_remote_post()` are not blocked

### "Too Many Requests" error

- You've hit the rate limit (100 requests per minute by default)
- Wait for the rate limit to reset (check `X-RateLimit-Reset` header)
- Implement caching to reduce API calls

## 📚 Resources

- [Chariow Documentation](https://chariow.dev/en/introduction/overview)
- [Chariow API Reference](https://chariow.dev/api-reference/introduction)
- [Get API Keys](https://app.chariow.com/settings/api)
- [Chariow Dashboard](https://app.chariow.com)
- [Support](https://help.chariow.com)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This plugin is licensed under the GPL v2 or later.

## 🔗 Links

- [Chariow Website](https://chariow.com)
- [Chariow Developers](https://chariow.dev)
- [API Documentation](https://chariow.dev/api-reference/introduction)

## ⚡ Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- Active Chariow account with API access

## 📧 Support

For plugin support, please visit [Chariow Help Center](https://help.chariow.com)

For API-related questions, check the [Chariow API Documentation](https://chariow.dev/api-reference/introduction)

---

Made with ❤️ for the Chariow community
