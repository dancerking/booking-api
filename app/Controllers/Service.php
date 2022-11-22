<?php

namespace App\Controllers;

use App\Models\ServiceLangModel;
use App\Models\ServiceMappingModel;
use App\Models\ServiceModel;
use App\Controllers\APIBaseController;
use App\Models\LanguageModel;
use App\Models\TypeMappingModel;
use CodeIgniter\API\ResponseTrait;

class Service extends APIBaseController
{
    /**
     * Return an array of Service
     * GET/services
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        /* Load Service Model */
        $service_model = new ServiceModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Getting service data from Service Model */
        $service_data = $service_model->get_service_data(
            $host_id
        );
        return $this->respond([
            'service_data' =>
                $service_data == null ? [] : $service_data,
        ]);
    }

    /**
     * Update a model resource
     * PUT/services/update
     * @return mixed
     */
    public function update($id = null)
    {
        // Load necessary Model
        $service_model = new ServiceModel();
        $service_mapping_model = new ServiceMappingModel();
        $service_lang_model = new ServiceLangModel();
        $type_mapping_model = new TypeMappingModel();
        $lang_model = new LanguageModel();

        // Service content
        if (
            !$this->validate([
                'service_id' => 'required',
                'service_mode' => 'required',
                'service_mandatory' => 'required',
                'service_mandatory_note' => 'required',
                'service_vat_percentage' => 'required',
                'service_status' => 'required',
            ])
        ) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'service'
            );
        }
        // Getting data from request fields
        $host_id = $this->get_host_id();
        $service_id = $this->request->getVar('service_id');
        $service_mode = $this->request->getVar(
            'service_mode'
        );
        $service_mandatory = $this->request->getVar(
            'service_mandatory'
        );
        $service_mandatory_group_name =
            $service_mandatory == 1
                ? ''
                : ($this->request->getVar(
                    'service_mandatory_group_name'
                ) == null
                    ? ''
                    : $this->request->getVar(
                        'service_mandatory_group_name'
                    ));
        $service_mandatory_note = $this->request->getVar(
            'service_mandatory_note'
        );
        $service_vat_percentage = $this->request->getVar(
            'service_vat_percentage'
        );
        $service_status = $this->request->getVar(
            'service_status'
        );
        $services_mapping = $this->request->getVar(
            'services_mapping'
        );
        $services_lang = $this->request->getVar(
            'services_lang'
        );

        // Validation input data
        if (!ctype_digit((string) $service_id)) {
            return $this->notifyError(
                'service_id format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if (!ctype_digit((string) $service_mode)) {
            return $this->notifyError(
                'service_mode format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if ($service_mode < 1 || $service_mode > 6) {
            return $this->notifyError(
                'service_mode value must be between 1 and 6',
                'invalid_data',
                'service'
            );
        }
        if (!ctype_digit((string) $service_mandatory)) {
            return $this->notifyError(
                'service_mandatory format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if (
            $service_mandatory < 0 ||
            $service_mandatory > 2
        ) {
            return $this->notifyError(
                'service_mode value must be between 0 and 2',
                'invalid_data',
                'service'
            );
        }
        if (
            !ctype_digit((string) $service_mandatory_note)
        ) {
            return $this->notifyError(
                'service_mandatory_note format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if (
            $service_mandatory_note < 0 ||
            $service_mandatory_note > 1
        ) {
            return $this->notifyError(
                'service_mandatory_note must be 0 or 1',
                'invalid_data',
                'service'
            );
        }
        if (fmod($service_vat_percentage, 1) !== 0.0) {
            return $this->notifyError(
                'service_vat_percentage format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if (!ctype_digit((string) $service_status)) {
            return $this->notifyError(
                'service_status format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if ($service_status < 0 || $service_status > 1) {
            return $this->notifyError(
                'service_status must be 0 or 1',
                'invalid_data',
                'service'
            );
        }
        if (
            $services_mapping != null &&
            !is_array($services_mapping)
        ) {
            return $this->notifyError(
                'services_mapping must be array',
                'invalid_data',
                'service'
            );
        }
        if ($services_mapping != null) {
            foreach (
                $services_mapping
                as $service_mapping
            ) {
                if (
                    !isset(
                        $service_mapping->service_mapping_type
                    ) ||
                    !isset(
                        $service_mapping->service_mapping_status
                    )
                ) {
                    return $this->notifyError(
                        'services_mapping requires service_mapping_type and service_mapping_status.',
                        'invalid_data',
                        'service'
                    );
                }
                if (
                    $service_mapping->service_mapping_status <
                        1 ||
                    $service_mapping->service_mapping_status >
                        4
                ) {
                    return $this->notifyError(
                        'service_mapping_status should be between 1 and 4.',
                        'invalid_data',
                        'service'
                    );
                }
                if (
                    $type_mapping_model
                        ->where([
                            'type_mapping_code' =>
                                $service_mapping->service_mapping_type,
                        ])
                        ->findAll() == null
                ) {
                    return $this->notifyError(
                        'Invalid service_mapping_type exists',
                        'invalid_data',
                        'service'
                    );
                }
                if (
                    $service_mapping->service_mapping_status <
                        1 ||
                    $service_mapping->service_mapping_status >
                        4
                ) {
                    return $this->notifyError(
                        'service_mapping_status should be between 1 and 4.',
                        'invalid_data',
                        'service'
                    );
                }
            }
        }

        if (!is_array($services_lang)) {
            return $this->notifyError(
                'services_lang must be array'
            );
        }
        if ($services_lang != null) {
            foreach ($services_lang as $service_lang) {
                if (
                    !isset(
                        $service_lang->service_lang_name
                    ) ||
                    !isset(
                        $service_lang->service_lang_description
                    ) ||
                    !isset($service_lang->service_lang_lang)
                ) {
                    return $this->notifyError(
                        'services_lang requires service_lang_name, service_lang_description and service_lang_lang.',
                        'invalid_data',
                        'service'
                    );
                }
                if (
                    $lang_model
                        ->where([
                            'language_code' =>
                                $service_lang->service_lang_lang,
                        ])
                        ->findAll() == null
                ) {
                    return $this->notifyError(
                        'Invalid language exists.',
                        'invalid_data',
                        'service'
                    );
                }
            }
        }

        // Update Service content
        $data = [
            'service_mode' => $service_mode,
            'service_host_id' => $host_id,
            'service_mandatory' => $service_mandatory,
            'service_mandatory_note' => $service_mandatory_note,
            'service_mandatory_group_name' => $service_mandatory_group_name,
            'service_vat_percentage' => $service_vat_percentage,
            'service_status' => $service_status,
        ];

        $check_id_exist = $service_model->is_existed_id(
            $service_id
        );
        if (!$check_id_exist) {
            return $this->notifyError(
                'No Such ID',
                'notFound',
                'service'
            );
        }
        if (!$service_model->update($service_id, $data)) {
            return $this->notifyError(
                'Failed update',
                'failed_update',
                'service'
            );
        }
        $service_mapping_ids = [];
        if ($services_mapping != null) {
            $service_mapping_model
                ->where([
                    'service_mapping_host_id' => $host_id,
                    'service_mapping_code' => $service_id,
                ])
                ->delete();
            foreach (
                $services_mapping
                as $service_mapping
            ) {
                $new_id = $service_mapping_model->insert([
                    'service_mapping_host_id' => $host_id,
                    'service_mapping_code' => $service_id,
                    'service_mapping_type' =>
                        $service_mapping->service_mapping_type,
                    'service_mapping_status' =>
                        $service_mapping->service_mapping_status,
                ]);
                array_push($service_mapping_ids, $new_id);
            }
        }

        if ($services_lang != null) {
            $service_lang_model
                ->where([
                    'service_lang_host_id' => $host_id,
                    'service_lang_service_id' => $service_id,
                ])
                ->delete();
            foreach ($services_lang as $service_lang) {
                $service_lang_model->insert([
                    'service_lang_host_id' => $host_id,
                    'service_lang_service_id' => $service_id,
                    'service_lang_name' =>
                        $service_lang->service_lang_name,
                    'service_lang_description' =>
                        $service_lang->service_lang_description,
                    'service_lang_lang' =>
                        $service_lang->service_lang_lang,
                    'service_lang_note_label' => isset(
                        $service_lang->service_lang_note_label
                    )
                        ? $service_lang->service_lang_note_label
                        : '',
                    'service_lang_group_label' => isset(
                        $service_lang->service_lang_group_label
                    )
                        ? $service_lang->service_lang_group_label
                        : '',
                ]);
            }
            if ($services_mapping != null) {
                return $this->respond([
                    'service_id' => $service_id,
                    'service_mapping_id' => $service_mapping_ids,
                    'message' => 'Successfully updated',
                ]);
            }
            return $this->respond([
                'service_id' => $service_id,
                'message' => 'Successfully updated',
            ]);
        }
    }

    /**
     * Create a model resource
     * POST/services/add
     * @return mixed
     */
    public function create()
    {
        // Load necessary Model
        $service_model = new ServiceModel();
        $service_mapping_model = new ServiceMappingModel();
        $service_lang_model = new ServiceLangModel();
        $type_mapping_model = new TypeMappingModel();
        $lang_model = new LanguageModel();

        // Service content
        if (
            !$this->validate([
                'service_mode' => 'required',
                'service_mandatory' => 'required',
                'service_mandatory_note' => 'required',
                'service_vat_percentage' => 'required',
                'service_status' => 'required',
                'services_mapping' => 'required',
                'services_lang' => 'required',
            ])
        ) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'service'
            );
        }

        $host_id = $this->get_host_id();
        $service_mode = $this->request->getVar(
            'service_mode'
        );
        $service_mandatory = $this->request->getVar(
            'service_mandatory'
        );
        $service_mandatory_group_name =
            $service_mandatory == 1
                ? ''
                : ($this->request->getVar(
                    'service_mandatory_group_name'
                ) == null
                    ? ''
                    : $this->request->getVar(
                        'service_mandatory_group_name'
                    ));
        $service_mandatory_note = $this->request->getVar(
            'service_mandatory_note'
        );
        $service_vat_percentage = $this->request->getVar(
            'service_vat_percentage'
        );
        $service_status = $this->request->getVar(
            'service_status'
        );
        $services_mapping = $this->request->getVar(
            'services_mapping'
        );
        $services_lang = $this->request->getVar(
            'services_lang'
        );

        // Validation input data
        if (!ctype_digit((string) $service_mode)) {
            return $this->notifyError(
                'service_mode format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if ($service_mode < 1 || $service_mode > 6) {
            return $this->notifyError(
                'service_mode value must be between 1 and 6',
                'invalid_data',
                'service'
            );
        }
        if (!ctype_digit((string) $service_mandatory)) {
            return $this->notifyError(
                'service_mandatory format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if (
            $service_mandatory < 0 ||
            $service_mandatory > 2
        ) {
            return $this->notifyError(
                'service_mode value must be between 0 and 2',
                'invalid_data',
                'service'
            );
        }
        if (
            !ctype_digit((string) $service_mandatory_note)
        ) {
            return $this->notifyError(
                'service_mandatory_note format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if (
            $service_mandatory_note < 0 ||
            $service_mandatory_note > 1
        ) {
            return $this->notifyError(
                'service_mandatory_note must be 0 or 1',
                'invalid_data',
                'service'
            );
        }
        if (fmod($service_vat_percentage, 1) !== 0.0) {
            return $this->notifyError(
                'service_vat_percentage format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if (!ctype_digit((string) $service_status)) {
            return $this->notifyError(
                'service_status format is incorrect',
                'invalid_data',
                'service'
            );
        }
        if ($service_status < 0 || $service_status > 1) {
            return $this->notifyError(
                'service_status must be 0 or 1',
                'invalid_data',
                'service'
            );
        }
        if (!is_array($services_mapping)) {
            return $this->notifyError(
                'services_mapping must be array',
                'invalid_data',
                'service'
            );
        }
        foreach ($services_mapping as $service_mapping) {
            if (
                !isset(
                    $service_mapping->service_mapping_type
                ) ||
                !isset(
                    $service_mapping->service_mapping_status
                )
            ) {
                return $this->notifyError(
                    'services_mapping requires service_mapping_type and service_mapping_status.',
                    'invalid_data',
                    'service'
                );
            }
            if (
                $service_mapping->service_mapping_status <
                    1 ||
                $service_mapping->service_mapping_status > 4
            ) {
                return $this->notifyError(
                    'service_mapping_status should be between 1 and 4.',
                    'invalid_data',
                    'service'
                );
            }
            if (
                $type_mapping_model
                    ->where([
                        'type_mapping_code' =>
                            $service_mapping->service_mapping_type,
                    ])
                    ->findAll() == null
            ) {
                return $this->notifyError(
                    'Invalid service_mapping_type exists',
                    'invalid_data',
                    'service'
                );
            }
            if (
                $service_mapping->service_mapping_status <
                    1 ||
                $service_mapping->service_mapping_status > 4
            ) {
                return $this->notifyError(
                    'service_mapping_status should be between 1 and 4.',
                    'invalid_data',
                    'service'
                );
            }
        }
        if (!is_array($services_lang)) {
            return $this->notifyError(
                'services_lang must be array'
            );
        }
        foreach ($services_lang as $service_lang) {
            if (
                !isset($service_lang->service_lang_name) ||
                !isset(
                    $service_lang->service_lang_description
                ) ||
                !isset($service_lang->service_lang_lang)
            ) {
                return $this->notifyError(
                    'services_lang requires service_lang_name, service_lang_description and service_lang_lang.',
                    'invalid_data',
                    'service'
                );
            }
            if (
                $lang_model
                    ->where([
                        'language_code' =>
                            $service_lang->service_lang_lang,
                    ])
                    ->findAll() == null
            ) {
                return $this->notifyError(
                    'Invalid language exists.',
                    'invalid_data',
                    'service'
                );
            }
        }
        // Insert Service content
        if (
            $service_model
                ->where([
                    'service_mode' => $service_mode,
                    'service_host_id' => $host_id,
                    'service_mandatory' => $service_mandatory,
                    'service_mandatory_note' => $service_mandatory_note,
                    'service_mandatory_group_name' => $service_mandatory_group_name,
                    'service_vat_percentage' => $service_vat_percentage,
                ])
                ->findAll() != null
        ) {
            return $this->notifyError(
                'Duplication error',
                'duplicate',
                'service'
            );
        }
        $data = [
            'service_mode' => $service_mode,
            'service_host_id' => $host_id,
            'service_mandatory' => $service_mandatory,
            'service_mandatory_note' => $service_mandatory_note,
            'service_mandatory_group_name' => $service_mandatory_group_name,
            'service_vat_percentage' => $service_vat_percentage,
            'service_status' => $service_status,
        ];

        $new_id = $service_model->insert($data);
        if ($new_id) {
            // Insert services_mapping table
            foreach (
                $services_mapping
                as $service_mapping
            ) {
                $service_mapping_model->insert([
                    'service_mapping_host_id' => $host_id,
                    'service_mapping_code' => $new_id,
                    'service_mapping_type' =>
                        $service_mapping->service_mapping_type,
                    'service_mapping_status' =>
                        $service_mapping->service_mapping_status,
                ]);
            }
            // Insert services_lang table
            foreach ($services_lang as $service_lang) {
                $service_lang_model->insert([
                    'service_lang_host_id' => $host_id,
                    'service_lang_service_id' => $new_id,
                    'service_lang_description' =>
                        $service_lang->service_lang_description,
                    'service_lang_name' =>
                        $service_lang->service_lang_name,
                    'service_lang_lang' =>
                        $service_lang->service_lang_lang,
                    'service_lang_note_label' => !isset(
                        $service_lang->service_lang_note_label
                    )
                        ? ''
                        : $service_lang->service_lang_note_label,
                    'service_lang_group_label' => !isset(
                        $service_lang_group_label
                    )
                        ? ''
                        : $service_lang->service_lang_group_label,
                ]);
            }
            return $this->respond([
                'id' => $new_id,
                'message' => 'Successfully created',
            ]);
        }
        return $this->notifyError(
            'Failed create',
            'failed_create',
            'photo'
        );
    }

    /**
     * Delete Service content
     * DELETE /services/delete
     * @return mixed
     */
    public function delete($id = null)
    {
        // Load Model
        $service_model = new ServiceModel();
        $service_mapping_model = new ServiceMappingModel();

        // Getting host id
        $host_id = $this->get_host_id();

        $service_id = $this->request->getVar('service_id');
        $service_mapping_id = $this->request->getVar(
            'service_mapping_id'
        );
        if ($service_id != null) {
            $check_id_exist = $service_model->is_existed_id(
                $service_id
            );
            if ($check_id_exist == null) {
                return $this->notifyError(
                    'No Such Data',
                    'notFound',
                    'service'
                );
            }
            if (
                !$service_model->update($service_id, [
                    'service_status' => 4,
                ])
            ) {
                return $this->notifyError(
                    'Failed delete',
                    'failed_delete',
                    'service'
                );
            }
            $service_mapping_ids = $service_mapping_model
                ->where([
                    'service_mapping_host_id' => $host_id,
                    'service_mapping_code' => $service_id,
                ])
                ->select('service_mapping_id')
                ->findAll();
            $service_mapping_ids_array = [];
            if (!empty($service_mapping_ids)) {
                foreach (
                    $service_mapping_ids
                    as $service_mapping_id
                ) {
                    array_push(
                        $service_mapping_ids_array,
                        $service_mapping_id[
                            'service_mapping_id'
                        ]
                    );
                    if (
                        !$service_mapping_model->update(
                            $service_mapping_id[
                                'service_mapping_id'
                            ],
                            [
                                'service_mapping_status' => 4,
                            ]
                        )
                    ) {
                        return $this->notifyError(
                            'Failed delete',
                            'failed_delete',
                            'service'
                        );
                    }
                }
            }
            if (!empty($service_mapping_ids_array)) {
                return $this->respond([
                    'service_id' => $service_id,
                    'service_mapping_ids' => $service_mapping_ids_array,
                    'message' => 'Successfully deleted',
                ]);
            }
            return $this->respond([
                'service_id' => $service_id,
                'message' => 'Successfully deleted',
            ]);
        }
        if ($service_mapping_id != null) {
            $check_id_exist = $service_mapping_model->is_existed_id(
                $service_mapping_id
            );
            if ($check_id_exist == null) {
                return $this->notifyError(
                    'No Such Data',
                    'notFound',
                    'service'
                );
            }
            if (
                !$service_mapping_model->update(
                    $service_mapping_id,
                    [
                        'service_mapping_status' => 4,
                    ]
                )
            ) {
                return $this->notifyError(
                    'Failed delete',
                    'failed_delete',
                    'service'
                );
            }
            $service_mapping = $service_mapping_model->find(
                $service_mapping_id
            );
            $check_service_id_exist = $service_model->is_existed_id(
                $service_mapping['service_mapping_code']
            );
            if ($check_service_id_exist) {
                if (
                    !$service_model->update(
                        $service_mapping[
                            'service_mapping_code'
                        ],
                        [
                            'service_status' => 4,
                        ]
                    )
                ) {
                    return $this->notifyError(
                        'Failed delete',
                        'failed_delete',
                        'service'
                    );
                }
            }

            if (!$check_service_id_exist) {
                return $this->respond([
                    'service_mapping_id' => $service_mapping_id,
                    'message' => 'Successfully deleted',
                ]);
            }
            return $this->respond([
                'service_id' =>
                    $service_mapping[
                        'service_mapping_code'
                    ],
                'service_mapping_id' => $service_mapping_id,
                'message' => 'Successfully deleted',
            ]);
        }
    }
}
