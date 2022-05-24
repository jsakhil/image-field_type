<?php namespace Anomaly\ImageFieldType\Command;

use Anomaly\ImageFieldType\ImageFieldType;
use Illuminate\Http\Request;

/**
 * Class GetUploadFile
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 */
class GetUploadFile
{

    /**
     * The field type instance.
     *
     * @var ImageFieldType
     */
    protected $fieldType;

    /**
     * Create a new GetUploadFile instance.
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
     * @param  Request                                                   $request
     * @return array|\Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function handle(Request $request)
    {
        return $request->file($this->fieldType->getInputName());
    }
}
