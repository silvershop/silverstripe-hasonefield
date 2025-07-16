<?php

namespace SilverShop\HasOneField;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Validation\ValidationException;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\View\SSViewer;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\ORM\{ DataList, DataObject };
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\View\TemplateEngine;
use SilverStripe\View\ViewLayerData;
use LogicException;

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
            if (!$parent->isInDB()) {
                return;
            }
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
        $searchStr = $request->getVar('gridfield_relationsearch');
        $dataClass = $gridField->getModelClass();

        if (!is_a($dataClass, DataObject::class, true)) {
            throw new LogicException(__CLASS__ . " must be used with DataObject subclasses. Found '$dataClass'");
        }

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

        $params = [];
        foreach ($searchFields as $searchField) {
            $name = (strpos($searchField ?? '', ':') !== false) ? $searchField : "$searchField:StartsWith";
            $params[$name] = $searchStr;
        }

        $results = null;
        if ($this->searchList) {
            // Assume custom sorting, don't apply default sorting
            $results = $this->searchList;
        } else {
            $results = DataList::create($dataClass);
        }

        // Apply baseline filtering and limits which should hold regardless of any customisations
        $results = $results
            ->filterAny($params)
            ->sort(strtok($searchFields[0] ?? '', ':'), 'ASC')
            ->limit($this->getResultsLimit())
            ->toArray();

        $json = [];
        Config::nest();
        SSViewer::config()->set('source_file_comments', false);

        $engine = Injector::inst()->create(TemplateEngine::class);
        foreach ($results as $result) {
            if (!$result->canView()) {
                continue;
            }
            $title = Convert::html2raw(
                $engine->renderString($this->resultsFormat, ViewLayerData::create($result), cache: false)
            );
            $json[] = [
                'label' => $title,
                'value' => $title,
                'id' => $result->ID,
            ];
        }
        Config::unnest();
        $response = new HTTPResponse(json_encode($json));
        $response->addHeader('Content-Type', 'application/json');
        return $response;
    }
}
