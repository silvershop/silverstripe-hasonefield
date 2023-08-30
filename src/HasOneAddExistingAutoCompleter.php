<?php

namespace SilverShop\HasOneField;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_HTMLProvider;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\View\ArrayData;
use SilverStripe\View\SSViewer;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\ORM\{ DataList, DataObject };
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\Control\HTTPResponse;

/**
 * Class GridFieldHasOneEditButton
 */
class HasOneAddExistingAutoCompleter extends GridFieldAddExistingAutocompleter
{
    /**
     * Check if a record has been set, if so, don't load the fields
     *
     * @param GridField $gridField
     *
     * @return array
     */
    public function getHTMLFragments($gridField)
    {
        if (!$gridField->getRecord()->exists()) {
            return parent::getHTMLFragments($gridField);
        }

        return [
            $this->targetFragment => ""
        ];
    }

    /**
     * Overwrite default add to and inlude redirect
     *
     * @param GridField $gridField
     * @param string $actionName Action identifier, see {@link getActions()}.
     * @param array $arguments Arguments relevant for this
     * @param array $data All form data
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == 'addto' && isset($data['relationID']) && $data['relationID']) {
            $parent = $gridField->getParent();
            $relation = $gridField->getRelation() . "ID";
            $item = DataObject::get($gridField->getModelClass())
                ->byID($data['relationID']);

            if (empty($parent)) {
                throw new ValidationException(
                    _t(__CLASS__ . '.ParentNotFound', "Parent record not found")
                );
            }

            if (empty($item)) {
                throw new ValidationException(
                    _t(__CLASS__ . '.ItemNotFound', "Related record not found")
                );
            }

            // Save this relation to the DB
            $parent->{$relation} = $data['relationID'];
            $parent->write();

            $gridField->State->GridFieldAddRelation = $data['relationID'];
            $gridField->getList()->add($item);

            Controller::curr()->getResponse()->setStatusCode(
                200,
                _t(__CLASS__ . '.Linked', "Linked")
            );
        }
    }

    /**
     * Returns a json array of a search results that can be used by for example Jquery.ui.autosuggestion
     *
     * @param GridField $gridField
     * @param HTTPRequest $request
     * @return string
     */
    public function doSearch($gridField, $request)
    {
        $dataClass = $gridField->getModelClass();

        $allList = $this->searchList ? $this->searchList : DataList::create($dataClass);

        $searchFields = ($this->getSearchFields())
            ? $this->getSearchFields()
            : $this->scaffoldSearchFields($dataClass);

        if (!$searchFields) {
            throw new LogicException(
                sprintf(
                    'HasOneAddExistingAutoCompleter: No searchable fields could be found for class "%s"',
                    $dataClass
                )
            );
        }

        $params = array();

        foreach ($searchFields as $searchField) {
            $name = (strpos($searchField, ':') !== false) ? $searchField : "$searchField:StartsWith";
            $params[$name] = $request->getVar('gridfield_relationsearch');
        }

        $results = $allList
            ->filterAny($params)
            ->sort(strtok($searchFields[0], ':'), 'ASC')
            ->limit($this->getResultsLimit())
            ->toArray();

        $savedList = $gridField->getList();

        foreach ($results as $i=>$result) {
            if ($savedList->find('ID', $result->ID)) {
                unset($results[$i]);
            }
        }

        $json = array();

        Config::nest();

        SSViewer::config()->set('source_file_comments', false);

        $viewer = SSViewer::fromString($this->resultsFormat);

        foreach ($results as $result) {
            $title = Convert::html2raw($viewer->process($result));

            $json[] = array(
                'label' => $title,
                'value' => $title,
                'id' => $result->ID,
            );
        }

        Config::unnest();

        return HTTPResponse::create()
            ->setBody(json_encode($json))
            ->addHeader('Content-Type', 'text/json');
    }
}
