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
            // Return empty collection for template
            return collect([]);
        }
        
        return $this->bookings;
    }

    public function headings(): array
    {
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
        return [
            // Style the header row
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