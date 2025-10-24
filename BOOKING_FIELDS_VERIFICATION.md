# Booking Fields Verification

## ✅ All Fields Are Being Used

After reviewing the code, **ALL fields are being used** for both primary and secondary participants. There are no unused fields.

## Required Fields for ALL Participants

### Personal Information
| Field | Required | Used For | Notes |
|-------|----------|----------|-------|
| `firstname` | ✅ Yes | Identification, emails, reports | - |
| `lastname` | ✅ Yes | Identification, emails, reports | - |
| `age` | ✅ Yes | Criteria validation, statistics | Used to check age restrictions |
| `gender` | ✅ Yes | Criteria validation, room allocation | male/female/other |
| `married` | ⚠️ Optional | Criteria validation | yes/no - Used for couples retreats |

### Contact Information
| Field | Required | Used For | Notes |
|-------|----------|----------|-------|
| `email` | ✅ Yes | Communication, confirmations | Each participant needs their own |
| `whatsapp_number` | ✅ Yes | Communication, status checking | 10 digits, used as identifier |
| `address` | ✅ Yes | Records, emergency contact | Full address |
| `city` | ✅ Yes | Demographics, reporting | - |
| `state` | ✅ Yes | Demographics, reporting | - |

### Religious Information
| Field | Required | Used For | Notes |
|-------|----------|----------|-------|
| `diocese` | ⚠️ Optional | Catholic retreats | Required for some retreat types |
| `parish` | ⚠️ Optional | Catholic retreats | Required for some retreat types |
| `congregation` | ⚠️ Conditional | Priests/Sisters only retreats | **Required** if retreat criteria is `priests_only` or `sisters_only` |

### Emergency Contact
| Field | Required | Used For | Notes |
|-------|----------|----------|-------|
| `emergency_contact_name` | ✅ Yes | Safety, emergencies | Must have for each participant |
| `emergency_contact_phone` | ✅ Yes | Safety, emergencies | Must have for each participant |

### Additional Information
| Field | Required | Used For | Notes |
|-------|----------|----------|-------|
| `special_remarks` | ⚠️ Optional | Dietary restrictions, medical needs | Important for retreat planning |

## System Fields (Auto-filled)

| Field | Set By | Purpose |
|-------|--------|---------|
| `booking_id` | System | Groups participants together |
| `retreat_id` | User selection | Links to retreat |
| `participant_number` | System | 1 = Primary, 2-4 = Secondary |
| `additional_participants` | System | Count of secondary participants (only for primary) |
| `flag` | System | Validation warnings (criteria mismatch, recurrent booking) |
| `is_active` | System | true = active, false = cancelled |
| `created_by` | System | Admin user ID (null for API bookings) |
| `updated_by` | System | Last admin who modified |

## Why ALL Fields Are Needed for Secondary Participants

### 1. **Individual Identification**
Each participant is a separate person who needs their own:
- Email for confirmations
- WhatsApp for communication
- Emergency contact for safety

### 2. **Criteria Validation**
The system validates EACH participant against retreat criteria:
- Age restrictions (e.g., adults only)
- Gender restrictions (e.g., women's retreat)
- Marital status (e.g., couples retreat)
- Religious status (e.g., priests only)

### 3. **Recurrent Booking Check**
The system checks if EACH participant has attended recently:
- Uses: firstname, lastname, whatsapp_number, email
- Prevents same person from booking multiple times

### 4. **Legal & Safety Requirements**
- Emergency contacts are legally required
- Full address needed for insurance
- Age verification for minors

### 5. **Communication**
- Each participant gets their own confirmation email
- WhatsApp notifications sent individually
- Cancellation notices sent to each person

## Database Structure

```sql
bookings table:
- id (primary key)
- booking_id (groups participants)
- participant_number (1, 2, 3, or 4)
- firstname, lastname, age, gender, married
- email, whatsapp_number
- address, city, state
- diocese, parish, congregation
- emergency_contact_name, emergency_contact_phone
- special_remarks
- retreat_id, flag, is_active
- created_by, updated_by
- timestamps
```

## Example: Family of 3

```
Booking ID: BK20241025001

Participant 1 (Primary):
- Name: John Doe
- Email: john@email.com
- WhatsApp: 1234567890
- Emergency: Jane Doe (9876543210)
- All other fields filled

Participant 2 (Secondary):
- Name: Mary Doe
- Email: mary@email.com  ← Different email
- WhatsApp: 2345678901    ← Different number
- Emergency: John Doe (1234567890)  ← Can be same
- All other fields filled

Participant 3 (Secondary):
- Name: Tom Doe
- Email: tom@email.com    ← Different email
- WhatsApp: 3456789012    ← Different number
- Emergency: John Doe (1234567890)
- All other fields filled
```

## Validation Rules

### For ALL Participants (Primary & Secondary):

```php
'firstname' => 'required|string|max:255',
'lastname' => 'required|string|max:255',
'whatsapp_number' => 'required|numeric|digits:10',
'age' => 'required|integer|min:1|max:120',
'email' => 'required|email|max:255',
'address' => 'required|string|max:500',
'gender' => 'required|in:male,female,other',
'married' => 'nullable|in:yes,no',
'city' => 'required|string|max:255',
'state' => 'required|string|max:255',
'diocese' => 'nullable|string|max:255',
'parish' => 'nullable|string|max:255',
'congregation' => 'nullable|string|max:255',  // Required for priests/sisters retreats
'emergency_contact_name' => 'required|string|max:255',
'emergency_contact_phone' => 'required|string|max:20',
'special_remarks' => 'nullable|string|max:1000',
```

## Admin vs Frontend

### Admin Booking Form
- Can create bookings with flags
- Can override validation
- Can set created_by/updated_by
- Has additional controls

### Frontend Booking Form
- Strict validation (no flags)
- Cannot override criteria
- Auto-blocks recurrent bookings
- Simpler interface

## Summary

✅ **All fields are necessary and being used**
✅ **No unused fields in the database**
✅ **Each participant needs complete information**
✅ **System validates each participant individually**
✅ **Both primary and secondary participants have same requirements**

The current implementation is correct and follows best practices for retreat management systems.

---

**Conclusion:** No changes needed. All fields serve important purposes for identification, validation, communication, and safety.
