<?php namespace Anomaly\ImageFieldType\Image\Contract;

use Anomaly\FilesModule\File\Contract\FileInterface;

/**
 * Interface ImageInterface
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ImageFieldType\Image\Contract
 */
interface ImageInterface extends FileInterface
{

    /**
     * Get the data.
     *
     * @return \stdClass
     */
    public function getData();

    /**
     * Set the data.
     *
     * @param \stdClass|null $data
     * @return $this
     */
    public function setData($data);
}
