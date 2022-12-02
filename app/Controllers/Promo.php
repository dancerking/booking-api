<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\PromosMappingModel;
use App\Models\PromosModel;
use App\Models\TypeMappingModel;
use CodeIgniter\API\ResponseTrait;
use DateTime;

class Promo extends APIBaseController
{
    /**
     * Return an array of Promo setting
     * GET/promos
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        /* Load necessary Model */
        $promo_model = new PromosModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validation request data */
        if (
            !$this->validate([
                'property_type' => 'required',
            ])
        ) {
            return $this->notifyError(
                'Input data format is incorrect.',
                'invalid_data',
                'promo'
            );
        }

        /* Getting request data */
        $promo_mapping_type = $this->request->getVar(
            'property_type'
        );

        /* Getting promo data per type from PromosModel */
        $promos = $promo_model->get_promos_per_type(
            $host_id,
            $promo_mapping_type
        );

        return $this->respond([
            'promos' => $promos == null ? [] : $promos,
        ]);
    }

    /**
     * Delete Promo setting
     * UPDATE /promos/update
     * @return mixed
     */
    public function update($id = null)
    {
        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Load necessary Model */
        $promo_model = new PromosModel();
        $promo_mapping_model = new PromosMappingModel();
        $type_mapping_model = new TypeMappingModel();

        /* Validate */
        if (
            !$this->validate([
                'promo_id' => 'required',
                'promo_rate' => 'required',
                'promo_code' => 'required',
                'promo_booking_to' => 'required',
                'promo_booking_from' => 'required',
                'promo_arrival' => 'required',
                'promo_departure' => 'required',
                'promo_percentage' => 'required',
                'promo_status' => 'required',
            ])
        ) {
            return $this->notifyError(
                'Input data format is incorrect.',
                'invalid_data',
                'promo'
            );
        }

        /* Getting request data */
        $promo_id = $this->request->getVar('promo_id');
        $promo_rate = $this->request->getVar('promo_rate');
        $promo_code = $this->request->getVar('promo_code');
        $promo_booking_to = $this->request->getVar(
            'promo_booking_to'
        );
        $promo_booking_from = $this->request->getVar(
            'promo_booking_from'
        );
        $promo_arrival = $this->request->getVar(
            'promo_arrival'
        );
        $promo_departure = $this->request->getVar(
            'promo_departure'
        );
        $promo_percentage = $this->request->getVar(
            'promo_percentage'
        );
        $promo_status = $this->request->getVar(
            'promo_status'
        );
        $promos_mapping = $this->request->getVar(
            'promos_mapping'
        );

        /* Validation for data format */
        if (!ctype_digit((string) $promo_id)) {
            return $this->notifyError(
                'promo_id format is incorrect.',
                'invalid_data',
                'promo'
            );
        }
        if ($promo_model->find($promo_id) == null) {
            return $this->notifyError('No such promo_id');
        }
        if (!ctype_digit((string) $promo_rate)) {
            return $this->notifyError(
                'promo_rate format is incorrect',
                'invalid_data',
                'promo'
            );
        }
        if (
            !$this->validateDate($promo_booking_to) ||
            !$this->validateDate($promo_booking_from) ||
            !$this->validateDate($promo_arrival) ||
            !$this->validateDate($promo_departure)
        ) {
            return $this->notifyError(
                'Date format is incorrect.',
                'invalid_data',
                'promo'
            );
        }
        if (
            new DateTime($promo_booking_to) <
            new DateTime($promo_booking_from)
        ) {
            return $this->notifyError(
                'To date should be larger than From date.',
                'invalid_data',
                'promo'
            );
        }
        if (
            new DateTime($promo_departure) <
            new DateTime($promo_arrival)
        ) {
            return $this->notifyError(
                'Departure date should be larger than Arrival date.',
                'invalid_data',
                'promo'
            );
        }
        if (!is_numeric($promo_percentage)) {
            return $this->notifyError(
                'promo_percentage format is incorrect.',
                'invalid_data',
                'promo'
            );
        }
        if (!ctype_digit((string) $promo_status)) {
            return $this->notifyError(
                'Status format is incorrect.',
                'invalid_data',
                'promo'
            );
        }
        if ($promo_status < 0 || $promo_status > 4) {
            return $this->notifyError(
                'promo_status should be between 0 and 4.',
                'invalid_data',
                'promo'
            );
        }
        if (!is_array($promos_mapping)) {
            return $this->notifyError(
                'promos_mapping should be array'
            );
        }
        if ($promos_mapping != null) {
            foreach ($promos_mapping as $promo_mapping) {
                if (
                    !isset(
                        $promo_mapping->promo_mapping_type
                    ) ||
                    !isset(
                        $promo_mapping->promo_mapping_status
                    )
                ) {
                    return $this->notifyError(
                        'promos_mapping requires promo_mapping_type and promo_mapping status.',
                        'invalied_data',
                        'promo'
                    );
                }
                if (
                    $promo_mapping->promo_mapping_status <
                        1 ||
                    $promo_mapping->promo_mapping_status > 4
                ) {
                    return $this->notifyError(
                        'promo_mapping_status should be between 1 and 4.',
                        'invalid_data',
                        'promo'
                    );
                }
                if (
                    $type_mapping_model
                        ->where([
                            'type_mapping_code' =>
                                $promo_mapping->promo_mapping_type,
                        ])
                        ->findAll() == null
                ) {
                    return $this->notifyError(
                        'Invalid promo_mapping_type exists',
                        'invalid_data',
                        'promo'
                    );
                }
            }
        }
        /* Update promos table*/
        if ($promo_model->find($promo_id) == null) {
            return $this->notifyError(
                'No Such ID',
                'notFound',
                'promo'
            );
        }
        if (
            !$promo_model->update($promo_id, [
                'promo_rate' => $promo_rate,
                'promo_code' => $promo_code,
                'promo_booking_to' => $promo_booking_to,
                'promo_booking_from' => $promo_booking_from,
                'promo_arrival' => $promo_arrival,
                'promo_departure' => $promo_departure,
                'promo_percentage' => $promo_percentage,
                'promo_status' => $promo_status,
            ])
        ) {
            return $this->notifyError(
                'Failed update',
                'failed_update',
                'promo'
            );
        }
        /* Update promos_mapping table */
        if ($promos_mapping != null) {
            $promo_mapping_model
                ->where([
                    'promo_mapping_host_id' => $host_id,
                    'promo_mapping_code' => $promo_id,
                ])
                ->delete();
            foreach ($promos_mapping as $promo_mapping) {
                $promo_mapping_model->insert([
                    'promo_mapping_host_id' => $host_id,
                    'promo_mapping_code' => $promo_id,
                    'promo_mapping_type' =>
                        $promo_mapping->promo_mapping_type,
                    'promo_mapping_status' =>
                        $promo_mapping->promo_mapping_status,
                ]);
            }
        }

        return $this->respond([
            'promo_id' => $promo_id,
            'message' => 'Successfully updated',
        ]);
    }

    /**
     * Delete Promo setting
     * DELETE /promos/delete
     * @return mixed
     */
    public function delete($id = null)
    {
        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Load necessary Model */
        $promo_model = new PromosModel();
        $promo_mapping_model = new PromosMappingModel();

        /* Getting request data */
        $promo_mapping_id = $this->request->getVar(
            'promo_mapping_id'
        );
        $promo_id = $this->request->getVar('promo_id');

        /* Validation for data format */
        if (
            $promo_id != null &&
            !ctype_digit((string) $promo_id)
        ) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'promo'
            );
        }
        if (
            $promo_mapping_id != null &&
            !ctype_digit((string) $promo_mapping_id)
        ) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'promo'
            );
        }
        /* Delete with status=4*/
        if (
            $promo_id == null &&
            $promo_mapping_id == null
        ) {
            return $this->notifyError(
                'No Input Data',
                'invalid_data',
                'promo'
            );
        }
        if ($promo_id != null) {
            if ($promo_model->find($promo_id) == null) {
                return $this->notifyError(
                    'No Such Data',
                    'notFound',
                    'promo'
                );
            }
            if (
                !$promo_model->update($promo_id, [
                    'promo_status' => 4,
                ])
            ) {
                return $this->notifyError(
                    'Failed delete',
                    'failed_delete',
                    'promo'
                );
            }
            $promo_mapping_ids = $promo_mapping_model
                ->where([
                    'promo_mapping_host_id' => $host_id,
                    'promo_mapping_code' => $promo_id,
                ])
                ->select('promo_mapping_id')
                ->findAll();
            if (!empty($promo_mapping_ids)) {
                foreach (
                    $promo_mapping_ids
                    as $promo_mapping_id
                ) {
                    if (
                        !$promo_mapping_model->update(
                            $promo_mapping_id[
                                'promo_mapping_id'
                            ],
                            [
                                'promo_mapping_status' => 4,
                            ]
                        )
                    ) {
                        return $this->notifyError(
                            'Failed delete',
                            'failed_delete',
                            'promo'
                        );
                    }
                }
            }
            return $this->respond([
                'promo_id' => $promo_id,
                'message' => 'Successfully deleted',
            ]);
        }
        if ($promo_mapping_id != null) {
            if (
                $promo_mapping_model->find(
                    $promo_mapping_id
                ) == null
            ) {
                return $this->notifyError(
                    'No Such Data',
                    'notFound',
                    'promo'
                );
            }
            if (
                !$promo_mapping_model->update(
                    $promo_mapping_id,
                    [
                        'promo_mapping_status' => 4,
                    ]
                )
            ) {
                return $this->notifyError(
                    'Failed delete',
                    'failed_delete',
                    'promo'
                );
            }
            $promo_mapping = $promo_mapping_model->find(
                $promo_mapping_id
            );
            if (
                !$promo_model->update(
                    $promo_mapping['promo_mapping_code'],
                    [
                        'promo_status' => 4,
                    ]
                )
            ) {
                return $this->notifyError(
                    'Failed delete',
                    'failed_delete',
                    'promo'
                );
            }
            return $this->respond([
                'promo_mapping_id' => $promo_mapping_id,
                'message' => 'Successfully deleted',
            ]);
        }
    }
}
