<?php namespace Anomaly\ImageFieldType;

use Anomaly\FilesModule\File\Contract\FileInterface;
use Anomaly\ImageFieldType\Image\Contract\ImageInterface;
use Anomaly\ImageFieldType\Image\ImageModel;
use Anomaly\ImageFieldType\Table\ValueTableBuilder;
use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use stdClass;

/**
 * Class ImageFieldType
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class ImageFieldType extends FieldType
{

    /**
     * The database column type.
     *
     * @var string
     */
    protected $columnType = 'integer';

    /**
     * The input view.
     *
     * @var string
     */
    protected $inputView = 'anomaly.field_type.image::input';

    /**
     * The field type config.
     *
     * @var array
     */
    protected $config = [
        'folders'      => [],
        'min_height'   => 400,
        'aspect_ratio' => null,
        'mode'         => 'default',
    ];

    /**
     * The cache repository.
     *
     * @var Repository
     */
    protected $cache;

    /**
     * Create a new FileFieldType instance.
     *
     * @param Repository $cache
     */
    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get the relation.
     *
     * @return BelongsTo
     */
    public function getRelation()
    {
        $entry = $this->getEntry();

        return $entry->belongsTo(
            array_get($this->config, 'related', 'Anomaly\ImageFieldType\Image\ImageModel'),
            $this->getColumnName()
        );
    }

    /**
     * Get the config.
     *
     * @return array
     */
    public function getConfig()
    {
        $config = parent::getConfig();

        $post = str_replace('M', '', ini_get('post_max_size'));
        $file = str_replace('M', '', ini_get('upload_max_filesize'));

        $server = $file > $post ? $post : $file;

        if (!$max = array_get($config, 'max')) {
            $max = $server;
        }

        if ($max > $server) {
            $max = $server;
        }

        array_set($config, 'max', $max);

        array_set($config, 'folders', (array)$this->config('folders', []));

        return $config;
    }

    /**
     * Get the database column name.
     *
     * @return null|string
     */
    public function getColumnName()
    {
        return parent::getColumnName() . '_id';
    }

    /**
     * Get the aspect ratio.
     *
     * @return mixed
     */
    public function aspectRatio()
    {
        return eval('return ' . strip_tags(str_replace(':', '/', $this->config('aspect_ratio'))) . ';');
    }

    /**
     * Return the config key.
     *
     * @return string
     */
    public function configKey()
    {
        $key = md5(json_encode($this->getConfig()));

        $this->cache->put('image-field_type::' . $key, $this->getConfig(), 30);

        return $key;
    }

    /**
     * Value table.
     *
     * @return string
     */
    public function valueTable()
    {
        $table = app(ValueTableBuilder::class);

        $file = $this->getValue();

        if ($file instanceof FileInterface) {
            $file = $file->getId();
        }

        return $table->setUploaded([$file])->build()->load()->getTableContent();
    }

    /**
     * Return the crop data.
     *
     * @return null|stdClass
     */
    public function data()
    {
        if (!$this->entry) {
            return null;
        }

        return json_decode($this->entry->{$this->getField() . '_data'});
    }

    /**
     * Append the crop data to the model.
     *
     * @param $value
     * @return \Anomaly\Streams\Platform\Support\Presenter|null
     */
    public function decorate($value)
    {
        if (!$value instanceof ImageInterface) {
            return null;
        }

        /* @var ImageModel $value */
        $value->setData(json_decode($this->entry->{$this->getField() . '_data'}));

        return parent::decorate($value);
    }

    /**
     * Handle saving the form data ourselves.
     *
     * @param FormBuilder $builder
     */
    public function handle(FormBuilder $builder)
    {
        $entry = $builder->getFormEntry();
        $id    = $builder->getPostValue($this->getField() . '.id');
        $data  = $builder->getPostValue($this->getField() . '.data');

        // See the accessor for how IDs are handled.
        $entry->{$this->getField()} = $data;
        $entry->{$this->getField()} = $id;

        $entry->save();
    }
}
