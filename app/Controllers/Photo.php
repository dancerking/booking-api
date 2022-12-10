<?php

namespace App\Controllers;

use App\Controllers\APIBaseController;
use App\Models\ContentCaptionModel;
use App\Models\LanguageModel;
use App\Models\PhotoContentModel;
use CodeIgniter\API\ResponseTrait;

class Photo extends APIBaseController
{
    /**
     * Return an array of photo content
     * GET/photos
     * @return mixed
     */
    use ResponseTrait;
    public function index()
    {
        /* Import config variable */
        $config = config('Config\App');

        /* Load Model */
        $photo_content_model = new PhotoContentModel();

        /* Getting host id */
        $host_id = $this->get_host_id();

        /* Getting photo for level1, level2 from PhotoContentModel */
        $L1_type_photos = $photo_content_model->get_level1_photo(
            $host_id,
            $config->LIMIT_FOR_L1_TYPE_PHOTO
        );
        $L2_type_photos = $photo_content_model->get_level2_photo(
            $host_id,
            $config->LIMIT_FOR_L2_TYPE_PHOTO
        );

        return parent::respond(
            [
                'l1_type_photos' =>
                    $L1_type_photos == null
                        ? []
                        : $L1_type_photos,
                'l2_type_photos' =>
                    $L2_type_photos == null
                        ? []
                        : $L2_type_photos,
            ],
            200
        );
    }

