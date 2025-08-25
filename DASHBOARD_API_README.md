# Dashboard API Documentation

## Overview
The Dashboard API provides comprehensive business analytics and metrics for POS users. It's based on the existing web dashboard functionality but adapted for API consumption.

## Base URL
```
GET /api/pos/dashboard
```

## Authentication
All dashboard endpoints require JWT authentication. Include the JWT token in the Authorization header:
```
Authorization: Bearer <your_jwt_token>
```

## Endpoints

### 1. Main Dashboard Data
**GET** `/api/pos/dashboard`

Returns all dashboard data in one request:
- Basic metrics
- Sales analytics
- Recent transactions
- Performance metrics

**Response:**
```json
{
  "success": true,
  "message": "Dashboard data retrieved successfully",
  "data": {
    "basic_metrics": { ... },
    "analytics": { ... },
    "recent_data": { ... },
    "performance": { ... }
  }
}
```

### 2. Basic Metrics
**GET** `/api/pos/dashboard/basic-metrics`

Returns core business metrics:
- Total sales transactions
- Total revenue
- Total purchase transactions
- Total expenses
- Total profit
- Total stock count

**Response:**
```json
{
  "success": true,
  "message": "Basic metrics retrieved successfully",
  "data": {
    "total_sales_transactions": 150,
    "total_revenue": 25000000,
    "total_purchase_transactions": 45,
    "total_expenses": 18000000,
    "total_profit": 7000000,
    "total_stock": 1250
  }
}
```

### 3. Sales Analytics
**GET** `/api/pos/dashboard/sales-analytics`

Returns comprehensive sales data:
- Monthly sales (last 12 months)
- Daily sales (current month)
- Sales by category
- Top selling products

**Response:**
```json
{
  "success": true,
  "message": "Sales analytics retrieved successfully",
  "data": {
    "monthly_sales": [...],
    "daily_sales": [...],
    "sales_by_category": [...],
    "top_selling_products": [...]
  }
}
```

### 4. Recent Transactions
**GET** `/api/pos/dashboard/recent-transactions`

Returns recent business activity:
- Recent sales transactions
- Recent purchase transactions
- Latest products

**Response:**
```json
{
  "success": true,
  "message": "Recent transactions retrieved successfully",
  "data": {
    "recent_sales": [...],
    "recent_purchases": [...],
    "latest_products": [...]
  }
}
```

### 5. Performance Metrics
**GET** `/api/pos/dashboard/performance-metrics`

Returns performance indicators:
- Current vs previous month sales
- Sales growth percentage
- Average order value
- Progress towards targets
- Customer analytics

**Response:**
```json
{
  "success": true,
  "message": "Performance metrics retrieved successfully",
  "data": {
    "current_month_sales": 8500000,
    "previous_month_sales": 7200000,
    "sales_growth_percentage": 18.06,
    "average_order_value": 166666.67,
    "monthly_target": 50000000,
    "daily_target": 1612903.23,
    "today_sales": 1200000,
    "monthly_progress_percentage": 17.0,
    "daily_progress_percentage": 74.4,
    "total_customers": 89,
    "new_customers_this_month": 12
  }
}
```

### 6. Monthly Sales Data
**GET** `/api/pos/dashboard/monthly-sales`

Returns monthly sales data for charts (last 12 months).

**Response:**
```json
{
  "success": true,
  "message": "Monthly sales data retrieved successfully",
  "data": [
    {
      "month": "Jan 2024",
      "month_code": "2024-01",
      "total_sales": 8500000,
      "transaction_count": 51
    }
  ]
}
```

### 7. Daily Sales Data
**GET** `/api/pos/dashboard/daily-sales`

Returns daily sales data for current month.

**Response:**
```json
{
  "success": true,
  "message": "Daily sales data retrieved successfully",
  "data": [
    {
      "day": 1,
      "date": "2024-01-01",
      "total_sales": 1200000,
      "transaction_count": 8
    }
  ]
}
```

### 8. Sales by Category
**GET** `/api/pos/dashboard/sales-by-category`

Returns top 5 sales categories.

**Response:**
```json
{
  "success": true,
  "message": "Sales by category retrieved successfully",
  "data": [
    {
      "category_id": 1,
      "category_name": "Elektronik",
      "total_sales": 8500000,
      "total_items": 45
    }
  ]
}
```

### 9. Top Selling Products
**GET** `/api/pos/dashboard/top-products`
**GET** `/api/pos/dashboard/top-products/{limit}`

Returns top selling products (default: 5, max: customizable).

**Response:**
```json
{
  "success": true,
  "message": "Top selling products retrieved successfully",
  "data": [
    {
      "product_id": 123,
      "product_name": "Laptop ASUS",
      "total_quantity": 25,
      "total_sales": 125000000,
      "transaction_count": 18
    }
  ]
}
```

## Data Types

### Numeric Values
- **Monetary amounts**: Float values (e.g., 25000000.00)
- **Counts**: Integer values (e.g., 150)
- **Percentages**: Float values with 2 decimal places (e.g., 18.06)

### Date Formats
- **Month codes**: YYYY-MM format (e.g., "2024-01")
- **Month names**: MMM YYYY format (e.g., "Jan 2024")
- **Dates**: YYYY-MM-DD format (e.g., "2024-01-15")

## Error Handling

All endpoints return consistent error responses:

```json
{
  "success": false,
  "message": "Error description"
}
```

**HTTP Status Codes:**
- `200`: Success
- `500`: Server error

## Usage Examples

### JavaScript/Fetch
```javascript
const response = await fetch('/api/pos/dashboard', {
  headers: {
    'Authorization': 'Bearer ' + jwtToken
  }
});
const data = await response.json();
```

### cURL
```bash
curl -X GET "https://yourdomain.com/api/pos/dashboard" \
  -H "Authorization: Bearer your_jwt_token"
```

### Postman
1. Set method to `GET`
2. Set URL to `/api/pos/dashboard`
3. Add header: `Authorization: Bearer your_jwt_token`

## Performance Notes

- All endpoints are optimized for quick response times
- Data is fetched directly from database with minimal processing
- Consider implementing caching for frequently accessed data
- Large datasets (like monthly sales) are paginated where appropriate

## Security

- All endpoints require valid JWT authentication
- Data is filtered based on user permissions
- No sensitive business data is exposed
- Input validation and sanitization implemented

## Support

For technical support or questions about the Dashboard API, please contact the development team.
