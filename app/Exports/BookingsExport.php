<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class BookingsExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithCustomValueBinder
{
    protected $bookings;
    protected $isTemplate;

    public function __construct($bookings, $isTemplate = false)
    {
        $this->bookings = $bookings;
        $this->isTemplate = $isTemplate;
    }

    public function collection()
    {
        if ($this->isTemplate) {
            // Return sample data for template with guidance showing family booking
            return collect([
                // Primary participant (Group 1)
                [
                    'group_id' => 1,
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'john.doe@example.com',
                    'country_code' => '+91',
                    'whatsapp_number' => '9876543210',
                    'age' => 35,
                    'gender' => 'male',
                    'married' => 'yes',
                    'address' => '123 Main Street, Apartment 4B',
                    'city' => 'Mumbai',
                    'state' => 'Maharashtra',
                    'diocese' => 'Archdiocese of Bombay',
                    'parish' => 'St. Mary\'s Church',
                    'congregation' => 'Jesuits',
                    'emergency_contact_name' => 'Jane Doe',
                    'emergency_contact_phone' => '9876543211',
                    'special_remarks' => 'Primary participant, vegetarian meals'
                ],
                // Additional participant 1 (Group 1 - Wife)
                [
                    'group_id' => 1,
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'email' => 'jane.doe@example.com',
                    'country_code' => '+91',
                    'whatsapp_number' => '9876543211',
                    'age' => 32,
                    'gender' => 'female',
                    'married' => 'yes',
                    'address' => '123 Main Street, Apartment 4B',
                    'city' => 'Mumbai',
                    'state' => 'Maharashtra',
                    'diocese' => 'Archdiocese of Bombay',
                    'parish' => 'St. Mary\'s Church',
                    'congregation' => '',
                    'emergency_contact_name' => 'John Doe',
                    'emergency_contact_phone' => '9876543210',
                    'special_remarks' => 'Wife of primary participant'
                ],
                // Additional participant 2 (Group 1 - Son)
                [
                    'group_id' => 1,
                    'first_name' => 'Bobby',
                    'last_name' => 'Doe',
                    'email' => '',
                    'country_code' => '',
                    'whatsapp_number' => '',
                    'age' => 12,
                    'gender' => 'male',
                    'married' => 'no',
                    'address' => '123 Main Street, Apartment 4B',
                    'city' => 'Mumbai',
                    'state' => 'Maharashtra',
                    'diocese' => 'Archdiocese of Bombay',
                    'parish' => 'St. Mary\'s Church',
                    'congregation' => '',
                    'emergency_contact_name' => 'John Doe',
                    'emergency_contact_phone' => '9876543210',
                    'special_remarks' => 'Son, minor - no email/phone required'
                ],
                // Single participant (Group 2)
                [
                    'group_id' => 2,
                    'first_name' => 'Mary',
                    'last_name' => 'Smith',
                    'email' => 'mary.smith@example.com',
                    'country_code' => '+91',
                    'whatsapp_number' => '9876543212',
                    'age' => 28,
                    'gender' => 'female',
                    'married' => 'no',
                    'address' => '456 Church Lane',
                    'city' => 'Delhi',
                    'state' => 'Delhi',
                    'diocese' => 'Archdiocese of Delhi',
                    'parish' => 'St. Joseph\'s Cathedral',
                    'congregation' => '',
                    'emergency_contact_name' => 'Robert Smith',
                    'emergency_contact_phone' => '9876543213',
                    'special_remarks' => 'Individual booking'
                ]
            ]);
        }
        
        return $this->bookings;
    }

    public function headings(): array
    {
        if ($this->isTemplate) {
            // Import template headers - only fields users need to fill
            return [
                'Group ID',
                'First Name',
                'Last Name', 
                'Email',
                'Country Code',
                'WhatsApp Number',
                'Age',
                'Gender',
                'Married',
                'Address',
                'City',
                'State',
                'Diocese',
                'Parish',
                'Congregation',
                'Emergency Contact Name',
                'Emergency Contact Phone',
                'Special Remarks'
            ];
        }
        
        // Export headers - full data
        return [
            'Booking ID',
            'Retreat Title',
            'Participant Type',
            'Participant Number',
            'First Name',
            'Last Name',
            'Email',
            'Country Code',
            'WhatsApp Number',
            'Age',
            'Gender',
            'Married',
            'Address',
            'City',
            'State',
            'Diocese',
            'Parish',
            'Congregation',
            'Emergency Contact Name',
            'Emergency Contact Phone',
            'Special Remarks',
            'Flag',
            'Booking Date',
            'Status'
        ];
    }

    public function map($booking): array
    {
        if ($this->isTemplate) {
            // For template, return sample data
            return [
                $booking['group_id'],
                $booking['first_name'],
                $booking['last_name'],
                $booking['email'],
                $booking['country_code'],
                $booking['whatsapp_number'],
                $booking['age'],
                $booking['gender'],
                $booking['married'],
                $booking['address'],
                $booking['city'],
                $booking['state'],
                $booking['diocese'],
                $booking['parish'],
                $booking['congregation'],
                $booking['emergency_contact_name'],
                $booking['emergency_contact_phone'],
                $booking['special_remarks']
            ];
        }
        
        // For export, return full booking data
        return [
            $booking->booking_id,
            $booking->retreat->title ?? '',
            $booking->participant_number == 1 ? 'Primary' : 'Additional',
            $booking->participant_number,
            $booking->firstname,
            $booking->lastname,
            $booking->email ?: '',
            $booking->country_code ?: '',
            $booking->whatsapp_number ?: '',
            $booking->age,
            ucfirst($booking->gender),
            $booking->married ?? '',
            $booking->address,
            $booking->city,
            $booking->state,
            $booking->diocese ?? '',
            $booking->parish ?? '',
            $booking->congregation ?? '',
            $booking->emergency_contact_name,
            $booking->emergency_contact_phone,
            $booking->special_remarks ?? '',
            $booking->flag ? str_replace(['_', ','], [' ', ', '], $booking->flag) : '',
            $booking->created_at->format('Y-m-d H:i:s'),
            $booking->is_active ? 'Active' : 'Cancelled'
        ];
    }

    public function columnWidths(): array
    {
        if ($this->isTemplate) {
            // Template column widths - optimized for input
            return [
                'A' => 10, // Group ID
                'B' => 15, // First Name
                'C' => 15, // Last Name
                'D' => 25, // Email
                'E' => 12, // Country Code
                'F' => 15, // WhatsApp Number
                'G' => 8,  // Age
                'H' => 10, // Gender
                'I' => 10, // Married
                'J' => 30, // Address
                'K' => 15, // City
                'L' => 15, // State
                'M' => 18, // Diocese
                'N' => 18, // Parish
                'O' => 18, // Congregation
                'P' => 20, // Emergency Contact Name
                'Q' => 18, // Emergency Contact Phone
                'R' => 25, // Special Remarks
            ];
        }
        
        // Export column widths - full data
        return [
            'A' => 12, // Booking ID
            'B' => 20, // Retreat Title
            'C' => 12, // Participant Type
            'D' => 8,  // Participant Number
            'E' => 15, // First Name
            'F' => 15, // Last Name
            'G' => 25, // Email
            'H' => 12, // Country Code
            'I' => 15, // WhatsApp Number
            'J' => 8,  // Age
            'K' => 10, // Gender
            'L' => 10, // Married
            'M' => 30, // Address
            'N' => 15, // City
            'O' => 15, // State
            'P' => 20, // Diocese
            'Q' => 20, // Parish
            'R' => 20, // Congregation
            'S' => 20, // Emergency Contact Name
            'T' => 18, // Emergency Contact Phone
            'U' => 30, // Special Remarks
            'V' => 20, // Flag
            'W' => 18, // Booking Date
            'X' => 10, // Status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        if ($this->isTemplate) {
            // Format Country Code column (E) as TEXT to allow + symbol
            $sheet->getStyle('E:E')->getNumberFormat()->setFormatCode('@');
            
            // Template-specific styling
            $headerStyle = [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4CAF50'], // Green header for template
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            
            $sampleDataStyle = [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF0F8FF'], // Light blue for sample data
                ],
                'font' => ['italic' => true],
            ];
            
            // Add data validation for Gender column (H)
            $sheet->getCell('H1')->getDataValidation()
                ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
                ->setAllowBlank(false)
                ->setShowInputMessage(true)
                ->setShowErrorMessage(true)
                ->setErrorTitle('Invalid Gender')
                ->setError('Please select: male, female, or other')
                ->setPromptTitle('Select Gender')
                ->setPrompt('Choose from the dropdown: male, female, or other')
                ->setFormula1('"male,female,other"');
            
            // Add data validation for Married column (I)
            $sheet->getCell('I1')->getDataValidation()
                ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
                ->setAllowBlank(true)
                ->setShowInputMessage(true)
                ->setShowErrorMessage(true)
                ->setErrorTitle('Invalid Marital Status')
                ->setError('Please select: yes or no')
                ->setPromptTitle('Select Marital Status')
                ->setPrompt('Choose from the dropdown: yes (married) or no (unmarried)')
                ->setFormula1('"yes,no"');
            
            // Add comments for important fields
            $sheet->getComment('A1')->getText()->createTextRun('Group ID: Use same number for family/group bookings (e.g., 1,1,1 for family of 3)');
            $sheet->getComment('E1')->getText()->createTextRun('Country Code: Format +91, +1, +44, etc. Default is +91 (India)');
            $sheet->getComment('F1')->getText()->createTextRun('WhatsApp Number: 10 digits only (without country code). Optional for minors.');
            $sheet->getComment('D1')->getText()->createTextRun('Email: Optional for additional participants/minors');
            $sheet->getComment('O1')->getText()->createTextRun('Congregation: Required for priest/sister retreats only');
            
            return [
                1 => $headerStyle, // Header row
                2 => $sampleDataStyle, // First sample row
                3 => $sampleDataStyle, // Second sample row
            ];
        }
        
        // Format Country Code column (H) as TEXT for export to preserve + symbol
        $sheet->getStyle('H:H')->getNumberFormat()->setFormatCode('@');
        
        // Export styling
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE2E6EA'],
                ],
            ],
        ];
    }

    /**
     * Custom value binder to force country codes to be stored as text
     */
    public function bindValue(Cell $cell, $value)
    {
        // Get column letter
        $column = $cell->getColumn();
        
        // For template: Column E is Country Code
        // For export: Column H is Country Code
        if (($this->isTemplate && $column === 'E') || (!$this->isTemplate && $column === 'H')) {
            // Force country code to be text to preserve the + sign
            if (is_string($value) && strpos($value, '+') === 0) {
                $cell->setValueExplicit($value, DataType::TYPE_STRING);
                return true;
            }
        }
        
        // Default behavior for other cells
        return parent::bindValue($cell, $value);
    }
}