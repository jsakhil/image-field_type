<?php namespace Anomaly\ImageFieldType;

use Anomaly\Streams\Platform\Addon\AddonServiceProvider;

/**
 * Class ImageFieldTypeServiceProvider
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ImageFieldType
 */
class ImageFieldTypeServiceProvider extends AddonServiceProvider
{

    /**
     * The addon routes.
     *
     * @var array
     */
    protected $routes = [
        'streams/image-field_type/index/{key}'     => 'Anomaly\ImageFieldType\Http\Controller\FilesController@index',
        'streams/image-field_type/choose/{key}'    => 'Anomaly\ImageFieldType\Http\Controller\FilesController@choose',
        'streams/image-field_type/selected'        => 'Anomaly\ImageFieldType\Http\Controller\FilesController@selected',
        'streams/image-field_type/view/{id}'       => 'Anomaly\ImageFieldType\Http\Controller\FilesController@view',
        'streams/image-field_type/upload/{folder}' => 'Anomaly\ImageFieldType\Http\Controller\UploadController@index',
        'streams/image-field_type/handle'          => 'Anomaly\ImageFieldType\Http\Controller\UploadController@upload',
        'streams/image-field_type/recent'          => 'Anomaly\ImageFieldType\Http\Controller\UploadController@recent',
    ];

}
