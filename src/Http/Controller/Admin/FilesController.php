<?php namespace Anomaly\FilesModule\Http\Controller\Admin;

use Anomaly\FilesModule\File\Contract\FileInterface;
use Anomaly\FilesModule\File\Contract\FileRepositoryInterface;
use Anomaly\FilesModule\File\Form\EntryFormBuilder;
use Anomaly\FilesModule\File\Form\FileEntryFormBuilder;
use Anomaly\FilesModule\File\Form\FileFormBuilder;
use Anomaly\FilesModule\File\Grid\FileGridBuilder;
use Anomaly\FilesModule\File\Table\FileTableBuilder;
use Anomaly\FilesModule\Folder\Command\GetFolder;
use Anomaly\FilesModule\Folder\Contract\FolderInterface;
use Anomaly\PreferencesModule\Preference\Contract\PreferenceRepositoryInterface;
use Anomaly\Streams\Platform\Http\Controller\AdminController;

/**
 * Class FilesController
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @author        Claus Hjort Bube <chb@behaviorlab.io>
 */
class FilesController extends AdminController
{

    /**
     * Display an index of existing entries.
     *
     * @param  FileTableBuilder $table
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(
        FileGridBuilder $grid,
        FileTableBuilder $table,
        PreferenceRepositoryInterface $preferences
    )
    {
        if ($preferences->value('anomaly.module.files::file_view', 'grid') == 'table') {
            return $table->render();
        }
        $table->make();
        return view('anomaly.module.files::admin/grid_view', ['grid' => $table]);
        return $grid->render();
    }

    /**
     * Change the pages view.
     *
     * @param PreferenceRepositoryInterface $preferences
     * @param                               $view
     * @return \Illuminate\Http\RedirectResponse
     */
    public function change(PreferenceRepositoryInterface $preferences, $view)
    {
        $preferences->set('anomaly.module.files::file_view', $view);

        return $this->redirect->back();
    }

    /**
     * Return the form for editing an existing file.
     *
     * @param  FileRepositoryInterface $files
     * @param  FileEntryFormBuilder $form
     * @param                                             $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(
        FileRepositoryInterface $files,
        FileFormBuilder $fileForm,
        EntryFormBuilder $entryForm,
        FileEntryFormBuilder $form,
        $id
    ) {
        /* @var FileInterface $file */
        $file = $files->find($id);

        $form->addForm(
            'entry',
            $entryForm
                ->setFormMode('edit')
                ->setModel($file->getFolder()->getEntryModelName())->setEntry($file->getEntry())
        );

        $form->addForm('file', $fileForm->setEntry($file));

        return $form->render($id);
    }

    /**
     * Redirect to a file's URL.
     *
     * @param  FileRepositoryInterface $files
     * @return \Illuminate\Http\RedirectResponse
     */
    public function view(FileRepositoryInterface $files)
    {
        /* @var FileInterface $file */
        if (!$file = $files->find($this->route->parameter('id'))) {
            abort(404);
        }

        return $this->redirect->to($file->route('view'));
    }

    /**
     * Return if a file exists or not.
     *
     * @param  FileRepositoryInterface $files
     * @param                                $folder
     * @return \Illuminate\Http\JsonResponse
     */
    public function exists(FileRepositoryInterface $files, $folder)
    {
        $success = true;
        $exists  = false;

        /* @var FolderInterface|null $folder */
        $folder = $this->dispatch(new GetFolder($folder));

        if ($folder && $file = $files->findByNameAndFolder($this->request->get('file'), $folder)) {
            $exists = true;
        }

        return $this->response->json(compact('success', 'exists'));
    }
}
