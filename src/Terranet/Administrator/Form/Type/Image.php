<?php

namespace Terranet\Administrator\Form\Type;

class Image extends File
{
    /**
     * @return array|string
     */
    protected function listFiles()
    {
        $files = [];
        foreach ($styles = $this->value()->getConfig()->styles as $style) {
            if ($this->isOriginal($style) && $this->hasMany($styles)) {
                continue;
            }
            $files[] = '<img src="'.$this->value()->url($style->name).'" />';
        }

        $files = implode('&nbsp;', $files);

        return $files;
    }

    private function isOriginal($style)
    {
        return 'original' == $style->name;
    }

    /**
     * @param $styles
     *
     * @return bool
     */
    protected function hasMany($styles)
    {
        return count($styles) > 1;
    }
}
