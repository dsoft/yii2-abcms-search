<?php
namespace abcms\search\components;

use yii\base\Component;

abstract class Search extends Component
{
    
    /**
     * Add or update page in the search db
     * @param int $id Page ID
     * @param string $title Title of the page
     * @param string $body Body of the page
     * @param string $url Link of the page
     * @return boolean saving status
     */
    abstract public function savePage($id, $title, $body, $url);
    
    /**
     * Remove page from the search db
     * @param int $id ID of the page to be removed
     * @return boolean if removed successfully or not
     */
    abstract public function removePage($id);
    
    
    /**
     * Find in the search db a certain keyword
     * @param string $keyword the keyword to be searched
     * @return array array containing matched pages and its content
     */
    abstract public function find($keyword);
}