    /**
     * Create a new photo content
     * POST: /photos/add
     * @return mixed
     */
    public function create()
    {
        /* Import config variable */
        $config = config('Config\App');

        /* Load Rate relation Models */
        $photo_content_model = new PhotoContentModel();
        $content_caption_model = new ContentCaptionModel();
        $language_model = new LanguageModel();

        /* Getting host_id from JWT token */
        $host_id = $this->get_host_id();

        /* Validate */
        if (
            !$this->validate([
                'photo_content_level' =>
                    'required|min_length[1]|max_length[1]',
                'img_url' => 'required',
            ])
        ) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'photo'
            );
        }

        /* Getting request data */
        $photo_content_level = $this->request->getVar(
            'photo_content_level'
        );
        $photo_content_connection =
            $photo_content_level == 2
                ? $this->request->getVar(
                    'photo_content_connection'
                )
                : '';
        $photo_content_order = $this->request->getVar(
            'photo_content_order'
        )
            ? $this->request->getVar('photo_content_order')
            : 0;
        $content_caption = $this->request->getVar(
            'content_caption'
        );
        $img_url = $this->request->getVar('img_url');
        /* Validation for data format */
        if (
            $photo_content_level < 1 ||
            $photo_content_level > 2
        ) {
            return $this->notifyError(
                'photo_content_level should be 1 or 2.',
                'invalid_data',
                'photo'
            );
        }
        if (!@getimagesize($img_url)) {
            return $this->notifyError(
                'No Such Image URL',
                'notFound',
                'photo'
            );
        }

        /* Insert photo content */
        // Check if already exist
        // $upload_file_content = file_get_contents($img_url);
        // $photo_contents = $photo_content_model->findAll();
        // if ($photo_contents != null) {
        //     foreach ($photo_contents as $photo) {
        //         $my_save_dir =
        //             $_SERVER['DOCUMENT_ROOT'] .
        //             '/' .
        //             $host_id .
        //             '/photos/0_' .
        //             $photo['photo_content_url'];
        //         if (file_exists($my_save_dir)) {
        //             $existed_file_content = file_get_contents(
        //                 $my_save_dir
        //             );

        //             if (
        //                 $upload_file_content ==
        //                 $existed_file_content
        //             ) {
        //                 if (
        //                     $photo[
        //                         'photo_content_host_id'
        //                     ] == $host_id &&
        //                     $photo['photo_content_level'] ==
        //                         $photo_content_level &&
        //                     $photo[
        //                         'photo_content_connection'
        //                     ] == $photo_content_connection
        //                 ) {
        //                     return $this->notifyError(
        //                         'Duplication error',
        //                         'duplicate',
        //                         'photo'
        //                     );
        //                 }
        //             }
        //         }
        //     }
        // }
        // Insert data
        $data = [
            'photo_content_host_id' => $host_id,
            'photo_content_level' => $photo_content_level,
            'photo_content_connection' => $photo_content_connection,
            'photo_content_order' => $photo_content_order,
            'photo_content_status' => 0,
        ];

        $new_id = $photo_content_model->insert($data);
        if ($new_id) {
            // Check if image size validates
            $image_width = getimagesize($img_url)[0];
            $image_height = getimagesize($img_url)[1];

            $valid_image_size =
                $config->MINIMUM_DOWNLOAD_IMAGE_SIZE;
            if (
                !(
                    $image_width >=
                        $valid_image_size['width'] &&
                    $image_height >=
                        $valid_image_size['height']
                )
            ) {
                $photo_content_model->delete($new_id);
                return $this->notifyError(
                    'Minimum photo size is 2048*1080',
                    'invalid_data',
                    'photo'
                );
            }

            // Upload image
            $is_upload = $this->uploadImage(
                $img_url,
                $new_id,
                $host_id
            );
            if ($is_upload == null) {
                $photo_content_model->delete($new_id);
                return $this->notifyError(
                    lang('Photo.failedUpload'),
                    'invalid_data',
                    'photo'
                );
            }

            // Update photo content
            if (
                !$photo_content_model->update($new_id, [
                    'photo_content_url' =>
                        $new_id .
                        '.' .
                        $is_upload['extension'],
                    'photo_content_status' => 1,
                ])
            ) {
                $photo_content_model->delete($new_id);
                return $this->notifyError(
                    lang('Photo.failedSave'),
                    'invalid_data',
                    'photo'
                );
            }

            // Insert Caption Info into content_captions table
            $languages = $language_model->get_available_languages(
                1
            );
            if ($languages != null) {
                foreach ($languages as $language) {
                    $language_code =
                        $language->language_code;
                    if (
                        isset(
                            $content_caption->$language_code
                        ) &&
                        $content_caption->$language_code !=
                            null
                    ) {
                        $caption_data = [
                            'content_caption_host_id' => $host_id,
                            'content_caption_type' => 1,
                            'content_caption_connection_id' => $new_id,
                            'content_caption' =>
                                $content_caption->$language_code,
                            'content_caption_lang' => $language_code,
                            'content_caption_status' => 1,
                        ];
                        if (
                            !$content_caption_model->insert(
                                $caption_data
                            )
                        ) {
                            $photo_content_model->delete(
                                $new_id
                            );
                            return $this->notifyError(
                                'Failed content caption data insert',
                                'failed_create',
                                'photo'
                            );
                        }
                    }
                }
            }
            return parent::respond([
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
     * Delete photo content
     * DELETE: /photos/delete
     * @return mixed
     */
    public function delete($id = null)
    {
        /* Import config variable */
        $config = config('Config\App');

        /* Getting host id from jwt token */
        $host_id = $this->get_host_id();

        /* Load necessary Model */
        $photo_content_model = new PhotoContentModel();

        /* Validate */
        if (
            !$this->validate([
                'photo_content_id' => 'required',
            ])
        ) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'photo'
            );
        }

        /* Getting request data */
        $photo_content_id = $this->request->getVar(
            'photo_content_id'
        );

        /* Validation for data format */
        if (!ctype_digit((string) $photo_content_id)) {
            return $this->notifyError(
                'Input data format is incorrect',
                'invalid_data',
                'photo'
            );
        }
        if (
            $photo_content_model->find($photo_content_id) ==
            null
        ) {
            return $this->notifyError(
                'No Such Data',
                'notFound',
                'photo'
            );
        }
        $photo_content_filename = $photo_content_model->find(
            $photo_content_id
        );

        /* Delete photo content */
        if (
            $photo_content_model->delete($photo_content_id)
        ) {
            $content_caption_model = new ContentCaptionModel();
            $content_caption_model->delete_by(
                $host_id,
                $config->CONTENT_CAPTION_TYPE['photo'],
                $photo_content_id
            );
            // Uploaded image delete
            if (
                $photo_content_filename[
                    'photo_content_url'
                ] != ''
            ) {
                if (
                    file_exists(
                        $_SERVER['DOCUMENT_ROOT'] .
                            '/' .
                            $host_id .
                            '/photos/0_' .
                            $photo_content_filename[
                                'photo_content_url'
                            ]
                    )
                ) {
                    unlink(
                        $_SERVER['DOCUMENT_ROOT'] .
                            '/' .
                            $host_id .
                            '/photos/0_' .
                            $photo_content_filename[
                                'photo_content_url'
                            ]
                    );
                }
                if (
                    file_exists(
                        $_SERVER['DOCUMENT_ROOT'] .
                            '/' .
                            $host_id .
                            '/photos/1_' .
                            $photo_content_filename[
                                'photo_content_url'
                            ]
                    )
                ) {
                    unlink(
                        $_SERVER['DOCUMENT_ROOT'] .
                            '/' .
                            $host_id .
                            '/photos/1_' .
                            $photo_content_filename[
                                'photo_content_url'
                            ]
                    );
                }
                if (
                    file_exists(
                        $_SERVER['DOCUMENT_ROOT'] .
                            '/' .
                            $host_id .
                            '/photos/2_' .
                            $photo_content_filename[
                                'photo_content_url'
                            ]
                    )
                ) {
                    unlink(
                        $_SERVER['DOCUMENT_ROOT'] .
                            '/' .
                            $host_id .
                            '/photos/2_' .
                            $photo_content_filename[
                                'photo_content_url'
                            ]
                    );
                }
            }
            return parent::respond([
                'id' => $photo_content_id,
                'message' => 'Successfully Deleted',
            ]);
        }
        return $this->notifyError(
            'Failed Delete',
            'failed_delete',
            'photo'
        );
    }

    /* Image upload function */
    public function uploadImage(
        $image_url,
        $photo_content_id,
        $host_id
    ) {
        $url_to_image = $image_url;

        // Import config variable
        $config = config('Config\App');

        // Creating directory
        if (
            !is_dir(
                $_SERVER['DOCUMENT_ROOT'] .
                    '/' .
                    $host_id .
                    '/photos'
            )
        ) {
            mkdir(
                $_SERVER['DOCUMENT_ROOT'] . '/' . $host_id
            );
            mkdir(
                $_SERVER['DOCUMENT_ROOT'] .
                    '/' .
                    $host_id .
                    '/photos'
            );
        }

        $my_save_dir =
            $_SERVER['DOCUMENT_ROOT'] .
            '/' .
            $host_id .
            '/photos/';
        $basename = basename($url_to_image);
        $ext = pathinfo($basename, PATHINFO_EXTENSION);
        $available_ext = ['jpg', 'webp'];

        if (!in_array($ext, $available_ext)) {
            return null;
        }
        $suffix_filename = $photo_content_id . '.' . $ext;
        $filename = '0_' . $suffix_filename;
        $complete_save_loc = $my_save_dir . $filename;
        $upload = file_put_contents(
            $complete_save_loc,
            file_get_contents($url_to_image)
        );
        if ($upload) {
            $custom_photo1 = $config->CUSTOM_PHOTO1;
            $custom_photo2 = $config->CUSTOM_PHOTO2;

            // Getting Image file size
            $source_image_size = round(
                filesize($complete_save_loc) / 1024 / 1024,
                1
            );
            // Image fitting(cropping image from center)
            \Config\Services::image()
                ->withFile($complete_save_loc)
                ->fit(
                    $custom_photo1['width'],
                    $custom_photo1['height'],
                    'center'
                )
                ->save(
                    $my_save_dir . '1_' . $suffix_filename,
                    $source_image_size > 1
                        ? $config->COMPRESSION_RATIO
                        : 90
                );

            \Config\Services::image()
                ->withFile($complete_save_loc)
                ->fit(
                    $custom_photo2['width'],
                    $custom_photo2['height'],
                    'center'
                )
                ->save(
                    $my_save_dir . '2_' . $suffix_filename,
                    $source_image_size > 1
                        ? $config->COMPRESSION_RATIO
                        : 90
                );

            return [
                'extension' => $ext,
            ];
        }
        return null;
    }
}
