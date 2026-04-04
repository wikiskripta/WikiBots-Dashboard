<?php

namespace Wikibots\Models;

/**
 * Class for editing (mostly) Python files holding configuration for Pywikibot
 * @author Jan Štěch
 */
class FileEditor
{
    /**
     * Method replacing text on a marked line
     * @param string $filename Path to the file to operate on
     * @param string $mark Mark that has to be present on a line to perform replacement on it
     * @param string $newContent Content to write on the marked line instead of the current content (preserving the mark text)
     * @param bool $replaceAfter TRUE if the replacement should be done after the mark, FALSE if before it
     * @return bool TRUE on success, FALSE on failure (file doesn't exist, no replacement or more than one replacement would be performed)
     */
    public function replaceMarkedLine(string $filename, string $mark, string $newContent, bool $replaceAfter = false) : bool
    {
        if (!file_exists($filename)) {
            return false;
        }
        $replacementsDone = 0;
        $file = fopen($filename, 'r+');
        $newFile = tmpfile();
        while (!feof($file)) {
            $line = fgets($file);
            $markPresent = str_contains($line, $mark);
            if ($markPresent) {
                $line = (($replaceAfter) ? $mark.$newContent : $newContent.$mark)."\n";
                $replacementsDone++;
            }
            fputs($newFile, $line); //Copy the read (or edited) line to the temporary file "copy"
        }

        if ($replacementsDone === 1) {
            fclose($file);
            rename(stream_get_meta_data($newFile)['uri'], $filename); //Replace current file with the no-longer-temporary copy
            fclose($newFile);
            return true;
        }
        fclose($file);
        fclose($newFile);
        return false;
    }
}

