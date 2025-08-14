# POS58 Printer System for Kopmensa

This document explains how to set up and use the POS58 printer system integrated with the Kopmensa application using the Mikey42 ESC/POS library.

## Features

- **Multiple Printer Support**: Configure and manage multiple printers
- **Network Printer Support**: Connect to printers via IP address and port
- **USB Printer Support**: Connect to USB printers via device path
- **File Output**: Save receipts to files for testing or archiving
- **Windows Printer Support**: Use Windows system printers
- **POS58 Driver**: Optimized for POS58 thermal printers
- **Printer Testing**: Test printer connections before use
- **Default Printer**: Set a default printer for automatic use

## Installation

### 1. Install Dependencies

```bash
composer install
```

This will install the Mikey42 ESC/POS library and other required dependencies.

### 2. Run Database Migration

```bash
php spark migrate
```

This creates the `tbl_m_printer` table for storing printer configurations.

### 3. Access Printer Management

Navigate to: **Pengaturan > Printer**

## Printer Configuration

### Supported Printer Types

1. **Network (IP)**: Connect to network-enabled printers
   - IP Address: Printer's IP address (e.g., 192.168.1.100)
   - Port: Usually 9100 for most network printers

2. **USB**: Connect to USB printers
   - Path: Device path (e.g., `/dev/usb/lp0` on Linux, `COM3` on Windows)

3. **File**: Save output to files
   - Path: File path (e.g., `/tmp/receipt.txt` or `C:\receipts\receipt.txt`)

4. **Windows**: Use Windows system printers
   - Path: Printer name as shown in Windows (e.g., "POS-58 Printer")

### Supported Drivers

- **POS58**: Optimized for POS58 thermal printers
- **Epson**: Epson ESC/POS compatible printers
- **Star**: Star thermal printers
- **Citizen**: Citizen thermal printers
- **Generic**: Generic ESC/POS compatible printers

## Adding a New Printer

### Step 1: Access Printer Management
1. Go to **Pengaturan > Printer**
2. Click **Tambah Printer**

### Step 2: Fill in Printer Details
1. **Nama Printer**: Give your printer a descriptive name
2. **Tipe Printer**: Select the connection type
3. **Connection Details**: Fill in IP/Port or Path based on type
4. **Driver**: Select the appropriate driver for your printer
5. **Lebar Kertas**: Set paper width in mm (58mm for POS58)
6. **Status**: Set to Active
7. **Default**: Check if this should be the default printer

### Step 3: Test Connection
1. Save the printer configuration
2. Use the **Test Koneksi** button to verify connectivity

## Using Printers in Cashier

### Automatic Printing
When a transaction is completed, the system will automatically use the default printer.

### Manual Printer Selection
1. Click **Cetak Struk** button
2. Select a specific printer from the dropdown
3. Test the connection if needed
4. Click **Cetak** to print

## Printer Troubleshooting

### Common Issues

#### 1. Network Printer Not Connecting
- Verify IP address and port are correct
- Check if printer is powered on and connected to network
- Ensure firewall allows connections to the printer port
- Test with `telnet [IP] [PORT]` from command line

#### 2. USB Printer Not Found
- Check if printer is properly connected
- Verify device path (use `lsusb` on Linux or Device Manager on Windows)
- Ensure proper permissions for the device path

#### 3. Windows Printer Issues
- Verify printer name matches exactly as shown in Windows
- Check if printer is set as default in Windows
- Ensure printer driver is properly installed

#### 4. Print Quality Issues
- Check paper width setting matches actual paper
- Verify driver selection matches printer model
- Clean printer head and rollers

### Testing Commands

#### Network Printer Test
```bash
# Test if port is open
telnet 192.168.1.100 9100

# Send test data (if telnet connects)
echo "TEST" | nc 192.168.1.100 9100
```

#### USB Device Check (Linux)
```bash
# List USB devices
lsusb

# Check device permissions
ls -la /dev/usb/lp0

# Test write access
echo "TEST" > /dev/usb/lp0
```

## Receipt Format

The system automatically formats receipts with:

- **Header**: Company name, address, contact info
- **Transaction Details**: Receipt number, date, cashier, customer
- **Items**: Product name, quantity, price, total
- **Totals**: Subtotal, discounts, tax, grand total
- **Footer**: Thank you message and terms

## Customization

### Modifying Receipt Format
Edit the `PrinterService.php` file to customize:
- Header content and formatting
- Item display format
- Total calculations
- Footer messages

### Adding New Printer Types
1. Add new type to `PrinterModel::getPrinterTypes()`
2. Update `PrinterService::createConnector()`
3. Add validation rules in `Printer::store()` and `Printer::update()`

## Security Considerations

- Printer management requires authentication
- Test printer connections before production use
- Monitor printer access logs
- Use network segmentation for network printers

## Performance Tips

- Use default printer for faster printing
- Test printer connections during off-peak hours
- Monitor printer queue status
- Regular printer maintenance

## Support

For technical support:
1. Check printer connection status
2. Verify printer configuration
3. Test with simple text output
4. Check system logs for errors

## File Structure

```
app/
├── Controllers/
│   └── Pengaturan/
│       └── Printer.php          # Printer management controller
├── Models/
│   └── PrinterModel.php         # Printer data model
├── Services/
│   └── PrinterService.php       # Printing service with ESC/POS
├── Views/
│   └── admin-lte-3/
│       └── pengaturan/
│           └── printer/         # Printer management views
└── Database/
    └── Migrations/
        └── create_tbl_m_printer.php  # Database migration
```

## API Endpoints

- `GET /pengaturan/printer` - List printers
- `POST /pengaturan/printer/store` - Create printer
- `GET /pengaturan/printer/edit/{id}` - Edit printer form
- `POST /pengaturan/printer/update/{id}` - Update printer
- `GET /pengaturan/printer/delete/{id}` - Delete printer
- `GET /pengaturan/printer/set-default/{id}` - Set default printer
- `GET /pengaturan/printer/test/{id}` - Test printer connection
- `POST /transaksi/jual/print-receipt/{id}` - Print receipt

## Dependencies

- **Mikey42 ESC/POS**: `mikey42/escpos-php ^3.0`
- **CodeIgniter 4**: Framework
- **AdminLTE 3**: UI framework
- **jQuery**: JavaScript library
- **Bootstrap**: CSS framework 