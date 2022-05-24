<?php namespace Anomaly\ImageFieldType\Command;

use Anomaly\FilesModule\Disk\Contract\DiskRepositoryInterface;
use Anomaly\FilesModule\File\Contract\FileInterface;
use Anomaly\FilesModule\File\Contract\FileRepositoryInterface;
use Anomaly\ImageFieldType\ImageFieldType;
use Anomaly\ImageFieldType\ImageFieldTypeParser;
use Illuminate\Http\Request;
use League\Flysystem\MountManager;

/**
 * Class PerformUpload
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ImageFieldType\Command
 */
class PerformUpload
{

    /**
     * The field type instance.
     *
     * @var ImageFieldType
     */
    protected $fieldType;

    /**
     * Create a new PerformUpload instance.
     *
     * @param ImageFieldType $fieldType
     */
    public function __construct(ImageFieldType $fieldType)
    {
        $this->fieldType = $fieldType;
    }

    /**
     * Handle the command.
     *
     * @param DiskRepositoryInterface $disks
     * @param FileRepositoryInterface $files
     * @param ImageFieldTypeParser    $parser
     * @param Request                 $request
     * @param MountManager            $manager
     *
     * @return null|bool|FileInterface
     */
    public function handle(
        DiskRepositoryInterface $disks,
        FileRepositoryInterface $files,
        ImageFieldTypeParser $parser,
        Request $request,
        MountManager $manager
    ) {
        $path = trim(array_get($this->fieldType->getConfig(), 'path'), './');

        $file  = $request->file($this->fieldType->getInputName());
        $value = $request->get($this->fieldType->getInputName() . '_id');

        /**
         * Make sure we have at least
         * some kind of input.
         */
        if ($file === null) {

            if (!$value) {
                return null;
            }

            return $files->find($value);
        }

        // Make sure we have a valid upload disk. First by slug.
        if (!$disk = $disks->findBySlug($slug = array_get($this->fieldType->getConfig(), 'disk'))) {
            // If that fails look up by id.
            if (!$disk = $disks->find($id = array_get($this->fieldType->getConfig(), 'disk'))) {
                return null;
            }
        }

        // Make the path.
        $path = $parser->parse($path, $this->fieldType);
        $path = (!empty($path) ? $path . '/' : null) . $file->getClientOriginalName();

        return $manager->putStream($disk->path($path), fopen($file->getRealPath(), 'r+'));
    }
}
