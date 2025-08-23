# Customer Group (Grup Pelanggan) System

## Overview
The Customer Group system has been successfully implemented and is ready to use. This system allows you to categorize customers into different groups (e.g., Umum, Anggota, Reseller) for pricing and marketing purposes.

## Features
- ✅ **Complete CRUD Operations**: Create, Read, Update, Delete customer groups
- ✅ **Soft Delete Support**: Deleted groups go to trash and can be restored
- ✅ **Customer Association**: Link customers to specific groups
- ✅ **Search & Filter**: Find groups by name, description, or customer name
- ✅ **Pagination**: Efficient data loading with pagination
- ✅ **Responsive UI**: Modern AdminLTE 3 interface
- ✅ **Validation**: Form validation with error handling
- ✅ **CSRF Protection**: Secure form submissions

## Database Structure
The system uses the `tbl_m_pelanggan_grup` table with the following structure:
- `id` - Primary key
- `id_pelanggan` - Foreign key to customer table
- `grup` - Group name (e.g., "Umum", "Anggota", "Reseller")
- `deskripsi` - Group description
- `status` - Active (1) or Inactive (0)
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

## Menu Location
The new menu is located at: **Master → Grup Pelanggan**

## Routes Available
- `GET /master/customer-group` - List all customer groups
- `GET /master/customer-group/create` - Create new group form
- `POST /master/customer-group/store` - Save new group
- `GET /master/customer-group/edit/{id}` - Edit group form
- `POST /master/customer-group/update/{id}` - Update group
- `GET /master/customer-group/detail/{id}` - View group details
- `GET /master/customer-group/delete/{id}` - Soft delete group
- `GET /master/customer-group/trash` - View deleted groups
- `GET /master/customer-group/restore/{id}` - Restore deleted group
- `GET /master/customer-group/delete_permanent/{id}` - Permanent delete

## Usage Instructions

### 1. Creating a Customer Group
1. Navigate to **Master → Grup Pelanggan**
2. Click **"Tambah Grup"** button
3. Select a customer from the dropdown
4. Enter group name (e.g., "Umum", "Anggota", "Reseller")
5. Add optional description
6. Set status (Active/Inactive)
7. Click **"Simpan"**

### 2. Managing Customer Groups
- **View**: Click the eye icon to see group details
- **Edit**: Click the edit icon to modify group information
- **Delete**: Click the trash icon to move to trash
- **Restore**: From trash, click the undo icon to restore
- **Permanent Delete**: From trash, click the trash icon to delete permanently

### 3. Searching Groups
Use the search box to find groups by:
- Group name
- Description
- Customer name
- Customer phone number

## Integration with Pricing System
The customer groups are integrated with the item pricing system:
- Groups can be used to set different prices for different customer types
- Pricing rules can be configured based on customer groups
- Checkout system can automatically apply group-based pricing

## Files Created/Modified

### New Files:
- `app/Controllers/Master/PelangganGrup.php` - Main controller
- `app/Views/admin-lte-3/master/pelanggan_grup/index.php` - List view
- `app/Views/admin-lte-3/master/pelanggan_grup/create.php` - Create form
- `app/Views/admin-lte-3/master/pelanggan_grup/edit.php` - Edit form
- `app/Views/admin-lte-3/master/pelanggan_grup/detail.php` - Detail view
- `app/Views/admin-lte-3/master/pelanggan_grup/trash.php` - Trash view

### Modified Files:
- `app/Models/PelangganGrupModel.php` - Enhanced with new methods
- `app/Views/admin-lte-3/layout/sidebar.php` - Added menu item

## Technical Details

### Controller Methods:
- `index()` - List all active groups with pagination
- `create()` - Show create form
- `store()` - Save new group
- `edit()` - Show edit form
- `update()` - Update existing group
- `detail()` - Show group details
- `delete()` - Soft delete group
- `trash()` - Show deleted groups
- `restore()` - Restore deleted group
- `delete_permanent()` - Permanent delete

### Model Methods:
- `getActiveGroups()` - Get all active groups
- `getGroupsByCustomerId()` - Get groups for specific customer
- `getUniqueGroupNames()` - Get unique group names
- `isCustomerInGroup()` - Check if customer belongs to group
- `getGroupsWithCustomerInfo()` - Get groups with customer details
- `getGroupWithCustomerInfo()` - Get single group with customer details

## Security Features
- CSRF token protection on all forms
- Authentication required for all routes
- Input validation and sanitization
- Soft delete to prevent data loss

## Browser Compatibility
- Modern browsers (Chrome, Firefox, Safari, Edge)
- Responsive design for mobile devices
- AdminLTE 3 theme integration

## Ready to Use
The system is fully functional and ready for production use. All CRUD operations, validation, and security measures are in place.

## Support
For any issues or questions, refer to the existing codebase patterns and ensure all dependencies are properly loaded.
