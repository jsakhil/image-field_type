<?php namespace Anomaly\ImageFieldType\Http\Controller;

use Anomaly\FilesModule\File\Contract\FileRepositoryInterface;
use Anomaly\FilesModule\File\FileReader;
use Anomaly\FilesModule\Folder\Command\GetFolder;
use Anomaly\FilesModule\Folder\Contract\FolderInterface;
use Anomaly\FilesModule\Folder\Contract\FolderRepositoryInterface;
use Anomaly\ImageFieldType\Table\FileTableBuilder;
use Anomaly\ImageFieldType\Table\ValueTableBuilder;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Request;

/**
 * Class FilesController
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ImageFieldType\Http\Controller
 */
class FilesController extends AdminController
{

    /**
     * Return an index of existing files.
     *
     * @param FileTableBuilder $table
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(FileTableBuilder $table)
    {
        return $table->render();
    }

    /**
     * Return a list of folders to choose from.
     *
     * @param FolderRepositoryInterface $folders
     * @param Repository                $cache
     * @param Request                   $request
     * @return \Illuminate\View\View
     */
    public function choose(FolderRepositoryInterface $folders, Repository $cache, Request $request)
    {
        $allowed = [];

        $config = $cache->get('image-field_type::' . $request->route('key'), []);

        foreach (array_get($config, 'folders', []) as $identifier) {

            /* @var FolderInterface $folder */
            if ($folder = $this->dispatch(new GetFolder($identifier))) {
                $allowed[] = $folder;
            }
        }

        if (!$allowed) {
            $allowed = $folders->all();
        }

        return $this->view->make(
            'anomaly.field_type.image::choose',
            [
                'folders' => $allowed,
            ]
        );
    }

    /**
     * Return a table of selected files.
     *
     * @param ValueTableBuilder $table
     * @return null|string
     */
    public function selected(ValueTableBuilder $table)
    {
        return $table->setUploaded(explode(',', $this->request->get('uploaded')))->make()->getTableContent();
    }

    /**
     * Return the view of an image.
     *
     * @param FileReader              $output
     * @param FileRepositoryInterface $files
     * @param                         $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(FileReader $output, FileRepositoryInterface $files, $id)
    {
        return $output->make($files->find($id));
    }
}
