<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\ORM\Internal\Hydration\ObjectHydrator;

class MovieHydrator extends ObjectHydrator
{
    /*
    protected function hydrateAllData(): Movie
    {
        $row = $this->_stmt->fetch(PDO::FETCH_ASSOC);
        dump($row);
        die();
        $movie = new Movie();
        return $movie;
    }
*/

    /**
     * {@inheritdoc}
     */
    protected function hydrateRowData(array $data, array &$result)
    {
        $hydrated_result = array();
        parent::hydrateRowData($data, $hydrated_result);
        //dump($hydrated_result);

        /** @var Movie $movie */
        $movie = $hydrated_result[0][0];
        if (!empty($data['sclr_0'])) $movie->setMovieNotation($data['sclr_0']);
        $result[] = $movie;
    }
}
