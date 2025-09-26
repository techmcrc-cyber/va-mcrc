<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BookingsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
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
                    'whatsapp_number' => '9876543210',
                    'age' => 35,
                    'gender' => 'male',
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
                    'whatsapp_number' => '9876543211',
                    'age' => 32,
                    'gender' => 'female',
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
                    'whatsapp_number' => '',
                    'age' => 12,
                    'gender' => 'male',
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
                    'whatsapp_number' => '9876543212',
                    'age' => 28,
                    'gender' => 'female',
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
                'WhatsApp Number',
                'Age',
                'Gender',
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
            'First Name',
            'Last Name',
            'Email',
            'WhatsApp Number',
            'Age',
            'Gender',
            'Address',
            'City',
            'State',
            'Diocese',
            'Parish',
            'Congregation',
            'Emergency Contact Name',
            'Emergency Contact Phone',
            'Additional Participants',
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
                $booking['whatsapp_number'],
                $booking['age'],
                $booking['gender'],
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
            $booking->firstname,
            $booking->lastname,
            $booking->email,
            $booking->whatsapp_number,
            $booking->age,
            ucfirst($booking->gender),
            $booking->address,
            $booking->city,
            $booking->state,
            $booking->diocese ?? '',
            $booking->parish ?? '',
            $booking->congregation ?? '',
            $booking->emergency_contact_name,
            $booking->emergency_contact_phone,
            $booking->additional_participants,
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
                'E' => 15, // WhatsApp Number
                'F' => 8,  // Age
                'G' => 10, // Gender
                'H' => 30, // Address
                'I' => 15, // City
                'J' => 15, // State
                'K' => 18, // Diocese
                'L' => 18, // Parish
                'M' => 18, // Congregation
                'N' => 20, // Emergency Contact Name
                'O' => 18, // Emergency Contact Phone
                'P' => 25, // Special Remarks
            ];
        }
        
        // Export column widths - full data
        return [
            'A' => 12, // Booking ID
            'B' => 20, // Retreat Title
            'C' => 15, // First Name
            'D' => 15, // Last Name
            'E' => 25, // Email
            'F' => 15, // WhatsApp Number
            'G' => 8,  // Age
            'H' => 10, // Gender
            'I' => 30, // Address
            'J' => 15, // City
            'K' => 15, // State
            'L' => 20, // Diocese
            'M' => 20, // Parish
            'N' => 20, // Congregation
            'O' => 20, // Emergency Contact Name
            'P' => 18, // Emergency Contact Phone
            'Q' => 12, // Additional Participants
            'R' => 30, // Special Remarks
            'S' => 20, // Flag
            'T' => 18, // Booking Date
            'U' => 10, // Status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        if ($this->isTemplate) {
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
            
            // Add data validation for Gender column (F)
            $sheet->getCell('F1')->getDataValidation()
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
            
            // Add comments for important fields
            $sheet->getComment('A1')->getText()->createTextRun('Group ID: Use same number for family/group bookings (e.g., 1,1,1 for family of 3)');
            $sheet->getComment('E1')->getText()->createTextRun('WhatsApp Number: 10 digits only (without +91). Optional for minors.');
            $sheet->getComment('D1')->getText()->createTextRun('Email: Optional for additional participants/minors');
            $sheet->getComment('M1')->getText()->createTextRun('Congregation: Required for priest/sister retreats only');
            
            return [
                1 => $headerStyle, // Header row
                2 => $sampleDataStyle, // First sample row
                3 => $sampleDataStyle, // Second sample row
            ];
        }
        
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
}