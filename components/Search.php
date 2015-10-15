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
     * @param string $lang Language of the page
     * @return boolean saving status
     */
    abstract public function savePage($id, $title, $body, $url, $lang);

    /**
     * Remove page from the search db
     * @param int $id ID of the page to be removed
     * @return boolean if removed successfully or not
     */
    abstract public function removePage($id);

    /**
     * Find in the search db a certain keyword
     * @param string $keyword the keyword to be searched
     * @param string $lang Language of the pages to be searched
     * @param int $limit Number of results to return
     * @param int $offset return results after this number
     * @return array array containing matched pages and its content
     */
    abstract public function find($keyword, $lang = null, $limit = 0, $offset = 0);
    
    /**
     * Return the total number of results for a cetain search 
     * @param string $keyword the keyword to be searched
     * @param string $lang Language of the pages to be searched
     * @return int total number of results
     */
    abstract public function count($keyword, $lang = null);
}
