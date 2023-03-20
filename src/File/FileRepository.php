<?php namespace Anomaly\FilesModule\File;

use Anomaly\FilesModule\File\Contract\FileInterface;
use Anomaly\FilesModule\File\Contract\FileRepositoryInterface;
use Anomaly\FilesModule\Folder\Contract\FolderInterface;
use Anomaly\Streams\Platform\Entry\EntryRepository;

/**
 * Class FileRepository
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class FileRepository extends EntryRepository implements FileRepositoryInterface
{

    /**
     * The file model.
     *
     * @var FileModel
     */
    protected $model;

    /**
     * Create a new FileRepository instance.
     *
     * @param FileModel $model
     */
    function __construct(FileModel $model)
    {
        $this->model = $model;
    }

    /**
     * Find a file by it's name and folder.
     *
     * @param                     $name
     * @param  FolderInterface    $folder
     * @return null|FileInterface
     */
    public function findByNameAndFolder($name, FolderInterface $folder)
    {
        return $this->model
            ->where('name', FileSanitizer::clean($name))
            ->where('folder_id', $folder->getId())
            ->first();
    }


    /**
     *
     */
    public static function activateUsage()
    {
        $fileStreamUsages = \Illuminate\Support\Facades\DB::select("SELECT ess.id stream_id, ess.namespace stream_namespace, ess.slug stream_slug, esf.slug field_slug, esf.id field_id, esa.translatable, esf.type
            FROM elos_streams_fields esf,
                 elos_streams_assignments esa,
                 elos_streams_streams ess
            WHERE ess.id = esa.stream_id
              AND esa.field_id = esf.id
              AND esf.type IN ('anomaly.field_type.file', 'anomaly.field_type.files', 'anomaly.field_type.image', 'conduct_lab.field_type.image2')");

        $matches = [];
        foreach ($fileStreamUsages as $fileStreamUsage) {
            try {
                if ($fileStreamUsage->type !== 'anomaly.field_type.files') {
                    if (!array_key_exists($fileStreamUsage->stream_namespace, $matches)) {
                        $matches[$fileStreamUsage->stream_namespace] = [];
                    }
                    if (!array_key_exists($fileStreamUsage->stream_slug, $matches[$fileStreamUsage->stream_namespace])) {
                        $matches[$fileStreamUsage->stream_namespace][$fileStreamUsage->stream_slug] = [];
                    }
                    if (!array_key_exists($fileStreamUsage->field_slug, $matches[$fileStreamUsage->stream_namespace][$fileStreamUsage->stream_slug])) {
                        $matches[$fileStreamUsage->stream_namespace][$fileStreamUsage->stream_slug][$fileStreamUsage->field_slug] = [];
                    }
                    $data = [];
                    if ($fileStreamUsage->translatable == 1) {
                        $table_data = \Illuminate\Support\Facades\DB::select(
                            "SELECT *, " . $fileStreamUsage->field_slug . "_id as stream_file_value
                                            FROM elos_" . $fileStreamUsage->stream_namespace . '_' . $fileStreamUsage->stream_slug . "_translations
                                            WHERE " . $fileStreamUsage->field_slug . "_id IS NOT NULL");
                        foreach ($table_data as $item) {
                            if (property_exists($item, 'deleted_at') && $item->deleted_at) {
                                continue;
                            }
                            $data[$item->id] = [
                                'id' => $item->id,
                                'entry_id' => $item->entry_id,
                                'value' => $item->stream_file_value,
                            ];
                        }
                    } else {
                        $table_data = \Illuminate\Support\Facades\DB::select(
                            "SELECT *, " . $fileStreamUsage->field_slug . "_id as stream_file_value
                                            FROM elos_" . $fileStreamUsage->stream_namespace . '_' . $fileStreamUsage->stream_slug . "
                                            WHERE " . $fileStreamUsage->field_slug . "_id IS NOT NULL");
                        foreach ($table_data as $item) {
                            if (property_exists($item, 'deleted_at') && $item->deleted_at) {
                                continue;
                            }
                            $data[$item->id] = [
                                'id' => $item->id,
                                'value' => $item->stream_file_value,
                            ];
                        }
                    }
                    $slug = ucfirst(camel_case($fileStreamUsage->stream_slug));
                    $namespace = ucfirst(camel_case($fileStreamUsage->stream_namespace));

                    $entryModelName = "Anomaly\\\\Streams\\\\Platform\\\\Model\\\\{$namespace}\\\\{$namespace}{$slug}EntryModel";
                    $matches[$fileStreamUsage->stream_namespace][$fileStreamUsage->stream_slug][$fileStreamUsage->field_slug] = [
                        'stream_id' => $fileStreamUsage->stream_id,
                        'stream_namespace' => $fileStreamUsage->stream_namespace,
                        'stream_slug' => $fileStreamUsage->stream_slug,
                        'field_id' => $fileStreamUsage->field_id,
                        'field_slug' => $fileStreamUsage->field_slug,
                        'entry_model_name' => $entryModelName,
                        'translatable' => $fileStreamUsage->translatable,
                        'data' => $data,
                    ];
                }
            } catch (\Throwable $throwable) {
                dd($fileStreamUsage->stream_namespace . '_' . $fileStreamUsage->stream_slug . '.' . $fileStreamUsage->field_slug . '::' . $fileStreamUsage->field_id, $throwable->getMessage());
            }
        }

        $usage = [];
        foreach ($matches as $namespace => $streams) {
            foreach ($streams as $stream => $fields) {
                foreach ($fields as $field => $info) {
                    foreach ($info['data'] as $data) {
                        $usage[] = [
                            'namespace' => $namespace,
                            'stream' => $stream,
                            'field' => $field,
                            'entry_model_name' => $info['entry_model_name'],
                            'entry_id' => $data['id'],
                            'file_id' => $data['value'],
                        ];
                    }
                }
            }
        }

        session(['file-usage' => $usage]);
    }
}
