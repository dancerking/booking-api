<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\HostBookingModel;
use CodeIgniter\API\ResponseTrait;
use DateTime;

class Booking extends APIBaseController
{
    /**
     * Return an array of Booking
     * GET/bookings
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        $config = config('Config\App');

        /* Load HostBooking Model */
        $host_booking_model = new HostBookingModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        $rules = [
            'bookingFrom' => 'required',
            'bookingTo' => 'required',
        ];
        if (!$this->validate($rules)) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'booking'
            );
        }

        /* Getting service calendar from ServicePriceCalendarModel */
        // today > bookingFrom, today-bookingFrom <= 90
        $bookingfrom = $this->request->getVar(
            'bookingFrom'
        );
        // bookingTo > bookingFrom, bookingTo-bookingFrom <= 90,
        $bookingto = $this->request->getVar('bookingTo');

        // check if date format
        if (
            !$this->validateDate($bookingfrom) ||
            !$this->validateDate($bookingto)
        ) {
            return $this->notifyError(
                'Date format is incorrect',
                'invalid_data',
                'booking'
            );
        }
        if (
            new DateTime($bookingto) <
            new DateTime($bookingfrom)
        ) {
            return $this->notifyError(
                'To date should be larger than From date.',
                'invalid_data',
                'booking'
            );
        }
        if (
            date_diff(
                new DateTime($bookingto),
                new DateTime($bookingfrom)
            )->days > $config->MAXIMUM_DATE_RANGE
        ) {
            return $this->notifyError(
                'date range is maximum ' .
                    $config->MAXIMUM_DATE_RANGE .
                    ' days',
                'invalid_data',
                'booking'
            );
        }
        if (new DateTime($bookingfrom) < new DateTime()) {
            if (
                date_diff(
                    new DateTime(),
                    new DateTime($bookingfrom)
                )->days > $config->MAXIMUM_DATE_RANGE
            ) {
                return $this->notifyError(
                    'maximum ' .
                        $config->MAXIMUM_DATE_RANGE .
                        ' days back date range',
                    'invalid_data',
                    'booking'
                );
            }
        }

        // Getting availabile data from model
        $booking = $host_booking_model->get_bookings(
            $host_id,
            $bookingfrom,
            $bookingto
        );

        return $this->respond([
            'booking' => $booking == null ? [] : $booking,
        ]);
    }
}
