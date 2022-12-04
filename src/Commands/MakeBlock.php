<?php

namespace Customberg\PHP\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Storage;

class MakeBlock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:block {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a customberg block';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $slug = 'cb-' . Str::of($name)->kebab();
        $block_content = file_get_contents(__DIR__ . '/../../stubs/BlockClass.stub');
        $view_content = file_get_contents(__DIR__ . '/../../stubs/block-view.blade.stub');
        $block_content = str_replace('BlockClass', $name, $block_content);
        $block_content = str_replace('%slug%', $slug, $block_content);

        config([
            'filesystems.disks.customberg_base_dir' => [
                'driver' => 'local',
                'root' => base_path(),
            ],
        ]);
        $disk = Storage::disk('customberg_base_dir');

        $block_path = "app/Blocks/$name.php";
        if (
            $disk->exists($block_path) == false ||
            ($disk->exists($block_path) == true &&
                $this->confirm("File $block_path already exists. Do you want to overwrite ?"))
        ) {
            $disk->put($block_path, $block_content);
            $this->info("Generated Block declaration file $block_path");
        }

        $view_path = "resources/views/blocks/$slug.blade.php";
        if (
            $disk->exists($view_path) == false ||
            ($disk->exists($view_path) == true &&
                $this->confirm("File $view_path already exists. Do you want to overwrite ?"))
        ) {
            $disk->put($view_path, $view_content);
            $this->info("Generated Block view $view_path");
        }

        return 0;
    }
}
