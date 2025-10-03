# Booking Import Template Guide

## What You Asked About - You're Right! 

You were absolutely correct to question the Excel template format. The original template included unnecessary columns that users shouldn't have to fill out.

## Fixed Template Format

### ✅ Columns You NEED to Fill (16 columns):
1. **Group ID** - Required, number to group family/participants together
2. **First Name** - Required
3. **Last Name** - Required  
4. **Email** - Optional for additional participants/minors
5. **WhatsApp Number** - Optional for additional participants/minors, 10 digits only (without +91)
6. **Age** - Required, number between 1-120
7. **Gender** - Required, must be: male, female, or other
8. **Address** - Required, full address
9. **City** - Required
10. **State** - Required
11. **Diocese** - Optional (can be empty)
12. **Parish** - Optional (can be empty)
13. **Congregation** - Required only for priest/sister retreats
14. **Emergency Contact Name** - Required
15. **Emergency Contact Phone** - Required, 10-15 digits
16. **Special Remarks** - Optional

## 🎯 **KEY INNOVATION: Group ID System**

### How It Works:
- **Same Group ID** = Family/Group booking (e.g., 1, 1, 1 for family of 3)
- **Different Group ID** = Separate individual bookings (e.g., 1, 2, 3)
- **First row in each group** = Primary participant
- **Subsequent rows** = Additional participants
- **Max participants per group**: Configurable via `MAX_ADDITIONAL_MEMBERS` in .env

### Example Template Data:
```
Group ID | First Name | Last Name | Email                | ...
---------|------------|-----------|----------------------|----
1        | John       | Doe       | john.doe@email.com   | ... (Primary)
1        | Jane       | Doe       | jane.doe@email.com   | ... (Wife)
1        | Bobby      | Doe       |                      | ... (Son, minor)
2        | Mary       | Smith     | mary.smith@email.com | ... (Individual)
```

### ❌ Columns REMOVED from Template (Auto-Generated):
- **Booking ID** - Auto-generated using `Booking::generateBookingId()`
- **Retreat Title** - Taken from the retreat you select in dropdown
- **Additional Participants** - Not used in import (handled separately)
- **Flag** - Auto-generated based on validation (RECURRENT_BOOKING, CRITERIA_FAILED)
- **Booking Date** - Auto-generated (current timestamp)
- **Status** - Auto-generated (defaults to Active)

## Template Features

### Sample Data Included
The template now includes 2 sample rows with realistic data to show the expected format.

### Data Validation
- **Gender column** has dropdown validation (male/female/other)
- **Helpful comments** on WhatsApp Number and Congregation fields
- **Color coding**: Green headers, light blue sample data

### Smart Validation
During import, the system automatically:
- Checks if participant meets retreat criteria (age, gender, congregation)
- Detects if participant has booked in the past year (RECURRENT_BOOKING flag)
- Validates all required fields and formats
- Shows warnings for criteria mismatches without blocking import

## Usage Flow

1. **Select Retreat** - Choose target retreat from dropdown (this provides the retreat context)
2. **Download Template** - Get the optimized template with only necessary columns
3. **Fill Data** - Replace sample data with real participant information
4. **Upload & Preview** - System validates and shows preview with any issues
5. **Confirm Import** - Only valid records are imported, errors are reported

## Benefits of Group ID Approach

- **✅ Scalable** - Works with any `MAX_ADDITIONAL_MEMBERS` setting (no template changes needed)
- **✅ Future-Proof** - Template never breaks when config changes
- **✅ Flexible** - Easy to add/remove family members
- **✅ User-Friendly** - Natural way to think about family bookings
- **✅ Consistent Validation** - Same rules apply to all rows
- **✅ Clean Code** - Simpler import logic, better maintainability
- **✅ Better UX** - Standard spreadsheet width, not excessively wide
- **✅ Smart Grouping** - System automatically creates proper booking relationships

## Why This Beats "Same Row" Approach

**Same Row Problems:**
- ❌ Fixed column structure breaks when MAX_ADDITIONAL_MEMBERS changes
- ❌ Wide spreadsheets hard to view/edit
- ❌ Complex validation logic for each participant column set
- ❌ Template maintenance nightmare

**Group ID Solution:**
- ✅ Template adapts automatically to any configuration
- ✅ Standard spreadsheet width
- ✅ Single validation ruleset
- ✅ Zero maintenance required

This approach makes family/group bookings intuitive while keeping the system flexible and maintainable!
