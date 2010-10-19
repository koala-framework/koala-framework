<?php
class Vps_Update_33029 extends Vps_Update
{
    public function update()
    {
        set_time_limit(300);
        ini_set('memory_limit', '768M');

        echo "Removing not needed application/temp/modelcsv* folders. THIS COULD TAKE A WHILE!\n\n";
        $removeCount = $processedCount = 0;

        $dir = new DirectoryIterator('application/temp');
        foreach ($dir as $file) {
            $processedCount++;
            if (!$file->isDir() || $file->isDot()) continue;
            if (substr($file->getFilename(), 0, 8) != 'modelcsv') continue;

            $subdir = new DirectoryIterator('application/temp/'.$file->getFilename());
            $foundAFile = false;
            foreach ($subdir as $subfile) {
                if ($subfile->isDot()) continue;
                $foundAFile = true;
            }

            if (!$foundAFile) {
                rmdir('application/temp/'.$file->getFilename());
                $removeCount++;
            }

            if ($processedCount % 50 == 0) echo "\n Processed items: $processedCount | Removed folders: $removeCount";
        }
        echo "\n";
    }
}
