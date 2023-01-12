<?php namespace Anomaly\FilesModule\File;

use Anomaly\AddonsModule\Addon\AddonRepository;
use Anomaly\BlocksModule\Block\BlockModel;
use Anomaly\BlocksModule\Block\BlockRepository;
use Anomaly\PagesModule\Page\PageModel;
use Anomaly\Streams\Platform\Stream\StreamRepository;
use League\Flysystem\File;
use Illuminate\Support\Str;
use League\Flysystem\MountManager;
use Anomaly\Streams\Platform\Image\Image;
use League\Flysystem\FilesystemInterface;
use Anomaly\FilesModule\File\Command\GetType;
use Anomaly\FilesModule\File\Command\GetImage;
use Anomaly\FilesModule\File\Command\GetResource;
use Anomaly\FilesModule\Disk\Contract\DiskInterface;
use Anomaly\FilesModule\File\Contract\FileInterface;
use Anomaly\FilesModule\Disk\Adapter\AdapterFilesystem;
use Anomaly\FilesModule\File\Command\GetPreviewSupport;
use Anomaly\FilesModule\Folder\Contract\FolderInterface;
use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Model\Files\FilesFilesEntryModel;

/**
 * Class FileModel
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class FileModel extends FilesFilesEntryModel implements FileInterface
{
    /**
     * This model is versionable.
     *
     * @var bool
     */
    protected $versionable = true;

    /**
     * Always eager load these.
     *
     * @var array
     */
    protected $with = [
        'disk',
        'folder',
        'entry',
    ];

    /**
     * The cascaded relations.
     *
     * @var array
     */
    protected $cascades = [
        'entry',
    ];

    /**
     * Return the filesystem URL.
     *
     * @return null|string
     */
    public function url()
    {
        if (!$filesystem = $this->filesystem()) {
            return null;
        }

        $url = $filesystem->url($this->path());

        if (Str::startsWith($url, ['http'])) {
            $url = str_replace(' ', '+', $url);
        }

        return str_replace('\\', '/', $url);
    }

    /**
     * Return the resource filesystem.
     *
     * @return null|AdapterFilesystem|FilesystemInterface
     */
    public function filesystem()
    {
        return app(MountManager::class)->getFilesystem($this->getDiskSlug());
    }

    /**
     * Return the file resource.
     *
     * @return null|File
     */
    public function resource()
    {
        return $this->dispatch(new GetResource($this));
    }

    /**
     * Return the file path.
     *
     * @return string
     */
    public function path()
    {
        if (!$folder = $this->getFolder()) {
            return null;
        }

        return "{$folder->getSlug()}/{$this->getName()}";
    }

    /**
     * Return the filename.
     *
     * @return string
     */
    public function filename()
    {
        return pathinfo($this->getName(), PATHINFO_FILENAME);
    }

    /**
     * Get the string ID.
     *
     * @return string
     */
    public function getStrId()
    {
        return $this->str_id;
    }

    /**
     * Get the alt text.
     *
     * @return string
     */
    public function getAltText()
    {
        return $this->alt_text;
    }

    /**
     * Return the alt text or default.
     *
     * @return string
     */
    public function altText($default = null)
    {
        return $this->getAltText() ?: ($default ?: humanize(pathinfo($this->getName(), PATHINFO_FILENAME)));
    }

    /**
     * Get the related folder.
     *
     * @return null|FolderInterface
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Clean the filename.
     *
     * @param $value
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = FileSanitizer::clean($value);
    }

    /**
     * Alias for image()
     *
     * @return Image
     */
    public function make()
    {
        return $this->image();
    }

    /**
     * Return a new image instance.
     *
     * @return Image
     */
    public function image()
    {
        return $this->dispatch(new GetImage($this));
    }

    /**
     * Return the file type.
     *
     * @return string
     */
    public function type()
    {
        return $this->dispatch(new GetType($this));
    }

    /**
     * Return if the image can
     * be previewed or not.
     *
     * @return boolean
     */
    public function canPreview()
    {
        return $this->dispatch(new GetPreviewSupport($this));
    }

    /**
     * Return the file's primary mime type.
     *
     * @return string
     */
    public function primaryMimeType()
    {
        $mimes = explode('/', $this->getMimeType());

        return array_shift($mimes);
    }

    /**
     * Get the mime type.
     *
     * @return string
     */
    public function getMimeType()
    {
        // SVG Mime type bug is fixed for PHP 7.x #79045
        if ($this->mime_type == 'image/svg') {
            return 'image/svg+xml';
        }
        return $this->mime_type;
    }

    /**
     * Return the file's sub mime type.
     *
     * @return string
     */
    public function secondaryMimeType()
    {
        $mimes = explode('/', $this->getMimeType());

        return array_pop($mimes);
    }

    /**
     * Get the size.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get the width.
     *
     * @return null|int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get the height.
     *
     * @return null|int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get the extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Lowercase the extension.
     *
     * @param $value
     */
    public function setExtensionAttribute($value)
    {
        $this->attributes['extension'] = strtolower($value);
    }

    /**
     * Get the keywords.
     *
     * @return array
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Get the description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the related entry ID.
     *
     * @return null|int
     */
    public function getEntryId()
    {
        return $this->entry_id;
    }

    /**
     * Return the entry as a routable array.
     *
     * @return array
     */
    public function toRoutableArray()
    {
        $array = self::toArray();

        $folder = $this->getFolder();

        $array['folder'] = $folder->getSlug();

        return $array;
    }

    /**
     * Return the entry as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        if ($entry = $this->getEntry()) {
            $array = array_merge($entry->toArray(), $array);
        }

        $array['path'] = $this->path();
        $array['location'] = $this->location();

        return $array;
    }

    /**
     * Get the related entry.
     *
     * @return EntryInterface
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Return the file location.
     *
     * @return string
     */
    public function location()
    {
        if (!$disk = $this->getDisk()) {
            return null;
        }

        return "{$disk->getSlug()}://{$this->path()}";
    }

    /**
     * Get the related disk.
     *
     * @return DiskInterface
     */
    public function getDisk()
    {
        return $this->disk;
    }

    /**
     * Get the related disk's slug.
     *
     * @return string
     */
    public function getDiskSlug()
    {
        return $this
            ->getDisk()
            ->getSlug();
    }

    /**
     *
     */
    public function getUsage(bool $withPage = false): array
    {
        $matches = [];
        if (!session()->has('file-usage')) {
            FileRepository::activateUsage();
        }
        if (session()->has('file-usage')) {
            $usageEntries = session('file-usage');
            foreach ($usageEntries as $entry) {
                if ($entry['file_id'] == $this->id) {
                    $matches[] = $entry;
                }
            }
        }
        return $matches;
    }

    public function getUsagesAttribute()
    {
        if (session()->has('file-usage')) {
            $matches = [];
            $usageEntries = $this->getUsage();
            foreach ($usageEntries as $entry) {
                $entryTitle = '';
                $table_block_data = null;
                if ($entry['namespace'] === 'blocks') {
                    $table_block_data = \Illuminate\Support\Facades\DB::select(
                        "SELECT *
                                    FROM elos_blocks_blocks
                                    WHERE entry_id = " . $entry['entry_id'] . "
                                      AND entry_type = '" . $entry['entry_model_name'] . "'");
                }

                if ($entry['namespace'] === 'pages') {
                    $table_pages_data = \Illuminate\Support\Facades\DB::select(
                        "SELECT *
                                    FROM elos_pages_pages
                                    WHERE entry_id = " . $entry['entry_id'] . "
                                      AND entry_type = '" . $entry['entry_model_name'] . "'");
                    if ($table_pages_data) {
                        $concretePageModel = PageModel::find($table_pages_data[0]->id);
                        $entryTitle = $concretePageModel?->getTitle() . ' (' . $concretePageModel?->getPath() .')';
                    }
                }

                if ($entry['namespace'] === 'pages') {
                    $table_pages_data = \Illuminate\Support\Facades\DB::select(
                        "SELECT *
                                    FROM elos_pages_pages
                                    WHERE entry_id = " . $entry['entry_id'] . "
                                      AND entry_type = '" . $entry['entry_model_name'] . "'");
                    if ($table_pages_data) {
                        $concretePageModel = PageModel::find($table_pages_data[0]->id);
                        $entryTitle = $concretePageModel?->getTitle() . ' (' . $concretePageModel?->getPath() .')';
                    }
                }

                if ($table_block_data) {
                    $table_page_data = \Illuminate\Support\Facades\DB::select(
                        "SELECT *
                                    FROM elos_pages_pages
                                    WHERE entry_id = " . $table_block_data[0]->area_id . "
                                      AND entry_type = '" . str_replace('\\', '\\\\', $table_block_data[0]->area_type) . "'");

                    if ($table_page_data) {
                        $concretePageModel = PageModel::find($table_page_data[0]->id);
                        $entryTitle = $concretePageModel?->getTitle() . ' (' . $concretePageModel?->getPath() .')';

                        $streamRepository = app(StreamRepository::class);
                        $blockModel = $streamRepository->findBySlugAndNamespace($entry['stream'], $entry['namespace']);
                        $entryTitle = $entryTitle ? $entryTitle . ' in block: ' . $blockModel->getName() : $blockModel->getName();
                    } else {
                        $areaModel = app($table_block_data[0]->area_type);
                        $areaEntry = $areaModel->find($table_block_data[0]->area_id);
                        $addonRepository = app(AddonRepository::class);
                        $streamRepository = app(StreamRepository::class);
                        $streamModel = $streamRepository->findBySlugAndNamespace($entry['stream'], $entry['namespace']);
                        $entryName = $streamModel->getName();
                        if (Str::contains($areaModel->getStream()->getName(), '::')) {
                            $entryNamespace = explode('::', $areaModel->getStream()->getName())[0];
                            $addon = $addonRepository->findByNamespace($entryNamespace);
                            $addonStream = app($streamModel->getBoundEntryModelName());
//                            $addonStreamEntry = $addonStream->find($entry['entry_id']);
                            $entryTitle = 'Block: ' . trans($entryName) . ' (Title: ' . $areaEntry->getTitle() . ') in ' . trans($addon->getTitle()) . ' Module';
                        }
//                        dd($entryTitle, $areaModel->getStream()->getName(), $table_block_data[0], $areaModel, get_class_methods($areaModel->getStream()), $addon);
                        $entryTitle = $entryTitle ?? 'Block: ' . trans($entryName) . ' (Title: ' . $areaEntry->getTitle() . ')';
                    }
                }

                if ($entryTitle) {
                    $matches[] = $entryTitle;
                } else {
//                    dd($entry['namespace'], $entry['stream']);
                    $streamRepository = app(StreamRepository::class);
                    $streamModel = $streamRepository->findBySlugAndNamespace($entry['stream'], $entry['namespace']);
                    $entryName = $streamModel->getName();
                    if (Str::contains($entryName, '::')) {
                        $entryNamespace = explode('::', $entryName)[0];
                        $addonRepository = app(AddonRepository::class);
                        $addon = $addonRepository->findByNamespace($entryNamespace);
                        $addonStream = app($streamModel->getBoundEntryModelName());
                        $addonStreamEntry = $addonStream->find($entry['entry_id']);
                        $entryTitle = trans($entryName) . ' (Title: ' . $addonStreamEntry->getTitle() . ') in ' . trans($addon->getTitle()) . ' Module';
                    } else {
//                        $addonRepository = app(AddonRepository::class);
//                        $addonStream = app($streamModel->getBoundEntryModelName());
//                        $addonStreamEntry = $addonStream->find($entry['entry_id']);
                        $entryTitle = Str::studly($entry['namespace']) . ': ' . trans($entryName) . ' (' . json_encode($entry) . ')';
                    }

                    $matches[] = $entryTitle ?? $entry['entry_model_name'] . '('.$entryTitle.') ' . $entry['field'] . ': ID' . $entry['entry_id'];
                }
                if (!$entryTitle) {
                    $matches[] = json_encode($entry);
                }
            }

            return $matches ? join("\n", $matches) : 'No usage found';
        }
        return 'Please active file usage first';
    }

    /**
     *
     */
    public function getUsageAsString(bool $withPage = false): string
    {
        $matches = [];
        if (session()->has('file-usage')) {
            $usageEntries = $this->getUsage($withPage);
            foreach ($usageEntries as $entry) {
                $matches[] = $entry['entry_model_name'] . '->' . $entry['field'];
            }

            return join(', ', $matches);
        }
        return 'Please active file usage first';
    }

    /**
     *
     */
    public function isUsed(): ?bool
    {
        $matches = [];
        if (session()->has('file-usage')) {
            if ($this->getUsage()) {
                return true;
            }
            return false;
        }
        return null;
    }

    /**
     * Return the searchable array.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = parent::toSearchableArray();

        if ($entry = $this->getEntry()) {
            $array = array_merge($entry->toSearchableArray(), $array);
        }

        return $array;
    }
}
