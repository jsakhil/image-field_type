<?php namespace Anomaly\ImageFieldType\Image;

use Anomaly\FilesModule\File\FilePresenter;
use Anomaly\ImageFieldType\Image\Contract\ImageInterface;
use Anomaly\Streams\Platform\Image\Image;

/**
 * Class ImagePresenter
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ImageFieldType\Image
 */
class ImagePresenter extends FilePresenter
{

    /**
     * The decorated object.
     *
     * @var ImageInterface
     */
    protected $object;

    /**
     * Return a cropped image resource.
     *
     * @return Image
     */
    public function cropped()
    {
        /* @var Image $image */
        $data  = $this->object->getData();
        $image = $this->object->image();

        if (!$data) {
            return $image;
        }

        return $image->crop($data->width, $data->height, $data->x, $data->y);
    }
}
