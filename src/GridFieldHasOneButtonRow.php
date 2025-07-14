<?php

namespace SilverShop\HasOneField;

use SilverStripe\View\SSViewer;
use SilverStripe\Model\ArrayData;
use SilverStripe\Forms\GridField\GridFieldButtonRow;

class GridFieldHasOneButtonRow extends GridFieldButtonRow
{

    public function getHTMLFragments($gridField)
    {
        $data = new ArrayData(array(
            "GridField" => $gridField,
            "TargetFragmentName" => $this->targetFragment,
            "LeftFragment" => "\$DefineFragment(buttons-{$this->targetFragment}-left)",
            "RightFragment" => "\$DefineFragment(buttons-{$this->targetFragment}-right)",
        ));

        $templates = SSViewer::get_templates_by_class($this, '', __CLASS__);
        return array(
            $this->targetFragment => $data->renderWith($templates)
        );
    }
}